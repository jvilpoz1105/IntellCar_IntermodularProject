# Lambda Function para análisis de imágenes con Rekognition
# Trigger: S3 ObjectCreated (cuando se sube una imagen)
# IMPORTANTE: Usa el LabRole existente del Learner Lab en lugar de crear uno nuevo

# Lambda Function - Python 3.11
resource "aws_lambda_function" "s3_rekognition_trigger" {
  filename      = "lambda/s3_trigger.zip"
  function_name = "intellcar-s3-trigger"
  role          = var.lambda_role_arn
  handler       = "lambda_function.lambda_handler"
  runtime       = "python3.11"
  timeout       = 30
  memory_size   = 256

  environment {
    variables = {
      INTERNAL_API_URL   = "https://${var.api_domain}/api/internal/media-verify"
      INTERNAL_API_TOKEN = var.internal_api_token
    }
  }

  lifecycle {
    create_before_destroy = true
  }
}

# Permiso para que S3 pueda invocar la Lambda
resource "aws_lambda_permission" "s3_trigger_permission" {
  statement_id  = "AllowS3Invoke"
  action        = "lambda:InvokeFunction"
  function_name = aws_lambda_function.s3_rekognition_trigger.function_name
  principal     = "s3.amazonaws.com"
  source_arn    = aws_s3_bucket.intellcar_media.arn
}

# Configuración de notificación de S3 para Lambda
resource "aws_s3_bucket_notification" "media_lambda_trigger" {
  bucket = aws_s3_bucket.intellcar_media.id

  lambda_function {
    lambda_function_arn = aws_lambda_function.s3_rekognition_trigger.arn
    events              = ["s3:ObjectCreated:Put", "s3:ObjectCreated:Post"]
    filter_suffix       = ".jpg"
  }

  lambda_function {
    lambda_function_arn = aws_lambda_function.s3_rekognition_trigger.arn
    events              = ["s3:ObjectCreated:Put", "s3:ObjectCreated:Post"]
    filter_suffix       = ".jpeg"
  }

  lambda_function {
    lambda_function_arn = aws_lambda_function.s3_rekognition_trigger.arn
    events              = ["s3:ObjectCreated:Put", "s3:ObjectCreated:Post"]
    filter_suffix       = ".png"
  }
}

# Output - URL de la función Lambda
output "lambda_function_name" {
  value = aws_lambda_function.s3_rekognition_trigger.function_name
}

output "lambda_function_arn" {
  value = aws_lambda_function.s3_rekognition_trigger.arn
}
