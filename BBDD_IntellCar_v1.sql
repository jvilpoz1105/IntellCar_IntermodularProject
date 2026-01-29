/** ========================================================================
 * Primera versión BBDD IntellCar Project
 * ======================================================================== **/

-- 1. ESTRUCTURA DE LA COMUNIDAD (MOODS)
-- -------------------------------------------------------------------------
CREATE TABLE paddock (
    paddock_id INT AUTO_INCREMENT PRIMARY KEY,
    paddock_name VARCHAR(50) NOT NULL UNIQUE, 
    paddock_description VARCHAR(255)
);

-- 2. USUARIOS DE LA APLICACIÓN
-- -------------------------------------------------------------------------
CREATE TABLE app_user (  
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(90) NOT NULL,
    email_address VARCHAR(255) NOT NULL UNIQUE,
    contact_email VARCHAR(255),
    address VARCHAR(255),
    phone VARCHAR(20) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    user_tag ENUM('admin', 'pro', 'indv', 'press') DEFAULT 'indv',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    paddock_id INT, 
    FOREIGN KEY (paddock_id) REFERENCES paddock(paddock_id) ON DELETE SET NULL  
); 

-- 3. ESPECIFICACIONES TÉCNICAS (CATÁLOGO DE ANUNCIOS)
-- -------------------------------------------------------------------------

/** Marca*/
CREATE TABLE make (
    make_id INT AUTO_INCREMENT PRIMARY KEY,
    make_name VARCHAR(40) NOT NULL UNIQUE,
    origin_country VARCHAR(45),
    official_website VARCHAR(790),
    status ENUM('low-cost','mass-market','premium', 'luxury') NOT NULL DEFAULT 'mass-market'
);

/** Modelo*/
CREATE TABLE car_model (
    model_id INT AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(101) NOT NULL,
    make_id INT NOT NULL,
    model_description VARCHAR(255),
    FOREIGN KEY (make_id) REFERENCES make(make_id) ON DELETE CASCADE
);

/** Motor*/
CREATE TABLE car_engine (
    engine_id INT AUTO_INCREMENT PRIMARY KEY,
    engine_name VARCHAR(101) NOT NULL, 
    engine_description VARCHAR(255),
    fuel_type ENUM('gasolina', 'diesel', 'electrico', 'hibrido', 'glp') NOT NULL,
    make_id INT NOT NULL,
    FOREIGN KEY (make_id) REFERENCES make(make_id) ON DELETE CASCADE
);

