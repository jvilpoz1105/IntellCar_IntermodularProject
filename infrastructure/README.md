# Infraestructura de IntellCar

Este directorio contiene el código en Terraform (IaC - Infraestructura como Código) para desplegar la arquitectura completa del proyecto IntellCar alojado en Amazon Web Services (AWS).

## Arquitectura Desplegada

La infraestructura se compone de los siguientes elementos (todos gestionados automáticamente):

1. **Red y Seguridad (`network.tf`)**
   - Una red privada virtual (VPC) con dos subredes públicas en zonas de disponibilidad diferentes (`us-east-1a` y `us-east-1b`).
   - Una **IP Elástica** (IP estática que no cambia si se reinicia la máquina).
   - "Security Groups" (Firewalls) que protegen los accesos. Por defecto, exponen los puertos HTTP (80) y HTTPS (443) y blindan el servidor de base de datos.

2. **Servidor Backend e Inyección de Dependencias (`compute.tf`)**
   - Máquina virtual principal basada en **Ubuntu Server 22.04 LTS** (`t3.small`).
   - Cuenta con un script en base que auto-instala **Docker** y **Docker Compose** en la máquina al encender. Permitiendo arrancar la API rapidísimo mediante contenedores.

3. **Base de Datos Gestionada (`database.tf`)**
   - Motor automatizado mediante AWS RDS con **MySQL 8.0** (`db.t3.micro`).
   - Cuenta con alta disponibilidad y máxima seguridad, ya que no sale de la red privada: su sistema de firewall fuerza a que *únicamente el Servidor EC2* tenga la capacidad de acceder al motor de MySQL. Todo el resto de la red mundial (Internet) lo tendrá denegado.

4. **Almacenamiento Masivo en la Nube (`storage.tf`)**
   - Bucket **`intellcar_media`**: Almacén masivo con políticas públicas pre-configuradas para lectura global de imágenes fotográficas (anuncios, posts de garaje automovilístico, imágenes de perfil y más).
   - Bucket **`angular_frontend`**: Bucket configurado para estar expuesto a todo internet usando la modalidad *Static Website Hosting*. Aquí el cliente depositará los archivos listos (compilados) y servirá la web oficial de Angular sin necesitar un servidor apache o similar.

---

## Guía Rápida de Despliegue 

### Requisitos Previos
Debes contar con el archivo de la "llave" SSH (archivo `vockey.pem`) descargado en tu ordenador de forma segura. Y las credenciales de AWS introducidas en tu directorio local (Ej, Windows: `C:\Users\tuusuario\.aws\credentials`).

### Paso 1: Inicialización
Ubícate en esta misma ruta (`infrastructure`) abriendo tu terminal y teclea el siguiente comando de iniciación obligatoria:
```bash
terraform init
```

### Paso 2: Ejecución e Inyección de Variables Seguras
Lanza directamente el comando real de construcción:
```bash
terraform apply
```

Durante el proceso serás pausado por la consola para completar dos parámetros que, por mayor seguridad, han sido eliminados del código y extraidos a la consola:
- **`db_password`**: Será tu nueva contraseña *Root* de MySQL. Invéntele una contraseña robusta, introdúcela y recuerda escribirla en un papel. Lo necesitarás en el paso final.
- **`ssh_key_name`**: Debes declarar el nombre estricto que recibe el archivo PEM dentro del panel de AWS. Si usas cuentas de Learner/Academy predeterminadas de estudiante de Amazon esto recibirá el nombre fijo: **`vockey`** y teclea <kbd>Enter</kbd>.

Terraform te preguntará por última vez si estás de acuerdo en empezar. Escribe literalmente **`yes`** y pulsa <kbd>Enter</kbd>. Comenzará un proceso que se demorará de entre 3 a 5 minutos aprox.

### Paso 3: Vinculación Post-Despliegue

¡Tu infra se levantó! Finalizado el proceso, Terraform imprimirá en la consola dos valores vitales (Los '*Outputs*'):
1. **`database_endpoint`**: La gran URL de la BBDD a la cual la aplicación debe conectarse.
2. **`server_public_ip`**: La IP de tu máquina EC2 para interactuar con su red (hacer ssh o entrar al endpoint desde local o tu explorador).

Copia en el portapapeles ese enorme string de `database_endpoint`. Abandona esta carpeta de código en terraform y ahora ve hasta la carpeta general del proyecto donde esté tu servidor actual, y modifica el archivo **`.env`** cambiando los valores de base de datos a los aportados por Terraform:

```env
DB_HOST=El_Mega_Endpoint_que_te_Acaba_de_Imprimir_Aqui_Sin_El_Puerto
DB_PORT=3306
DB_DATABASE=intellcar_db  <-- (Este es el que se le marcó a Terraform por código fuente)
DB_USERNAME=admin         <-- (El admin oficial de RDS declarado en Terraform)
DB_PASSWORD=La_misma_Contraseña_que_Le_asigaste_en_la_consola_durante_Apply
```
