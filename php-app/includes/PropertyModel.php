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
                'is_active' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN is_active INTEGER DEFAULT 1",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN is_active TINYINT(1) DEFAULT 1"
                ],
                'is_featured' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN is_featured INTEGER DEFAULT 0",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN is_featured TINYINT(1) DEFAULT 0"
                ],
                'is_project' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN is_project INTEGER DEFAULT 0",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN is_project TINYINT(1) DEFAULT 0"
                ],
                'section_type' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN section_type VARCHAR(50) DEFAULT 'propiedades'",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN section_type VARCHAR(50) DEFAULT 'propiedades'"
                ],
                'property_type' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN property_type VARCHAR(100)",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN property_type VARCHAR(100)"
                ],
                'property_category' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN property_category VARCHAR(100) DEFAULT ''",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN property_category VARCHAR(100) DEFAULT ''"
                ],
                'partner_id' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN partner_id INTEGER",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN partner_id INT"
                ],
                'total_area' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN total_area REAL",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN total_area DECIMAL(10,2)"
                ],
                'parking_spots' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN parking_spots INTEGER DEFAULT 0",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN parking_spots INT DEFAULT 0"
                ],
                'images' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN images TEXT",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN images TEXT"
                ],
                'features' => [
                    'sqlite' => "ALTER TABLE properties ADD COLUMN features TEXT",
                    'mysql'  => "ALTER TABLE properties ADD COLUMN features TEXT"
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
                $sql .= " AND p.bedrooms >= ?";
                $params[] = (int)$filters['bedrooms'];
            }
        }

        // FILTRO BAÑOS
        if (!empty($filters['bathrooms'])) {
            $sql .= " AND p.bathrooms >= ?";
            $params[] = (int)$filters['bathrooms'];
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

        // SUPERFICIE (usa built_area o total_area)
        if (!empty($filters['min_area'])) {
            $sql .= " AND (p.built_area >= ? OR p.total_area >= ?)";
            $params[] = (float)$filters['min_area'];
            $params[] = (float)$filters['min_area'];
        }

        if (!empty($filters['max_area'])) {
            $sql .= " AND (p.built_area <= ? OR (p.built_area = 0 AND p.total_area <= ?))";
            $params[] = (float)$filters['max_area'];
            $params[] = (float)$filters['max_area'];
        }

        // ESTACIONAMIENTOS
        if (!empty($filters['parking_spots'])) {
            $sql .= " AND p.parking_spots >= ?";
            $params[] = (int)$filters['parking_spots'];
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

        // EXCLUIR SECCIONES ESPECIALES (para propiedades generales)
        if (!empty($filters['exclude_sections']) && is_array($filters['exclude_sections'])) {
            $placeholders = implode(',', array_fill(0, count($filters['exclude_sections']), '?'));
            $sql .= " AND (p.section_type IS NULL OR p.section_type NOT IN ($placeholders))";
            foreach ($filters['exclude_sections'] as $section) {
                $params[] = $section;
            }
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

    public function getSimilar($propertyId, $sectionType = 'propiedades', $propertyType = '', $price = 0, $limit = 4) {
        if ($sectionType === 'usa') {
            $sql = "SELECT p.*, 
                           ud.price_usd as usa_price,
                           c.name as comuna_name, 
                           r.name as region_name
                    FROM properties p 
                    LEFT JOIN property_usa_details ud ON p.id = ud.property_id
                    LEFT JOIN comunas c ON p.comuna_id = c.id 
                    LEFT JOIN regions r ON p.region_id = r.id
                    WHERE p.is_active = 1 
                    AND p.id != ? 
                    AND p.section_type = 'usa'";
            
            $params = [$propertyId];
            
            if (!empty($propertyType)) {
                $sql .= " AND p.property_type = ?";
                $params[] = $propertyType;
            }
            
            $sql .= " ORDER BY ABS(COALESCE(ud.price_usd, p.price, 0) - ?) ASC, p.created_at DESC LIMIT " . (int)$limit;
            $params[] = (float)$price;
        } else {
            $sql = "SELECT p.*, 
                           c.name as comuna_name, 
                           r.name as region_name
                    FROM properties p 
                    LEFT JOIN comunas c ON p.comuna_id = c.id 
                    LEFT JOIN regions r ON p.region_id = r.id
                    WHERE p.is_active = 1 
                    AND p.id != ? 
                    AND p.section_type = ?";
            
            $params = [$propertyId, $sectionType];
            
            if (!empty($propertyType)) {
                $sql .= " AND p.property_type = ?";
                $params[] = $propertyType;
            }
            
            $sql .= " ORDER BY ABS(COALESCE(p.price, 0) - ?) ASC, p.created_at DESC LIMIT " . (int)$limit;
            $params[] = (float)$price;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
                $sql .= " AND bedrooms >= ?";
                $params[] = (int)$filters['bedrooms'];
            }
        }

        if (!empty($filters['bathrooms'])) {
            $sql .= " AND bathrooms >= ?";
            $params[] = (int)$filters['bathrooms'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['min_area'])) {
            $sql .= " AND (built_area >= ? OR total_area >= ?)";
            $params[] = (float)$filters['min_area'];
            $params[] = (float)$filters['min_area'];
        }

        if (!empty($filters['max_area'])) {
            $sql .= " AND (built_area <= ? OR (built_area = 0 AND total_area <= ?))";
            $params[] = (float)$filters['max_area'];
            $params[] = (float)$filters['max_area'];
        }

        if (!empty($filters['parking_spots'])) {
            $sql .= " AND parking_spots >= ?";
            $params[] = (int)$filters['parking_spots'];
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

        // EXCLUIR SECCIONES ESPECIALES (para propiedades generales)
        if (!empty($filters['exclude_sections']) && is_array($filters['exclude_sections'])) {
            $placeholders = implode(',', array_fill(0, count($filters['exclude_sections']), '?'));
            $sql .= " AND (section_type IS NULL OR section_type NOT IN ($placeholders))";
            foreach ($filters['exclude_sections'] as $section) {
                $params[] = $section;
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return isset($result['total']) ? (int)$result['total'] : 0;
    }
}
