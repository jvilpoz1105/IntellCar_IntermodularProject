# Definición de la instancia EC2
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"]
  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }
}

# Variables locales para el user_data
locals {
  bucket_name  = "intellcar-web-tfg-jose"
  api_port    = "8080"
}

resource "aws_instance" "intellcar_server" {
  ami                    = data.aws_ami.ubuntu.id
  instance_type          = "t3.small"
  subnet_id              = aws_subnet.public_subnet.id
  vpc_security_group_ids = [aws_security_group.web_sg.id]
  key_name               = var.ssh_key_name

  iam_instance_profile = "LabInstanceProfile" # <--- ESTO ES CLAVE

 user_data = base64encode(<<-EOT
#!/bin/bash

# 1. Variables (Inyectadas por Terraform)
DUCK_DOMAIN="${var.duck_domain}"
BUCKET_NAME="${local.bucket_name}"
AWS_REGION="${var.aws_region}"

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y nginx certbot python3-certbot-nginx awscli docker.io docker-compose

systemctl start docker
usermod -aG docker ubuntu

# 2. Sincronización (Funcionará solo si añades el iam_instance_profile a la EC2)
# He añadido un pequeño bucle por si la red tarda en arrancar
for i in {1..5}; do
  aws s3 sync s3://$BUCKET_NAME/ /var/www/html/ --delete --region $AWS_REGION && break || sleep 10
done

chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# 3. Configuración Nginx Inicial (HTTP)
# IMPORTANTE: He quitado las comillas de 'NGINX_EOF' para que las variables 
# de Terraform se inyecten, pero escapamos los $ de Nginx con \
cat > /etc/nginx/sites-available/default <<NGINX_EOF
server {
    listen 80;
    listen [::]:80;
    server_name _;
    root /var/www/html;
    index index.html;
    location / {
        try_files \$uri \$uri/ /index.html;
    }
    location /api/ {
        proxy_pass http://localhost:8080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
    }
}
NGINX_EOF

systemctl restart nginx

# 4. Intento de Certbot (Añadimos un pequeño delay para que el DNS respire)
sleep 20
certbot --nginx -d $DUCK_DOMAIN --non-interactive --agree-tos --email ${var.certbot_email} || echo "Certbot failed"

# 5. Configuración HTTPS (Solo si Certbot creó el certificado)
if [ -f /etc/letsencrypt/live/$DUCK_DOMAIN/fullchain.pem ]; then
    cat > /etc/nginx/sites-available/default <<NGINX_HTTPS_EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DUCK_DOMAIN;
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DUCK_DOMAIN;

    ssl_certificate /etc/letsencrypt/live/$DUCK_DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DUCK_DOMAIN/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;

    root /var/www/html;
    index index.html;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    location /api/ {
        proxy_pass http://localhost:8080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
    }
}
NGINX_HTTPS_EOF
    systemctl restart nginx
fi

# 6. Cron (Sincronización automática cada 5 min para que siempre esté al día)
echo "*/5 * * * * aws s3 sync s3://$BUCKET_NAME/ /var/www/html/ --delete --region $AWS_REGION && chown -R www-data:www-data /var/www/html" | tee -a /etc/cron.d/s3-sync
EOT
)

  tags = { Name = "IntellCar-API-Server" }
}

resource "aws_eip_association" "eip_assoc" {
  instance_id   = aws_instance.intellcar_server.id
  allocation_id = aws_eip.web_ip.id
}