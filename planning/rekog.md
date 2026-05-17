Objetivo: Implementar un sistema de validación de imágenes de vehículos que combine la potencia de la computación serverless (AWS Lambda) con una experiencia de usuario fluida y reactiva mediante RxJS en el frontend.
1. Desacoplamiento de Carga: S3 Presigned URLs
Optimización de Recursos: Angular solicita a la API de Laravel una URL firmada. La subida se realiza directamente desde el cliente a S3 mediante un método PUT.
Beneficio: Se elimina el cuello de botella en la instancia EC2, permitiendo que el servidor solo gestione metadatos y no tráfico pesado de archivos.
2. Automatización de Infraestructura (Event-Driven)
AWS Lambda (Python): Al completarse la subida a S3, se dispara automáticamente una función Lambda.
Notificación de Estado: La Lambda realiza una petición PATCH a un endpoint interno de Laravel. Este paso garantiza que el sistema sea consciente de que el archivo existe físicamente en el storage antes de iniciar cualquier proceso de IA.
Seguridad: La comunicación está protegida mediante una X-Internal-Token compartida entre la Lambda y Laravel.
3. Comunicación Reactiva en Angular (BehaviorSubject)
En lugar de utilizar WebSockets complejos, implementamos un patrón de estado mediante RxJS:
PhotoAiService: Un servicio centralizado con un BehaviorSubject que gestiona los estados: IDLE, UPLOADING, SCANNING, SUCCESS o ERROR.
Componentes Sincronizados: El carrusel de fotos y el formulario de detalles se suscriben al observable status$. Cuando el estado cambia a SCANNING, se dispara automáticamente la animación de escaneo láser con GSAP.
4. Inteligencia Artificial y Lógica de Negocio (Laravel + Rekognition)
Análisis síncrono: Laravel invoca a AWS Rekognition para obtener etiquetas de la imagen.
Procesamiento de Arrays: Se filtran las etiquetas para evitar duplicidad (ej. eliminar múltiples entradas de "Car") y se extraen Marca, Modelo y Color.
Validación de Reglas: Si Rekognition detecta contenido no permitido o falta de coherencia (ej. no hay un coche en la foto), Laravel responde con un error 422, lo que actualiza el BehaviorSubject a ERROR y bloquea la publicación.
5. Ventajas para la Defensa del Proyecto
Escalabilidad: Uso de S3 y Lambda para tareas asíncronas.
Modernidad: Implementación de patrones reactivos en el Frontend (RxJS).
Robustez: Validación por IA antes de permitir la persistencia en la base de datos RDS.
