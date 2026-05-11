# Tus credenciales y región de AWS
variable "aws_region" {
  default = "us-east-1" # La región de tu laboratorio
}

variable "db_password" {
  description = "Contraseña de la base de datos"
  type        = string
  sensitive   = true # Esto evita que la contraseña salga en los logs
}

variable "ssh_key_name" {
  description = "Nombre de la clave .pem que creaste en la consola de AWS"
  type        = string
}


variable "api_domain" {
  description = "Dominio de la API Laravel"
  type        = string
  default     = "api.intellcar.com"
}

variable "internal_api_token" {
  description = "Token para comunicación interna Lambda -> Laravel"
  type        = string
  sensitive   = true
}