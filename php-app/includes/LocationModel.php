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

    public function getComunasByRegion($regionId) {
        $stmt = $this->db->prepare("SELECT * FROM comunas WHERE region_id = ? ORDER BY name ASC");
        $stmt->execute([$regionId]);
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

    public function createRegion($name, $code = '') {
        $stmt = $this->db->prepare("INSERT INTO regions (name, code) VALUES (?, ?)");
        $stmt->execute([$name, $code]);
        return $this->db->lastInsertId();
    }

    public function updateRegion($id, $name, $code = '') {
        $stmt = $this->db->prepare("UPDATE regions SET name = ?, code = ? WHERE id = ?");
        return $stmt->execute([$name, $code, $id]);
    }

    public function deleteRegion($id) {
        $stmt = $this->db->prepare("DELETE FROM regions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createComuna($name, $regionId) {
        $stmt = $this->db->prepare("INSERT INTO comunas (name, region_id) VALUES (?, ?)");
        $stmt->execute([$name, $regionId]);
        return $this->db->lastInsertId();
    }

    public function updateComuna($id, $name, $regionId) {
        $stmt = $this->db->prepare("UPDATE comunas SET name = ?, region_id = ? WHERE id = ?");
        return $stmt->execute([$name, $regionId, $id]);
    }

    public function deleteComuna($id) {
        $stmt = $this->db->prepare("DELETE FROM comunas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
