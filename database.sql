-- Base de datos para WhatsApp Commerce
-- Ejecutar este script en phpMyAdmin o MySQL Workbench

CREATE DATABASE IF NOT EXISTS whatsapp_commerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE whatsapp_commerce;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    categoria_id INT,
    stock INT DEFAULT 0,
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    INDEX idx_categoria (categoria_id),
    INDEX idx_activo (activo),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios admin
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'operador') DEFAULT 'operador',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre VARCHAR(255) NOT NULL,
    cliente_telefono VARCHAR(20),
    cliente_direccion TEXT,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'completado', 'cancelado') DEFAULT 'pendiente',
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de items de pedidos
CREATE TABLE IF NOT EXISTS pedido_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT,
    producto_nombre VARCHAR(255) NOT NULL,
    producto_precio DECIMAL(10, 2) NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL,
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías iniciales
INSERT INTO categorias (nombre, descripcion) VALUES
('Maquillaje', 'Productos de maquillaje para rostro, ojos y labios'),
('Skincare', 'Cuidado de la piel: cremas, serums, limpiadores'),
('Perfumes', 'Fragancias para mujer y hombre'),
('Cabello', 'Productos para el cuidado del cabello'),
('Accesorios', 'Brochas, esponjas y herramientas de belleza'),
('Kits', 'Sets y kits especiales');

-- Insertar usuarios administradores (password: password123)
-- Hash generado con password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO usuarios (username, password, nombre, rol) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin'),
('operador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operador', 'operador');

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, slug, descripcion, precio, categoria_id, stock, imagen, activo) VALUES
('Base de Maquillaje HD', 'base-maquillaje-hd', 'Base de maquillaje de alta definición con cobertura media a completa. Acabado natural y duradero.', 299.00, 1, 50, 'base-hd.jpg', 1),
('Sérum Vitamina C', 'serum-vitamina-c', 'Sérum facial con vitamina C pura al 20%. Ilumina y unifica el tono de la piel.', 450.00, 2, 30, 'serum-vitc.jpg', 1),
('Perfume Floral Elegance', 'perfume-floral-elegance', 'Fragancia floral con notas de jazmín y rosa. Duración de 8+ horas.', 890.00, 3, 20, 'perfume-floral.jpg', 1),
('Shampoo Reparador', 'shampoo-reparador', 'Shampoo para cabello dañado con keratina y aceite de argán.', 180.00, 4, 100, 'shampoo-rep.jpg', 1),
('Set de Brochas Premium', 'set-brochas-premium', 'Set de 12 brochas profesionales con estuche. Cerdas sintéticas de alta calidad.', 650.00, 5, 25, 'brochas-set.jpg', 1),
('Kit Skincare Básico', 'kit-skincare-basico', 'Kit completo: limpiador, tónico, sérum y crema hidratante.', 1200.00, 6, 15, 'kit-skincare.jpg', 1),
('Labial Mate Larga Duración', 'labial-mate-larga-duracion', 'Labial líquido mate con acabado aterciopelado. 12 horas de duración.', 150.00, 1, 80, 'labial-mate.jpg', 1),
('Crema Hidratante Facial', 'crema-hidratante-facial', 'Crema hidratante con ácido hialurónico y niacinamida. Para todo tipo de piel.', 320.00, 2, 60, 'crema-hidratante.jpg', 1),
('Eau de Toilette Citrus', 'eau-toilette-citrus', 'Fragancia fresca cítrica perfecta para el día. Notas de limón y bergamota.', 550.00, 3, 35, 'perfume-citrus.jpg', 1),
('Acondicionador Nutritivo', 'acondicionador-nutritivo', 'Acondicionador con manteca de karité para cabello seco.', 190.00, 4, 90, 'acondicionador.jpg', 1);

-- Crear índices adicionales para optimización
CREATE INDEX idx_producto_precio ON productos(precio);
CREATE INDEX idx_producto_stock ON productos(stock);
CREATE INDEX idx_pedido_total ON pedidos(total);
