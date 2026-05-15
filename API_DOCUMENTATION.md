# 📚 Documentación de la API IntellCar

Este documento contiene la guía oficial de los endpoints disponibles en la API de IntellCar, organizada por distritos (servicios). Recientemente, la API ha sido reestructurada para enfocarse en **servicios de dominio** en lugar de tablas de base de datos, mejorando la escalabilidad y separando la gestión pública de la privada (Soft Deletes vs. Borrado Real).

---

## 🛠️ Swagger / OpenAPI

La API dispone de documentación interactiva generada automáticamente con **L5-Swagger** (OpenAPI 3.0.0).

### Acceso a la UI

| Entorno | URL |
|---------|-----|
| Local (Artisan serve) | `http://localhost:8000/api/documentation` |
| Docker | `http://localhost:80/api/documentation` (según el puerto mapeado) |

### Especificación JSON

El fichero generado se encuentra en:
```
src/storage/api-docs/api-docs.json
```

### Regenerar la documentación

Cada vez que se añadan o modifiquen anotaciones `@OA` en los controladores, hay que ejecutar:

```bash
php artisan l5-swagger:generate
```

Para regenerar automáticamente en cada petición durante el desarrollo, establece en `.env`:
```env
L5_SWAGGER_GENERATE_ALWAYS=true
```

### Autenticación en Swagger UI

Los endpoints protegidos requieren un token Sanctum. Pasos:

1. Llama a `POST /api/auth/login` desde Swagger UI y copia el `token` de la respuesta.
2. Haz clic en **Authorize** (icono del candado) en la esquina superior derecha de la UI.
3. Introduce el valor `Bearer <tu_token>` en el campo `sanctum (http, Bearer)`.
4. Confirma con **Authorize**.

### Dependencias instaladas

| Paquete | Versión |
|---------|---------|
| `darkaonline/l5-swagger` | ^11.0 |
| `zircote/swagger-php` | ^6.0 (transitiva) |
| `doctrine/annotations` | ^2.0 (requerida para parsear anotaciones `@OA`) |

### Configuración relevante

Fichero de configuración: `src/config/l5-swagger.php`

- **Ruta UI:** `api/documentation`
- **Directorio de escaneo:** `app/` (todos los controladores y esquemas bajo `app/`)
- **Analizador:** `ReflectionAnalyser` con soporte de atributos PHP y docblocks `@OA`
- **Especificación por defecto:** OpenAPI 3.0.0

---

## 🔐 Distrito 0: Autenticación
Gestión de acceso y tokens de seguridad (Laravel Sanctum).

### 1. Registrar Usuario
Crea una nueva cuenta de usuario en la plataforma.
- **URL:** `/api/auth/register`
- **Método:** `POST`
- **Cuerpo (JSON):**
  ```json
  {
    "user_name": "Nombre Real",
    "email_address": "email@ejemplo.com",
    "phone": "+34600000000",
    "user_password": "password123",
    "contact_email": "opcional@email.com",
    "address": "Calle Falsa 123",
    "paddock_id": 1
  }
  ```
- **Respuesta Exitosa (201):** Devuelve el objeto del usuario y el `token`.

### 2. Iniciar Sesión (Login)
Obtén un token Bearer para realizar peticiones protegidas.
- **URL:** `/api/auth/login`
- **Método:** `POST`
- **Cuerpo (JSON):**
  ```json
  {
    "email_address": "email@ejemplo.com",
    "user_password": "password123"
  }
  ```
- **Respuesta Exitosa (200):** Devuelve el `token`.

### 3. Mi Perfil
Obtén la información del usuario actualmente autenticado (obsoleto frente al nuevo `/api/profile`, pero mantenido por compatibilidad).
- **URL:** `/api/auth/me`
- **Método:** `GET`
- **Protección:** `auth:sanctum`

### 4. Cerrar Sesión (Logout)
Invalida el token actual.
- **URL:** `/api/auth/logout`
- **Método:** `POST`
- **Protección:** `auth:sanctum`

---

## 👤 Distrito 1: Usuarios (Administración)
Gestión general de perfiles por parte del equipo de administración.

### 1. Listar Usuarios
- **URL:** `/api/users`
- **Método:** `GET`
- **Protección:** `Admin` (Solo administradores)

### 2. Ver Perfil Específico (Público/Protegido)
- **URL:** `/api/users/{id}`
- **Método:** `GET`
- **Protección:** `auth:sanctum`

### 3. Borrar Usuario Definitivamente
- **URL:** `/api/users/{id}`
- **Método:** `DELETE`
- **Protección:** `Admin` (Borra relaciones y multimedia)

*(Nota: La edición de uno mismo se ha movido al Distrito 2).*

---

## 🏎️ Distrito 2: Perfil Propio y Garaje (`ProfileControl`)
Gestión del área personal del usuario autenticado.

