# IntellCar_IntermodularProject
## 🚗 What is IntellCar?

IntellCar is much more than a car website; it is the ultimate meeting point for motor enthusiasts. It is a 360º platform where you can do everything from buying your next vehicle to sharing photos of your road trips or meeting up with people who share your same passion.

To help you understand it better, the app is divided into four main "districts":

### 1. 🏁 Marketplace: Your next road companion
This is our buying and selling zone, but with a professional touch. Here, listings are not just photos and a price; they are categorized by **Paddocks** (lifestyles).

* **How it works:** You can search for cars by filtering by brand, horsepower, or even by their "vibe" (are you looking for a classic to pamper or a 4x4 to get muddy?).
* **Direct contact:** If you like a car, you have a contact button to speak directly with the seller via email or phone. No detours.

### 2. 📅 Events (Kdds): From the phone to the asphalt
What is the point of having a spectacular car if you have no one to enjoy it with? This section is for socializing in the real world.

* **Create and attend:** Any user can organize a "Kdd" (meetup), set a point on the map, a date, and a time.
* **Community:** You can see who else is attending, what cars they will bring, and sign up with a single click. It is the perfect place to put a face to the people in your Paddock.

### 3. 🌌 The Universe: Your gasoline-powered Social Network
This is the feed where passion lives. Forget about food photos; here, there is only metal, tires, and landscapes.

* **Quality content:** Users (enthusiasts, owners, and even industry journalists) publish their stories, photos of their latest modifications, or articles about motor news.
* **Interaction:** You can "like" and follow other users to keep up with what is happening in their garage or on their routes.

### 4. 🛠️ My Garage: Your personal pride
This is your private showcase within the app. It is the space where you show the world which machines have passed through your hands.

* **Your history:** You can add the cars you currently own and also those you had in the past (your "exes").
* **Personalization:** Give them a nickname, upload their best photo, and tell their story. It is your introduction to the community: when someone views your profile, they will know exactly what kind of petrolhead you are.

---

**In short:** IntellCar is the place you enter to look for a car, but you stay for the people, the stories, and the routes. It is the vertex where everything with an engine comes to life!

# IntellCar_ProyectoIntermodular
Proyecto Intermodular DAW 2025-2026 Juan Benítez y José M. Villanúa

🚗 ¿Qué es IntellCar?
IntellCar es mucho más que una web de coches; es el punto de encuentro definitivo para los amantes del motor. Es una plataforma 360º donde puedes desde comprar tu próximo vehículo hasta compartir fotos de tus rutas o quedar con gente que siente la misma pasión que tú.

Para que lo entiendas mejor, la app se divide en cuatro grandes "distritos":

1. 🏁 Marketplace: Tu próximo compañero de ruta
Es nuestra zona de compraventa, pero con un toque profesional. Aquí los anuncios no son solo fotos y un precio; están categorizados por Paddocks (estilos de vida).

Cómo funciona: Puedes buscar coches filtrando por marca, potencia o incluso por su "vibe" (¿buscas un clásico para mimar o un 4x4 para llenarlo de barro?).

Contacto directo: Si te gusta un coche, tienes un botón de contacto para hablar directamente con el vendedor por email o teléfono. Sin rodeos.

2. 📅 Eventos (Kdds): Del móvil al asfalto
¿De qué sirve tener un coche espectacular si no tienes con quién disfrutarlo? Esta sección es para socializar en el mundo real.

Crea y asiste: Cualquier usuario puede organizar una "Kdd" (quedada), poner un punto en el mapa, una fecha y una hora.

Comunidad: Puedes ver quién más va a asistir, qué coches llevarán y apuntarte con un solo clic. Es el lugar perfecto para poner cara a la gente de tu Paddock.

3. 🌌 El Universo: Tu Red Social de gasolina
Es el muro donde vive la pasión. Olvídate de fotos de comida; aquí solo hay metal, neumáticos y paisajes.

Contenido de calidad: Los usuarios (entusiastas, propietarios e incluso periodistas del sector) publican sus historias, fotos de sus últimas modificaciones o artículos sobre novedades del motor.

Interacción: Puedes dar "likes" y seguir a otros usuarios para estar al tanto de lo que pasa en su garaje o en sus rutas.

4. 🛠️ Mi Garaje: Tu orgullo personal
Esta es tu vitrina privada dentro de la app. Es el espacio donde muestras al mundo qué máquinas han pasado por tus manos.

