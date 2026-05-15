param(
    [switch]$NoOpen
)

$ErrorActionPreference = 'Stop'

Write-Host '==> Iniciando stack Docker (build + detach)...' -ForegroundColor Cyan
docker compose up -d --build

Write-Host '==> Limpiando cache de config Laravel dentro del contenedor...' -ForegroundColor Cyan
docker compose exec laravel php artisan config:clear

Write-Host '==> Verificando estado de migraciones...' -ForegroundColor Cyan
docker compose exec laravel php artisan migrate:status

Write-Host '==> Regenerando Swagger...' -ForegroundColor Cyan
docker compose exec laravel php artisan l5-swagger:generate

Write-Host '==> Estado de contenedores:' -ForegroundColor Cyan
docker compose ps

$apiUrl = 'http://localhost:8080'
$swaggerUrl = 'http://localhost:8080/api/documentation'
$phpmyadminUrl = 'http://localhost:8081'

Write-Host ''
Write-Host 'Stack listo:' -ForegroundColor Green
Write-Host "API:        $apiUrl"
Write-Host "Swagger:    $swaggerUrl"
Write-Host "phpMyAdmin: $phpmyadminUrl"

if (-not $NoOpen) {
    Write-Host ''
    Write-Host '==> Abriendo URLs en el navegador...' -ForegroundColor Cyan
    Start-Process $apiUrl
    Start-Process $swaggerUrl
    Start-Process $phpmyadminUrl
}
