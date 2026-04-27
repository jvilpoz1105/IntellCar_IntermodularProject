# 📚 Documentación de la API IntellCar

Este documento contiene la guía oficial de los endpoints disponibles en la API de IntellCar, organizada por distritos.

---

## 🔐 Distrito 0: Autenticación
Gestión de acceso y tokens de seguridad.

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
Obtén la información del usuario actualmente autenticado.
- **URL:** `/api/auth/me`
- **Método:** `GET`
- **Protección:** `auth:sanctum` (Token Requerido)

### 4. Cerrar Sesión (Logout)
Invalida el token actual.
- **URL:** `/api/auth/logout`
- **Método:** `POST`
- **Protección:** `auth:sanctum`

---

## 👤 Distrito 1: Usuarios
Gestión y CRUD de perfiles de la comunidad.

### 1. Listar Usuarios
Obtiene una lista paginada de todos los usuarios registrados.
- **URL:** `/api/users`
- **Método:** `GET`
- **Protección:** `Admin` (Solo usuarios con `user_tag == 'admin'`)

### 2. Ver Perfil Específico
Obtiene los detalles de un usuario, incluyendo su garaje y posts.
- **URL:** `/api/users/{id}`
- **Método:** `GET`
- **Protección:** `auth:sanctum`

### 3. Actualizar Perfil
Modifica los datos de un usuario.
- **URL:** `/api/users/{id}`
- **Método:** `PUT` / `PATCH`
- **Protección:** `Dueño` o `Admin` (Un usuario solo puede editarse a sí mismo).

### 4. Eliminar Usuario
Borra permanentemente una cuenta.
- **URL:** `/api/users/{id}`
- **Método:** `DELETE`
- **Protección:** `Admin` (Un administrador no puede borrarse a sí mismo).

---

> [!TIP]
> **Formato de Autenticación:**  
> Para todos los métodos protegidos, debes incluir la cabecera:  
> `Authorization: Bearer <TU_TOKEN_AQUÍ>`
