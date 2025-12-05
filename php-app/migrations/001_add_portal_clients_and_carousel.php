<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

echo "Running migration: Add portal_clients, carousel_images, and extended property fields...\n";

$db->exec("
CREATE TABLE IF NOT EXISTS portal_clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    razon_social VARCHAR(255) NOT NULL,
    rut VARCHAR(20) NOT NULL UNIQUE,
    representante_legal VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    cedula_identidad VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alias VARCHAR(100) NOT NULL,
    consent_accepted INTEGER DEFAULT 0,
    consent_date DATETIME,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");
echo "Table portal_clients created.\n";

$db->exec("
CREATE TABLE IF NOT EXISTS carousel_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    file_path VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    alt_text VARCHAR(255),
    display_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");
echo "Table carousel_images created.\n";

$db->exec("
CREATE TABLE IF NOT EXISTS property_terreno_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER NOT NULL UNIQUE,
    roles INTEGER,
    fecha_permiso_edificacion DATE,
    zona_prc_edificacion VARCHAR(100),
    usos_suelo TEXT,
    sistema_agrupamiento VARCHAR(100),
    altura_maxima VARCHAR(100),
    rasante VARCHAR(100),
    coef_constructibilidad_max REAL,
    coef_ocupacion_suelo_max REAL,
    coef_area_libre_min REAL,
    antejardin_min REAL,
    distanciamientos VARCHAR(255),
    densidad_bruta_max VARCHAR(100),
    densidad_neta_max VARCHAR(100),
    superficie_predial_min REAL,
    
    -- Superficies Aprobadas Anteproyecto
    superficie_util REAL,
    superficie_comun REAL,
    superficie_total REAL,
    edificada_sobre_terreno REAL,
    edificada_bajo_terreno REAL,
    edificada_total REAL,
    num_viviendas INTEGER,
    num_estacionamientos INTEGER,
    num_est_bicicletas INTEGER,
    num_locales_comerciales INTEGER,
    num_bodegas INTEGER,
    
    -- Datos económicos
    superficie_bruta REAL,
    superficie_bruta_ha REAL,
    expropiacion REAL,
    expropiacion_ha REAL,
    superficie_util_total REAL,
    superficie_util_ha REAL,
    precio_uf_m2 REAL,
    precio_total_uf REAL,
    comision_porcentaje REAL DEFAULT 2.0,
    
    has_anteproyecto INTEGER DEFAULT 0,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)
");
echo "Table property_terreno_details created.\n";

$columns_to_add = [
    "section_type VARCHAR(50) DEFAULT 'general'",
    "is_terreno INTEGER DEFAULT 0",
    "is_activo_inmobiliario INTEGER DEFAULT 0",
    "is_usa_property INTEGER DEFAULT 0"
];

foreach ($columns_to_add as $column) {
    try {
        $db->exec("ALTER TABLE properties ADD COLUMN $column");
        echo "Added column: $column\n";
    } catch (Exception $e) {
        echo "Column may already exist: $column\n";
    }
}

echo "\nMigration completed successfully!\n";
