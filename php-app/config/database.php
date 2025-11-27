<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../data/urbanpropiedades.db';
        
        $dataDir = dirname($this->dbPath);
        if (!file_exists($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        try {
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initializeDatabase();
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

    private function initializeDatabase() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                phone TEXT,
                role TEXT NOT NULL DEFAULT 'partner',
                company_name TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS regions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                code TEXT UNIQUE NOT NULL
            )
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS comunas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                region_id INTEGER NOT NULL,
                FOREIGN KEY (region_id) REFERENCES regions(id)
            )
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS properties (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                property_type TEXT NOT NULL,
                operation_type TEXT NOT NULL,
                price REAL NOT NULL,
                currency TEXT DEFAULT 'CLP',
                bedrooms INTEGER,
                bathrooms INTEGER,
                built_area REAL,
                total_area REAL,
                parking_spots INTEGER DEFAULT 0,
                address TEXT,
                comuna_id INTEGER,
                region_id INTEGER,
                latitude REAL,
                longitude REAL,
                images TEXT,
                features TEXT,
                is_featured INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                partner_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (comuna_id) REFERENCES comunas(id),
                FOREIGN KEY (region_id) REFERENCES regions(id),
                FOREIGN KEY (partner_id) REFERENCES users(id)
            )
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                property_id INTEGER,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                message TEXT NOT NULL,
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (property_id) REFERENCES properties(id)
            )
        ");

        $this->seedData();
    }

    private function seedData() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return;
        }

        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $partnerPassword = password_hash('socio123', PASSWORD_DEFAULT);

        $this->pdo->exec("
            INSERT INTO users (username, password, name, email, phone, role, company_name, is_active) VALUES
            ('admin', '$adminPassword', 'Administrador', 'admin@urbanpropiedades.cl', '+56 9 1234 5678', 'admin', 'UrbanGroup', 1),
            ('socio1', '$partnerPassword', 'Juan Pérez', 'juan@inmobiliaria.cl', '+56 9 8765 4321', 'partner', 'Inmobiliaria JuanPe', 1),
            ('socio2', '$partnerPassword', 'María González', 'maria@propiedades.cl', '+56 9 5555 1234', 'partner', 'Propiedades MG', 1)
        ");

        $regions = [
            ['Arica y Parinacota', 'XV'],
            ['Tarapacá', 'I'],
            ['Antofagasta', 'II'],
            ['Atacama', 'III'],
            ['Coquimbo', 'IV'],
            ['Valparaíso', 'V'],
            ['Metropolitana de Santiago', 'RM'],
            ["Libertador General Bernardo O'Higgins", 'VI'],
            ['Maule', 'VII'],
            ['Ñuble', 'XVI'],
            ['Biobío', 'VIII'],
            ['La Araucanía', 'IX'],
            ['Los Ríos', 'XIV'],
            ['Los Lagos', 'X'],
            ['Aysén del General Carlos Ibáñez del Campo', 'XI'],
            ['Magallanes y de la Antártica Chilena', 'XII']
        ];

        foreach ($regions as $region) {
            $stmt = $this->pdo->prepare("INSERT INTO regions (name, code) VALUES (?, ?)");
            $stmt->execute($region);
        }

        $comunas = [
            ['Santiago', 7], ['Providencia', 7], ['Las Condes', 7], ['Vitacura', 7], ['Lo Barnechea', 7],
            ['Ñuñoa', 7], ['La Reina', 7], ['Peñalolén', 7], ['Macul', 7], ['San Joaquín', 7],
            ['La Florida', 7], ['Puente Alto', 7], ['Maipú', 7], ['Pudahuel', 7], ['Cerrillos', 7],
            ['Viña del Mar', 6], ['Valparaíso', 6], ['Concón', 6], ['Quilpué', 6], ['Villa Alemana', 6],
            ['Concepción', 11], ['Talcahuano', 11], ['Chillán', 10], ['Temuco', 12], ['Puerto Montt', 14],
            ['Antofagasta', 3], ['La Serena', 5], ['Coquimbo', 5], ['Rancagua', 8], ['Talca', 9],
            ['Arica', 1], ['Iquique', 2], ['Punta Arenas', 16], ['Puerto Varas', 14], ['Osorno', 14]
        ];

        foreach ($comunas as $comuna) {
            $stmt = $this->pdo->prepare("INSERT INTO comunas (name, region_id) VALUES (?, ?)");
            $stmt->execute($comuna);
        }

        $images1 = json_encode(['https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800']);
        $images2 = json_encode(['https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800']);
        $images3 = json_encode(['https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800', 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800']);
        $images4 = json_encode(['https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800']);
        $images5 = json_encode(['https://images.unsplash.com/photo-1600573472591-ee6c563aeec4?w=800']);
        $images6 = json_encode(['https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=800']);

        $features1 = json_encode(['Piscina', 'Quincho', 'Jardín', 'Bodega', 'Alarma', 'Portón eléctrico']);
        $features2 = json_encode(['Gimnasio', 'Piscina', 'Sala de eventos', 'Conserjería 24h', 'Estacionamiento visitas']);
        $features3 = json_encode(['Vista al mar', 'Terraza', 'Calefacción central', 'Logia', 'Bodega']);
        $features4 = json_encode(['Bodega', 'Estacionamiento', 'Calefacción']);
        $features5 = json_encode(['Amplio', 'Buena ubicación', 'Acceso fácil']);
        $features6 = json_encode(['Piscina', 'Jardín', 'Quincho', 'Seguridad']);

        $this->pdo->exec("
            INSERT INTO properties (title, description, property_type, operation_type, price, currency, bedrooms, bathrooms, built_area, total_area, parking_spots, address, comuna_id, region_id, images, features, is_featured, is_active, partner_id) VALUES
            ('Espectacular Casa en Lo Barnechea', 'Hermosa casa de lujo con amplios espacios, terminaciones de primera calidad. Ideal para familias que buscan comodidad y estilo.', 'Casa', 'Venta', 850000000, 'CLP', 5, 4, 350, 800, 3, 'Av. La Dehesa 1234', 5, 7, '$images1', '$features1', 1, 1, 2),
            ('Moderno Departamento en Providencia', 'Departamento completamente remodelado con vista panorámica a la ciudad. Cerca de metro y comercio.', 'Departamento', 'Arriendo', 1200000, 'CLP', 2, 2, 85, 85, 1, 'Av. Providencia 2345', 2, 7, '$images2', '$features2', 1, 1, 2),
            ('Penthouse con Vista al Mar en Viña del Mar', 'Exclusivo penthouse con terraza de 50m2 y vista panorámica al océano Pacífico. Amenities de lujo.', 'Departamento', 'Venta', 420000000, 'CLP', 3, 3, 180, 230, 2, 'Av. Perú 567', 16, 6, '$images3', '$features3', 1, 1, 3),
            ('Oficina Comercial en Las Condes', 'Oficina corporativa en edificio clase A, piso alto con vista despejada. Incluye estacionamientos.', 'Oficina', 'Arriendo', 2500000, 'CLP', 0, 2, 120, 120, 2, 'Av. Apoquindo 4500', 3, 7, '$images4', '$features4', 0, 1, 2),
            ('Terreno en La Serena', 'Excelente terreno para desarrollo inmobiliario con factibilidad de agua y luz.', 'Terreno', 'Venta', 180000000, 'CLP', 0, 0, 0, 5000, 0, 'Camino a La Herradura', 27, 5, '$images5', '$features5', 0, 1, 3),
            ('Casa Familiar en Ñuñoa', 'Acogedora casa en barrio residencial, ideal para familia. Cercana a colegios y comercio.', 'Casa', 'Venta', 320000000, 'CLP', 4, 3, 180, 300, 2, 'Calle Los Jardines 789', 6, 7, '$images6', '$features6', 1, 1, 2)
        ");
    }
}
