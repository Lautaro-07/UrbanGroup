<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Agregar columna photo_url
    $db->exec("ALTER TABLE users ADD COLUMN photo_url VARCHAR(255) NULL DEFAULT NULL");
    
    echo "✅ Columna photo_url agregada correctamente!";
} catch (Exception $e) {
    echo "⚠️ " . $e->getMessage();
}
?>