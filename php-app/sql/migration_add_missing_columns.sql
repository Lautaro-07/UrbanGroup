-- ============================================
-- Migration: Add Missing Columns
-- Run this in phpMyAdmin if you already have the database
-- Compatible with MySQL 5.7+
-- ============================================

USE urbanpropiedades;

-- ============================================
-- Helper: Add column if not exists (MySQL 5.7 compatible)
-- Uses stored procedure to check before adding
-- ============================================

DELIMITER //

DROP PROCEDURE IF EXISTS add_column_if_not_exists//

CREATE PROCEDURE add_column_if_not_exists(
    IN p_table VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_definition VARCHAR(500)
)
BEGIN
    SET @col_exists = 0;
    SELECT COUNT(*) INTO @col_exists FROM information_schema.columns 
    WHERE table_schema = DATABASE() AND table_name = p_table AND column_name = p_column;
    
    IF @col_exists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', p_table, ' ADD COLUMN ', p_column, ' ', p_definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

DELIMITER ;

-- ============================================
-- Add missing columns to properties table
-- ============================================

CALL add_column_if_not_exists('properties', 'property_type', "VARCHAR(100) COMMENT 'Tipo de propiedad (texto)'");
CALL add_column_if_not_exists('properties', 'property_category', "VARCHAR(100) DEFAULT '' COMMENT 'Categoría adicional'");
CALL add_column_if_not_exists('properties', 'total_area', "DECIMAL(10,2) COMMENT 'Área total (alternativo)'");
CALL add_column_if_not_exists('properties', 'parking_spots', "INT DEFAULT 0 COMMENT 'Estacionamientos'");
CALL add_column_if_not_exists('properties', 'images', "TEXT COMMENT 'JSON array de URLs de imágenes'");
CALL add_column_if_not_exists('properties', 'features', "TEXT COMMENT 'JSON array de características'");
CALL add_column_if_not_exists('properties', 'is_featured', "TINYINT(1) DEFAULT 0 COMMENT 'Propiedad destacada'");
CALL add_column_if_not_exists('properties', 'is_project', "TINYINT(1) DEFAULT 0 COMMENT 'Es un proyecto (USA)'");
CALL add_column_if_not_exists('properties', 'partner_id', "INT COMMENT 'FK a users (socio propietario)'");

-- ============================================
-- Add is_usa column to property_types table
-- ============================================
CALL add_column_if_not_exists('property_types', 'is_usa', "TINYINT(1) DEFAULT 0 COMMENT 'Es tipo para propiedades USA'");

-- ============================================
-- Create property_usa_details table if not exists
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
-- Insert USA property types (if not exist)
-- ============================================
INSERT IGNORE INTO property_types (name, icon, is_usa) VALUES
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

-- ============================================
-- Add indexes (ignore if exist)
-- ============================================
-- Note: MySQL will throw error if index exists, run individually if needed

-- ============================================
-- Cleanup helper procedure
-- ============================================
DROP PROCEDURE IF EXISTS add_column_if_not_exists;

-- ============================================
-- Done!
-- ============================================
SELECT 'Migration completed successfully!' AS status;
