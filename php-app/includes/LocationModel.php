<?php
require_once __DIR__ . '/../config/database.php';

class LocationModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getRegions() {
        $stmt = $this->db->query("SELECT * FROM regions ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getComunas($regionId = null) {
        if ($regionId) {
            $stmt = $this->db->prepare("SELECT * FROM comunas WHERE region_id = ? ORDER BY name ASC");
            $stmt->execute([$regionId]);
        } else {
            $stmt = $this->db->query("SELECT c.*, r.name as region_name FROM comunas c JOIN regions r ON c.region_id = r.id ORDER BY r.name, c.name ASC");
        }
        return $stmt->fetchAll();
    }

    public function getRegionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM regions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getComunaById($id) {
        $stmt = $this->db->prepare("SELECT c.*, r.name as region_name FROM comunas c JOIN regions r ON c.region_id = r.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
