<?php
// Script CLI: hashea contraseñas en portal_clients que estén en texto plano.
// Uso: php tools/hash_portal_passwords.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (php_sapi_name() !== 'cli') {
    echo "Run from CLI only\n";
    exit(1);
}

$db = Database::getInstance()->getConnection();
try {
    $rows = $db->query("SELECT id, password FROM portal_clients")->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0;
    foreach ($rows as $r) {
        $pw = $r['password'] ?? '';
        if (empty($pw)) continue;
        // consider hashed if starts with $2y$ or $2b$ or $argon
        if (strpos($pw, '$2y$') === 0 || strpos($pw, '$2b$') === 0 || strpos($pw, '$argon') === 0) {
            continue; // already hashed
        }
        // Otherwise hash and update
        $hash = password_hash($pw, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE portal_clients SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $r['id']]);
        $updated++;
        echo "Updated portal_client id={$r['id']}\n";
    }
    echo "Done. Total updated: $updated\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