Tu historial: Puedes añadir los coches que tienes actualmente y también los que tuviste en el pasado (tus "ex").

Personalización: Ponles un mote, sube su mejor foto y cuenta su historia. Es tu carta de presentación ante la comunidad: cuando alguien vea tu perfil, sabrá exactamente qué clase de petrolhead eres.

En resumen: IntellCar es el lugar donde entras para buscar un coche, pero te quedas por la gente, las historias y las rutas. ¡Es el vértice donde todo lo que tiene motor cobra vida!

---

## 🔑 Credenciales de Acceso (Entorno de Desarrollo)

Para facilitar las pruebas de la API y el inicio de sesión, se han configurado los siguientes usuarios en el Seeder:

### Usuarios Principales
| Perfil | Email | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@intellcar.com` | `admin123` |
| **Profesional** | `carlos@ferrari.com` | `ferrari123` |
| **Individual** | `maria.gonzalez@email.com` | `password123` |
| **Tuner** | `juan.perez@tuning.com` | `tuning123` |
| **Prensa** | `laura@motorpress.com` | `press123` |

### Usuarios Aleatorios
Para cualquier otro usuario generado automáticamente por las factories, la contraseña por defecto es:
👈 **`password123`**

---

## 🎨 Arquitectura del Frontend (Monorepo)

El frontend de IntellCar está organizado como un **Monorepo** moderno utilizando **Turborepo** y **pnpm**.

### Estructura de Carpetas
- **`front/apps/`**: Contiene las aplicaciones finales.
  - **`browser-client/`**: Aplicación principal en **Angular 20**. Aquí reside toda la lógica de la web.
- **`front/packages/`**: Librerías y configuraciones compartidas (TypeScript, ESLint, etc.).
- **`infrastructure/`**: Ficheros de Terraform para el despliegue en AWS.

### Guía de Desarrollo (Angular)
Cuando desarrolles el cliente, sigue esta organización para mantener el código limpio:

1. **Pantallas/Páginas**: Crea las vistas completas en `src/app/pages/` (ej. `home`, `catalog`, `login`).
2. **Componentes de Negocio**: Crea piezas reutilizables con lógica propia en `src/app/shared/components/` (ej. `CarCard`).
3. **Componentes de UI (Spartan UI)**: 
   - Se encuentran en `front/apps/browser-client/libs/ui/`.
   - Para añadir nuevos componentes base (modales, tablas, etc.), usa el CLI desde la carpeta de la app:
     `npx @spartan-ng/cli:ui [nombre-del-componente]`

### Comandos Útiles (desde carpeta `front/`)
- `pnpm dev`: Arranca el servidor de desarrollo en modo monorepo ([http://localhost:4200](http://localhost:4200)).
- `pnpm build`: Genera el build de producción optimizado.
- `pnpm lint`: Ejecuta el linter en todo el monorepo.

### 🧭 Navegación y Enrutado
La navegación en Angular Standalone es sencilla y se basa en tres pilares:

1. **Configuración de Rutas**: Se definen en `src/app/app.routes.ts`.
   ```typescript
   export const routes: Routes = [
     { path: 'home', component: HomeComponent },
     { path: 'marketplace', component: MarketComponent },
     { path: '', redirectTo: '/home', pathMatch: 'full' }
   ];
   ```

2. **Navegación desde el HTML**: Usa `routerLink` para moverte sin recargar la página.
   ```html
   <a routerLink="/marketplace" hlmBtn variant="outline">Ir al Marketplace</a>
   ```

3. **Navegación desde el Código**: Inyecta el `Router` para navegar tras una acción (como un login).
   ```typescript
   private _router = inject(Router);
   
   navegar() {
     this._router.navigate(['/dashboard']);
   }
   ```

### ✨ Animaciones y Movimiento
Para conseguir una experiencia premium, el proyecto integra las dos librerías de animación más potentes del ecosistema:

1. **Motion** (`motion.dev`): Utilizada para micro-interacciones y entradas suaves de componentes. Es ligera y aprovecha las APIs nativas del navegador.
2. **GSAP** (`gsap.com`): El estándar de la industria para animaciones complejas, efectos de scroll y líneas de tiempo avanzadas.

**¿Cómo conviven con Spartan UI?**
Ambas librerías son agnósticas. Mientras Spartan/Tailwind se encargan del estilo estático, Motion y GSAP manipulan el DOM directamente para añadir vida a los componentes sin interferir con su lógica.
