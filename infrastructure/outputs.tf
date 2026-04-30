# Para que Terraform te escupa la IP pública al terminar
output "server_public_ip" {
  value = aws_eip.web_ip.public_ip
}

output "database_endpoint" {
  value = aws_db_instance.mysql.endpoint
}