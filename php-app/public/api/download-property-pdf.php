<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PropertyModel.php';
require_once __DIR__ . '/../../includes/PDF.php';

function convertToISO($text) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die("ID invalido"); }

$propertyModel = new PropertyModel();
$property = $propertyModel->getById($id);

if (!$property) { die("Propiedad no encontrada"); }

// Características
$features = [];
if (!empty($property['features'])) {
    $arr = json_decode($property['features'], true);
    if (is_array($arr)) {
        $features = $arr;
    }
}

$pdf = new PDF_UrbanGroup();
$pdf->AddPage();

// ================== PORTADA PROFESIONAL ==================
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(0, 120, 215);
$pdf->Cell(0, 15, "FICHA DE PROPIEDAD", 0, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(30, 30, 30);
$pdf->MultiCell(0, 8, convertToISO($property['title']));

$pdf->Ln(3);

// Ubicación profesional
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(80, 80, 80);
$location = ($property['address'] ?? "") . " - " . ($property['comuna_name'] ?? "") . ", " . ($property['region_name'] ?? "");
$pdf->MultiCell(0, 7, convertToISO($location));

$pdf->Ln(5);

// Info operación
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(100, 100, 100);
$operacion = strtoupper($property['operation_type'] ?? "");
$pdf->Cell(0, 7, convertToISO("Operacion: $operacion"), 0, 1, 'C');
$pdf->Ln(3);

// ================== INFORMACIÓN PRINCIPAL ==================
$pdf->SectionTitle("INFORMACION GENERAL");

$precio = "$" . number_format($property['price'], 0, ',', '.');
if ($property['operation_type'] === 'Arriendo') {
    $precio .= " /mes";
}

$pdf->InfoRow("Precio", $precio);
$pdf->Ln(1);

if ($property['bedrooms']) {
    $pdf->TwoColumnRow("Dormitorios", $property["dormitorios"], "Baños", $property["banos"]);
} else {
    $pdf->Ln(1);
}

if ($property['built_area']) {
    $pdf->TwoColumnRow("Area Construida", round($property['built_area']) . " m2", "Area Total", round($property['total_area'] ?? 0) . " m2");
} else {
    $pdf->Ln(1);
}

if ($property['parking_spots']) {
    $pdf->InfoRow("Estacionamientos", (string)$property['parking_spots']);
    $pdf->Ln(1);
}

// ================== CARACTERÍSTICAS ADICIONALES ==================
if (!empty($features)) {
    $pdf->SectionTitle("CARACTERISTICAS DESTACADAS");
    $pdf->FeaturesList($features);
}

// ================== DESCRIPCIÓN ==================
if (!empty($property['description'])) {
    $pdf->SectionTitle("DESCRIPCION");
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->MultiCell(0, 6, convertToISO($property['description']), 0, 'L');
    $pdf->Ln(3);
}

// ================== INFORMACIÓN DE UBICACIÓN ==================
$pdf->SectionTitle("UBICACION");
$pdf->TwoColumnRow("Comuna", $property['comuna_name'] ?? "N/A",
                   "Región", $property['region_name'] ?? "N/A");

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetFillColor(240, 248, 255);
$pdf->Rect(10, $pdf->GetY(), 190, 12, 'F');
$pdf->SetXY(12, $pdf->GetY() + 1);
$pdf->MultiCell(0, 5, "Nota: La ubicacion mostrada es aproximada por razones de privacidad. Para informacion detallada de ubicacion, contacte a nuestros agentes.", 0, 'L');

$pdf->Ln(3);

// ================== INFORMACIÓN ADICIONAL ==================
$pdf->SectionTitle("INFORMACION ADICIONAL");
$pdf->TwoColumnRow("Codigo de Propiedad", (string)$property['id'], "Tipo de Propiedad", convertToISO(ucfirst($property['property_type'] ?? "N/A")));

$pdf->TwoColumnRow("Generado", date("d/m/Y"), "Hora", date("H:i:s"));

$pdf->Ln(3);

// ================== CONTACTO ==================
$pdf->SectionTitle("CONTACTO");
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(0, 6, "Urban Group - Portal Inmobiliario Profesional", 0, 1);
$pdf->Cell(0, 6, "www.urbangroup.cl", 0, 1);
$pdf->Cell(0, 6, "Tel: +56 2 XXXX XXXX", 0, 1);

// Salida
if (ob_get_length()) ob_end_clean();

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=propiedad_" . $property['id'] . ".pdf");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$pdf->Output('D');
exit;
