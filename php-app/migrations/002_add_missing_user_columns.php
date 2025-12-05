<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

echo "Running migration: Add missing columns to users table...\n";

try {
    if ($driver === 'mysql') {
        // Check if columns exist before adding them (MySQL)
        $checkPhone = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='phone' AND TABLE_SCHEMA=DATABASE()")->fetch();
        if (!$checkPhone) {
            $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER name");
            echo "✓ Column 'phone' added to users table.\n";
        } else {
            echo "✓ Column 'phone' already exists.\n";
        }

        $checkCompanyName = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='company_name' AND TABLE_SCHEMA=DATABASE()")->fetch();
        if (!$checkCompanyName) {
            $db->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255) AFTER phone");
            echo "✓ Column 'company_name' added to users table.\n";
        } else {
            echo "✓ Column 'company_name' already exists.\n";
        }

        $checkPhotoUrl = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='photo_url' AND TABLE_SCHEMA=DATABASE()")->fetch();
        if (!$checkPhotoUrl) {
            $db->exec("ALTER TABLE users ADD COLUMN photo_url VARCHAR(500) AFTER company_name");
            echo "✓ Column 'photo_url' added to users table.\n";
        } else {
            echo "✓ Column 'photo_url' already exists.\n";
        }
    } else {
        // SQLite approach
        // SQLite doesn't have a simple ALTER TABLE ADD COLUMN check, so we'll try and handle exceptions
        try {
            $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
            echo "✓ Column 'phone' added to users table.\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate column') !== false) {
                echo "✓ Column 'phone' already exists.\n";
            } else {
                throw $e;
            }
        }

        try {
            $db->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255)");
            echo "✓ Column 'company_name' added to users table.\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate column') !== false) {
                echo "✓ Column 'company_name' already exists.\n";
            } else {
                throw $e;
            }
        }

        try {
            $db->exec("ALTER TABLE users ADD COLUMN photo_url VARCHAR(500)");
            echo "✓ Column 'photo_url' added to users table.\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate column') !== false) {
                echo "✓ Column 'photo_url' already exists.\n";
            } else {
                throw $e;
            }
        }
    }

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}
