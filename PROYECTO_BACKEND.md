# 🚗 Proyecto IntellCar - Documentación del Backend

## 📁 Estructura del Proyecto

Este repositorio contiene:
- `BBDD_IntellCar_v1.sql` - Script SQL original de la base de datos
- `intellcar-api/` - **API REST de Laravel** (Backend completo)

## ✅ Estado del Proyecto

### ✨ Completado

El backend de IntellCar ha sido desarrollado completamente según las especificaciones del proyecto intermodular:

#### 🗄️ Base de Datos
- ✅ **19 migraciones creadas** - Toda la estructura en migraciones de Laravel
- ✅ Respeta el esquema original del archivo SQL
- ✅ Relaciones correctamente definidas (FK, cascadas, etc.)
- ✅ Soporte para tipos de datos especiales (ENUM, JSON, decimales, etc.)

#### 🔧 Modelos Eloquent
- ✅ **14 modelos creados** con todas sus relaciones:
  - AppUser (autenticación con Sanctum)
  - Paddock (comunidades)
  - Make, CarModel, CarEngine (catálogo de vehículos)
  - CarAdvert (anuncios)
  - Post, PostComment, PostMedia (sistema social)
  - EventKdd (eventos/quedadas)
  - UserGarage (garaje personal)
  - SavedSearch (búsquedas guardadas)
  - Notification (notificaciones)
  - AdMedia, PostMedia (multimedia)

#### 🎨 Factories & Seeders
- ✅ **7 factories** para generar datos de prueba
- ✅ **1 seeder principal** que puebla toda la BD
- ✅ Datos realistas (marcas reales, tipos de motor, etc.)
- ✅ 3 usuarios de prueba (admin, dealer, user)

#### 🔌 API REST
- ✅ **6 controladores API** con CRUD completo
- ✅ Endpoints documentados con Swagger/OpenAPI
- ✅ Validación con Form Requests personalizados
- ✅ Mensajes de error en español
- ✅ Paginación en listados
- ✅ Filtros dinámicos en búsquedas

#### 🔐 Seguridad
- ✅ Laravel Sanctum configurado
- ✅ Rutas públicas y protegidas separadas
- ✅ Middleware de admin
- ✅ Validación de propietario en edición/eliminación
- ✅ Hash de contraseñas

#### 📚 Documentación
- ✅ README.md completo con instrucciones
- ✅ API_GUIDE.md con ejemplos de uso
- ✅ Comentarios Swagger en controladores
- ✅ Script de instalación automatizado (PowerShell)

## 🚀 Inicio Rápido

### 1. Navega al directorio de la API
```powershell
cd intellcar-api
```

### 2. Ejecuta el script de instalación
```powershell
.\install.ps1
```

Este script automáticamente:
- Instala dependencias de Composer
- Copia el archivo `.env.example` a `.env`
- Genera la key de la aplicación
- Ejecuta todas las migraciones
- Puebla la base de datos con datos de prueba

### 3. Configura tu base de datos

Antes de ejecutar las migraciones, edita `intellcar-api/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=intellcar_db
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Inicia el servidor

```powershell
php artisan serve
```

La API estará disponible en: `http://localhost:8000`

## 📊 Endpoints Principales

### Autenticación
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Login (obtener token)
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Usuario actual

### Anuncios
- `GET /api/adverts` - Listar anuncios (con filtros)
- `POST /api/adverts` - Crear anuncio (requiere auth)
- `GET /api/adverts/{id}` - Ver detalles
- `PUT /api/adverts/{id}` - Actualizar
- `DELETE /api/adverts/{id}` - Eliminar

### Posts
- `GET /api/posts` - Listar posts
- `POST /api/posts` - Crear post
- `POST /api/posts/{id}/like` - Dar like
- `DELETE /api/posts/{id}/like` - Quitar like

### Eventos
- `GET /api/events` - Listar eventos
- `POST /api/events` - Crear evento
- `POST /api/events/{id}/join` - Unirse
- `DELETE /api/events/{id}/leave` - Salir

Ver documentación completa en: [intellcar-api/README.md](intellcar-api/README.md)

## 🔑 Credenciales de Prueba

Después de ejecutar el seeder:

| Rol | Email | Password |
|-----|-------|----------|
| Administrador | admin@intellcar.com | admin123 |
| Concesionario | dealer@intellcar.com | dealer123 |
| Usuario | user@intellcar.com | user123 |

## 📋 Requisitos Cumplidos

### ✅ Requisitos Obligatorios del Proyecto

