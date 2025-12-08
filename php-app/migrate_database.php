<?php
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance()->getConnection();

echo "Running database migration...\n";

// Migration: Add missing columns to users table
try {
    $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
    echo "Added phone column to users.\n";
} catch (Exception $e) {
    echo "phone column already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255)");
    echo "Added company_name column to users.\n";
} catch (Exception $e) {
    echo "company_name column already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE users ADD COLUMN photo_url VARCHAR(500)");
    echo "Added photo_url column to users.\n";
} catch (Exception $e) {
    echo "photo_url column already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE properties ADD COLUMN section_type VARCHAR(50) DEFAULT 'propiedades'");
    echo "Added section_type column to properties.\n";
} catch (Exception $e) {
    echo "section_type column already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE properties ADD COLUMN property_category VARCHAR(100)");
    echo "Added property_category column to properties.\n";
} catch (Exception $e) {
    echo "property_category column already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("
    CREATE TABLE IF NOT EXISTS client_favorites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        client_id INTEGER NOT NULL,
        property_id INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (client_id) REFERENCES portal_clients(id) ON DELETE CASCADE,
        FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
        UNIQUE(client_id, property_id)
    )
    ");
    echo "Created client_favorites table.\n";
} catch (Exception $e) {
    echo "client_favorites table error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("
    CREATE TABLE IF NOT EXISTS property_details (
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
    echo "Created property_details table.\n";
} catch (Exception $e) {
    echo "property_details table error: " . $e->getMessage() . "\n";
}

echo "\nMigration complete!\n";
