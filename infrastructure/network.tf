# VPC, Subnets, IP Elástica y Grupos de Seguridad (Firewall)
# 1. Crear la red privada (VPC)
resource "aws_vpc" "intellcar_vpc" {
  cidr_block           = "10.0.0.0/16"
  enable_dns_hostnames = true
  tags = { Name = "intellcar-vpc" }
}

# 2. Crear una Subred Pública primaria (Donde vivirá la EC2)
resource "aws_subnet" "public_subnet" {
  vpc_id            = aws_vpc.intellcar_vpc.id
  cidr_block        = "10.0.1.0/24"
  map_public_ip_on_launch = true
  availability_zone = "us-east-1a" # O la que use tu lab de AWS
  tags = { Name = "intellcar-public-subnet-1a" }
}

# 2.1 Crear una Subred Pública secundaria (Requisito para Base de Datos)
resource "aws_subnet" "public_subnet_2" {
  vpc_id            = aws_vpc.intellcar_vpc.id
  cidr_block        = "10.0.2.0/24"
  map_public_ip_on_launch = true
  availability_zone = "us-east-1b" # Diferente Zona de Disponibilidad
  tags = { Name = "intellcar-public-subnet-1b" }
}

# 3. Internet Gateway (La puerta para que la red tenga internet)
resource "aws_internet_gateway" "igw" {
  vpc_id = aws_vpc.intellcar_vpc.id
}

# 4. Tabla de rutas (El mapa para que el tráfico sepa salir a internet)
resource "aws_route_table" "public_rt" {
  vpc_id = aws_vpc.intellcar_vpc.id
  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.igw.id
  }
}

resource "aws_route_table_association" "public_assoc" {
  subnet_id      = aws_subnet.public_subnet.id
  route_table_id = aws_route_table.public_rt.id
}

resource "aws_route_table_association" "public_assoc_2" {
  subnet_id      = aws_subnet.public_subnet_2.id
  route_table_id = aws_route_table.public_rt.id
}

# 5. GRUPO DE SEGURIDAD (El Firewall del servidor)
resource "aws_security_group" "web_sg" {
  name        = "intellcar-web-sg"
  description = "Permitir HTTP, HTTPS y SSH"
  vpc_id      = aws_vpc.intellcar_vpc.id

  # Entrada: SSH (Para que tú te conectes)
  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"] # En un entorno real, pondrias solo tu IP
  }

  # Entrada: HTTP
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Entrada: HTTPS (Requisito DAWEB)
  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Salida: Permitir que el servidor descargue cosas (como Docker)
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# 6. IP ELÁSTICA (Requisito DAWEB)
resource "aws_eip" "web_ip" {
  domain = "vpc"
  tags   = { Name = "intellcar-static-ip" }
}