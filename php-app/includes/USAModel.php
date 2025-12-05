<?php
require_once __DIR__ . '/../config/database.php';

class USAModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUSAPropertyTypes() {
        $stmt = $this->db->query("SELECT * FROM property_types WHERE is_usa = 1 ORDER BY name");
        return $stmt->fetchAll();
    }

    public function getUSAProperties($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, 
                       ud.surface_sqft, ud.lot_size_sqft, ud.price_usd, ud.is_project as usa_is_project,
                       ud.year_built as usa_year_built, ud.stories, ud.garage_spaces, ud.pool,
                       ud.view_type, ud.cooling, ud.project_units, ud.project_developer,
                       ud.project_amenities
                FROM properties p 
                LEFT JOIN property_usa_details ud ON p.id = ud.property_id
                WHERE p.is_active = 1 AND p.section_type = 'usa'";

        $params = [];

        if (!empty($filters['operation_type'])) {
            $sql .= " AND p.operation_type = ?";
            $params[] = $filters['operation_type'];
        }

        if (!empty($filters['property_type'])) {
            $sql .= " AND p.property_type = ?";
            $params[] = $filters['property_type'];
        }

        if (!empty($filters['is_project'])) {
            $sql .= " AND p.is_project = 1";
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        $sql .= " ORDER BY p.is_project DESC, p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getProjects($limit = null) {
        $sql = "SELECT p.*, 
                       ud.surface_sqft, ud.price_usd, ud.project_units, ud.project_developer,
                       ud.project_amenities, ud.project_completion_date
                FROM properties p 
                LEFT JOIN property_usa_details ud ON p.id = ud.property_id
                WHERE p.is_active = 1 AND p.section_type = 'usa' AND p.is_project = 1
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ?";
        }

        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
            $stmt->execute([(int)$limit]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getUSADetailsByPropertyId($propertyId) {
        $stmt = $this->db->prepare("SELECT * FROM property_usa_details WHERE property_id = ?");
        $stmt->execute([$propertyId]);
        return $stmt->fetch();
    }

    public function createOrUpdateUSADetails($propertyId, $data) {
        $existing = $this->getUSADetailsByPropertyId($propertyId);
        
        if ($existing) {
            return $this->updateUSADetails($propertyId, $data);
        } else {
            return $this->createUSADetails($propertyId, $data);
        }
    }

    public function createUSADetails($propertyId, $data) {
        $sql = "INSERT INTO property_usa_details (
            property_id, is_project, surface_sqft, lot_size_sqft, price_usd,
            hoa_fee, property_tax, year_built, stories, garage_spaces,
            pool, waterfront, view_type, heating, cooling, flooring,
            appliances, exterior_features, interior_features, community_features,
            project_units, project_developer, project_completion_date, project_amenities
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $propertyId,
            $data['is_project'] ?? 0,
            $data['surface_sqft'] ?? null,
            $data['lot_size_sqft'] ?? null,
            $data['price_usd'] ?? null,
            $data['hoa_fee'] ?? null,
            $data['property_tax'] ?? null,
            $data['year_built'] ?? null,
            $data['stories'] ?? null,
            $data['garage_spaces'] ?? null,
            $data['pool'] ?? 0,
            $data['waterfront'] ?? 0,
            $data['view_type'] ?? null,
            $data['heating'] ?? null,
            $data['cooling'] ?? null,
            $data['flooring'] ?? null,
            $data['appliances'] ?? null,
            $data['exterior_features'] ?? null,
            $data['interior_features'] ?? null,
            $data['community_features'] ?? null,
            $data['project_units'] ?? null,
            $data['project_developer'] ?? null,
            $data['project_completion_date'] ?? null,
            $data['project_amenities'] ?? null
        ]);
    }

    public function updateUSADetails($propertyId, $data) {
        $fields = [];
        $params = [];

        $allowedFields = [
            'is_project', 'surface_sqft', 'lot_size_sqft', 'price_usd',
            'hoa_fee', 'property_tax', 'year_built', 'stories', 'garage_spaces',
            'pool', 'waterfront', 'view_type', 'heating', 'cooling', 'flooring',
            'appliances', 'exterior_features', 'interior_features', 'community_features',
            'project_units', 'project_developer', 'project_completion_date', 'project_amenities'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) return true;

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $propertyId;

        $sql = "UPDATE property_usa_details SET " . implode(', ', $fields) . " WHERE property_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function formatSqft($sqft) {
        if (!$sqft) return 'N/A';
        return number_format($sqft, 0, ',', ',') . ' sqft';
    }

    public static function formatUSD($price) {
        if (!$price) return 'N/A';
        return '$' . number_format($price, 0, '.', ',') . ' USD';
    }
}
