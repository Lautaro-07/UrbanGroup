<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Support PostgreSQL (recommended for Replit), MySQL, and SQLite
        $dbType = defined('DB_TYPE') ? DB_TYPE : 'pgsql';

        try {
            if ($dbType === 'pgsql') {
                // PostgreSQL connection using DATABASE_URL from Replit
                $databaseUrl = getenv('DATABASE_URL');
                if ($databaseUrl) {
                    // Parse the DATABASE_URL
                    // Format: postgresql://user:password@host/dbname?options
                    $parts = parse_url($databaseUrl);
                    $host = $parts['host'] ?? 'localhost';
                    $port = $parts['port'] ?? 5432;
                    $dbname = ltrim($parts['path'] ?? '/heliumdb', '/');
                    $user = $parts['user'] ?? 'postgres';
                    $pass = $parts['pass'] ?? '';
                    
                    // Build PDO DSN for PostgreSQL
                    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                    $this->pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } else {
                    // Fallback to manual PostgreSQL configuration
                    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
                    $db   = defined('DB_NAME') ? DB_NAME : 'urbanpropiedades';
                    $user = defined('DB_USER') ? DB_USER : 'postgres';
                    $pass = defined('DB_PASS') ? DB_PASS : '';
                    $port = defined('DB_PORT') ? DB_PORT : '5432';

                    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
                    $this->pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                }
            } elseif ($dbType === 'mysql') {
                // MySQL connection for XAMPP (local) or Hostinger (production)
                // Priority: Environment variables > Config constants > Defaults
                $host = getenv('MYSQL_HOST') ?: (defined('DB_HOST') ? DB_HOST : 'localhost');
                $db   = defined('DB_NAME') ? DB_NAME : 'urbanpropiedades';
                $user = getenv('MYSQL_USER') ?: (defined('DB_USER') ? DB_USER : 'root');
                $pass = getenv('MYSQL_PASSWORD') ?: (defined('DB_PASS') ? DB_PASS : '');
                $port = getenv('MYSQL_PORT') ?: (defined('DB_PORT') ? DB_PORT : '3306');
                $charset = 'utf8mb4';

                $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->pdo = new PDO($dsn, $user, $pass, $options);
            } else {
                // default to sqlite
                $dbPath = __DIR__ . '/../data/urbanpropiedades.db';
                $dbDir = dirname($dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }
                $this->pdo = new PDO(
                    "sqlite:$dbPath",
                    null,
                    null,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                // Enable foreign keys for sqlite
                $this->pdo->exec('PRAGMA foreign_keys = ON');
            }
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
