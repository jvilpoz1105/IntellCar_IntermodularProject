Objetivo: Diseñar y desarrollar una experiencia de usuario disruptiva para el registro de vehículos y posts, integrando animaciones de alta fidelidad con feedback en tiempo real de los servicios de IA.
1. Arquitectura del Componente: Smart Modal Polimórfico
Versatilidad: Un único componente modular que adapta sus campos dependiendo del contexto (Publicar Anuncio vs. Crear Post de Comunidad).
Layout Split-View:
Izquierda (Media Engine): Carrusel interactivo con sistema de "Drag & Drop" y estados visuales (Analizando, Aceptada, Rechazada).
Derecha (Smart Form): Formulario dinámico con inputs especializados (Spartan UI).
2. Feedback Visual y Animaciones (GSAP & Motion.dev)
Efecto Escáner Láser: Al subir una imagen, se activa un barrido de luz mediante GSAP que recorre la miniatura, simbolizando el análisis de Rekognition.
Micro-interacciones: Uso de Motion.dev para transiciones suaves, efectos de "pop" al autorrellenar campos y estados de carga tipo Skeleton Screens en los inputs mientras la IA procesa la información.
Bocadillos de Auditoría (NLP): Implementación de tooltips animados tipo "commit" que aparecen junto a los campos de texto para mostrar advertencias de Comprehend (detección de teléfonos, idioma o tono).
3. Formulario Inteligente (Adaptive Inputs)
Control de Precisión: Combinación de Sliders y Textbox numéricos para precios y kilometraje, permitiendo ajustes rápidos y precisos.
Validación por Color: Los bordes de los inputs cambian dinámicamente:
Azul: Análisis en curso (IA pensando).
Verde: Datos validados y coherentes.
Rojo: Error de moderación o incoherencia detectada.
4. Implementación Técnica en Angular (Spartan UI)
Componentes Base: Uso de Comboboxes para selección de combustibles/marcas y Textareas expansibles para descripciones.
Optimización (Zoneless/Signals): Gestión eficiente del estado de la UI para asegurar que las animaciones de GSAP se ejecuten a 60fps sin bloqueos del hilo principal.
5. Resumen de Valor para la Defensa
Esta UI no es solo estética; es una herramienta de asistencia al usuario. Reduce la fricción en el alta de anuncios, garantiza la calidad de los datos mediante validación visual y protege la privacidad mediante el análisis de texto en tiempo real.
