<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/LocationModel.php';

header('Content-Type: application/json');

$regionId = (int)($_GET['region_id'] ?? 0);

if (!$regionId) {
    echo json_encode([]);
    exit;
}

$locationModel = new LocationModel();
$comunas = $locationModel->getComunas($regionId);

echo json_encode($comunas);