- [x] **Framework**: Laravel 12
- [x] **Base de Datos**: MySQL
- [x] **Migraciones**: Todo el esquema en migraciones (no PHPMyAdmin)
- [x] **Seeders & Factories**: Datos de prueba automatizados
- [x] **API REST**: Endpoints completos con CRUD
- [x] **Laravel Sanctum**: Autenticación por tokens
- [x] **Validación**: Form Requests con mensajes personalizados
- [x] **Rutas organizadas**: Route::group con nombres
- [x] **Control de acceso**: Middleware de autenticación y roles
- [x] **Documentación**: Swagger/OpenAPI + README completo

### ✅ Funcionalidades Implementadas

- [x] Sistema de usuarios con roles (admin, pro, indv, press)
- [x] CRUD completo de anuncios de vehículos
- [x] Sistema de posts con likes y comentarios
- [x] Eventos/quedadas con inscripciones
- [x] Paddocks/comunidades por estilo de coche
- [x] Garaje personal de usuarios
- [x] Búsquedas guardadas (JSON)
- [x] Sistema de notificaciones
- [x] Relaciones sociales (seguir usuarios)
- [x] Filtros avanzados en listados
- [x] Paginación en todos los listados

## 📂 Estructura de Archivos

```
IntellCar_IntermodularProject/
├── BBDD_IntellCar_v1.sql          # Base de datos original (referencia)
├── README.md                      # Este archivo
└── intellcar-api/                 # API Laravel
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/Api/   # Controladores de la API
    │   │   ├── Middleware/        # AdminMiddleware
    │   │   └── Requests/          # Validadores
    │   └── Models/                # 14 modelos Eloquent
    ├── database/
    │   ├── factories/             # 7 factories
    │   ├── migrations/            # 19 migraciones
    │   └── seeders/               # DatabaseSeeder
    ├── routes/
    │   └── api.php                # Todas las rutas API
    ├── .env.example               # Configuración de ejemplo
    ├── composer.json              # Dependencias PHP
    ├── install.ps1                # Script de instalación
    ├── README.md                  # Documentación técnica
    └── API_GUIDE.md               # Guía de uso de la API
```

## 🧪 Testing

Para ejecutar los tests:

```powershell
cd intellcar-api
php artisan test
```

## 🛠️ Tecnologías Utilizadas

- **PHP** 8.1+
- **Laravel** 12
- **MySQL** 8.0+
- **Laravel Sanctum** - Autenticación API
- **Eloquent ORM** - Manejo de base de datos
- **Composer** - Gestor de dependencias

## 📖 Documentación Adicional

- [README completo de la API](intellcar-api/README.md)
- [Guía de uso de la API](intellcar-api/API_GUIDE.md)
- Swagger Documentation: `http://localhost:8000/api/documentation` (cuando esté en ejecución)

## 🎯 Próximos Pasos

### Para el Frontend (Desarrollo Web en Entorno Cliente)
1. Crear aplicación Angular/React
2. Integrar con los endpoints de esta API
3. Implementar las vistas de usuario
4. Consumir los datos mediante HTTP requests

### Mejoras Futuras (Opcionales)
- [ ] Implementar panel de administración web con Laravel Blade
- [ ] Añadir sistema de chat en tiempo real
- [ ] Integración con servicios de pago
- [ ] Subida real de imágenes (actualmente URLs)
- [ ] Sistema de valoraciones/reviews
- [ ] Geolocalización en eventos

## 👥 Contribución

Este es un proyecto educativo. Para contribuir:

1. Fork el repositorio
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📄 Licencia

Proyecto educativo - Módulo de Desarrollo en Entorno Servidor

---

**Desarrollado para el Proyecto Intermodular 2026** 🎓


## IntellCar (src) - Setup rapido Laravel Breeze

Este proyecto corre dentro de Docker con Apache y MySQL.

### Requisitos

- Docker y Docker Compose

### Levantar contenedores

```bash
docker compose up -d
```

La app queda en:

```
http://localhost:8080
```

### Instalar Laravel Breeze (auth con interfaz)

Ejecutar dentro del contenedor:

```bash
docker exec -it laravel12_app composer require laravel/breeze --dev
docker exec -it laravel12_app php artisan breeze:install
docker exec -it laravel12_app npm install
docker exec -it laravel12_app npm run build
```

### Configurar APP_KEY y migraciones

```bash
docker exec -it laravel12_app php artisan key:generate
docker exec -it laravel12_app php artisan config:clear
docker exec -it laravel12_app php artisan migrate
```

### Verificar autenticacion

- `http://localhost:8080/register`
- `http://localhost:8080/login`