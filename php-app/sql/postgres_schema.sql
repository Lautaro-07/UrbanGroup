-- ============================================
-- UrbanPropiedades - PostgreSQL Schema
-- ============================================

-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS client_favorites CASCADE;
DROP TABLE IF EXISTS property_terreno_details CASCADE;
DROP TABLE IF EXISTS carousel_images CASCADE;
DROP TABLE IF EXISTS property_photos CASCADE;
DROP TABLE IF EXISTS portal_clients CASCADE;
DROP TABLE IF EXISTS properties CASCADE;
DROP TABLE IF EXISTS property_types CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS comunas CASCADE;
DROP TABLE IF EXISTS regions CASCADE;

-- ============================================
-- TABLA: regions (Regiones de Chile)
-- ============================================
CREATE TABLE regions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: comunas
-- ============================================
CREATE TABLE comunas (
    id SERIAL PRIMARY KEY,
    region_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE
);

-- ============================================
-- TABLA: users (Administradores y Socios)
-- ============================================
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    name VARCHAR(100),
    phone VARCHAR(20),
    company_name VARCHAR(255),
    photo_url VARCHAR(500),
    role VARCHAR(20) CHECK (role IN ('admin', 'partner')) DEFAULT 'partner',
    is_active SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: property_types (Tipos de Propiedad)
-- ============================================
CREATE TABLE property_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: properties (Propiedades)
-- ============================================
CREATE TABLE properties (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'UF',
    operation_type VARCHAR(20) CHECK (operation_type IN ('Venta', 'Arriendo')) DEFAULT 'Venta',
    property_type VARCHAR(100),
    property_type_id INT,
    region_id INT,
    comuna_id INT,
    address VARCHAR(255),
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    total_area DECIMAL(10,2),
    built_area DECIMAL(10,2),
    parking_spots INT DEFAULT 0,
    year_built INT,
    featured SMALLINT DEFAULT 0,
    is_active SMALLINT DEFAULT 1,
    is_featured SMALLINT DEFAULT 0,
    partner_id INT,
    section_type VARCHAR(50) DEFAULT 'propiedades',
    property_category VARCHAR(100),
    images TEXT,
    features TEXT,
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_type_id) REFERENCES property_types(id) ON DELETE SET NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL,
    FOREIGN KEY (comuna_id) REFERENCES comunas(id) ON DELETE SET NULL,
    FOREIGN KEY (partner_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- TABLA: property_photos (Fotos de Propiedades)
-- ============================================
CREATE TABLE property_photos (
    id SERIAL PRIMARY KEY,
    property_id INT NOT NULL,
    photo_url VARCHAR(500) NOT NULL,
    url VARCHAR(500),
    is_main SMALLINT DEFAULT 0,
    display_order INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- ============================================
-- TABLA: portal_clients (Clientes del Portal Premium)
-- ============================================
CREATE TABLE portal_clients (
    id SERIAL PRIMARY KEY,
    razon_social VARCHAR(255),
    rut VARCHAR(20),
    registered_sections VARCHAR(255) DEFAULT NULL,
    representante_legal VARCHAR(255),
    nombre_completo VARCHAR(255) NOT NULL,
    cedula_identidad VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alias VARCHAR(100),
    consent_accepted SMALLINT DEFAULT 0,
    consent_date TIMESTAMP NULL,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: carousel_images (Imágenes del Carousel)
-- ============================================
CREATE TABLE carousel_images (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    link_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: property_terreno_details (Detalles de Terrenos)
-- ============================================
CREATE TABLE property_terreno_details (
    id SERIAL PRIMARY KEY,
    property_id INT NOT NULL UNIQUE,
    zoning_type VARCHAR(100),
    land_use VARCHAR(100),
    buildability_coefficient DECIMAL(5,2),
    soil_occupation DECIMAL(5,2),
    max_height DECIMAL(10,2),
    has_anteproyecto SMALLINT DEFAULT 0,
    anteproyecto_details TEXT,
    approved_departments INT,
    approved_floors INT,
    approved_parking INT,
    water_connection SMALLINT DEFAULT 0,
    electricity_connection SMALLINT DEFAULT 0,
    gas_connection SMALLINT DEFAULT 0,
    sewage_connection SMALLINT DEFAULT 0,
    topography VARCHAR(100),
    access_type VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- ============================================
-- TABLA: client_favorites (Favoritos de Clientes)
-- ============================================
CREATE TABLE client_favorites (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES portal_clients(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE(client_id, property_id)
);

-- ============================================
-- ÍNDICES ADICIONALES
-- ============================================
CREATE INDEX idx_properties_section ON properties(section_type);
CREATE INDEX idx_properties_is_active ON properties(is_active);
CREATE INDEX idx_properties_featured ON properties(featured);
CREATE INDEX idx_portal_clients_email ON portal_clients(email);
CREATE INDEX idx_portal_clients_rut ON portal_clients(rut);
CREATE INDEX idx_comunas_region ON comunas(region_id);

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Usuarios de prueba
INSERT INTO users (username, password, email, name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@urbanpropiedades.cl', 'Administrador', 'admin'),
('socio1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'socio@urbanpropiedades.cl', 'Socio Demo', 'partner');

-- Tipos de propiedad
INSERT INTO property_types (name, icon) VALUES
('Casa', 'home'),
('Departamento', 'building'),
('Oficina', 'briefcase'),
('Local Comercial', 'store'),
('Bodega', 'warehouse'),
('Terreno', 'map'),
('Galpón', 'warehouse'),
('Estacionamiento', 'car');

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

-- Comunas de la Región Metropolitana
INSERT INTO comunas (region_id, name) VALUES
(7, 'Santiago'),
(7, 'Providencia'),
(7, 'Las Condes'),
(7, 'Vitacura'),
(7, 'Lo Barnechea'),
(7, 'Ñuñoa'),
(7, 'La Reina'),
(7, 'Peñalolén'),
(7, 'Macul'),
(7, 'San Miguel'),
(7, 'La Florida'),
(7, 'Puente Alto'),
(7, 'Maipú'),
(7, 'Pudahuel'),
(7, 'Cerrillos'),
(7, 'Estación Central'),
(7, 'Quilicura'),
(7, 'Huechuraba'),
(7, 'Recoleta'),
(7, 'Independencia');

-- Comunas de Valparaíso
INSERT INTO comunas (region_id, name) VALUES
(6, 'Valparaíso'),
(6, 'Viña del Mar'),
(6, 'Concón'),
(6, 'Quilpué'),
(6, 'Villa Alemana'),
(6, 'Quillota'),
(6, 'San Antonio');

-- Comunas adicionales
INSERT INTO comunas (region_id, name) VALUES
(11, 'Concepción'),
(11, 'Talcahuano'),
(10, 'Chillán'),
(12, 'Temuco'),
(14, 'Puerto Montt'),
(3, 'Antofagasta'),
(5, 'La Serena'),
(5, 'Coquimbo'),
(8, 'Rancagua'),
(9, 'Talca'),
(1, 'Arica'),
(2, 'Iquique'),
(16, 'Punta Arenas'),
(14, 'Puerto Varas'),
(14, 'Osorno');

-- Trigger para actualizar updated_at en PostgreSQL
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_properties_updated_at BEFORE UPDATE ON properties FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_portal_clients_updated_at BEFORE UPDATE ON portal_clients FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_carousel_images_updated_at BEFORE UPDATE ON carousel_images FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_property_terreno_details_updated_at BEFORE UPDATE ON property_terreno_details FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
