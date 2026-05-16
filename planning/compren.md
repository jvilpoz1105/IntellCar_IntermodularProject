Objetivo: Implementar un sistema de auditoría de contenido textual (NLP) que garantice la calidad, seguridad y cumplimiento normativo de lo que los usuarios escriben en los campos de texto de los formularios (Título, descripción...)
1. Análisis Lingüístico y Restricciones Regionales
Detección de Idioma Dominante: Laravel enviará el texto de la descripción a Comprehend para identificar el idioma.
Lógica de Negocio: Si el idioma detectado no es el permitido (ej. el usuario escribe en inglés en una plataforma para España), el sistema lanzará un aviso preventivo para mejorar el posicionamiento del anuncio.
2. Protección de Datos y Privacidad (PII - Personally Identifiable Information)
Este es el punto más crítico para la seguridad del modelo de negocio:
Detección de Entidades Sensibles: Uso de DetectEntities para identificar números de teléfono, correos electrónicos o direcciones físicas ocultas en el texto.
Acción Preventiva: Si se detectan datos de contacto, el sistema bloqueará el botón "Publicar" y mostrará una alerta de seguridad: "Por tu seguridad, no incluyas datos personales aquí, ya tenemos los datos de contacto de tu cuenta"
3. Análisis de Sentimiento y Calidad del Anuncio
Sentiment Analysis: Clasificación del tono de la descripción (Positive, Neutral, Negative, Mixed).
Sugerencias al Usuario: Si el sentimiento es marcadamente negativo, el sistema sugerirá al usuario reescribir la descripción para aumentar las posibilidades de venta.
4. Implementación Técnica en el Backend (Laravel)
Endpoint de Validación: Se creará un servicio ComprehendService en Laravel que encapsule las llamadas al SDK de AWS.
Flujo Asíncrono: La validación se disparará mediante un evento de Angular (debounced input) para no saturar la API mientras el usuario escribe.

El componente de entrada de texto (creo que era smart-field) que se utiliza en cada formulario, ahora tendrá diferentes estados. Según lo que detecte Comphrenhend.  
