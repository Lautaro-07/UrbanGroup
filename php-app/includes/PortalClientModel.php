<?php
require_once __DIR__ . '/../config/database.php';

class PortalClientModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        // Ensure portal_clients has expected columns to avoid runtime SQL errors
        $this->ensurePortalClientsColumns();
    }

    private function ensurePortalClientsColumns() {
        try {
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            // We'll ensure 'email', 'password', 'nombre_completo', 'rut', 'status' exist
            if ($driver === 'sqlite') {
                $stmt = $this->db->query("PRAGMA table_info('portal_clients')");
                $cols = array_map(function($c){ return strtolower($c['name']); }, $stmt->fetchAll(PDO::FETCH_ASSOC));

                $toAdd = [];
                if (!in_array('email', $cols)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN email VARCHAR(255)";
                if (!in_array('password', $cols)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN password VARCHAR(255)";
                if (!in_array('nombre_completo', $cols)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN nombre_completo VARCHAR(255)";
                if (!in_array('rut', $cols)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN rut VARCHAR(20)";
                if (!in_array('status', $cols)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN status VARCHAR(20) DEFAULT 'active'";

                foreach ($toAdd as $sql) { try { $this->db->exec($sql); } catch (Exception $e) { /* ignore */ } }
            } else {
                $dbName = defined('DB_NAME') ? DB_NAME : null;
                $existing = [];
                if ($dbName) {
                    $stmt = $this->db->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = ? AND table_name = 'portal_clients'");
                    $stmt->execute([$dbName]);
                    $existing = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
                }

                $toAdd = [];
                if (!in_array('email', $existing)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN email VARCHAR(255)";
                if (!in_array('password', $existing)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN password VARCHAR(255)";
                if (!in_array('nombre_completo', $existing)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN nombre_completo VARCHAR(255)";
                if (!in_array('rut', $existing)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN rut VARCHAR(20)";
                if (!in_array('status', $existing)) $toAdd[] = "ALTER TABLE portal_clients ADD COLUMN status VARCHAR(20) DEFAULT 'active'";

                foreach ($toAdd as $sql) { try { $this->db->exec($sql); } catch (Exception $e) { /* ignore */ } }
            }
        } catch (Exception $e) {
            // ignore
        }
    }

    public function authenticate($email, $password) {
        // Allow authentication by email or by RUT
        $client = null;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $client = $this->getByEmail($email);
        }

        if (!$client) {
            // Try as RUT (normalized)
            $norm = self::normalizeRut($email);
            if (!empty($norm)) {
                $client = $this->getByRutNormalized($norm);
            }
        }

        if ($client) {
            // If password matches a hash, it's valid.
            if (password_verify($password, $client['password'])) {
                $this->updateLastLogin($client['id']);
                return $client;
            }

            // If not, check if it's a plain text password (insecure legacy).
            if ($password === $client['password']) {
                // This is an insecure password. Let's upgrade it to a hash automatically.
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $this->db->prepare("UPDATE portal_clients SET password = ? WHERE id = ?");
                $updateStmt->execute([$newHash, $client['id']]);
                
                $this->updateLastLogin($client['id']);
                return $client;
            }
        }

        return false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM portal_clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM portal_clients WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getByRut($rut) {
        $stmt = $this->db->prepare("SELECT * FROM portal_clients WHERE rut = ?");
        $stmt->execute([$rut]);
        return $stmt->fetch();
    }

    public function getByRutNormalized($normalizedRut) {
        // Compare normalized rut (numbers + DV) ignoring dots and hyphens, case-insensitive
        $sql = "SELECT * FROM portal_clients WHERE REPLACE(REPLACE(UPPER(rut),'.',''),'-','') = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$normalizedRut]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM portal_clients ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        if ($this->getByEmail($data['email'])) {
            return ['error' => 'El email ya está registrado'];
        }
        
        $formattedRut = '';
        if (!empty($data['rut'])) {
            $normalizedRut = self::normalizeRut($data['rut']);
            $formattedRut = self::formatRut($data['rut']);

            if ($this->getByRut($formattedRut) || $this->getByRutNormalized($normalizedRut)) {
                return ['error' => 'El RUT ya está registrado'];
            }
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO portal_clients (
            razon_social, rut, registered_sections, representante_legal, nombre_completo, 
            cedula_identidad, celular, email, password, alias,
            consent_accepted, consent_date, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $registered = '';
        if (!empty($data['registered_sections'])) {
            if (is_array($data['registered_sections'])) {
                $registered = implode(',', $data['registered_sections']);
            } else {
                $registered = trim($data['registered_sections']);
            }
        }

        $stmt->execute([
            $data['razon_social'],
            $formattedRut,
            $registered,
            $data['representante_legal'],
            $data['nombre_completo'],
            $data['cedula_identidad'],
            $data['celular'],
            $data['email'],
            $hashedPassword,
            $data['alias'],
            1,
            date('Y-m-d H:i:s'),
            'active'
        ]);
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'razon_social', 'rut', 'representante_legal', 'nombre_completo',
            'cedula_identidad', 'celular', 'email', 'alias', 'status', 'registered_sections'
        ];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE portal_clients SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM portal_clients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleStatus($id) {
        $client = $this->getById($id);
        $newStatus = $client['status'] === 'active' ? 'inactive' : 'active';
        
        $stmt = $this->db->prepare("UPDATE portal_clients SET status = ? WHERE id = ?");
        return $stmt->execute([$newStatus, $id]);
    }

    private function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE portal_clients SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM portal_clients");
        $result = $stmt->fetch();
        return $result['total'];
    }

    public static function formatRut($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));
        if (strlen($rut) < 2) return $rut;
        
        $body = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        
        $formatted = '';
        $body = strrev($body);
        for ($i = 0; $i < strlen($body); $i++) {
            if ($i > 0 && $i % 3 === 0) {
                $formatted .= '.';
            }
            $formatted .= $body[$i];
        }
        
        return strrev($formatted) . '-' . strtoupper($dv);
    }

    public static function normalizeRut($rut) {
        // Return uppercase, digits + DV without dots or dashes
        $clean = preg_replace('/[^0-9kK]/', '', strtoupper($rut));
        return $clean;
    }

    public static function validateRut($rut) {
        $rut = preg_replace('/[^0-9kK]/', '', strtoupper($rut));
        if (strlen($rut) < 2) return false;
        
        $body = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        $sum = 0;
        $multiplier = 2;
        
        for ($i = strlen($body) - 1; $i >= 0; $i--) {
            $sum += intval($body[$i]) * $multiplier;
            $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
        }
        
        $expectedDv = 11 - ($sum % 11);
        if ($expectedDv === 11) {
            $expectedDv = '0';
        } elseif ($expectedDv === 10) {
            $expectedDv = 'K';
        } else {
            // Ensure comparison is between strings
            $expectedDv = (string)$expectedDv;
        }

        return strtoupper($dv) === $expectedDv;
    }
}
