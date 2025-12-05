<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance()->getConnection();

echo "Setting up SQLite database...\n";
// Prevent accidental web execution — only allow CLI runs
if (php_sapi_name() !== 'cli') {
    echo "This setup script can only be run from the command line.\n";
    exit;
}

// Safety: prevent accidental web execution. Require CLI and explicit env flag to run.
if (php_sapi_name() !== 'cli') {
    if (getenv('ALLOW_RESET') !== '1') {
        echo "ERROR: setup_database.php can only be run from CLI when ALLOW_RESET=1 is set.\n";
        exit(1);
    }
}

// Drop tables if they exist (in correct order due to foreign keys)
$db->exec("DROP TABLE IF EXISTS property_details");
$db->exec("DROP TABLE IF EXISTS client_favorites");
$db->exec("DROP TABLE IF EXISTS portal_clients");
$db->exec("DROP TABLE IF EXISTS property_photos");
$db->exec("DROP TABLE IF EXISTS contact_messages");
$db->exec("DROP TABLE IF EXISTS properties");
$db->exec("DROP TABLE IF EXISTS comunas");
$db->exec("DROP TABLE IF EXISTS regions");
$db->exec("DROP TABLE IF EXISTS property_types");
$db->exec("DROP TABLE IF EXISTS users");

// Create regions table
$db->exec("
CREATE TABLE regions (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE
)
");

// Create comunas table
$db->exec("
CREATE TABLE comunas (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    region_id INTEGER NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id)
)
");

// Create users table
$db->exec("
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    photo_url VARCHAR(255),
    phone VARCHAR(50),
    role VARCHAR(50) NOT NULL DEFAULT 'partner',
    company_name VARCHAR(255),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

// Create property_types table
$db->exec("
CREATE TABLE property_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    property_type_id INTEGER
)
");

// Create properties table
$db->exec("
CREATE TABLE properties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    property_type VARCHAR(100) NOT NULL,
    operation_type VARCHAR(100) NOT NULL,
    price REAL NOT NULL,
    currency VARCHAR(10) DEFAULT 'CLP',
    bedrooms INTEGER,
    bathrooms INTEGER,
    built_area REAL,
    total_area REAL,
    parking_spots INTEGER DEFAULT 0,
    address VARCHAR(255),
    comuna_id INTEGER,
    region_id INTEGER,
    latitude REAL,
    longitude REAL,
    images TEXT,
    features TEXT,
    is_featured INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    partner_id INTEGER,
    section_type VARCHAR(50) DEFAULT 'propiedades',
    property_category VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comuna_id) REFERENCES comunas(id),
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (partner_id) REFERENCES users(id)
)
");

// Create property_photos table
$db->exec("
CREATE TABLE property_photos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    display_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)
");

// Create contact_messages table
$db->exec("
CREATE TABLE contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT NOT NULL,
    is_read INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id)
)
");

