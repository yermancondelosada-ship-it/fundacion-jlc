-- ESPECIFICACIÓN TÉCNICA: PLATAFORMA JLC V2.0
-- Script de creación de tablas y datos semilla

CREATE DATABASE IF NOT EXISTS plataforma_jlc;
USE plataforma_jlc;

-- 1. Usuarios (Admins y Estudiantes)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol ENUM('admin', 'estudiante') DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Cursos / Oferta Académica
CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    resumen TEXT,
    contenido_detallado LONGTEXT, -- Temario del curso
    precio DECIMAL(10,2) NOT NULL,
    imagen_portada VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
) ENGINE=InnoDB;

-- 3. Inscripciones y Validación de Pagos (CRÍTICO)
CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    curso_id INT,
    comprobante_img VARCHAR(255), -- Ruta de la foto del pago
    metodo_pago VARCHAR(50) DEFAULT 'QR / Transferencia',
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    observaciones_admin TEXT, -- Por si se rechaza el pago
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Configuración Dinámica (CMS)
CREATE TABLE IF NOT EXISTS site_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    llave VARCHAR(50) UNIQUE, -- Ej: 'primary_color', 'qr_image', 'mision_texto'
    valor TEXT,
    categoria VARCHAR(50) -- Ej: 'apariencia', 'contacto', 'institucion'
) ENGINE=InnoDB;

-- Datos Semilla (Admin inicial)
-- Password por defecto: admin123 (Hash de ejemplo)
INSERT INTO users (nombre_completo, email, password, rol) 
VALUES ('Administrador JLC', 'admin@jlc.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Configuraciones iniciales
INSERT INTO site_config (llave, valor, categoria) VALUES 
('mision', 'Nuestra misión es transformar vidas a través de la educación y la tecnología.', 'institucion'),
('vision', 'Ser la fundación líder en capacitación digital en el Caquetá para el 2030.', 'institucion'),
('qr_pago', 'qr_default.png', 'pagos'),
('color_primario', '#15803d', 'apariencia')
ON DUPLICATE KEY UPDATE id=id;
