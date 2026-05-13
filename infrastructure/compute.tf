# Definición de la instancia EC2
# 1. Buscar la imagen de Ubuntu más reciente (para no poner IDs raros)
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical (los creadores de Ubuntu)
  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }
}

# 2. Crear la instancia EC2
resource "aws_instance" "intellcar_server" {
  ami           = data.aws_ami.ubuntu.id
  instance_type = "t3.small" # Ajustado a tu presupuesto de 50$
  subnet_id     = aws_subnet.public_subnet.id
  vpc_security_group_ids = [aws_security_group.web_sg.id]
  key_name      = var.ssh_key_name # Usar la variable de Terraform

  # SCRIPT DE ARRANQUE (Docker + Nginx + SSL + Frontend)
  user_data = <<-EOF
              #!/bin/bash

              # Variables
              DUCK_DOMAIN="${var.duck_domain}"
              BUCKET_NAME="intellcar-web-tfg-jose"
              AWS_REGION="${var.aws_region}"
              API_PORT="8080"

              # 1. Actualizar e instalar paquetes
              export DEBIAN_FRONTEND=noninteractive
              apt-get update
              apt-get install -y nginx certbot python3-certbot-nginx awscli docker.io docker-compose

              # 2. Iniciar Docker
              systemctl start docker
              usermod -aG docker ubuntu

              # 3. Descargar frontend desde S3
              aws s3 sync s3://${BUCKET_NAME}/ /var/www/html/ --delete

              # 4. Crear configuración inicial de Nginx (HTTP solo para certbot)
              cat > /etc/nginx/sites-available/default << 'NGINX_EOF'
              server {
                  listen 80;
                  listen [::]:80;
                  server_name _;

                  root /var/www/html;
                  index index.html;

                  location / {
                      try_files $uri $uri/ /index.html;
                  }

                  location /api/ {
                      proxy_pass http://localhost:${API_PORT}/;
                      proxy_http_version 1.1;
                      proxy_set_header Upgrade $http_upgrade;
                      proxy_set_header Connection 'upgrade';
                      proxy_set_header Host $host;
                      proxy_set_header X-Real-IP $remote_addr;
                      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                      proxy_set_header X-Forwarded-Proto $scheme;
                      proxy_cache_bypass $http_upgrade;
                  }
              }
              NGINX_EOF

              # 5. Reiniciar Nginx para que funcione en puerto 80
              systemctl enable nginx
              systemctl restart nginx

              # 6. Obtener certificado SSL (primero en HTTP, luego configurar HTTPS)
              certbot --nginx -d ${DUCK_DOMAIN} --non-interactive --agree-tos --email ${var.certbot_email} || echo "Certbot failed, retry later"

              # 7. Forzar redirección HTTP -> HTTPS (actualizar config)
              if [ -f /etc/letsencrypt/options-ssl-nginx.conf ]; then
                  cat > /etc/nginx/sites-available/default << 'NGINX_HTTPS_EOF'
                  server {
                      listen 80;
                      listen [::]:80;
                      server_name ${DUCK_DOMAIN};
                      return 301 https://$host$request_uri;
                  }

                  server {
                      listen 443 ssl http2;
                      listen [::]:443 ssl http2;
                      server_name ${DUCK_DOMAIN};

                      ssl_certificate /etc/letsencrypt/live/${DUCK_DOMAIN}/fullchain.pem;
                      ssl_certificate_key /etc/letsencrypt/live/${DUCK_DOMAIN}/privkey.pem;
                      include /etc/letsencrypt/options-ssl-nginx.conf;

                      root /var/www/html;
                      index index.html;

                      location / {
                          try_files $uri $uri/ /index.html;
                      }

                      location /api/ {
                          proxy_pass http://localhost:${API_PORT}/;
                          proxy_http_version 1.1;
                          proxy_set_header Upgrade $http_upgrade;
                          proxy_set_header Connection 'upgrade';
                          proxy_set_header Host $host;
                          proxy_set_header X-Real-IP $remote_addr;
                          proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                          proxy_set_header X-Forwarded-Proto $scheme;
                          proxy_cache_bypass $http_upgrade;
                      }
                  }
                  NGINX_HTTPS_EOF

                  systemctl restart nginx
              fi

              # 8. Configurar renovación automática de certificado
              echo "0 0 * * * certbot renew --quiet" | tee -a /etc/cron.d/certbot-renew

              # 9. Sincronización periódica del frontend (opcional, cada 5 min)
              echo "*/5 * * * * aws s3 sync s3://${BUCKET_NAME}/ /var/www/html/ --delete" | tee -a /etc/cron.d/s3-sync

              EOF

  tags = { Name = "IntellCar-API-Server" }
}

# 3. Asociar la IP Elástica a esta instancia
resource "aws_eip_association" "eip_assoc" {
  instance_id   = aws_instance.intellcar_server.id
  allocation_id = aws_eip.web_ip.id
}

