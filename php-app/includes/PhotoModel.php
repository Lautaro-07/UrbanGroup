<?php
require_once __DIR__ . '/../config/database.php';

class PhotoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByPropertyId($propertyId) {
        $stmt = $this->db->prepare("SELECT * FROM property_photos WHERE property_id = ? ORDER BY display_order ASC");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function create($propertyId, $photoUrl, $displayOrder = 0) {
        $stmt = $this->db->prepare("INSERT INTO property_photos (property_id, photo_url, display_order) VALUES (?, ?, ?)");
        return $stmt->execute([$propertyId, $photoUrl, $displayOrder]);
    }

    public function delete($photoId) {
        $stmt = $this->db->prepare("DELETE FROM property_photos WHERE id = ?");
        return $stmt->execute([$photoId]);
    }

    public function deleteByPropertyId($propertyId) {
        $stmt = $this->db->prepare("DELETE FROM property_photos WHERE property_id = ?");
        return $stmt->execute([$propertyId]);
    }

    public function getById($photoId) {
        $stmt = $this->db->prepare("SELECT * FROM property_photos WHERE id = ?");
        $stmt->execute([$photoId]);
        return $stmt->fetch();
    }

    public function updateDisplayOrder($photoId, $displayOrder) {
        $stmt = $this->db->prepare("UPDATE property_photos SET display_order = ? WHERE id = ?");
        return $stmt->execute([$displayOrder, $photoId]);
    }
}
