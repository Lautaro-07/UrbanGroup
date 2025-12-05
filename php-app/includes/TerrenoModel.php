<?php
require_once __DIR__ . '/../config/database.php';

class TerrenoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getDetailsByPropertyId($propertyId) {
        $stmt = $this->db->prepare("SELECT * FROM property_terreno_details WHERE property_id = ?");
        $stmt->execute([$propertyId]);
        return $stmt->fetch();
    }

    public function createOrUpdate($propertyId, $data) {
        $existing = $this->getDetailsByPropertyId($propertyId);
        
        if ($existing) {
            return $this->update($propertyId, $data);
        } else {
            return $this->create($propertyId, $data);
        }
    }

    public function create($propertyId, $data) {
        $sql = "INSERT INTO property_terreno_details (
            property_id, roles, fecha_permiso_edificacion, zona_prc_edificacion,
            usos_suelo, sistema_agrupamiento, altura_maxima, rasante,
            coef_constructibilidad_max, coef_ocupacion_suelo_max, coef_area_libre_min,
            antejardin_min, distanciamientos, densidad_bruta_max, densidad_neta_max,
            superficie_predial_min, superficie_util, superficie_comun, superficie_total,
            edificada_sobre_terreno, edificada_bajo_terreno, edificada_total,
            num_viviendas, num_estacionamientos, num_est_bicicletas,
            num_locales_comerciales, num_bodegas, superficie_bruta, superficie_bruta_ha,
            expropiacion, expropiacion_ha, superficie_util_total, superficie_util_ha,
            precio_uf_m2, precio_total_uf, comision_porcentaje, has_anteproyecto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $propertyId,
            $data['roles'] ?? null,
            $data['fecha_permiso_edificacion'] ?? null,
            $data['zona_prc_edificacion'] ?? null,
            $data['usos_suelo'] ?? null,
            $data['sistema_agrupamiento'] ?? null,
            $data['altura_maxima'] ?? null,
            $data['rasante'] ?? null,
            $data['coef_constructibilidad_max'] ?? null,
            $data['coef_ocupacion_suelo_max'] ?? null,
            $data['coef_area_libre_min'] ?? null,
            $data['antejardin_min'] ?? null,
            $data['distanciamientos'] ?? null,
            $data['densidad_bruta_max'] ?? null,
            $data['densidad_neta_max'] ?? null,
            $data['superficie_predial_min'] ?? null,
            $data['superficie_util'] ?? null,
            $data['superficie_comun'] ?? null,
            $data['superficie_total'] ?? null,
            $data['edificada_sobre_terreno'] ?? null,
            $data['edificada_bajo_terreno'] ?? null,
            $data['edificada_total'] ?? null,
            $data['num_viviendas'] ?? null,
            $data['num_estacionamientos'] ?? null,
            $data['num_est_bicicletas'] ?? null,
            $data['num_locales_comerciales'] ?? null,
            $data['num_bodegas'] ?? null,
            $data['superficie_bruta'] ?? null,
            $data['superficie_bruta_ha'] ?? null,
            $data['expropiacion'] ?? null,
            $data['expropiacion_ha'] ?? null,
            $data['superficie_util_total'] ?? null,
            $data['superficie_util_ha'] ?? null,
            $data['precio_uf_m2'] ?? null,
            $data['precio_total_uf'] ?? null,
            $data['comision_porcentaje'] ?? 2.0,
            $data['has_anteproyecto'] ?? 0
        ]);
    }

    public function update($propertyId, $data) {
        $fields = [];
        $params = [];

        $allowedFields = [
            'roles', 'fecha_permiso_edificacion', 'zona_prc_edificacion',
            'usos_suelo', 'sistema_agrupamiento', 'altura_maxima', 'rasante',
            'coef_constructibilidad_max', 'coef_ocupacion_suelo_max', 'coef_area_libre_min',
            'antejardin_min', 'distanciamientos', 'densidad_bruta_max', 'densidad_neta_max',
            'superficie_predial_min', 'superficie_util', 'superficie_comun', 'superficie_total',
            'edificada_sobre_terreno', 'edificada_bajo_terreno', 'edificada_total',
            'num_viviendas', 'num_estacionamientos', 'num_est_bicicletas',
            'num_locales_comerciales', 'num_bodegas', 'superficie_bruta', 'superficie_bruta_ha',
            'expropiacion', 'expropiacion_ha', 'superficie_util_total', 'superficie_util_ha',
            'precio_uf_m2', 'precio_total_uf', 'comision_porcentaje', 'has_anteproyecto'
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

        $sql = "UPDATE property_terreno_details SET " . implode(', ', $fields) . " WHERE property_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($propertyId) {
        $stmt = $this->db->prepare("DELETE FROM property_terreno_details WHERE property_id = ?");
        return $stmt->execute([$propertyId]);
    }

    public function getTerrenos($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name,
                       td.*
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                LEFT JOIN property_terreno_details td ON p.id = td.property_id
                WHERE p.is_active = 1 AND p.section_type = 'terrenos'";

        $params = [];

        if (!empty($filters['has_anteproyecto'])) {
            $sql .= " AND td.has_anteproyecto = 1";
        }

        if (!empty($filters['region_id'])) {
            $sql .= " AND p.region_id = ?";
            $params[] = $filters['region_id'];
        }

        if (!empty($filters['comuna_id'])) {
            $sql .= " AND p.comuna_id = ?";
            $params[] = $filters['comuna_id'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['min_area'])) {
            $sql .= " AND p.total_area >= ?";
            $params[] = (float)$filters['min_area'];
        }

        if (!empty($filters['max_area'])) {
            $sql .= " AND p.total_area <= ?";
            $params[] = (float)$filters['max_area'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getActivosInmobiliarios($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
                WHERE p.is_active = 1 AND p.section_type = 'activos'";

        $params = [];

        if (!empty($filters['operation_type'])) {
            $sql .= " AND p.operation_type = ?";
            $params[] = $filters['operation_type'];
        }

        if (!empty($filters['property_type'])) {
            $sql .= " AND p.property_type = ?";
            $params[] = $filters['property_type'];
        }

        if (!empty($filters['region_id'])) {
            $sql .= " AND p.region_id = ?";
            $params[] = $filters['region_id'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUSAProperties($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, 
                       c.name as comuna_name, 
                       r.name as region_name
                FROM properties p 
                LEFT JOIN comunas c ON p.comuna_id = c.id 
                LEFT JOIN regions r ON p.region_id = r.id
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

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
