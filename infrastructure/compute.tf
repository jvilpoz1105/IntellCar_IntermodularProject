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

  # SCRIPT DE ARRANQUE (Instalar Docker)
  user_data = <<-EOF
              #!/bin/bash
              sudo apt-get update
              sudo apt-get install -y docker.io docker-compose
              sudo systemctl start docker
              sudo usermod -aG docker ubuntu
              EOF

  tags = { Name = "IntellCar-API-Server" }
}

# 3. Asociar la IP Elástica a esta instancia
resource "aws_eip_association" "eip_assoc" {
  instance_id   = aws_instance.intellcar_server.id
  allocation_id = aws_eip.web_ip.id
}

