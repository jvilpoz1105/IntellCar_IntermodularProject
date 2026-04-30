# Configuración de la instancia RDS (MySQL)
# 1. Grupo de subredes para la BBDD (RDS necesita al menos dos zonas por seguridad)
resource "aws_db_subnet_group" "db_subs" {
  name       = "intellcar-db-subnets"
  subnet_ids = [aws_subnet.public_subnet.id, aws_subnet.public_subnet_2.id] # Dos AZ distintas
  tags       = { Name = "IntellCar DB Subnets" }
}

# 2. Security Group para la Base de Datos
resource "aws_security_group" "db_sg" {
  name        = "intellcar-db-sg"
  vpc_id      = aws_vpc.intellcar_vpc.id

  ingress {
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    # ¡ESTO ES LO IMPORTANTE!: Solo permite entrada desde el SG de la Web
    security_groups = [aws_security_group.web_sg.id]
  }
}

# 3. La instancia de MySQL (RDS)
resource "aws_db_instance" "mysql" {
  allocated_storage    = 20
  storage_type         = "gp2"
  engine               = "mysql"
  engine_version       = "8.0"
  instance_class       = "db.t3.micro" # La más barata para el lab
  db_name              = "intellcar_db"
  username             = "admin"
  password             = var.db_password # Utilizando la variable guardada de forma segura
  parameter_group_name = "default.mysql8.0"
  skip_final_snapshot  = true
  vpc_security_group_ids = [aws_security_group.db_sg.id]
  db_subnet_group_name   = aws_db_subnet_group.db_subs.name
}