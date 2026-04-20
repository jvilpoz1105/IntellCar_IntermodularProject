# Configuración del Bucket S3 para las fotos
# 1. Bucket para las fotos de los anuncios, posts y garaje
resource "aws_s3_bucket" "intellcar_media" {
  bucket = "intellcar-media-tfg-jose" # El nombre debe ser único en todo AWS
}

# Configuración para que las fotos sean accesibles desde la App
resource "aws_s3_bucket_public_access_block" "media_access" {
  bucket = aws_s3_bucket.intellcar_media.id

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false
}

# Habilitar el acceso público para la lectura de imágenes
resource "aws_s3_bucket_policy" "media_policy" {
  bucket = aws_s3_bucket.intellcar_media.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action    = "s3:GetObject"
        Effect    = "Allow"
        Resource  = "${aws_s3_bucket.intellcar_media.arn}/*"
        Principal = "*"
      },
    ]
  })
}

# 2. Bucket para el despliegue de Angular (Static Website)
resource "aws_s3_bucket" "angular_frontend" {
  bucket = "intellcar-web-tfg-jose"
}

resource "aws_s3_bucket_website_configuration" "angular_config" {
  bucket = aws_s3_bucket.angular_frontend.id

  index_document {
    suffix = "index.html"
  }

  error_document {
    key = "index.html" # En Angular, el error suele redirigir al index para el routing
  }
}

# Configuración para desbloquear el acceso público del bucket de la web
resource "aws_s3_bucket_public_access_block" "angular_access" {
  bucket = aws_s3_bucket.angular_frontend.id

  block_public_acls       = false
  block_public_policy     = false
  ignore_public_acls      = false
  restrict_public_buckets = false
}

# Política para permitir que internet vea los archivos y scripts de la web
resource "aws_s3_bucket_policy" "angular_policy" {
  bucket = aws_s3_bucket.angular_frontend.id
  
  # Depender del desbloqueo público antes de aplicar la política
  depends_on = [aws_s3_bucket_public_access_block.angular_access]

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action    = "s3:GetObject"
        Effect    = "Allow"
        Resource  = "${aws_s3_bucket.angular_frontend.arn}/*"
        Principal = "*"
      },
    ]
  })
}