<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Support both SQLite (default) and MySQL (recommended for production)
        $dbType = defined('DB_TYPE') ? DB_TYPE : 'sqlite';

        try {
            if ($dbType === 'mysql') {
                $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
                $db   = defined('DB_NAME') ? DB_NAME : 'urbanpropiedades';
                $user = defined('DB_USER') ? DB_USER : 'root';
                $pass = defined('DB_PASS') ? DB_PASS : '';
                $charset = 'utf8mb4';

                $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
                $this->pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
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
