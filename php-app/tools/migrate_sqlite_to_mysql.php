<?php
/**
 * Migrate data from SQLite (data/urbanpropiedades.db) to MySQL.
 *
 * Usage (CLI):
 *   php tools/migrate_sqlite_to_mysql.php
 *
 * Before running:
 * - Update `config/config.php` and set DB_TYPE to 'mysql' and fill DB_HOST, DB_NAME, DB_USER, DB_PASS.
 * - Ensure MySQL database exists and credentials are correct.
 * - This script will create tables in MySQL and copy data from the local SQLite DB.
 * - Run from project root.
 */

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";

echo "Starting migration from SQLite to MySQL...\n";

// Ensure DB_TYPE mysql
if (defined('DB_TYPE') && DB_TYPE !== 'mysql') {
    echo "ERROR: DB_TYPE is not set to 'mysql' in config/config.php. Please update it and rerun.\n";
    exit(1);
}

// Connect to SQLite
$sqlitePath = __DIR__ . "/../data/urbanpropiedades.db";
if (!file_exists($sqlitePath)) {
    echo "ERROR: SQLite DB not found at $sqlitePath\n";
    exit(1);
}

try {
    $sqlite = new PDO('sqlite:' . $sqlitePath, null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $sqlite->exec('PRAGMA foreign_keys = OFF');
} catch (PDOException $e) {
    echo "Failed to open SQLite: " . $e->getMessage() . "\n";
    exit(1);
}

// Connect to MySQL via Database class
try {
    $mysql = Database::getInstance()->getConnection();
} catch (Exception $e) {
    echo "Failed to connect to MySQL: " . $e->getMessage() . "\n";
    exit(1);
}

// Simple list of tables to migrate in appropriate order
$tables = [
    'regions', 'comunas', 'users', 'property_types', 'properties', 'property_photos', 'contact_messages', 'portal_clients', 'client_favorites', 'property_details'
];

// We'll create tables using MySQL-friendly definitions (based on setup_database.php)
echo "Creating tables in MySQL...\n";

$createSql = [];
$createSql[] = "CREATE TABLE IF NOT EXISTS regions (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS comunas (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    photo_url VARCHAR(255),
    phone VARCHAR(50),
    role VARCHAR(50) NOT NULL DEFAULT 'partner',
    company_name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS property_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    property_type_id INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    property_type VARCHAR(100) NOT NULL,
    operation_type VARCHAR(100) NOT NULL,
    price DOUBLE NOT NULL,
    currency VARCHAR(10) DEFAULT 'CLP',
    bedrooms INT,
    bathrooms INT,
    built_area DOUBLE,
    total_area DOUBLE,
    parking_spots INT DEFAULT 0,
    address VARCHAR(255),
    comuna_id INT,
    region_id INT,
    latitude DOUBLE,
    longitude DOUBLE,
    images TEXT,
    features TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    partner_id INT,
    section_type VARCHAR(50) DEFAULT 'propiedades',
    property_category VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (comuna_id) REFERENCES comunas(id),
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (partner_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS property_photos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS portal_clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    razon_social VARCHAR(255),
    rut VARCHAR(20) UNIQUE,
    representante_legal VARCHAR(255),
    nombre_completo VARCHAR(255) NOT NULL,
    cedula_identidad VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    alias VARCHAR(100),
    consent_accepted TINYINT(1) DEFAULT 0,
    consent_date DATETIME,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS client_favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES portal_clients(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE KEY unique_client_property (client_id, property_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$createSql[] = "CREATE TABLE IF NOT EXISTS property_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL UNIQUE,
    property_category VARCHAR(100),
    section_type VARCHAR(50),
    details_json TEXT,
    features_json TEXT,
    costs_json TEXT,
    anteproyecto_json TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

foreach ($createSql as $sql) {
    try {
        $mysql->exec($sql);
    } catch (PDOException $e) {
        echo "Warning creating table: " . $e->getMessage() . "\n";
    }
}

echo "Tables created/verified.\n";

// Prepare dump file to write SQL compatible with MySQL
$dumpFile = __DIR__ . '/../sql/dump_sqlite_to_mysql.sql';
@unlink($dumpFile);
$dumpHandle = fopen($dumpFile, 'w');
fwrite($dumpHandle, "-- Dump generated by migrate_sqlite_to_mysql.php\n-- Run in phpMyAdmin or mysql client.\n-- Verify and adjust DB name before import.\n\n");

// If a canonical MySQL schema file exists, include its CREATE statements at the top
$schemaFile = __DIR__ . '/../sql/mysql_schema.sql';
if (file_exists($schemaFile)) {
    $schemaContent = file_get_contents($schemaFile);
    // Write the schema content to the dump (user may edit DB name afterwards)
    fwrite($dumpHandle, "-- BEGIN SCHEMA FROM sql/mysql_schema.sql\n\n");
    fwrite($dumpHandle, $schemaContent . "\n\n");
    fwrite($dumpHandle, "-- END SCHEMA\n\n");
}

// Function to copy rows from sqlite to mysql for a table and write INSERTs to dump
function copyTable(PDO $src, PDO $dest, $table, $dumpHandle = null) {
    echo "Copying table $table... ";
    // get column names
    $colsStmt = $src->query("PRAGMA table_info('$table')");
    $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cols)) {
        echo "(no columns)\n";
        return;
    }
    $colNames = array_map(function($c){ return $c['name']; }, $cols);
    $colList = implode(', ', array_map(function($c){ return "`$c`"; }, $colNames));
    $placeholders = implode(', ', array_map(function($c){ return '?'; }, $colNames));

    $sel = $src->query("SELECT * FROM `$table`");
    $insertSql = "INSERT INTO `$table` ($colList) VALUES ($placeholders)";
    $insertStmt = $dest->prepare($insertSql);

    $count = 0;
    while ($row = $sel->fetch(PDO::FETCH_NUM)) {
        try {
            $insertStmt->execute($row);
            $count++;

            // Write INSERT statement to dump file if requested
            if ($dumpHandle) {
                $values = array_map(function($v) use ($dest) {
                    if ($v === null) return 'NULL';
                    // Escape single quotes
                    $escaped = str_replace("'", "''", $v);
                    return "'" . $escaped . "'";
                }, $row);
                $valuesList = implode(', ', $values);
                fwrite($dumpHandle, "INSERT INTO `$table` ($colList) VALUES ($valuesList);\n");
            }
        } catch (PDOException $e) {
            // ignore duplicate or other errors per row
        }
    }

    // After inserting, set AUTO_INCREMENT to max(id)+1 where applicable
    try {
        // check if table has `id` column
        $maxIdStmt = $dest->query("SELECT MAX(id) as maxid FROM `$table`");
        $maxRes = $maxIdStmt->fetch(PDO::FETCH_ASSOC);
        if ($maxRes && isset($maxRes['maxid']) && $maxRes['maxid'] !== null) {
            $next = intval($maxRes['maxid']) + 1;
            try {
                $dest->exec("ALTER TABLE `$table` AUTO_INCREMENT = $next");
                if ($dumpHandle) {
                    fwrite($dumpHandle, "ALTER TABLE `$table` AUTO_INCREMENT = $next;\n");
                }
            } catch (PDOException $e) {
                // Some tables (without AI) will throw — ignore
            }
        }
    } catch (Exception $e) {
        // ignore
    }

    echo "done ($count rows)\n";
}

// Copy order: regions -> comunas -> users -> property_types -> properties -> property_photos -> contact_messages -> portal_clients -> client_favorites -> property_details
foreach ($tables as $t) {
    copyTable($sqlite, $mysql, $t);
}

echo "Migration finished. Please verify data in MySQL (phpMyAdmin).\n";
echo "Note: this script attempts a best-effort copy. Review constraints and indexes in MySQL afterward.\n";

?>
