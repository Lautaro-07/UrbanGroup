<?php
// Configurar antes de cualquier otra cosa
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json; charset=utf-8');

// Verificar sesión sin helpers (evitar redirecciones)
if (empty($_SESSION['user_id']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Verificar archivo
if (!isset($_FILES['photo'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se envió archivo']);
    exit;
}

$file = $_FILES['photo'];

// Validar upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Error en la carga del archivo']);
    exit;
}

// Validar tamaño
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Archivo muy grande (máximo 5MB)']);
    exit;
}

// Validar MIME type
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido']);
    exit;
}

// Crear directorio si no existe
$uploadDir = __DIR__ . '../uploads/partners/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo crear directorio']);
        exit;
    }
}

// Generar nombre único
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'partner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$filepath = $uploadDir . $filename;

// Mover archivo
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']);
    exit;
}

// Éxito
$photoUrl = '/uploads/partners/' . $filename;
http_response_code(200);
echo json_encode(['success' => true, 'photo_url' => $photoUrl]);
exit;
