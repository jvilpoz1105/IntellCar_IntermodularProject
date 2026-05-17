#!/bin/bash
# Script para generar el ZIP de la Lambda
# Usage: ./build_lambda.sh

cd "$(dirname "$0")"

echo "Creating Lambda deployment package..."

# Crear directorio temporal
mkdir -p lambda_build

# Copiar el código de la Lambda
cp lambda_function.py lambda_build/

# Crear el archivo ZIP
cd lambda_build
zip -r ../s3_trigger.zip lambda_function.py

cd ..

# Limpiar
rm -rf lambda_build

echo "Done! s3_trigger.zip created"
echo "Run: cd ../infrastructure && terraform apply"