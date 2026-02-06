# 📘 Guía Rápida de la API IntellCar

## 🚀 Inicio Rápido

### 1. Instalación Completa (Script PowerShell)

Crea un archivo `install.ps1` en la raíz del proyecto con este contenido:

```powershell
# Script de instalación para IntellCar API
Write-Host "🚗 Instalando IntellCar API..." -ForegroundColor Green

# Instalar dependencias de Composer
Write-Host "`n📦 Instalando dependencias de PHP..." -ForegroundColor Yellow
composer install

# Copiar archivo de configuración
if (-Not (Test-Path .env)) {
    Write-Host "`n📝 Copiando archivo de configuración..." -ForegroundColor Yellow
    Copy-Item .env.example .env
}

# Generar key de aplicación
Write-Host "`n🔑 Generando clave de aplicación..." -ForegroundColor Yellow
php artisan key:generate

# Ejecutar migraciones
Write-Host "`n🗄️ Creando base de datos..." -ForegroundColor Yellow
php artisan migrate

# Poblar con datos de prueba
Write-Host "`n🌱 Poblando base de datos con datos de prueba..." -ForegroundColor Yellow
php artisan db:seed

Write-Host "`n✅ Instalación completada!" -ForegroundColor Green
Write-Host "`nCredenciales de acceso:" -ForegroundColor Cyan
Write-Host "Admin: admin@intellcar.com / admin123"
Write-Host "Dealer: dealer@intellcar.com / dealer123"
Write-Host "User: user@intellcar.com / user123"
Write-Host "`nPara iniciar el servidor ejecuta:" -ForegroundColor Yellow
Write-Host "php artisan serve`n"
```

**Ejecutar:**
```powershell
.\install.ps1
```

---

## 🔐 Autenticación

### Registro de Usuario

**Endpoint:** `POST /api/auth/register`

```json
{
  "user_name": "Juan Pérez",
  "email_address": "juan@example.com",
  "phone": "+34600123456",
  "user_password": "password123",
  "user_password_confirmation": "password123",
  "paddock_id": 1
}
```

**Respuesta:**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {
    "user_id": 4,
    "user_name": "Juan Pérez",
    "email_address": "juan@example.com",
    "user_tag": "indv"
  },
  "token": "1|abc123def456..."
}
```

### Login

**Endpoint:** `POST /api/auth/login`

```json
{
  "email_address": "user@intellcar.com",
  "user_password": "user123"
}
```

**Respuesta:**
```json
{
  "message": "Login exitoso",
  "user": { ... },
  "token": "2|xyz789..."
}
```

### Usar el Token

En todas las peticiones autenticadas, añade el header:

```
Authorization: Bearer tu_token_aqui
```

---

## 🚗 Ejemplos de Uso

### 1. Listar Anuncios con Filtros

```bash
GET /api/adverts?visible=true&ad_type=used&min_price=10000&max_price=30000&car_color=rojo
```

**Respuesta:**
```json
{
  "current_page": 1,
  "data": [
    {
      "ad_id": 1,
      "ad_title": "BMW Serie 3 320d Impecable",
      "price": "25000.00",
      "kilometers": 85000,
      "car_color": "rojo",
      "year_manufacture": 2018,
      "model": {
        "model_name": "Serie 3",
        "make": {
          "make_name": "BMW"
        }
      },
      "engine": {
        "engine_name": "2.0 TDI",
        "fuel_type": "diesel"
      },
      "media": [
        {
          "media_url": "https://example.com/image1.jpg",
          "media_type": "image"
        }
      ]
    }
  ],
  "total": 15,
  "per_page": 20
}
```

### 2. Crear un Anuncio

**Endpoint:** `POST /api/adverts`  
**Auth:** Requerido

```json
{
  "ad_title": "Vendo Audi A4 2020",
  "ad_type": "used",
  "ad_details": "Vehículo en perfecto estado, revisiones al día...",
  "price": 32000,
  "kilometers": 45000,
  "car_color": "gris",
  "year_manufacture": 2020,
  "region": "Madrid",
  "city": "Madrid",
  "model_id": 5,
  "engine_id": 8
}
```

### 3. Crear un Post

**Endpoint:** `POST /api/posts`  
**Auth:** Requerido

```json
{
  "title": "Mi experiencia con el Tesla Model 3",
  "content": "Después de 6 meses con el Model 3, puedo decir que...",
  "model_id": 10
}
```

### 4. Dar Like a un Post

**Endpoint:** `POST /api/posts/3/like`  
**Auth:** Requerido

