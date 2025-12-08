-- ============================================
-- UrbanPropiedades - MySQL Schema
-- Código para phpMyAdmin / MySQL
-- ============================================

-- Crear base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS urbanpropiedades 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE urbanpropiedades;

-- ============================================
-- TABLA: regions (Regiones de Chile)
-- ============================================
CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: comunas
-- ============================================
CREATE TABLE IF NOT EXISTS comunas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLA: users (Administradores y Socios)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    name VARCHAR(100),
    phone VARCHAR(20),
    company_name VARCHAR(255),
    photo_url VARCHAR(500),
    role ENUM('admin', 'partner') DEFAULT 'partner',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: property_types (Tipos de Propiedad)
-- ============================================
CREATE TABLE IF NOT EXISTS property_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50),
    is_usa TINYINT(1) DEFAULT 0 COMMENT 'Es tipo para propiedades USA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: properties (Propiedades)
-- ============================================
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'CLP',
    operation_type ENUM('Venta', 'Arriendo') DEFAULT 'Venta',
    property_type VARCHAR(100) COMMENT 'Tipo de propiedad (texto)',
    property_type_id INT COMMENT 'FK a property_types (opcional)',
    property_category VARCHAR(100) DEFAULT '' COMMENT 'Categoría adicional',
    region_id INT,
    comuna_id INT,
    address VARCHAR(255),
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    area DECIMAL(10,2) COMMENT 'Área total terreno',
    built_area DECIMAL(10,2) COMMENT 'Área construida',
    total_area DECIMAL(10,2) COMMENT 'Área total (alternativo)',
    parking_spots INT DEFAULT 0 COMMENT 'Estacionamientos',
    year_built INT,
    images TEXT COMMENT 'JSON array de URLs de imágenes',
    features TEXT COMMENT 'JSON array de características',
    featured TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0 COMMENT 'Propiedad destacada',
    is_active TINYINT(1) DEFAULT 1,
    is_project TINYINT(1) DEFAULT 0 COMMENT 'Es un proyecto (USA)',
    user_id INT COMMENT 'FK a users (legacy)',
    partner_id INT COMMENT 'FK a users (socio propietario)',
    section_type VARCHAR(50) DEFAULT 'propiedades' COMMENT 'propiedades, terrenos, activos, usa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_type_id) REFERENCES property_types(id) ON DELETE SET NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL,
    FOREIGN KEY (comuna_id) REFERENCES comunas(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (partner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- TABLA: property_photos (Fotos de Propiedades)
-- ============================================
CREATE TABLE IF NOT EXISTS property_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLA: portal_clients (Clientes del Portal Premium)
-- ============================================
CREATE TABLE IF NOT EXISTS portal_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(255) NOT NULL,
    rut VARCHAR(20) NOT NULL,
    registered_sections VARCHAR(255) DEFAULT NULL,
    representante_legal VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    cedula_identidad VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alias VARCHAR(100) NOT NULL,
    consent_accepted TINYINT(1) DEFAULT 0,
    consent_date TIMESTAMP NULL,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: carousel_images (Imágenes del Carousel)
-- ============================================
CREATE TABLE IF NOT EXISTS carousel_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    link_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA: property_terreno_details (Detalles de Terrenos)
-- ============================================
CREATE TABLE IF NOT EXISTS property_terreno_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL UNIQUE,
    zoning_type VARCHAR(100) COMMENT 'Tipo de zonificación',
    land_use VARCHAR(100) COMMENT 'Uso de suelo',
    buildability_coefficient DECIMAL(5,2) COMMENT 'Coeficiente de constructibilidad',
    soil_occupation DECIMAL(5,2) COMMENT 'Ocupación de suelo',
    max_height DECIMAL(10,2) COMMENT 'Altura máxima permitida',
    has_anteproyecto TINYINT(1) DEFAULT 0 COMMENT 'Tiene anteproyecto aprobado',
    anteproyecto_details TEXT COMMENT 'Detalles del anteproyecto',
    approved_departments INT COMMENT 'Departamentos aprobados',
    approved_floors INT COMMENT 'Pisos aprobados',
    approved_parking INT COMMENT 'Estacionamientos aprobados',
    water_connection TINYINT(1) DEFAULT 0,
    electricity_connection TINYINT(1) DEFAULT 0,
    gas_connection TINYINT(1) DEFAULT 0,
    sewage_connection TINYINT(1) DEFAULT 0,
    topography VARCHAR(100) COMMENT 'Topografía del terreno',
    access_type VARCHAR(100) COMMENT 'Tipo de acceso',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLA: client_favorites (Favoritos de Clientes)
-- ============================================
CREATE TABLE IF NOT EXISTS client_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES portal_clients(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE(client_id, property_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLA: property_usa_details (Detalles de Propiedades USA)
-- ============================================
CREATE TABLE IF NOT EXISTS property_usa_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL UNIQUE,
    is_project TINYINT(1) DEFAULT 0 COMMENT 'Es un proyecto de desarrollo',
    surface_sqft DECIMAL(10,2) COMMENT 'Superficie en sqft',
    lot_size_sqft DECIMAL(10,2) COMMENT 'Tamaño del lote en sqft',
    price_usd DECIMAL(15,2) COMMENT 'Precio en USD',
    hoa_fee DECIMAL(10,2) COMMENT 'Cuota HOA mensual',
    property_tax DECIMAL(10,2) COMMENT 'Impuesto predial anual',
    year_built INT COMMENT 'Año de construcción',
    stories INT COMMENT 'Número de pisos',
    garage_spaces INT DEFAULT 0 COMMENT 'Espacios de garage',
    pool TINYINT(1) DEFAULT 0 COMMENT 'Tiene piscina',
    waterfront TINYINT(1) DEFAULT 0 COMMENT 'Frente al agua',
    view_type VARCHAR(100) COMMENT 'Tipo de vista',
    heating VARCHAR(100) COMMENT 'Sistema de calefacción',
    cooling VARCHAR(100) COMMENT 'Sistema de aire acondicionado',
    flooring VARCHAR(255) COMMENT 'Tipo de piso',
    appliances TEXT COMMENT 'Electrodomésticos incluidos',
    exterior_features TEXT COMMENT 'Características exteriores',
    interior_features TEXT COMMENT 'Características interiores',
    community_features TEXT COMMENT 'Características de la comunidad',
    project_units INT COMMENT 'Unidades del proyecto',
    project_developer VARCHAR(255) COMMENT 'Desarrollador del proyecto',
    project_completion_date DATE COMMENT 'Fecha de entrega del proyecto',
    project_amenities TEXT COMMENT 'Amenidades del proyecto',
    whatsapp_number VARCHAR(50) COMMENT 'Número de WhatsApp',
    mls_id VARCHAR(50) COMMENT 'ID del MLS',
    state VARCHAR(50) COMMENT 'Estado (ej: Florida, Texas)',
    city VARCHAR(100) COMMENT 'Ciudad',
    zip_code VARCHAR(20) COMMENT 'Código postal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- ÍNDICES ADICIONALES
-- ============================================
CREATE INDEX idx_properties_section ON properties(section_type);
CREATE INDEX idx_properties_is_active ON properties(is_active);
CREATE INDEX idx_properties_featured ON properties(featured);
CREATE INDEX idx_portal_clients_email ON portal_clients(email);
CREATE INDEX idx_portal_clients_rut ON portal_clients(rut);

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Usuarios de prueba (la contraseña para ambos es 'password')
INSERT INTO users (username, password, email, name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@urbanpropiedades.cl', 'Administrador', 'admin'),
('socio1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'socio@urbanpropiedades.cl', 'Socio Demo', 'partner');

-- Tipos de propiedad (Chile)
INSERT INTO property_types (name, icon, is_usa) VALUES
('Casa', 'home', 0),
('Departamento', 'building', 0),
('Oficina', 'briefcase', 0),
('Local Comercial', 'store', 0),
('Bodega', 'warehouse', 0),
('Terreno', 'map', 0),
('Galpón', 'warehouse', 0),
('Estacionamiento', 'car', 0);

-- Tipos de propiedad (USA)
INSERT INTO property_types (name, icon, is_usa) VALUES
('Single Family Home', 'home', 1),
('Condo', 'building', 1),
('Townhouse', 'home', 1),
('Multi-Family', 'building', 1),
('Land', 'map', 1),
('Commercial', 'briefcase', 1),
('Vacation Home', 'umbrella', 1),
('Investment Property', 'chart', 1),
('New Construction', 'construction', 1),
('Luxury Home', 'star', 1);

-- Regiones de Chile
INSERT INTO regions (name, code) VALUES
('Arica y Parinacota', 'XV'),
('Tarapacá', 'I'),
('Antofagasta', 'II'),
('Atacama', 'III'),
('Coquimbo', 'IV'),
('Valparaíso', 'V'),
('Metropolitana de Santiago', 'RM'),
('O''Higgins', 'VI'),
('Maule', 'VII'),
('Ñuble', 'XVI'),
('Biobío', 'VIII'),
('La Araucanía', 'IX'),
('Los Ríos', 'XIV'),
('Los Lagos', 'X'),
('Aysén', 'XI'),
('Magallanes', 'XII');

-- Comunas de la Región Metropolitana (ejemplo)
INSERT INTO comunas (region_id, name) VALUES
((SELECT id FROM regions WHERE code = 'RM'), 'Santiago'),
((SELECT id FROM regions WHERE code = 'RM'), 'Providencia'),
((SELECT id FROM regions WHERE code = 'RM'), 'Las Condes'),
((SELECT id FROM regions WHERE code = 'RM'), 'Vitacura'),
((SELECT id FROM regions WHERE code = 'RM'), 'Lo Barnechea'),
((SELECT id FROM regions WHERE code = 'RM'), 'Ñuñoa'),
((SELECT id FROM regions WHERE code = 'RM'), 'La Reina'),
((SELECT id FROM regions WHERE code = 'RM'), 'Peñalolén'),
((SELECT id FROM regions WHERE code = 'RM'), 'Macul'),
((SELECT id FROM regions WHERE code = 'RM'), 'San Miguel'),
((SELECT id FROM regions WHERE code = 'RM'), 'La Florida'),
((SELECT id FROM regions WHERE code = 'RM'), 'Puente Alto'),
((SELECT id FROM regions WHERE code = 'RM'), 'Maipú'),
((SELECT id FROM regions WHERE code = 'RM'), 'Pudahuel'),
((SELECT id FROM regions WHERE code = 'RM'), 'Cerrillos'),
((SELECT id FROM regions WHERE code = 'RM'), 'Estación Central'),
((SELECT id FROM regions WHERE code = 'RM'), 'Quilicura'),
((SELECT id FROM regions WHERE code = 'RM'), 'Huechuraba'),
((SELECT id FROM regions WHERE code = 'RM'), 'Recoleta'),
((SELECT id FROM regions WHERE code = 'RM'), 'Independencia');

-- Comunas de Valparaíso (ejemplo)
INSERT INTO comunas (region_id, name) VALUES
((SELECT id FROM regions WHERE code = 'V'), 'Valparaíso'),
((SELECT id FROM regions WHERE code = 'V'), 'Viña del Mar'),
((SELECT id FROM regions WHERE code = 'V'), 'Concón'),
((SELECT id FROM regions WHERE code = 'V'), 'Quilpué'),
((SELECT id FROM regions WHERE code = 'V'), 'Villa Alemana'),
((SELECT id FROM regions WHERE code = 'V'), 'Quillota'),
((SELECT id FROM regions WHERE code = 'V'), 'San Antonio');

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================
-- 1. Las contraseñas deben ser hasheadas con password_hash() de PHP
-- 2. El hash de ejemplo es para 'password' - cambiar en producción
-- 3. Ajustar el CHARACTER SET según necesidades
-- 4. Los índices pueden optimizarse según patrones de consulta
-- 5. Considerar particionamiento para tablas grandes
