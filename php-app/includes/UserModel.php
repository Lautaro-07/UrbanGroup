<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getPartners() {
        $stmt = $this->db->query("SELECT * FROM users WHERE role = 'partner' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, name, email, phone, role, company_name, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $hashedPassword,
            $data['name'],
            $data['email'],
            $data['phone'] ?? '',
            $data['role'] ?? 'partner',
            $data['company_name'] ?? '',
            $data['is_active'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'email', 'phone', 'company_name', 'is_active'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleActive($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPropertyCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM properties WHERE partner_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