**Respuesta:**
```json
{
  "message": "Like añadido exitosamente"
}
```

### 5. Crear un Evento

**Endpoint:** `POST /api/events`  
**Auth:** Requerido

```json
{
  "title": "Quedada coches clásicos Madrid",
  "event_description": "Encuentro mensual de coches clásicos en el Jarama",
  "event_date": "2026-03-15 10:00:00",
  "location_name": "Circuito del Jarama",
  "city": "Madrid",
  "latitude": 40.6201,
  "longitude": -3.5875,
  "max_participants": 50,
  "paddock_id": 1
}
```

### 6. Unirse a un Evento

**Endpoint:** `POST /api/events/2/join`  
**Auth:** Requerido

**Respuesta:**
```json
{
  "message": "Te has unido al evento exitosamente"
}
```

---

## 📊 Endpoints Completos

### Autenticación
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout (auth)
- `GET /api/auth/me` - Usuario actual (auth)

### Anuncios
- `GET /api/adverts` - Listar (filtros: visible, ad_type, min_price, max_price, car_color, region, city)
- `GET /api/adverts/{id}` - Ver detalle
- `POST /api/adverts` - Crear (auth)
- `PUT /api/adverts/{id}` - Actualizar (auth, owner/admin)
- `DELETE /api/adverts/{id}` - Eliminar (auth, owner/admin)

### Posts
- `GET /api/posts` - Listar
- `GET /api/posts/{id}` - Ver detalle
- `POST /api/posts` - Crear (auth)
- `PUT /api/posts/{id}` - Actualizar (auth, owner/admin)
- `DELETE /api/posts/{id}` - Eliminar (auth, owner/admin)
- `POST /api/posts/{id}/like` - Dar like (auth)
- `DELETE /api/posts/{id}/like` - Quitar like (auth)

### Eventos
- `GET /api/events` - Listar próximos eventos
- `GET /api/events/{id}` - Ver detalle
- `POST /api/events` - Crear (auth)
- `POST /api/events/{id}/join` - Unirse (auth)
- `DELETE /api/events/{id}/leave` - Salir (auth)

### Marcas y Comunidades
- `GET /api/makes` - Listar marcas
- `GET /api/makes/{id}` - Ver marca
- `GET /api/paddocks` - Listar paddocks/comunidades
- `GET /api/paddocks/{id}` - Ver paddock

---

## 🛡️ Autorización

### Perfiles de Usuario

| Tag | Permisos |
|-----|----------|
| `admin` | Acceso total, puede editar/eliminar cualquier contenido |
| `pro` | Puede crear múltiples anuncios (concesionario) |
| `indv` | Usuario individual estándar |
| `press` | Usuario de prensa/medios |

### Validación de Permisos

Los controladores validan automáticamente:
- El usuario debe ser dueño del recurso O ser admin para editar/eliminar
- Solo usuarios autenticados pueden crear contenido
- Algunos endpoints públicos no requieren auth (listados, detalles)

---

## 🧪 Testing con Postman/Insomnia

### Colección de ejemplo

1. **Crear entorno con:**
   - `base_url`: `http://localhost:8000/api`
   - `token`: (se rellena tras login)

2. **Flujo típico:**
   1. Register/Login → Guardar token
   2. Listar marcas → Obtener `make_id`
   3. Listar modelos de una marca → Obtener `model_id`
   4. Crear anuncio con `model_id` y `engine_id`
   5. Listar anuncios propios

---

## 🔧 Troubleshooting

### Error: "Unauthenticated"
- Verifica que el token esté en el header `Authorization: Bearer {token}`
- Comprueba que el token no haya expirado

### Error 403: "No tienes permiso..."
- Solo puedes editar/eliminar tu propio contenido (o ser admin)

### Error 422: Validación
- Revisa que todos los campos requeridos estén presentes
- Verifica que los IDs de relaciones existan (model_id, engine_id, etc.)

---

## 📱 Integración con Frontend

### Angular Example

```typescript
// service
const headers = new HttpHeaders({
  'Authorization': `Bearer ${this.token}`,
  'Content-Type': 'application/json'
});

this.http.post('http://localhost:8000/api/adverts', data, { headers })
  .subscribe(response => console.log(response));
```

### React Example

```javascript
const response = await fetch('http://localhost:8000/api/adverts', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(data)
});
```

---

## 📖 Swagger Documentation

Accede a la documentación interactiva completa:

```
http://localhost:8000/api/documentation
```

---

**¡Happy Coding! 🚗💨**
