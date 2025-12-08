<?php
// Ensure config is loaded before database initialization
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class FavoriteModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addFavorite($clientId, $propertyId) {
        try {
            $stmt = $this->db->prepare("INSERT INTO client_favorites (client_id, property_id) VALUES (?, ?)");
            $stmt->execute([$clientId, $propertyId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Handle duplicate entry for both SQLite and MySQL
            $msg = $e->getMessage();
            $isDuplicate = false;
            if (strpos($msg, 'UNIQUE constraint failed') !== false) $isDuplicate = true;
            // MySQL duplicate key error code 1062
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) $isDuplicate = true;
            if ($isDuplicate) {
                return false;
            }
            throw $e;
        }
    }

    public function removeFavorite($clientId, $propertyId) {
        $stmt = $this->db->prepare("DELETE FROM client_favorites WHERE client_id = ? AND property_id = ?");
        return $stmt->execute([$clientId, $propertyId]);
    }

    public function isFavorite($clientId, $propertyId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM client_favorites WHERE client_id = ? AND property_id = ?");
        $stmt->execute([$clientId, $propertyId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getClientFavorites($clientId) {
        $stmt = $this->db->prepare("
            SELECT p.*, r.name as region_name, c.name as comuna_name,
                   (SELECT url FROM property_photos WHERE property_id = p.id ORDER BY sort_order, id LIMIT 1) as main_photo
            FROM client_favorites f
            JOIN properties p ON f.property_id = p.id
            LEFT JOIN regions r ON p.region_id = r.id
            LEFT JOIN comunas c ON p.comuna_id = c.id
            WHERE f.client_id = ? AND p.is_active = 1
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function getFavoriteCount($propertyId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM client_favorites WHERE property_id = ?");
        $stmt->execute([$propertyId]);
        return $stmt->fetchColumn();
    }

    public function getClientFavoriteIds($clientId) {
        $stmt = $this->db->prepare("SELECT property_id FROM client_favorites WHERE client_id = ?");
        $stmt->execute([$clientId]);
        return array_column($stmt->fetchAll(), 'property_id');
    }

    public function toggleFavorite($clientId, $propertyId) {
        if ($this->isFavorite($clientId, $propertyId)) {
            $this->removeFavorite($clientId, $propertyId);
            return ['success' => true, 'action' => 'removed', 'message' => 'Propiedad eliminada de favoritos'];
        } else {
            $this->addFavorite($clientId, $propertyId);
            return ['success' => true, 'action' => 'added', 'message' => 'Propiedad agregada a favoritos'];
        }
    }
}
