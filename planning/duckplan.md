# Plan de Implementación: HTTPS, DuckDNS y Proxy Inverso

## 1. Contexto Actual (Estado del Proyecto)
- **Infraestructura (Terraform):** VPC, Security Group (Puertos 22, 80, 443 abiertos), IP Elástica (EIP) configurada.
- **Backend:** API desplegada en instancia EC2 (Docker).
- **Frontend:** Angular build alojado en S3 (Static Website Hosting).
- **Objetivo:** Acceso unificado via `https://intellcar.duckdns.org` con SSL automático.

## 2. Arquitectura de Red Objetivo

El usuario accederá a través de `https://intellcar.duckdns.org`.

**Nginx (EC2)** actuará como único punto de entrada:
- `/` -> Archivos estáticos del Frontend (servidos desde S3 via proxy o nginx)
- `/api/` -> Backend Laravel (localhost:8080)
- Redirección automática HTTP -> HTTPS

## 3. Hoja de Ruta de Implementación

### Fase 1: Configuración DNS (Manual) ⚠️ IMPORTANTE
- [ ] Ir a [DuckDNS](https://www.duckdns.org) e iniciar sesión.
- [ ] Crear dominio: `intellcar` (quedará como `intellcar.duckdns.org`).
- [ ] En "Your domains", seleccionar el dominio creado.
- [ ] En "IP update", introducir la **EIP** de AWS (ej: `54.123.45.67`).
- [ ] Guardar cambios.
- [ ] Verificar propagación: `ping intellcar.duckdns.org` (debe responder la EIP).

### Fase 2: Automatización con Terraform (User Data)
El script de inicio (user_data) de la EC2 debe:
- [ ] Instalar Nginx.
- [ ] Instalar Certbot y plugin Nginx: `apt install certbot python3-certbot-nginx`.
- [ ] Descargar contenido del bucket S3 a `/var/www/html` (frontend).
- [ ] Configurar Nginx como reverse proxy:
  - Configurar server block para HTTP (puertos 80) y HTTPS (443).
  - `/` -> Servir archivos locales de `/var/www/html`.
  - `/api/` -> Proxy_pass a `http://localhost:8080`.
  - Configurar certificado SSL con Certbot.
  - Forzar redirección HTTP -> HTTPS.
- [ ] Configurar renovación automática de certificado (cronjob).

### Fase 3: Actualizar Terraform
- [ ] Añadir variable `duck_domain` en `variables.tf`.
- [ ] Modificar `compute.tf` para pasar el dominio al user_data.
- [ ] El user_data generará el certificado automáticamente en el primer arranque.

## 4. Notas Importantes

### Rate Limiting de Let's Encrypt
- En entornos efímeros (destroy/recreate frecuentes), Let's Encrypt puede bloquearte.
- **Solución**: Usar `--test-cert` para pruebas, o esperar 1 semana entre obtenciones de certificados.
- Si te bloquean: usar otro dominio diferente o esperar.

### Renovación de Certificados
- Certbot crea automáticamente un cronjob en `/etc/cron.d/certbot`.
- El certificado dura 90 días, se renueva automáticamente.
- **Problema**: Cada `terraform destroy` pierde el certificado.
- **Solución**: En desarrollo, regenerar certificado solo cuando sea necesario (no en cada destroy).

### Ahorro de Créditos
- Siempre ejecutar `terraform destroy` al finalizar la sesión.
- **Importante**: La EIP se destruirá junto a la instancia (evitar cargos).

## 5. Variables Clave
- **Dominio DuckDNS:** `intellcar.duckdns.org`
- **Bucket S3 (Frontend):** `intellcar-web-tfg-jose`
- **API Port (Docker):** `8080`
- **Ruta Frontend local:** `/var/www/html`

## 6. Outputs Esperados
Tras el despliegue, acceder a:
- **URL Final:** `https://intellcar.duckdns.org`
- El candado de "seguro" debe aparecer (certificado válido).
- El frontend debe cargar correctamente.
- Las llamadas a `/api/*` deben llegar al backend Laravel.

## 7. Troubleshooting
- **El dominio no resuelve**: Verificar que la EIP en DuckDNS es correcta.
- **Certificado no se genera**: Verificar que el dominio apunta a la IP pública.
- **Frontend no carga**: Comprobar que los archivos se descargaron del S3 a `/var/www/html`.
- **API no responde**: Verificar que el contenedor Docker está corriendo y en puerto 8080.