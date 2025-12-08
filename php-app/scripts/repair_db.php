<?php
// CLI script to diagnose and repair common schema/auth issues
// Usage: php scripts/repair_db.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function out($s) { echo $s . PHP_EOL; }

try {
    $db = Database::getInstance()->getConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    out("Connected using driver: $driver");

    if ($driver === 'sqlite') {
        $dbFile = realpath(__DIR__ . '/../data/urbanpropiedades.db');
        out("SQLite file: " . ($dbFile ?: '(not found)'));
    } else {
        out("MySQL DB: " . (defined('DB_NAME') ? DB_NAME : '(unknown)'));
    }

    // Helper to check column existence
    $columnExists = function($table, $column) use ($db, $driver) {
        try {
            if ($driver === 'sqlite') {
                $stmt = $db->prepare("PRAGMA table_info('$table')");
                $stmt->execute();
                $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($cols as $c) if (strcasecmp($c['name'], $column) === 0) return true;
                return false;
            } else {
                $dbName = defined('DB_NAME') ? DB_NAME : null;
                $stmt = $db->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ? LIMIT 1");
                $stmt->execute([$dbName, $table, $column]);
                return (bool)$stmt->fetchColumn();
            }
        } catch (Exception $e) {
            return false;
        }
    };

    // Ensure tables exist
    $requiredTables = ['users','properties','portal_clients','carousel_images'];
    foreach ($requiredTables as $t) {
        try {
            if ($driver === 'sqlite') {
                $res = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$t'")->fetchColumn();
                out("Table $t: " . ($res ? 'exists' : 'missing'));
            } else {
                $res = $db->query("SHOW TABLES LIKE '$t'")->fetchColumn();
                out("Table $t: " . ($res ? 'exists' : 'missing'));
            }
        } catch (Exception $e) {
            out("Table check $t error: " . $e->getMessage());
        }
    }

    // Schema fixes (non-destructive if possible): add columns if missing
    $alterStatements = [];

    if (!$columnExists('users','is_active')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE users ADD COLUMN is_active INTEGER DEFAULT 1";
        else $alterStatements[] = "ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1";
    }

    if (!$columnExists('properties','is_active')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE properties ADD COLUMN is_active INTEGER DEFAULT 1";
        else $alterStatements[] = "ALTER TABLE properties ADD COLUMN is_active TINYINT(1) DEFAULT 1";
    }

    if (!$columnExists('properties','is_featured')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE properties ADD COLUMN is_featured INTEGER DEFAULT 0";
        else $alterStatements[] = "ALTER TABLE properties ADD COLUMN is_featured TINYINT(1) DEFAULT 0";
    }

    if (!$columnExists('portal_clients','password')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE portal_clients ADD COLUMN password VARCHAR(255)";
        else $alterStatements[] = "ALTER TABLE portal_clients ADD COLUMN password VARCHAR(255)";
    }

    if (!$columnExists('portal_clients','status')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE portal_clients ADD COLUMN status VARCHAR(20) DEFAULT 'active'";
        else $alterStatements[] = "ALTER TABLE portal_clients ADD COLUMN status VARCHAR(20) DEFAULT 'active'";
    }

    if (!$columnExists('carousel_images','is_active')) {
        if ($driver === 'sqlite') $alterStatements[] = "ALTER TABLE carousel_images ADD COLUMN is_active INTEGER DEFAULT 1";
        else $alterStatements[] = "ALTER TABLE carousel_images ADD COLUMN is_active TINYINT(1) DEFAULT 1";
    }

    foreach ($alterStatements as $sql) {
        try {
            out("Applying: $sql");
            $db->exec($sql);
        } catch (Exception $e) {
            out("Failed: " . $e->getMessage());
        }
    }

    // Detect portal_clients password format and hash plaintexts
    try {
        $rows = $db->query("SELECT id, password FROM portal_clients")->fetchAll(PDO::FETCH_ASSOC);
        $rehash = 0;
        foreach ($rows as $r) {
            $pw = $r['password'] ?? '';
            if ($pw === null || $pw === '') continue;
            // If not bcrypt-like ($2y$ or $2a$), assume plaintext and hash it
            if (!preg_match('/^\$2[ayb]\$[0-9]{2}\$/', $pw)) {
                $new = password_hash($pw, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE portal_clients SET password = ? WHERE id = ?");
                $stmt->execute([$new, $r['id']]);
                $rehash++;
                out("Re-hashed portal_clients.id={$r['id']}");
            }
        }
        out("Portal clients re-hashed: $rehash");
    } catch (Exception $e) {
        out("portal_clients check failed: " . $e->getMessage());
    }

    // Ensure users passwords look hashed (do NOT change them automatically)
    try {
        $users = $db->query("SELECT id, password FROM users LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
        $countPlain = 0;
        foreach ($users as $u) {
            $pw = $u['password'] ?? '';
            if ($pw === null || $pw === '') continue;
            if (!preg_match('/^\$2[ayb]\$[0-9]{2}\$/', $pw)) {
                $countPlain++;
                out("User id={$u['id']} password not bcrypt-hashed (manual review recommended)");
            }
        }
        out("Users with non-bcrypt passwords (sample check): $countPlain");
    } catch (Exception $e) { out("users check failed: " . $e->getMessage()); }

    out("Repair script finished. Please try to login now.");

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

?>