/** Anuncio*/
CREATE TABLE car_advert (
    ad_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_title VARCHAR(165) NOT NULL,
    ad_type ENUM('new', 'km0', 'used','renting', 'leasing', 'supcription') NOT NULL,
    ad_details TEXT,
    price DECIMAL(12, 2) NOT NULL,
    kilometers INT DEFAULT 0,
    car_color ENUM('blanco', 'negro', 'gris', 'plata', 'rojo', 'azul', 'verde', 'amarillo', 'naranja', 'otro') NOT NULL,
    year_manufacture INT,
    region VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,

    visible BOOLEAN DEFAULT FALSE,
    
    publish_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    model_id INT NOT NULL,
    engine_id INT NOT NULL,
    seller_id INT NOT NULL,
    
    FOREIGN KEY (model_id) REFERENCES car_model(model_id) ON DELETE CASCADE,
    FOREIGN KEY (engine_id) REFERENCES car_engine(engine_id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);

/** Moods de anuncio*/
CREATE TABLE advert_moods (
    ad_id INT,
    mood_id INT,
    PRIMARY KEY (ad_id, mood_id),
    FOREIGN KEY (ad_id) REFERENCES car_advert(ad_id) ON DELETE CASCADE,
    FOREIGN KEY (mood_id) REFERENCES paddock(paddock_id) ON DELETE CASCADE
);

/** Fotos y videos de anuncio (URL)*/
CREATE TABLE ad_media (
    media_id INT AUTO_INCREMENT PRIMARY KEY,
    media_url VARCHAR(790) NOT NULL,
    media_type ENUM('image', 'video') NOT NULL,
    ad_id INT NOT NULL,
    FOREIGN KEY (ad_id) REFERENCES car_advert(ad_id) ON DELETE CASCADE
);

-- 4. EL UNIVERSO (POST Y NOTICIAS SOBRE COCHES, TAMBIÉN DEPENDIENDO DE SU MOOD)
-- -------------------------------------------------------------------------
CREATE TABLE post (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    title VARCHAR(150),
    content TEXT NOT NULL,
    model_id INT,
    engine_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES car_model(model_id) ON DELETE CASCADE,
    FOREIGN KEY (engine_id) REFERENCES car_engine(engine_id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);

/** Moods de los post*/
CREATE TABLE post_moods (
    post_id INT,
    mood_id INT,
    PRIMARY KEY (post_id, mood_id),
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE,
    FOREIGN KEY (mood_id) REFERENCES paddock(paddock_id) ON DELETE CASCADE
);

/** Fotos y videos de los post (URL)   */
CREATE TABLE post_media (
    media_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    media_url VARCHAR(790) NOT NULL,
    media_type ENUM('image', 'video') NOT NULL,
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE
);

/** Likes de los post */
CREATE TABLE post_like (
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE
);

CREATE TABLE post_comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);

-- Seguidores (Relación de un usuario con otro)
CREATE TABLE user_follow (
    follower_id INT NOT NULL, -- El que sigue
    followed_id INT NOT NULL, -- El seguido
    PRIMARY KEY (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);

-- 5. BÚSQUEDAS DE ANUNCIOS GUARDADAS (JSON)
-- -------------------------------------------------------------------------
CREATE TABLE saved_search (
    search_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    search_name VARCHAR(100) NOT NULL,
    filters_json JSON NOT NULL, -- Aquí Angular envía todos los filtros dinámicos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);

-- 6. NOTIFICACIONES (SI HAY TIEMPO)
-- -------------------------------------------------------------------------
CREATE TABLE notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notif_type ENUM('like', 'comment', 'follow', 'ad_interest') NOT NULL,
    notif_text VARCHAR(255) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);


/** ========================================================================
 * 7. GARAJE DEL USUARIO
 * ======================================================================== **/
CREATE TABLE user_garage (
    garage_item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    model_id INT NOT NULL,
    motor_id INT,           -- Motor específico del coche
    car_nickname VARCHAR(50), -- Ej: "El bicho", "Mi joya"
    description TEXT,         -- Breve historia del coche
    is_current_car BOOLEAN DEFAULT FALSE, -- TRUE si aún lo tiene, FALSE si es ex-coche
    photo_url VARCHAR(790),   -- Foto principal del coche en el garaje
    verified_owner BOOLEAN DEFAULT FALSE,


    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES car_model(model_id) ON DELETE CASCADE,
    FOREIGN KEY (motor_id) REFERENCES car_engine(engine_id) ON DELETE SET NULL
);

/** ========================================================================
 * 8. EVENTOS
 * ======================================================================== **/
CREATE TABLE event_kdd (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    creator_id INT NOT NULL,
    paddock_id INT,           -- Kdd vinculada a un estilo (ej: solo clásicos)
    title VARCHAR(150) NOT NULL,
    event_description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location_name VARCHAR(255), -- Ej: "Parking del Jarama"
    address VARCHAR(255),
    city VARCHAR(100),
    latitude DECIMAL(10, 8),    -- Para mostrarlo en un mapa en Angular
    longitude DECIMAL(11, 8),
    max_participants INT DEFAULT 0, -- 0 para ilimitado
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES app_user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (paddock_id) REFERENCES paddock(paddock_id) ON DELETE SET NULL
);

-- Tabla intermedia para saber quién va a qué Kdd
CREATE TABLE event_attendance (
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES event_kdd(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES app_user(user_id) ON DELETE CASCADE
);