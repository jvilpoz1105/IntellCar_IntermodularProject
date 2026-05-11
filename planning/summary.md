Objetivo: Implementar un sistema de validación de imágenes de vehículos altamente escalable, utilizando procesos asíncronos y servicios serverless para maximizar la eficiencia del servidor principal (EC2).
1. Flujo de Subida de Archivos (Presigned URLs)
Angular: Solicita a Laravel una URL firmada para cada imagen seleccionada.
Laravel: Utiliza el SDK de AWS para generar una Presigned URL con una validez de 15-20 minutos.
Angular: Realiza un PUT directo a AWS S3. El servidor Laravel no procesa los bytes de la imagen, ahorrando ancho de banda y CPU.
2. Disparador Serverless (AWS Lambda en Python)
Trigger: Configuración de un evento s3:ObjectCreated en el bucket de Media.
Función Lambda: Escrita en Python, se activa instantáneamente al terminar la subida.
Acción: La Lambda realiza una petición PATCH a un endpoint interno de Laravel (ej: /api/internal/media-verify) enviando el nombre del archivo y un token de seguridad.
Propósito académico: Demostrar el uso de arquitecturas orientadas a eventos y computación distribuida.
3. Procesamiento de Inteligencia Artificial (Laravel + Rekognition)
Una vez que la Lambda avisa a Laravel, el backend toma el control:
Llamada a Rekognition: Laravel solicita el análisis de etiquetas (Labels) y moderación de contenido.
Normalización de Datos:
Se eliminan etiquetas genéricas o repetidas (ej: "Car", "Vehicle", "Transportation").
Se filtran etiquetas específicas: Marca, Modelo, Color, extras (ej: "Sunroof").
Cruce con BBDD: Se comparan las etiquetas con las tablas de Make y Model para sugerir la categoría y el Paddock correspondiente (Incluso la propulsión, si alguna de las etiquetas sugiere que el coche es eléctrico o híbrido).
4. Experiencia de Usuario (Angular + GSAP)
Animación de Escaneo: Al recibir la confirmación de la Lambda/Laravel, se activa un "barrido láser" sobre la miniatura de la foto en el carrusel.
Smooth Fill: Los datos validados por la IA se inyectan en el formulario de publicación con una transición suave.
Validación Final: El botón "Publicar" permanece bloqueado si la IA detecta contenido inapropiado o si el objeto no es un vehículo válido.
5. Seguridad y Optimización
API Key Interna: La comunicación Lambda -> Laravel está protegida por un secreto compartido para evitar peticiones fraudulentas.
FinOps: Uso de servicios Serverless para mantener el consumo dentro de la capa gratuita del AWS Learner Lab.
