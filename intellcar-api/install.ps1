# Script de instalación para IntellCar API
Write-Host "🚗 Instalando IntellCar API..." -ForegroundColor Green

# Verificar que estamos en el directorio correcto
if (-Not (Test-Path composer.json)) {
    Write-Host "❌ Error: Debes ejecutar este script desde la raíz del proyecto (donde está composer.json)" -ForegroundColor Red
    exit 1
}

# Instalar dependencias de Composer
Write-Host "`n📦 Instalando dependencias de PHP..." -ForegroundColor Yellow
composer install

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error al instalar dependencias de Composer" -ForegroundColor Red
    exit 1
}

# Copiar archivo de configuración
if (-Not (Test-Path .env)) {
    Write-Host "`n📝 Copiando archivo de configuración..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    Write-Host "⚠️  IMPORTANTE: Edita el archivo .env y configura tu base de datos" -ForegroundColor Cyan
    
    # Preguntar si quiere continuar
    $continue = Read-Host "`n¿Has configurado la base de datos en .env? (s/n)"
    if ($continue -ne 's') {
        Write-Host "`nEdita .env y ejecuta el script nuevamente" -ForegroundColor Yellow
        exit 0
    }
}

# Generar key de aplicación
Write-Host "`n🔑 Generando clave de aplicación..." -ForegroundColor Yellow
php artisan key:generate

# Ejecutar migraciones
Write-Host "`n🗄️  Creando base de datos..." -ForegroundColor Yellow
Write-Host "Esto creará todas las tablas mediante migraciones..." -ForegroundColor Gray
php artisan migrate

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error al ejecutar migraciones. Verifica tu configuración de base de datos en .env" -ForegroundColor Red
    exit 1
}

# Poblar con datos de prueba
Write-Host "`n🌱 Poblando base de datos con datos de prueba..." -ForegroundColor Yellow
php artisan db:seed

if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  Error al poblar la base de datos" -ForegroundColor Yellow
}

# Instalar dependencias de Node (opcional)
Write-Host "`n📦 Instalando dependencias de Node.js..." -ForegroundColor Yellow
npm install

Write-Host "`n✅ ¡Instalación completada exitosamente!" -ForegroundColor Green
Write-Host "`n═══════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "📧 Credenciales de acceso:" -ForegroundColor Cyan
Write-Host "   Admin:  admin@intellcar.com  / admin123" -ForegroundColor White
Write-Host "   Dealer: dealer@intellcar.com / dealer123" -ForegroundColor White
Write-Host "   User:   user@intellcar.com   / user123" -ForegroundColor White
Write-Host "═══════════════════════════════════════════════════════" -ForegroundColor Cyan

Write-Host "`n🚀 Para iniciar el servidor ejecuta:" -ForegroundColor Yellow
Write-Host "   php artisan serve" -ForegroundColor White
Write-Host "`n   La API estará disponible en: http://localhost:8000" -ForegroundColor Gray
Write-Host "   Documentación: http://localhost:8000/api/documentation`n" -ForegroundColor Gray
