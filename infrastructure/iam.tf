# ──────────────────────────────────────────────────────
# Usuario IAM dedicado para GitHub Actions (CD Pipeline)
# ──────────────────────────────────────────────────────
# Este usuario será el que use GitHub Actions para:
#   - Subir el build de Angular al bucket S3 del frontend
#   - Acceder al bucket S3 de media (fotos)
#   - Usar Amazon Rekognition (análisis de imágenes)
#   - Usar Amazon Comprehend (análisis de textos/NLP)

# 1. Crear el usuario IAM
resource "aws_iam_user" "github_cd" {
  name = "intellcar-github-cd"
  tags = { Purpose = "GitHub Actions CD Pipeline" }
}

# 2. Crear las Access Keys (las credenciales que usará GitHub)
resource "aws_iam_access_key" "github_cd_key" {
  user = aws_iam_user.github_cd.name
}

# 3. Política con todos los permisos necesarios
resource "aws_iam_user_policy" "github_cd_policy" {
  name = "intellcar-cd-policy"
  user = aws_iam_user.github_cd.name

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      # ── Permisos de S3: Deploy del frontend + gestión de media ──
      {
        Sid    = "S3FrontendDeploy"
        Effect = "Allow"
        Action = [
          "s3:PutObject",
          "s3:GetObject",
          "s3:DeleteObject",
          "s3:ListBucket"
        ]
        Resource = [
          aws_s3_bucket.angular_frontend.arn,
          "${aws_s3_bucket.angular_frontend.arn}/*",
          aws_s3_bucket.intellcar_media.arn,
          "${aws_s3_bucket.intellcar_media.arn}/*"
        ]
      },
      # ── Permisos de Rekognition: Análisis de imágenes ──
      # DetectLabels: Identificar objetos en fotos (coches, piezas, etc.)
      # DetectFaces: Detectar rostros en fotos de perfil
      # DetectModerationLabels: Moderar contenido inapropiado
      # DetectText: Leer matrículas u otros textos en fotos
      # RecognizeCelebrities: Identificar personas famosas
      {
        Sid    = "RekognitionAccess"
        Effect = "Allow"
        Action = [
          "rekognition:DetectLabels",
          "rekognition:DetectFaces",
          "rekognition:DetectModerationLabels",
          "rekognition:DetectText",
          "rekognition:RecognizeCelebrities"
        ]
        Resource = "*"
      },
      # ── Permisos de Comprehend: Análisis de textos/NLP ──
      # DetectSentiment: Analizar sentimiento de posts y comentarios
      # DetectEntities: Extraer entidades (marcas de coches, ciudades, etc.)
      # DetectKeyPhrases: Extraer frases clave de publicaciones
      # DetectDominantLanguage: Detectar idioma del texto
      # DetectPiiEntities: Detectar datos personales (GDPR)
      # DetectToxicContent: Moderar textos tóxicos u ofensivos
      {
        Sid    = "ComprehendAccess"
        Effect = "Allow"
        Action = [
          "comprehend:DetectSentiment",
          "comprehend:DetectEntities",
          "comprehend:DetectKeyPhrases",
          "comprehend:DetectDominantLanguage",
          "comprehend:DetectPiiEntities",
          "comprehend:DetectToxicContent"
        ]
        Resource = "*"
      }
    ]
  })
}
