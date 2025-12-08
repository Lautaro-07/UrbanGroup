<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$propertyId = (int)($_POST['property_id'] ?? 0);
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    flash('error', 'Por favor completa todos los campos requeridos.');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO contact_messages (property_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$propertyId, $name, $email, $phone, $message]);
    
    flash('success', 'Tu mensaje ha sido enviado correctamente. Te contactaremos pronto.');
} catch (Exception $e) {
    flash('error', 'Hubo un error al enviar el mensaje. Por favor intenta nuevamente.');
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