// Create portal_clients table for client authentication
$db->exec("
CREATE TABLE portal_clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    razon_social VARCHAR(255),
    rut VARCHAR(20) UNIQUE,
    registered_sections VARCHAR(255),
    representante_legal VARCHAR(255),
    nombre_completo VARCHAR(255) NOT NULL,
    cedula_identidad VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alias VARCHAR(100),
    consent_accepted INTEGER DEFAULT 0,
    consent_date DATETIME,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

// Create client_favorites table
$db->exec("
CREATE TABLE client_favorites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    property_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES portal_clients(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE(client_id, property_id)
)
");

// Create property_details table for category-specific data
$db->exec("
CREATE TABLE property_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER NOT NULL UNIQUE,
    property_category VARCHAR(100),
    section_type VARCHAR(50),
    details_json TEXT,
    features_json TEXT,
    costs_json TEXT,
    anteproyecto_json TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)
");

echo "Tables created successfully.\n";
echo "Inserting data...\n";

// Insert regions
$regions = [
    [1, 'Arica y Parinacota', 'XV'],
    [2, 'Tarapacá', 'I'],
    [3, 'Antofagasta', 'II'],
    [4, 'Atacama', 'III'],
    [5, 'Coquimbo', 'IV'],
    [6, 'Valparaíso', 'V'],
    [7, 'Metropolitana de Santiago', 'RM'],
    [8, 'Libertador General Bernardo O\'Higgins', 'VI'],
    [9, 'Maule', 'VII'],
    [10, 'Ñuble', 'XVI'],
    [11, 'Biobío', 'VIII'],
    [12, 'La Araucanía', 'IX'],
    [13, 'Los Ríos', 'XIV'],
    [14, 'Los Lagos', 'X'],
    [15, 'Aysén del General Carlos Ibáñez del Campo', 'XI'],
    [16, 'Magallanes y de la Antártica Chilena', 'XII']
];

$stmt = $db->prepare("INSERT INTO regions (id, name, code) VALUES (?, ?, ?)");
foreach ($regions as $region) {
    $stmt->execute($region);
}
echo "Regions inserted.\n";

// Insert comunas
$comunas = [
    [1, 'Santiago', 7], [2, 'Providencia', 7], [3, 'Las Condes', 7], [4, 'Vitacura', 7],
    [5, 'Lo Barnechea', 7], [6, 'Ñuñoa', 7], [7, 'La Reina', 7], [8, 'Peñalolén', 7],
    [9, 'Macul', 7], [10, 'San Joaquín', 7], [11, 'La Florida', 7], [12, 'Puente Alto', 7],
    [13, 'Maipú', 7], [14, 'Pudahuel', 7], [15, 'Cerrillos', 7], [16, 'Viña del Mar', 6],
    [17, 'Valparaíso', 6], [18, 'Concón', 6], [19, 'Quilpué', 6], [20, 'Villa Alemana', 6],
    [21, 'Concepción', 11], [22, 'Talcahuano', 11], [23, 'Chillán', 10], [24, 'Temuco', 12],
    [25, 'Puerto Montt', 14], [26, 'Antofagasta', 3], [27, 'La Serena', 5], [28, 'Coquimbo', 5],
    [29, 'Rancagua', 8], [30, 'Talca', 9], [31, 'Arica', 1], [32, 'Iquique', 2],
    [33, 'Punta Arenas', 16], [34, 'Puerto Varas', 14], [35, 'Osorno', 14]
];

$stmt = $db->prepare("INSERT INTO comunas (id, name, region_id) VALUES (?, ?, ?)");
foreach ($comunas as $comuna) {
    $stmt->execute($comuna);
}
echo "Comunas inserted.\n";

// Insert property types
$propertyTypes = [
    'Bodegas con Renta', 'Bodegas en Arriendo', 'Casa Comercial en A (Arriendo)', 
    'Casa Comercial en V (Venta)', 'Casa en Arriendo', 'Casa en Venta', 'Centro Vacacional',
    'Complejo Turístico', 'Depto en Arriendo', 'Depto en Renta', 'Depto en Venta',
    'Deptos con Renta', 'Deptos Inversionistas', 'Deptos Nuevos', 'Derechos de Llave',
    'Edificio de Deptos', 'Edificio de Oficinas', 'Educacional', 'Estacionamientos',
    'Fundo', 'Loft', 'Loteo', 'Mall', 'Motel', 'Oficinas en Arriendo', 'Oficinas en Venta',
    'Outlet Mall', 'P Minera (Propiedad Minera)', 'Packing', 'Parcela', 'Parcelas',
    'Parking', 'Propiedad Educacional', 'Propiedad Industrial', 'Restaurant', 'Sitio',
    'Strip Center', 'Supermercado', 'T en Arriendo (Terreno en Arriendo)', 'T Indus (Terreno Industrial)',
    'T para 1 Casa (Terreno para 1 Casa)', 'Viña'
];

$stmt = $db->prepare("INSERT INTO property_types (name) VALUES (?)");
foreach ($propertyTypes as $type) {
    $stmt->execute([$type]);
}
echo "Property types inserted.\n";

// Insert users
$stmt = $db->prepare("INSERT INTO users (id, username, password, name, email, photo_url, phone, role, company_name, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([1, 'admin', password_hash('admin123', PASSWORD_BCRYPT), 'Administrador', 'admin@urbanpropiedades.cl', NULL, '+56 9 1234 5678', 'admin', 'UrbanGroup', 1]);
$stmt->execute([4, 'olgiatielizondo@gmail.com', '$2y$10$mrsSobk5qOBalqUi.vfiMO3uFvD3lXJAAUm4bbq./.9nlW6uUdS0C', 'Lautaro Elizondo', 'shopii.versee@gmail.com', '/uploads/partners/1764450548_692b60f4bcfc8.jpg', '02914125043', 'admin', '', 1]);
$stmt->execute([5, 'Juana', '$2y$10$u9kJrwxFlDBdChfTHgtpdOrqtTGen8P6QeDLOApfG2urRx1ZeHrua', 'Juana Almada', 'juana@gmail.com', '../uploads/partners/1764452376_692b6818ed4ae.jpg', '', 'partner', '', 1]);
echo "Users inserted.\n";

// Insert properties
$stmt = $db->prepare("INSERT INTO properties (id, title, description, property_type, operation_type, price, currency, bedrooms, bathrooms, built_area, total_area, parking_spots, address, comuna_id, region_id, latitude, longitude, images, features, is_featured, is_active, partner_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$properties = [
    [4, 'Casa Hermosa Valparaiso', 'Hermosa Casa en valparaiso', 'Casa en Arriendo', 'Arriendo', 350000, 'CLP', 2, 1, 101, 140, 1, 'cramer 3770', 17, 6, NULL, NULL, '[]', '[]', 1, 1, 5, '2025-11-29 18:41:43', '2025-12-03 15:25:16'],
    [18, 'Departamento moderno en Ñuñoa', 'Hermoso departamento cercano al metro Chile España, excelente conectividad y vista despejada.', 'Departamento', 'Venta', 125000000, 'CLP', 2, 2, 68, 72, 1, 'Av. Irarrázaval 2345', 6, 7, -33.4567, -70.5987, '[]', '["Balcón","Piso flotante","Logia","Conserjería 24/7"]', 0, 1, 4, '2025-11-29 18:49:06', '2025-12-03 14:53:29'],
    [19, 'Casa Remodelada en La Reina', 'Casa completamente remodelada con amplios espacios, patio grande y excelente conectividad.', 'Casa en Venta', 'Venta', 289000000, 'CLP', 4, 3, 145, 260, 2, 'Av. Príncipe de Gales 8900', 7, 7, -33.4412, -70.5334, '[]', '["Patio grande","Bodega","Cocina americana","Living amplio"]', 1, 1, 4, '2025-11-29 18:49:06', '2025-12-03 15:25:56'],
    [20, 'Departamento frente al mar en Viña del Mar', 'Departamento con vista directa al océano, excelente plusvalía y ubicación privilegiada.', 'Departamento', 'Venta', 165000000, 'CLP', 2, 2, 78, 82, 1, 'Av. San Martín 1230', 16, 6, -33.0165, -71.5518, '[]', '["Vista al mar","Conserjería","Piscina","Gimnasio"]', 1, 1, 4, '2025-11-29 18:49:06', '2025-12-03 14:53:36'],
    [21, 'Casa en Cerro Alegre, Valparaíso', 'Casa en Cerro Alegre con vista panorámica, estilo patrimonial y excelente iluminación.', 'Casa', 'Venta', 215000000, 'CLP', 3, 2, 130, 150, 0, 'Cerro Alegre 900', 17, 6, -33.0405, -71.6273, '[]', '["Vista panorámica","Remodelada","Patio interior"]', 1, 1, 4, '2025-11-29 18:49:06', '2025-12-01 14:05:05'],
    [22, 'Departamento en Las Condes, sector El Golf', 'Departamento de lujo en El Golf, piso alto, orientación nororiente, edificio premium.', 'Departamento', 'Venta', 320000000, 'CLP', 2, 2, 80, 85, 2, 'Av. Apoquindo 4500', 3, 7, -33.4142, -70.5901, '[]', '["Gimnasio","Seguridad 24/7","Salón de eventos","Piscina"]', 0, 1, 4, '2025-11-29 18:49:06', '2025-12-03 14:54:06'],
    [23, 'Terreno en Venta con Vista Parcial al Lago Llanquihue – Puerto Varas', 'Terreno de 240 m² ubicado en el tranquilo sector Altos de Puerto Varas, una zona residencial en constante crecimiento dentro de la comuna de Puerto Varas, Región de Los Lagos.

El lote ofrece una excelente orientación y acceso rápido a servicios, comercio y vías principales. Su entorno natural, acompañado de áreas verdes y la cercanía al Lago Llanquihue, lo convierte en una gran oportunidad para proyectos habitacionales o inversión futura.

Ideal para quienes buscan construir en una zona segura, con buen nivel de urbanización y a pocos minutos del centro de Puerto Varas.', 'T para 1 Casa (Terreno para 1 Casa)', 'Venta', 34999999, 'CLP', 0, 0, 0, 240, 0, 'Vista Hermosa - Parcelas 16-14', 34, 14, NULL, NULL, '[]', '[]', 0, 1, 5, '2025-12-01 10:32:22', '2025-12-03 15:25:16']
];

foreach ($properties as $property) {
    $stmt->execute($property);
}
echo "Properties inserted.\n";

// Insert property photos
$stmt = $db->prepare("INSERT INTO property_photos (id, property_id, photo_url, display_order, created_at) VALUES (?, ?, ?, ?, ?)");
$photos = [
    [5, 21, '../uploads/properties/1764457369_692b7b996124a.jpg', 0, '2025-11-29 20:02:49'],
    [6, 20, '../uploads/properties/1764457384_692b7ba8c4991.jpg', 0, '2025-11-29 20:03:04'],
    [7, 4, '../uploads/properties/1764593124_692d8de48a99f.jpg', 0, '2025-12-01 09:45:24'],
    [8, 23, '../uploads/properties/1764595942_692d98e6a1053.jpg', 1, '2025-12-01 10:32:22'],
    [9, 23, '../uploads/properties/1764595942_692d98e6a33b8.jpg', 0, '2025-12-01 10:32:22'],
    [10, 19, '../uploads/properties/1764786356_693080b4089a4.jpg', 0, '2025-12-03 15:25:56']
];

foreach ($photos as $photo) {
    $stmt->execute($photo);
}
echo "Property photos inserted.\n";

echo "\nDatabase setup complete!\n";
echo "Default credentials:\n";
echo "  Admin: username=admin, password=admin123\n";
echo "  Partner: username=Juana, password=socio123\n";
