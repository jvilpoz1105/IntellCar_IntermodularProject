# 🚀 Infraestructura de IntellCar (AWS + Terraform)

Este directorio contiene la arquitectura **IaC (Infrastructure as Code)** de IntellCar. Hemos diseñado una solución escalable, segura y totalmente automatizada en Amazon Web Services.

![Infraestructure](/assets/esquema_aws.png)


## 🏗️ Componentes de la Arquitectura

### 1. Computación y Web Server (`compute.tf`)
- **Instancia EC2 (`t3.small`)**: El cerebro de la operación. Ejecuta un servidor Ubuntu Server 22.04.
- **Docker & Docker Compose**: El servidor viene auto-configurado para levantar la API Laravel en contenedores de forma aislada.
- **Nginx Reverse Proxy**: Gestiona el tráfico entrante.
- **DuckDNS & SSL (Certbot)**: La instancia se auto-registra en DuckDNS y obtiene certificados SSL de Let's Encrypt automáticamente al arrancar. ¡Tu web siempre será HTTPS!

### 2. Almacenamiento Inteligente (`storage.tf`)
- **Bucket `intellcar-web`**: Aloja el frontend de Angular. Está configurado como *Static Website Hosting*.
- **Bucket `intellcar-media`**: Almacena las fotos de anuncios y posts. Tiene habilitado `force_destroy` para facilitar pruebas en desarrollo y políticas de acceso público para lectura.

### 3. Base de Datos Gestionada (`database.tf`)
- **AWS RDS (MySQL 8.0)**: Base de datos robusta y gestionada.
- **Seguridad Máxima**: Está en una subred privada. Solo la instancia EC2 tiene permiso para hablar con la base de datos (puerto 3306).

### 4. Inteligencia Artificial (`lambdas.tf`)
- **AWS Rekognition**: Cuando subes una foto (`.jpg`, `.png`) al bucket de media, se dispara automáticamente una **Función Lambda**.
- **Proceso**: La Lambda analiza la imagen con Rekognition, extrae etiquetas (ej: "Car", "Luxury") y notifica a la API de Laravel mediante un token de seguridad interno para validar el contenido.

![Rekognition & Lambda](/assets/esquema_reko_workflow.png)


---

## 🔄 Flujo de Despliegue Continuo (CI/CD)

No necesitas desplegar a mano. El archivo `.github/workflows/deploy.yml` gestiona todo el ciclo de vida:

1.  **Push a `main`**: Al subir código, se activan dos rutas paralelas.
2.  **Frontend**: Se compila Angular, se genera el `environment.prod.ts` con la IP/Dominio real y se sincroniza con el **S3 de Frontend**.
3.  **Backend**: GitHub se conecta vía SSH a la EC2, descarga el nuevo código, crea el `.env` desde los **GitHub Secrets** y reinicia los contenedores Docker.

---

## 🌐 Visualización y Post-Despliegue

Gracias a la integración con **DuckDNS**, ya no necesitas recordar IPs raras. 

1.  **Acceso Web**: Entra directamente a `https://tu-dominio.duckdns.org`.
2.  **SSL**: Verás el candado verde en el navegador. El script de `user_data` de la EC2 se encarga de renovar el certificado y configurar Nginx para forzar el tráfico a HTTPS.
3.  **Sincronización**: El frontend se actualiza cada 5 minutos automáticamente en el servidor mediante un CRON job que sincroniza el S3 con la carpeta `/var/www/html`.

---

## 🛠️ Comandos Útiles

Si necesitas hacer cambios manuales en la infraestructura:

```bash
# Inicializar (solo la primera vez)
terraform init

# Ver qué cambios se van a realizar
terraform plan

# Aplicar cambios (pedirá confirmación)
terraform apply

# Destruir toda la infraestructura (¡Cuidado!)
terraform destroy
```

> [!IMPORTANT]
> Los buckets S3 tienen activado `force_destroy = true`. Esto permite que `terraform destroy` funcione incluso si hay fotos dentro, borrándolo todo de golpe. ¡Útil para desarrollo!
