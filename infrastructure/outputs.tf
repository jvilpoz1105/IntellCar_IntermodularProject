# Para que Terraform te escupa la IP pública al terminar
output "server_public_ip" {
  value = aws_eip.web_ip.public_ip
}

output "database_endpoint" {
  value = aws_db_instance.mysql.endpoint
}

# ── Credenciales del IAM User para GitHub Actions ──
# Ejecutar: terraform output github_cd_access_key_id
# Ejecutar: terraform output github_cd_secret_access_key
output "github_cd_access_key_id" {
  value     = aws_iam_access_key.github_cd_key.id
  sensitive = true
}

output "github_cd_secret_access_key" {
  value     = aws_iam_access_key.github_cd_key.secret
  sensitive = true
}