### 1. Ver y Editar Datos Personales
- **URL:** `/api/profile`
- **Métodos:** `GET` (Ver datos propios) | `PUT` (Editar nombre, teléfono, email de contacto...)
- **Protección:** `auth:sanctum`

### 2. Actualizar Foto de Perfil
- **URL:** `/api/profile/picture`
- **Método:** `POST` (Soporta `multipart/form-data`)
- **Protección:** `auth:sanctum`

### 3. Solicitar Borrado de Cuenta (Soft Delete)
Marca la cuenta para eliminación lógica (`onDeleteRequest = now()`).
- **URL:** `/api/profile/soft-delete`
- **Método:** `PATCH`
- **Protección:** `auth:sanctum`

### 4. Gestionar el Garaje Virtual
- **Añadir Coche:** `POST /api/profile/garage`
- **Actualizar Coche:** `POST /api/profile/garage/{id}` *(Usamos POST con form-data en lugar de PUT para facilitar la subida de fotos)*.
- **Eliminar Coche:** `DELETE /api/profile/garage/{id}`
- **Protección:** `auth:sanctum`

---

## 🛒 Distrito 3: Market (`MarketControl`)
Compra y venta de vehículos (Anuncios).

### 1. Listar Anuncios (Público)
- **URL:** `/api/market`
- **Método:** `GET`
- **Nota:** Filtra automáticamente los anuncios que no son visibles (`visible = false`) o que han solicitado borrado.

### 2. Detalle de Anuncio (Público)
- **URL:** `/api/market/{id}`
- **Método:** `GET`

### 3. Editar Anuncio
- **URL:** `/api/market/{id}`
- **Método:** `PUT` / `PATCH`
- **Protección:** `Dueño` o `Admin`

### 4. Solicitar Borrado (Soft Delete)
- **URL:** `/api/market/{id}/soft-delete`
- **Método:** `PATCH`
- **Protección:** `Dueño`

### 5. Borrado Definitivo
- **URL:** `/api/market/{id}`
- **Método:** `DELETE`
- **Protección:** `Admin` (Limpia fotos en disco y relaciones)

---

## 🌐 Distrito 4: Social (`UnivControl`)
Red social, posts y publicaciones de la comunidad.

### 1. Listar Posts (Público)
- **URL:** `/api/social`
- **Método:** `GET`
- **Nota:** Excluye contenido marcado como `visible = false` o con `onDeleteRequest`.

### 2. Detalle de Post (Público)
- **URL:** `/api/social/{id}`
- **Método:** `GET`

### 3. Editar Post
- **URL:** `/api/social/{id}`
- **Método:** `PUT` / `PATCH`
- **Protección:** `Autor` o `Admin`

### 4. Solicitar Borrado (Soft Delete)
- **URL:** `/api/social/{id}/soft-delete`
- **Método:** `PATCH`
- **Protección:** `Autor`

### 5. Borrado Definitivo
- **URL:** `/api/social/{id}`
- **Método:** `DELETE`
- **Protección:** `Admin` (Limpia multimedia, desvincula likes y borra comentarios).

---

## 📍 Distrito 5: KDDs (`KddControl`)
Eventos, rutas y quedadas de motor.

### 1. Listar Eventos Próximos (Público)
- **URL:** `/api/kdds`
- **Método:** `GET`
- **Nota:** Solo lista eventos futuros (`event_date >= now()`) y visibles.

### 2. Detalle de Evento (Público)
- **URL:** `/api/kdds/{id}`
- **Método:** `GET`

### 3. Editar Evento
- **URL:** `/api/kdds/{id}`
- **Método:** `PUT` / `PATCH`
- **Protección:** `Creador` o `Admin`

### 4. Solicitar Borrado (Soft Delete)
- **URL:** `/api/kdds/{id}/soft-delete`
- **Método:** `PATCH`
- **Protección:** `Creador`

### 5. Borrado Definitivo
- **URL:** `/api/kdds/{id}`
- **Método:** `DELETE`
- **Protección:** `Admin` (Desvincula asistentes de la tabla relacional).

---

> [!TIP]
> **Formato de Autenticación:**  
> Para todos los métodos protegidos (`auth:sanctum`), debes incluir la cabecera:  
> `Authorization: Bearer <TU_TOKEN_AQUÍ>`

> [!NOTE]
> **Estrategia de Borrado (Soft Delete vs Hard Delete)**
> Los usuarios (dueños/creadores) solo pueden hacer **Soft Delete** (`PATCH /soft-delete`), lo cual oculta el recurso al público añadiendo un timestamp a `onDeleteRequest`. Solo los Administradores tienen permiso para ejecutar el método **DELETE** tradicional, el cual destruye el recurso de la base de datos limpiando de forma segura los archivos físicos multimedia y desenlazando las relaciones complejas.
