# 🚗 IntellCar API - Backend Laravel

API REST para la plataforma IntellCar, un proyecto intermodular de compraventa y comunidad de vehículos.

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
- [Endpoints de la API](#endpoints-de-la-api)
- [Autenticación](#autenticación)
- [Testing](#testing)
- [Contribución](#contribución)

---

## 🎯 Descripción

IntellCar es una aplicación web full-stack que combina:
- **Panel de Administración**: Interfaz web con Laravel Blade para gestión administrativa
- **API REST**: Backend para consumo desde aplicaciones frontend (Angular/React)
- **Autenticación dual**: Sesiones para admin, tokens (Sanctum) para API

### Características Principales

✅ Gestión de anuncios de vehículos (nuevos, km0, usados, renting, etc.)  
✅ Sistema de posts y comunidad (likes, comentarios)  
✅ Paddocks/Moods (comunidades por estilo de coche)  
✅ Eventos/Quedadas entre usuarios  
✅ Garaje personal de vehículos  
✅ Búsquedas guardadas con filtros dinámicos (JSON)  
✅ Sistema de notificaciones  
✅ Relaciones sociales (seguir usuarios)  

---

## 🛠️ Tecnologías

| Categoría | Tecnología |
|-----------|------------|
| **Backend** | PHP 8.x |
| **Framework** | Laravel 12 |
| **Base de Datos** | MySQL |
| **Autenticación** | Laravel Sanctum (API), Breeze/Jetstream (Web) |
| **Documentación** | Swagger/OpenAPI |
| **Datos de Prueba** | Factories & Seeders |

---

## 📦 Requisitos

- PHP >= 8.1
- Composer
- MySQL >= 8.0
- Node.js >= 16 (para compilar assets)

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
cd intellcar-api
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=intellcar_db
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Generar clave de aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones

⚠️ **IMPORTANTE**: Todo el esquema se crea mediante migraciones (requisito del proyecto).

```bash
php artisan migrate
```

### 6. Poblar base de datos con datos de prueba

```bash
php artisan db:seed
```

Esto creará usuarios de prueba:
- **Admin**: `admin@intellcar.com` / `admin123`
- **Dealer**: `dealer@intellcar.com` / `dealer123`
- **User**: `user@intellcar.com` / `user123`

### 7. Instalar Laravel Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 8. Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000`

---

## ⚙️ Configuración

### Laravel Sanctum

Configura los dominios permitidos en `.env`:

```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1
```

### CORS (para frontend externo)

Edita `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:4200'], // Tu app Angular
```

---

## 🗄️ Estructura de la Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `paddock` | Comunidades/Estilos (Clásicos, Deportivos, etc.) |
| `app_user` | Usuarios de la aplicación |
| `make` | Marcas de coches (BMW, Toyota, etc.) |
| `car_model` | Modelos de coches |
| `car_engine` | Motores (gasolina, diesel, eléctrico, etc.) |
| `car_advert` | Anuncios de vehículos |
| `post` | Posts/publicaciones de usuarios |
| `event_kdd` | Eventos/quedadas |
| `user_garage` | Garaje personal de usuarios |
| `notification` | Sistema de notificaciones |
| `saved_search` | Búsquedas guardadas (JSON) |

### Diagrama E/R

El diagrama entidad-relación está disponible en: `docs/ER_Diagram.png`

---

## 🔌 Endpoints de la API

### 🔐 Autenticación

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| `POST` | `/api/auth/register` | Registrar nuevo usuario | No |
| `POST` | `/api/auth/login` | Iniciar sesión | No |
| `POST` | `/api/auth/logout` | Cerrar sesión | Sí |
| `GET` | `/api/auth/me` | Obtener usuario actual | Sí |

**Ejemplo Login:**

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email_address": "user@intellcar.com",
    "user_password": "user123"
  }'
```

**Respuesta:**

```json
{
  "message": "Login exitoso",
  "user": { ... },
  "token": "1|laravel_sanctum_token..."
}
```

### 🚗 Anuncios

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| `GET` | `/api/adverts` | Listar anuncios (con filtros) | No |
| `GET` | `/api/adverts/{id}` | Ver detalles de anuncio | No |
| `POST` | `/api/adverts` | Crear anuncio | Sí |
| `PUT` | `/api/adverts/{id}` | Actualizar anuncio | Sí |
| `DELETE` | `/api/adverts/{id}` | Eliminar anuncio | Sí |

**Filtros disponibles:**
- `?visible=true` - Solo visibles
- `?ad_type=used` - Por tipo
- `?min_price=10000&max_price=30000` - Rango de precio
- `?car_color=rojo` - Por color
- `?region=Madrid` - Por región

### 📝 Posts

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| `GET` | `/api/posts` | Listar posts | No |
| `GET` | `/api/posts/{id}` | Ver detalles de post | No |
| `POST` | `/api/posts` | Crear post | Sí |
| `PUT` | `/api/posts/{id}` | Actualizar post | Sí |
| `DELETE` | `/api/posts/{id}` | Eliminar post | Sí |
| `POST` | `/api/posts/{id}/like` | Dar like | Sí |
| `DELETE` | `/api/posts/{id}/like` | Quitar like | Sí |

### 🏁 Eventos

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| `GET` | `/api/events` | Listar eventos | No |
| `GET` | `/api/events/{id}` | Ver detalles de evento | No |
| `POST` | `/api/events` | Crear evento | Sí |
| `POST` | `/api/events/{id}/join` | Unirse a evento | Sí |
| `DELETE` | `/api/events/{id}/leave` | Salir de evento | Sí |

### 🏷️ Marcas y Paddocks

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/makes` | Listar marcas de coches |
| `GET` | `/api/makes/{id}` | Ver detalles de marca |
| `GET` | `/api/paddocks` | Listar comunidades |
| `GET` | `/api/paddocks/{id}` | Ver detalles de comunidad |

---

## 🔒 Autenticación

### Uso de Tokens

Una vez obtenido el token en el login, inclúyelo en las peticiones protegidas:

```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 1|tu_token_aqui"
```

### Perfiles de Usuario

| Tag | Descripción |
|-----|-------------|
| `admin` | Administrador (acceso total) |
| `pro` | Profesional/Concesionario |
| `indv` | Usuario individual |
| `press` | Prensa/Medios |

---

## 📚 Documentación Swagger

La documentación completa de la API está disponible en:

```
http://localhost:8000/api/documentation
```

Para generarla:

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
php artisan l5-swagger:generate
```

---

## 🧪 Testing

```bash
php artisan test
```

---

## 📁 Estructura del Proyecto

```
intellcar-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # Controladores de la API
│   │   ├── Middleware/         # Middlewares personalizados
│   │   └── Requests/           # Form Requests (validación)
│   └── Models/                 # Modelos Eloquent
├── database/
│   ├── factories/              # Factories para datos de prueba
│   ├── migrations/             # Migraciones (estructura BD)
│   └── seeders/                # Seeders (datos iniciales)
├── routes/
│   ├── api.php                 # Rutas de la API
│   └── web.php                 # Rutas web (panel admin)
├── .env.example                # Configuración de ejemplo
└── README.md                   # Este archivo
```

---

## 📝 Notas Importantes

⛔ **Prohibido**: Crear tablas manualmente con PHPMyAdmin o consola MySQL.  
✅ **Obligatorio**: Todo mediante migraciones de Laravel.

El proyecto cumple con todos los requisitos del módulo de Desarrollo en Entorno Servidor:

- ✅ Migraciones para toda la estructura de BD
- ✅ Seeders y Factories para datos de prueba
- ✅ Laravel Sanctum para autenticación API
- ✅ Rutas protegidas y organizadas con `Route::group`
- ✅ Naming de rutas con `->name()`
- ✅ Validadores de Laravel (Form Requests)
- ✅ Feedback visual de errores
- ✅ Control de accesos por roles
- ✅ Documentación de API (Swagger)

---

## 👥 Contribución

Este proyecto es parte de un trabajo intermodular. Para contribuir:

1. Fork el repositorio
2. Crea una rama: `git checkout -b feature/nueva-funcionalidad`
3. Commit: `git commit -m 'Añadir nueva funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto es de uso educativo para el módulo de Desarrollo en Entorno Servidor.

---

## 📞 Contacto

Para dudas o sugerencias sobre el proyecto, contacta con el equipo de desarrollo.

---

**Desarrollado con ❤️ para el Proyecto Intermodular**
