# Plan de Implementación: HTTPS, DuckDNS y Proxy Inverso

## 1. Contexto Actual (Estado del Proyecto)
- **Infraestructura (Terraform):** VPC, Security Group (Puertos 22, 80, 443 abiertos), IP Elástica (EIP) configurada.
- **Backend:** API desplegada en instancia EC2.
- **Frontend:** Angular build alojado en S3 (Static Website Hosting).
- **Objetivo:** Conseguir HTTPS (SSL) con dominio DuckDNS y unificar acceso bajo un único punto de entrada.

## 2. Arquitectura de Red Objetivo
El usuario accederá a través de `https://intellcar.duckdns.org`.
- **Nginx (EC2)** actuará como Proxy Inverso.
- `/` (Raíz) -> Redirige al Endpoint de S3 (Frontend).
- `/api/` -> Redirige a `localhost:PORT` (Backend).

## 3. Hoja de Ruta de Implementación

### Fase 1: Configuración DNS (Manual)
- [ ] Vincular la EIP de AWS en el panel de control de DuckDNS.
- [ ] Verificar propagación: `ping intellcar.duckdns.org`.

### Fase 2: Automatización con Terraform (User Data)
- [ ] Integrar Script de Bash en el recurso `aws_instance` para:
    - Instalar Nginx.
    - Configurar el archivo `/etc/nginx/sites-available/default`.
    - Establecer los `proxy_pass` hacia el S3 y el proceso local de la API.

### Fase 3: Seguridad y Certificados (Manual con Certbot)
*Nota: Se realiza manual para evitar bloqueos de Rate Limit de Let's Encrypt en despliegues efímeros.*
- [ ] Ejecutar `sudo apt install certbot python3-certbot-nginx`.
- [ ] Ejecutar `sudo certbot --nginx -d intellcar.duckdns.org`.
- [ ] Forzar redirección de HTTP a HTTPS.

## 4. Notas de Mantenimiento (Ahorro de Créditos)
- Siempre ejecutar `terraform destroy` al finalizar la sesión.
- **Importante:** La EIP debe destruirse junto a la instancia para evitar cargos por IP no asociada.
- El certificado de Let's Encrypt se perderá en cada `destroy`. Solo regenerar para entregas o pruebas finales de integración.

## 5. Variables Clave
- **Bucket Endpoint:** `http://<nombre-bucket>.s3-website-<region>.amazonaws.com`
- **API Port:** `8080` (ajustar según entorno).
- **Dominio:** `intellcar.duckdns.org`