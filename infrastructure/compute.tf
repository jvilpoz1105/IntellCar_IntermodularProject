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

  user_data = base64encode(<<-EOT
#!/bin/bash

# Variables de Terraform (Se inyectan correctamente)
DUCK_DOMAIN="${var.duck_domain}"
BUCKET_NAME="${local.bucket_name}"
API_PORT="${local.api_port}"
AWS_REGION="${var.aws_region}"

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y nginx certbot python3-certbot-nginx awscli docker.io docker-compose

systemctl start docker
usermod -aG docker ubuntu

# Sincronización inicial
aws s3 sync s3://${local.bucket_name}/ /var/www/html/ --delete

# 1. Configuración Nginx Inicial (HTTP)
# Usamos $$ para que Terraform lo convierta en un solo $ en la máquina final
cat > /etc/nginx/sites-available/default << 'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    server_name _;
    root /var/www/html;
    index index.html;
    location / {
        try_files $$uri $$uri/ /index.html;
    }
    location /api/ {
        proxy_pass http://localhost:8080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $$host;
        proxy_set_header X-Real-IP $$remote_addr;
        proxy_set_header X-Forwarded-For $$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $$scheme;
        proxy_cache_bypass $$http_upgrade;
    }
}
NGINX_EOF

systemctl restart nginx

# 2. Intento de Certbot
certbot --nginx -d ${var.duck_domain} --non-interactive --agree-tos --email ${var.certbot_email} || echo "Certbot failed"

# 3. Si Certbot tuvo éxito, sobreescribimos con la config HTTPS
if [ -f /etc/letsencrypt/live/${var.duck_domain}/fullchain.pem ]; then
    cat > /etc/nginx/sites-available/default << 'NGINX_HTTPS_EOF'
server {
    listen 80;
    listen [::]:80;
    server_name ${var.duck_domain};
    return 301 https://$$host$$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ${var.duck_domain};

    ssl_certificate /etc/letsencrypt/live/${var.duck_domain}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${var.duck_domain}/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;

    root /var/www/html;
    index index.html;

    location / {
        try_files $$uri $$uri/ /index.html;
    }

    location /api/ {
        proxy_pass http://localhost:8080/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $$host;
        proxy_set_header X-Real-IP $$remote_addr;
        proxy_set_header X-Forwarded-For $$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $$scheme;
        proxy_cache_bypass $$http_upgrade;
    }
}
NGINX_HTTPS_EOF
    systemctl restart nginx
fi

# Tareas programadas
echo "0 0 * * * certbot renew --quiet" | tee -a /etc/cron.d/certbot-renew
echo "*/5 * * * * aws s3 sync s3://${local.bucket_name}/ /var/www/html/ --delete" | tee -a /etc/cron.d/s3-sync
EOT
)

  tags = { Name = "IntellCar-API-Server" }
}