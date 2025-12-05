<?php
require_once __DIR__ . '/../config/database.php';

class PropertyModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        // Ensure properties table has required columns (prevent fatal errors on older schemas)
        $this->ensurePropertiesColumns();
    }
    private function ensurePropertiesColumns() {
        try {
            $required = [
                // column_name => ['sqlite' => SQL, 'mysql' => SQL]
                'is_active' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN is_active INTEGER DEFAULT 1",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN is_active TINYINT(1) DEFAULT 1"
                ],
                'is_featured' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN is_featured INTEGER DEFAULT 0",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN is_featured TINYINT(1) DEFAULT 0"
                ],
                'section_type' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN section_type VARCHAR(50) DEFAULT 'propiedades'",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN section_type VARCHAR(50) DEFAULT 'propiedades'"
                ],
                'property_category' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN property_category VARCHAR(100) DEFAULT ''",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN property_category VARCHAR(100) DEFAULT ''"
                ],
                'created_at' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN created_at DATETIME",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP"
                ],
                'updated_at' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN updated_at DATETIME",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
                ]
            ];

            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

            if ($driver === 'sqlite') {
                $stmt = $this->db->query("PRAGMA table_info('properties')");
                $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $existing = array_map(function($c){ return strtolower($c['name']); }, $cols);

                foreach ($required as $col => $sqls) {
                    if (!in_array(strtolower($col), $existing)) {
                        // attempt to add column; SQLite allows simple ADD COLUMN
                        try { $this->db->exec($sqls['sqlite']); } catch (Exception $e) { /* ignore */ }
                    }
                }
            } else {
                // mysql
                $dbName = defined('DB_NAME') ? DB_NAME : null;
                $existing = [];
                if ($dbName) {
                    $stmt = $this->db->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = ? AND table_name = 'properties'");
                    $stmt->execute([$dbName]);
                    $existing = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
                }

                foreach ($required as $col => $sqls) {
                    if (!in_array(strtolower($col), $existing)) {
                        try { $this->db->exec($sqls['mysql']); } catch (Exception $e) { /* ignore */ }
                    }
                }
            }
        } catch (Exception $e) {
            // non-fatal — don't block page load
        }
    }

    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                WHERE p.is_active = 1";

        $params = [];

        // FILTRO TIPO DE OPERACIÓN (Venta/Arriendo)
        if (!empty($filters['operation_type'])) {
            $sql .= " AND p.operation_type = ?";
            $params[] = $filters['operation_type'];
        }

        // FILTRO POR TIPO DE PROPIEDAD (campo VARCHAR property_type)
        if (!empty($filters['property_type'])) {
            $sql .= " AND p.property_type = ?";
            $params[] = $filters['property_type'];
        }

        // FILTRO POR REGIÓN
        if (!empty($filters['region_id'])) {
            $sql .= " AND p.region_id = ?";
            $params[] = $filters['region_id'];
        }

        // FILTRO POR COMUNA
        if (!empty($filters['comuna_id'])) {
            $sql .= " AND p.comuna_id = ?";
            $params[] = $filters['comuna_id'];
        }

        // FILTRO DORMITORIOS
        if (!empty($filters['bedrooms'])) {
            if ($filters['bedrooms'] === '5+') {
                $sql .= " AND p.bedrooms >= 5";
            } else {
                $sql .= " AND p.bedrooms = ?";
                $params[] = (int)$filters['bedrooms'];
            }
        }

        // PRECIOS
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        // BÚSQUEDA GENERAL
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.address LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // FILTRO POR SECCIÓN (propiedades, terrenos, activos, usa)
        if (!empty($filters['section_type'])) {
            $sql .= " AND p.section_type = ?";
            $params[] = $filters['section_type'];
        }

        // ORDEN
        $sql .= " ORDER BY p.created_at DESC";

        // PAGINACIÓN — inyectamos valores enteros directamente para evitar problemas con drivers
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getFeatured($limit = 6) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                WHERE p.is_active = 1 AND p.is_featured = 1 
                ORDER BY p.created_at DESC 
                LIMIT " . (int)$limit;

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name,
                       u.name as partner_name,
                       u.company_name,
                       u.phone as partner_phone,
                       u.email as partner_email,
                       u.photo_url as partner_photo
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                LEFT JOIN users u ON p.partner_id = u.id
                WHERE p.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByPartnerId($partnerId) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                WHERE p.partner_id = ? 
                ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$partnerId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO properties (
                    title, description, property_type, operation_type, price, currency, 
                    bedrooms, bathrooms, built_area, total_area, parking_spots, 
                    address, comuna_id, region_id, images, features, 
                    is_featured, is_active, partner_id, section_type, property_category
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['property_type'],
            $data['operation_type'],
            $data['price'],
            $data['currency'] ?? 'CLP',
            $data['bedrooms'] ?? 0,
            $data['bathrooms'] ?? 0,
            $data['built_area'] ?? 0,
            $data['total_area'] ?? 0,
            $data['parking_spots'] ?? 0,
            $data['address'] ?? '',
            $data['comuna_id'] ?? null,
            $data['region_id'] ?? null,
            $data['images'] ?? '[]',
            $data['features'] ?? '[]',
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1,
            $data['partner_id'],
            $data['section_type'] ?? 'propiedades',
            $data['property_category'] ?? ''
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'title', 'description', 'property_type', 'operation_type', 'price', 'currency',
            'bedrooms', 'bathrooms', 'built_area', 'total_area', 'parking_spots',
            'address', 'comuna_id', 'region_id', 'images', 'features',
            'is_featured', 'is_active', 'section_type', 'property_category'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $sql = "UPDATE properties SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM properties WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleFeatured($id) {
        $stmt = $this->db->prepare("UPDATE properties SET is_featured = NOT is_featured WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleActive($id) {
        $stmt = $this->db->prepare("UPDATE properties SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM properties WHERE is_active = 1";
        $params = [];

        if (!empty($filters['operation_type'])) {
            $sql .= " AND operation_type = ?";
            $params[] = $filters['operation_type'];
        }

        if (!empty($filters['property_type'])) {
            $sql .= " AND property_type = ?";
            $params[] = $filters['property_type'];
        }

        if (!empty($filters['region_id'])) {
            $sql .= " AND region_id = ?";
            $params[] = $filters['region_id'];
        }

        if (!empty($filters['comuna_id'])) {
            $sql .= " AND comuna_id = ?";
            $params[] = $filters['comuna_id'];
        }

        if (!empty($filters['bedrooms'])) {
            if ($filters['bedrooms'] === '5+') {
                $sql .= " AND bedrooms >= 5";
            } else {
                $sql .= " AND bedrooms = ?";
                $params[] = (int)$filters['bedrooms'];
            }
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR description LIKE ? OR address LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['section_type'])) {
            $sql .= " AND section_type = ?";
            $params[] = $filters['section_type'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return isset($result['total']) ? (int)$result['total'] : 0;
    }
}
