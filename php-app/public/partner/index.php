<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PropertyModel.php';
require_once __DIR__ . '/../../includes/PhotoModel.php';
require_once __DIR__ . '/../../includes/LocationModel.php';
require_once __DIR__ . '/../../includes/PropertyTypeModel.php';
require_once __DIR__ . '/../../includes/PropertyDetailsModel.php';
require_once __DIR__ . '/../../includes/TerrenoModel.php';
require_once __DIR__ . '/../../includes/USAModel.php';

requirePartner();

$propertyModel = new PropertyModel();
$photoModel = new PhotoModel();
$locationModel = new LocationModel();
$propertyTypeModel = new PropertyTypeModel();
$propertyDetailsModel = new PropertyDetailsModel();
$terrenoModel = new TerrenoModel();
$usaModel = new USAModel();

$action = $_GET['action'] ?? 'dashboard';
$propertyId = (int)($_GET['id'] ?? 0);
$sectionType = $_GET['type'] ?? 'propiedades';

$regions = $locationModel->getRegions();
$propertyTypes = $propertyTypeModel->getAll();
$usaPropertyTypes = $propertyTypeModel->getUSATypes();

if (isset($_GET['move_photo']) && isset($_GET['move_to'])) {
    $photoId = (int)$_GET['move_photo'];
    $newOrder = (int)$_GET['move_to'];
    $propertyId = (int)($_GET['id'] ?? 0);
    
    $photo = $photoModel->getById($photoId);
    if ($photo && $photo['property_id'] == $propertyId) {
        $checkProperty = $propertyModel->getById($propertyId);
        if ($checkProperty && $checkProperty['partner_id'] == $_SESSION['user_id']) {
            $allPhotos = $photoModel->getByPropertyId($propertyId);
            $oldOrder = $photo['display_order'];
            
            if ($newOrder > $oldOrder) {
                foreach ($allPhotos as $p) {
                    if ($p['display_order'] > $oldOrder && $p['display_order'] <= $newOrder) {
                        $photoModel->updateDisplayOrder($p['id'], $p['display_order'] - 1);
                    }
                }
            } else {
                foreach ($allPhotos as $p) {
                    if ($p['display_order'] >= $newOrder && $p['display_order'] < $oldOrder) {
                        $photoModel->updateDisplayOrder($p['id'], $p['display_order'] + 1);
                    }
                }
            }
            $photoModel->updateDisplayOrder($photoId, $newOrder);
        }
    }
    header('Location: ?action=edit&id=' . $propertyId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'create_property') {
        $sType = sanitizeInput($_POST['section_type'] ?? 'propiedades');
        
        $address = sanitizeInput($_POST['address'] ?? '');
        if ($sType === 'terrenos' && !empty($_POST['terreno']['ubicacion'])) {
            $address = sanitizeInput($_POST['terreno']['ubicacion']);
        }
        
        $propertyId = $propertyModel->create([
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'property_type' => sanitizeInput($_POST['property_type'] ?? ''),
            'operation_type' => sanitizeInput($_POST['operation_type'] ?? 'Venta'),
            'price' => (float)($_POST['price'] ?? 0),
            'currency' => sanitizeInput($_POST['currency'] ?? 'CLP'),
            'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
            'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
            'built_area' => (float)($_POST['built_area'] ?? 0),
            'total_area' => (float)($_POST['total_area'] ?? 0),
            'parking_spots' => (int)($_POST['parking_spots'] ?? 0),
            'address' => $address,
            'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
            'region_id' => !empty($_POST['region_id']) ? (int)$_POST['region_id'] : null,
            'images' => json_encode([]),
            'is_featured' => 0,
            'is_active' => 1,
            'partner_id' => $_SESSION['user_id'],
            'section_type' => $sType,
            'property_category' => sanitizeInput($_POST['property_category'] ?? '')
        ]);
        
        if ($propertyId) {
            if ($sType === 'terrenos' && !empty($_POST['terreno'])) {
                $terrenoData = [];
                $terrenoFields = [
                    'nombre_proyecto', 'ubicacion', 'usos_suelo_permitidos',
                    'roles', 'fecha_permiso_edificacion', 'zona_prc_edificacion', 'fecha_cip',
                    'usos_suelo', 'sistema_agrupamiento', 'altura_maxima', 'rasante',
                    'coef_constructibilidad_max', 'coef_ocupacion_suelo_max', 'coef_area_libre_min',
                    'antejardin_min', 'distanciamientos', 'articulos_normativos',
                    'frente', 'fondo', 'superficie_total_terreno', 'superficie_util', 'superficie_bruta', 'expropiacion',
                    'superficie_predial_min', 'densidad_bruta_max_hab_ha', 'densidad_bruta_max_viv_ha',
                    'densidad_neta_max_hab_ha', 'densidad_neta_max_viv_ha',
                    'num_viviendas', 'superficie_edificada', 'superficie_util_anteproyecto',
                    'densidad_neta', 'densidad_maxima', 'num_estacionamientos',
                    'num_est_visitas', 'num_est_bicicletas', 'num_locales_comerciales', 'num_bodegas', 'superficies_aprobadas',
                    'ap_bajo_util', 'ap_bajo_comun', 'ap_bajo_total',
                    'ap_sobre_util', 'ap_sobre_comun', 'ap_sobre_total',
                    'ap_total_util', 'ap_total_comun', 'ap_total_total',
                    'sin_superficie_bruta', 'sin_superficie_util', 'sin_superficie_expropiacion',
                    'precio', 'precio_uf_m2', 'comision', 'observaciones', 'video_url', 'has_anteproyecto', 'estado', 'ciudad'
                ];
                
                foreach ($terrenoFields as $field) {
                    if (isset($_POST['terreno'][$field]) && $_POST['terreno'][$field] !== '') {
                        $terrenoData[$field] = is_string($_POST['terreno'][$field]) ? sanitizeInput($_POST['terreno'][$field]) : $_POST['terreno'][$field];
                    }
                }
                
                if (isset($_FILES['pdf_documento']) && $_FILES['pdf_documento']['error'] === UPLOAD_ERR_OK) {
                    $pdfDir = __DIR__ . '/../uploads/terrenos/';
                    if (!is_dir($pdfDir)) mkdir($pdfDir, 0777, true);
                    
                    $ext = strtolower(pathinfo($_FILES['pdf_documento']['name'], PATHINFO_EXTENSION));
                    if ($ext === 'pdf') {
                        $pdfFilename = 'terreno_' . $propertyId . '_' . time() . '.pdf';
                        if (@move_uploaded_file($_FILES['pdf_documento']['tmp_name'], $pdfDir . $pdfFilename)) {
                            $terrenoData['pdf_documento'] = '../uploads/terrenos/' . $pdfFilename;
                        }
                    }
                }
                
                $terrenoModel->createOrUpdate($propertyId, $terrenoData);
                
            } elseif ($sType === 'usa' && !empty($_POST['usa'])) {
                $usaData = [];
                $usaFields = [
                    'is_project', 'surface_sqft', 'lot_size_sqft', 'price_usd',
                    'hoa_fee', 'property_tax', 'year_built', 'stories', 'garage_spaces',
                    'pool', 'waterfront', 'view_type', 'heating', 'cooling', 'flooring',
                    'appliances', 'exterior_features', 'interior_features', 'community_features',
                    'project_units', 'project_developer', 'project_completion_date', 'project_amenities',
                    'whatsapp_number', 'mls_id', 'state', 'city', 'zip_code'
                ];
                
                foreach ($usaFields as $field) {
                    if (isset($_POST['usa'][$field]) && $_POST['usa'][$field] !== '') {
                        $value = $_POST['usa'][$field];
                        if (in_array($field, ['pool', 'waterfront', 'is_project'])) {
                            $usaData[$field] = (int)$value;
                        } elseif (in_array($field, ['surface_sqft', 'lot_size_sqft', 'price_usd', 'hoa_fee', 'property_tax'])) {
                            $usaData[$field] = (float)$value;
                        } elseif (in_array($field, ['year_built', 'stories', 'garage_spaces', 'project_units'])) {
                            $usaData[$field] = (int)$value;
                        } else {
                            $usaData[$field] = sanitizeInput($value);
                        }
                    }
                }
                
                if (!empty($_POST['usa']['is_project'])) {
                    $propertyModel->update($propertyId, ['is_project' => 1]);
                }
                
                $usaModel->createOrUpdateUSADetails($propertyId, $usaData);
                
            } elseif ($sType === 'activos') {
                $details = [
                    'asset_condition' => sanitizeInput($_POST['asset_condition'] ?? ''),
                    'brand' => sanitizeInput($_POST['brand'] ?? '')
                ];
                $propertyDetailsModel->save($propertyId, [
                    'property_category' => sanitizeInput($_POST['property_category'] ?? ''),
                    'section_type' => $sType,
                    'details' => $details,
                    'features' => [],
                    'costs' => []
                ]);
            } else {
                $detailsData = [];
                $featuresData = [];
                $costsData = [];
                
                if (!empty($_POST['details']) && is_array($_POST['details'])) {
                    $detailsData = array_filter($_POST['details'], fn($v) => $v !== '');
                }
                if (!empty($_POST['property_features']) && is_array($_POST['property_features'])) {
                    $featuresData = $_POST['property_features'];
                }
                if (!empty($_POST['costs']) && is_array($_POST['costs'])) {
                    $costsData = array_filter($_POST['costs'], fn($v) => $v !== '');
                }
                
                $propertyDetailsModel->save($propertyId, [
                    'property_category' => sanitizeInput($_POST['property_category'] ?? ''),
                    'section_type' => $sType,
                    'details' => $detailsData,
                    'features' => $featuresData,
                    'costs' => $costsData
                ]);
            }
            
            if (isset($_FILES['property_photos']) && is_array($_FILES['property_photos']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/properties/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $displayOrder = 0;
                for ($i = 0; $i < count($_FILES['property_photos']['name']); $i++) {
                    if ($_FILES['property_photos']['error'][$i] === UPLOAD_ERR_OK && $displayOrder < 12) {
                        $ext = strtolower(pathinfo($_FILES['property_photos']['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            $filename = time() . '_' . uniqid() . '.' . $ext;
                            if (@move_uploaded_file($_FILES['property_photos']['tmp_name'][$i], $uploadDir . $filename)) {
                                $photoModel->create($propertyId, '../uploads/properties/' . $filename, $displayOrder);
                                $displayOrder++;
                            }
                        }
                    }
                }
            }
        }
        
        $redirectSection = $sType !== 'propiedades' ? '&type=' . urlencode($sType) : '';
        header('Location: ?action=properties' . $redirectSection);
        exit;
    }
    
    if ($postAction === 'update_property' && !empty($_POST['property_id'])) {
        $propertyId = (int)$_POST['property_id'];
        $property = $propertyModel->getById($propertyId);
        
        if ($property && $property['partner_id'] == $_SESSION['user_id']) {
            $sType = sanitizeInput($_POST['section_type'] ?? $property['section_type'] ?? 'propiedades');
            
            $propertyModel->update($propertyId, [
                'title' => sanitizeInput($_POST['title']),
                'description' => sanitizeInput($_POST['description'] ?? ''),
                'property_type' => sanitizeInput($_POST['property_type'] ?? ''),
                'operation_type' => sanitizeInput($_POST['operation_type'] ?? 'Venta'),
                'price' => (float)($_POST['price'] ?? 0),
                'currency' => sanitizeInput($_POST['currency'] ?? 'CLP'),
                'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
                'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
                'built_area' => (float)($_POST['built_area'] ?? 0),
                'total_area' => (float)($_POST['total_area'] ?? 0),
                'parking_spots' => (int)($_POST['parking_spots'] ?? 0),
                'address' => sanitizeInput($_POST['address'] ?? ''),
                'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
                'region_id' => !empty($_POST['region_id']) ? (int)$_POST['region_id'] : null,
                'section_type' => $sType,
                'property_category' => sanitizeInput($_POST['property_category'] ?? '')
            ]);
            
            if ($sType === 'terrenos' && !empty($_POST['terreno'])) {
                $terrenoData = [];
                $terrenoFields = [
                    'nombre_proyecto', 'ubicacion', 'usos_suelo_permitidos',
                    'roles', 'fecha_permiso_edificacion', 'zona_prc_edificacion', 'fecha_cip',
                    'usos_suelo', 'sistema_agrupamiento', 'altura_maxima', 'rasante',
                    'coef_constructibilidad_max', 'coef_ocupacion_suelo_max', 'coef_area_libre_min',
                    'antejardin_min', 'distanciamientos', 'articulos_normativos',
                    'frente', 'fondo', 'superficie_total_terreno', 'superficie_util', 'superficie_bruta', 'expropiacion',
                    'superficie_predial_min', 'densidad_bruta_max_hab_ha', 'densidad_bruta_max_viv_ha',
                    'densidad_neta_max_hab_ha', 'densidad_neta_max_viv_ha',
                    'num_viviendas', 'superficie_edificada', 'superficie_util_anteproyecto',
                    'densidad_neta', 'densidad_maxima', 'num_estacionamientos',
                    'num_est_visitas', 'num_est_bicicletas', 'num_locales_comerciales', 'num_bodegas', 'superficies_aprobadas',
                    'ap_bajo_util', 'ap_bajo_comun', 'ap_bajo_total',
                    'ap_sobre_util', 'ap_sobre_comun', 'ap_sobre_total',
                    'ap_total_util', 'ap_total_comun', 'ap_total_total',
                    'sin_superficie_bruta', 'sin_superficie_util', 'sin_superficie_expropiacion',
                    'precio', 'precio_uf_m2', 'comision', 'observaciones', 'video_url', 'has_anteproyecto', 'estado', 'ciudad'
                ];
                
                foreach ($terrenoFields as $field) {
                    if (isset($_POST['terreno'][$field])) {
                        $terrenoData[$field] = is_string($_POST['terreno'][$field]) ? sanitizeInput($_POST['terreno'][$field]) : $_POST['terreno'][$field];
                    }
                }
                
                $terrenoModel->createOrUpdate($propertyId, $terrenoData);
                
            } elseif ($sType === 'usa' && !empty($_POST['usa'])) {
                $usaData = [];
                $usaFields = [
                    'is_project', 'surface_sqft', 'lot_size_sqft', 'price_usd',
                    'hoa_fee', 'property_tax', 'year_built', 'stories', 'garage_spaces',
                    'pool', 'waterfront', 'view_type', 'heating', 'cooling', 'flooring',
                    'appliances', 'exterior_features', 'interior_features', 'community_features',
                    'project_units', 'project_developer', 'project_completion_date', 'project_amenities',
                    'whatsapp_number', 'mls_id', 'state', 'city', 'zip_code'
                ];
                
                foreach ($usaFields as $field) {
                    if (isset($_POST['usa'][$field])) {
                        $value = $_POST['usa'][$field];
                        if (in_array($field, ['pool', 'waterfront', 'is_project'])) {
                            $usaData[$field] = (int)$value;
                        } elseif (in_array($field, ['surface_sqft', 'lot_size_sqft', 'price_usd', 'hoa_fee', 'property_tax'])) {
                            $usaData[$field] = (float)$value;
                        } elseif (in_array($field, ['year_built', 'stories', 'garage_spaces', 'project_units'])) {
                            $usaData[$field] = (int)$value;
                        } else {
                            $usaData[$field] = sanitizeInput($value);
                        }
                    }
                }
                
                $propertyModel->update($propertyId, ['is_project' => !empty($_POST['usa']['is_project']) ? 1 : 0]);
                $usaModel->createOrUpdateUSADetails($propertyId, $usaData);
            }
            
            if (isset($_POST['delete_photos']) && is_array($_POST['delete_photos'])) {
                foreach ($_POST['delete_photos'] as $photoId) {
                    $photo = $photoModel->getById((int)$photoId);
                    if ($photo && $photo['property_id'] == $propertyId) {
                        $photoFile = __DIR__ . '/../uploads/properties/' . basename($photo['photo_url']);
                        if (file_exists($photoFile)) @unlink($photoFile);
                        $photoModel->delete((int)$photoId);
                    }
                }
            }
            
            if (isset($_FILES['property_photos']) && is_array($_FILES['property_photos']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/properties/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $existingPhotos = $photoModel->getByPropertyId($propertyId);
                $displayOrder = count($existingPhotos);
                
                for ($i = 0; $i < count($_FILES['property_photos']['name']); $i++) {
                    if ($_FILES['property_photos']['error'][$i] === UPLOAD_ERR_OK && $displayOrder < 12) {
                        $ext = strtolower(pathinfo($_FILES['property_photos']['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            $filename = time() . '_' . uniqid() . '.' . $ext;
                            if (@move_uploaded_file($_FILES['property_photos']['tmp_name'][$i], $uploadDir . $filename)) {
                                $photoModel->create($propertyId, '../uploads/properties/' . $filename, $displayOrder);
                                $displayOrder++;
                            }
                        }
                    }
                }
            }
        }
        
        $redirectSection = isset($_POST['section_type']) && $_POST['section_type'] !== 'propiedades' ? '&type=' . urlencode($_POST['section_type']) : '';
        header('Location: ?action=properties' . $redirectSection);
        exit;
    }
    
    if ($postAction === 'delete_property' && !empty($_POST['property_id'])) {
        $propertyId = (int)$_POST['property_id'];
        $property = $propertyModel->getById($propertyId);
        
        if ($property && $property['partner_id'] == $_SESSION['user_id']) {
            $allPhotos = $photoModel->getByPropertyId($propertyId);
            foreach ($allPhotos as $photo) {
                $photoFile = __DIR__ . '/../uploads/properties/' . basename($photo['photo_url']);
                if (file_exists($photoFile)) @unlink($photoFile);
            }
            $photoModel->deleteByPropertyId($propertyId);
            $propertyModel->delete($propertyId);
        }
        
        $redirectSection = isset($_POST['section_type']) && $_POST['section_type'] !== 'propiedades' ? '&type=' . urlencode($_POST['section_type']) : '';
        header('Location: ?action=properties' . $redirectSection);
        exit;
    }
    
    if (isset($_POST['delete_photo_ajax']) && isset($_POST['photo_id'])) {
        header('Content-Type: application/json');
        $photoId = (int)$_POST['photo_id'];
        $photo = $photoModel->getById($photoId);
        if ($photo) {
            $property = $propertyModel->getById($photo['property_id']);
            if ($property && $property['partner_id'] == $_SESSION['user_id']) {
                $photoFile = __DIR__ . '/../uploads/properties/' . basename($photo['photo_url']);
                if (file_exists($photoFile)) @unlink($photoFile);
                $photoModel->delete($photoId);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }
}

$myProperties = $propertyModel->getByPartnerId($_SESSION['user_id']);
$totalProperties = count($myProperties);
$activeProperties = count(array_filter($myProperties, fn($p) => $p['is_active']));

$propiedadesCount = count(array_filter($myProperties, fn($p) => ($p['section_type'] ?? 'propiedades') === 'propiedades'));
$terrenosCount = count(array_filter($myProperties, fn($p) => ($p['section_type'] ?? '') === 'terrenos'));
$activosCount = count(array_filter($myProperties, fn($p) => ($p['section_type'] ?? '') === 'activos'));
$usaCount = count(array_filter($myProperties, fn($p) => ($p['section_type'] ?? '') === 'usa'));

$editProperty = null;
$editPropertyDetails = ['details' => [], 'features' => [], 'costs' => []];
$terrenoDetails = null;
$usaDetails = null;

if ($action === 'edit' && $propertyId) {
    $editProperty = $propertyModel->getById($propertyId);
    if (!$editProperty || $editProperty['partner_id'] != $_SESSION['user_id']) {
        header('Location: ?action=properties');
        exit;
    }
    $editPropertyDetails = $propertyDetailsModel->getByPropertyId($propertyId);
    
    if (($editProperty['section_type'] ?? '') === 'terrenos') {
        $terrenoDetails = $terrenoModel->getDetailsByPropertyId($propertyId);
    } elseif (($editProperty['section_type'] ?? '') === 'usa') {
        $usaDetails = $usaModel->getUSADetails($propertyId);
    }
}

$sectionLabels = [
    'propiedades' => 'Propiedades',
    'terrenos' => 'Terrenos',
    'activos' => 'Activos',
    'usa' => 'Prop. USA'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Socio - Urban Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">

<header class="sticky top-0 z-50 border-b border-gray-200 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-2 text-xl lg:text-2xl font-bold text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 lg:w-8 lg:h-8" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
            </svg>
            <span class="hidden sm:inline">Urban Group</span>
        </a>
        <div class="flex items-center gap-2 lg:gap-4">
            <span class="hidden md:inline text-sm text-gray-600">Hola, <?= htmlspecialchars($_SESSION['name']) ?></span>
            <a href="../index.php" class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">Ir al Sitio</a>
            <a href="../logout.php" class="px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Salir</a>
        </div>
    </div>
</header>

<div class="flex min-h-screen">
    <aside class="w-64 bg-slate-900 text-white hidden lg:block">
        <div class="p-6 border-b border-slate-700">
            <p class="text-xs font-semibold text-slate-400 mb-1">MI CUENTA</p>
            <p class="text-sm font-medium truncate"><?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        
        <nav class="p-4 space-y-1">
            <a href="?action=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'dashboard' ? 'bg-blue-600' : 'hover:bg-slate-800' ?> transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-slate-400">PROPIEDADES</p>
            
            <a href="?action=properties" class="flex items-center gap-3 px-4 py-2 rounded-lg <?= $action === 'properties' && $sectionType === 'propiedades' ? 'bg-blue-600' : 'hover:bg-slate-800' ?> transition text-sm">
                <span>🏠</span> Propiedades <span class="ml-auto bg-slate-700 px-2 py-0.5 rounded text-xs"><?= $propiedadesCount ?></span>
            </a>
            <a href="?action=properties&type=terrenos" class="flex items-center gap-3 px-4 py-2 rounded-lg <?= $action === 'properties' && $sectionType === 'terrenos' ? 'bg-blue-600' : 'hover:bg-slate-800' ?> transition text-sm">
                <span>📍</span> Terrenos <span class="ml-auto bg-slate-700 px-2 py-0.5 rounded text-xs"><?= $terrenosCount ?></span>
            </a>
            <a href="?action=properties&type=activos" class="flex items-center gap-3 px-4 py-2 rounded-lg <?= $action === 'properties' && $sectionType === 'activos' ? 'bg-blue-600' : 'hover:bg-slate-800' ?> transition text-sm">
                <span>⚙️</span> Activos <span class="ml-auto bg-slate-700 px-2 py-0.5 rounded text-xs"><?= $activosCount ?></span>
            </a>
            <a href="?action=properties&type=usa" class="flex items-center gap-3 px-4 py-2 rounded-lg <?= $action === 'properties' && $sectionType === 'usa' ? 'bg-blue-600' : 'hover:bg-slate-800' ?> transition text-sm">
                <span>🇺🇸</span> Prop. USA <span class="ml-auto bg-slate-700 px-2 py-0.5 rounded text-xs"><?= $usaCount ?></span>
            </a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-slate-400">AGREGAR</p>
            
            <a href="?action=add&type=propiedades" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm">
                <span class="text-green-400">+</span> Nueva Propiedad
            </a>
            <a href="?action=add&type=terrenos" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm">
                <span class="text-green-400">+</span> Nuevo Terreno
            </a>
            <a href="?action=add&type=activos" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm">
                <span class="text-green-400">+</span> Nuevo Activo
            </a>
            <a href="?action=add&type=usa" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm">
                <span class="text-green-400">+</span> Nueva Prop. USA
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
        
        <?php if ($action === 'dashboard'): ?>
        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Bienvenido, <?= htmlspecialchars(explode(' ', $_SESSION['name'])[0]) ?></h1>
            <p class="text-gray-600">Gestiona tus propiedades de forma simple y eficiente</p>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border-l-4 border-blue-600 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Propiedades</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $totalProperties ?></p>
                    </div>
                    <div class="text-blue-200 text-4xl">🏠</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border-l-4 border-green-600 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Activas</p>
                        <p class="text-3xl font-bold text-green-600"><?= $activeProperties ?></p>
                    </div>
                    <div class="text-green-200 text-4xl">✓</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md p-6 border-l-4 border-amber-600 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Terrenos</p>
                        <p class="text-3xl font-bold text-amber-600"><?= $terrenosCount ?></p>
                    </div>
                    <div class="text-amber-200 text-4xl">📍</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 border-l-4 border-purple-600 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">USA</p>
                        <p class="text-3xl font-bold text-purple-600"><?= $usaCount ?></p>
                    </div>
                    <div class="text-purple-200 text-4xl">🇺🇸</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-t-4 border-blue-600">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rapidas</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="?action=add&type=propiedades" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-center">
                    <span class="block">+ Propiedad</span>
                    <span class="text-xs opacity-90">Residencial/Comercial</span>
                </a>
                <a href="?action=add&type=terrenos" class="px-4 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium text-center">
                    <span class="block">+ Terreno</span>
                    <span class="text-xs opacity-90">Terreno disponible</span>
                </a>
                <a href="?action=add&type=activos" class="px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium text-center">
                    <span class="block">+ Activo</span>
                    <span class="text-xs opacity-90">Maquinaria/Equipo</span>
                </a>
                <a href="?action=add&type=usa" class="px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-center">
                    <span class="block">+ Prop. USA</span>
                    <span class="text-xs opacity-90">Propiedad internacional</span>
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-blue-600">
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Propiedades Recientes</h2>
                <a href="?action=properties" class="text-blue-100 text-sm hover:text-white font-medium">Ver todas →</a>
            </div>
            <?php $recentProps = array_slice($myProperties, 0, 5); ?>
            <?php if (empty($recentProps)): ?>
                <div class="p-12 text-center">
                    <div class="text-5xl mb-3">📋</div>
                    <p class="text-gray-600 font-medium">Sin propiedades aun</p>
                    <p class="text-gray-500 text-sm mt-1">Comienza agregando tu primera propiedad</p>
                    <a href="?action=add&type=propiedades" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Agregar Primera Propiedad</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 text-xs text-gray-700 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">Propiedad</th>
                                <th class="px-6 py-4 text-left hidden md:table-cell">Tipo</th>
                                <th class="px-6 py-4 text-right">Precio</th>
                                <th class="px-6 py-4 text-center hidden sm:table-cell">Estado</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($recentProps as $p): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-6 py-4">
                                    <a href="?action=edit&id=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium"><?= htmlspecialchars(substr($p['title'], 0, 40)) ?></a>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><?= $sectionLabels[$p['section_type'] ?? 'propiedades'] ?? 'Propiedades' ?></span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 text-right"><?= formatPrice($p['price']) ?></td>
                                <td class="px-6 py-4 hidden sm:table-cell text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $p['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $p['is_active'] ? '● Activa' : '● Inactiva' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="?action=edit&id=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php elseif ($action === 'properties'): ?>
        <?php
            $currentSection = $_GET['type'] ?? 'propiedades';
            $filteredProps = array_filter($myProperties, fn($p) => ($p['section_type'] ?? 'propiedades') === $currentSection);
        ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= $sectionLabels[$currentSection] ?? 'Propiedades' ?></h1>
                <p class="text-gray-600 text-sm mt-1">Gestiona todas tus <?= strtolower($sectionLabels[$currentSection] ?? 'propiedades') ?></p>
            </div>
            <a href="?action=add&type=<?= $currentSection ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium transition">+ Agregar <?= $sectionLabels[$currentSection] ?? 'Propiedad' ?></a>
        </div>
        
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <a href="?action=properties" class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= $currentSection === 'propiedades' ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">Propiedades (<?= $propiedadesCount ?>)</a>
            <a href="?action=properties&type=terrenos" class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= $currentSection === 'terrenos' ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">Terrenos (<?= $terrenosCount ?>)</a>
            <a href="?action=properties&type=activos" class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= $currentSection === 'activos' ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">Activos (<?= $activosCount ?>)</a>
            <a href="?action=properties&type=usa" class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= $currentSection === 'usa' ? 'bg-blue-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">USA (<?= $usaCount ?>)</a>
        </div>
        
        <?php if (empty($filteredProps)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center border-t-4 border-gray-300">
                <div class="text-5xl mb-3">📭</div>
                <p class="text-gray-700 font-medium text-lg">No tienes <?= strtolower($sectionLabels[$currentSection] ?? 'propiedades') ?> registradas</p>
                <p class="text-gray-500 text-sm mt-2">Comienza agregando tu primera <?= strtolower($sectionLabels[$currentSection] ?? 'propiedad') ?></p>
                <a href="?action=add&type=<?= $currentSection ?>" class="inline-block mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Agregar Ahora</a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-blue-600">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 text-xs text-gray-700 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">Titulo</th>
                                <th class="px-6 py-4 text-left hidden md:table-cell">Tipo</th>
                                <th class="px-6 py-4 text-right">Precio</th>
                                <th class="px-6 py-4 text-center hidden sm:table-cell">Estado</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($filteredProps as $p): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars(substr($p['title'], 0, 45)) ?></td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($p['property_type'] ?? '-') ?></span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 text-right"><?= formatPrice($p['price']) ?></td>
                                <td class="px-6 py-4 hidden sm:table-cell text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $p['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $p['is_active'] ? '● Activa' : '● Inactiva' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 justify-center">
                                        <a href="?action=edit&id=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</a>
                                        <form method="post" class="inline" onsubmit="return confirm('¿Eliminar esta propiedad?')">
                                            <input type="hidden" name="action" value="delete_property">
                                            <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="section_type" value="<?= $currentSection ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <?php
            $formSection = $_GET['type'] ?? ($editProperty['section_type'] ?? 'propiedades');
            $isEdit = $action === 'edit';
        ?>
        <div class="mb-8">
            <a href="?action=properties&type=<?= $formSection ?>" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium mb-3">
                <span>←</span> Volver a <?= $sectionLabels[$formSection] ?? 'Propiedades' ?>
            </a>
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900"><?= $isEdit ? '✏️ Editar' : '➕ Agregar' ?> <?= $sectionLabels[$formSection] ?? 'Propiedad' ?></h1>
            <p class="text-gray-600 text-sm mt-2">Completa todos los campos para <?= $isEdit ? 'actualizar' : 'crear' ?> tu <?= strtolower($sectionLabels[$formSection] ?? 'propiedad') ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8 border-t-4 border-blue-600">
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="<?= $isEdit ? 'update_property' : 'create_property' ?>">
                <input type="hidden" name="section_type" value="<?= htmlspecialchars($formSection) ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="property_id" value="<?= $editProperty['id'] ?>">
                <?php endif; ?>
                
                <?php if ($formSection === 'terrenos'): ?>
                <?php include __DIR__ . '/forms/terreno_form.php'; ?>
                
                <?php elseif ($formSection === 'usa'): ?>
                <?php include __DIR__ . '/forms/usa_form.php'; ?>
                
                <?php elseif ($formSection === 'activos'): ?>
                <?php include __DIR__ . '/forms/activo_form.php'; ?>
                
                <?php else: ?>
                <?php include __DIR__ . '/forms/propiedad_form.php'; ?>
                <?php endif; ?>
                
                <div class="flex gap-3 mt-10 pt-8 border-t">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md">
                        <?= $isEdit ? '💾 Guardar Cambios' : '✨ Crear ' . ($sectionLabels[$formSection] ?? 'Propiedad') ?>
                    </button>
                    <a href="?action=properties&type=<?= $formSection ?>" class="px-8 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition">Cancelar</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
    </main>
</div>

<script>
function deletePhoto(photoId, button) {
    if (!confirm('¿Eliminar esta foto?')) return;
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'delete_photo_ajax=1&photo_id=' + photoId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            button.closest('.photo-item').remove();
        }
    });
}

document.querySelectorAll('[id^="region_id"]').forEach(regionSelect => {
    const comunaId = regionSelect.id.replace('region_id', 'comuna_id');
    const comunaSelect = document.getElementById(comunaId);
    
    if (regionSelect && comunaSelect) {
        regionSelect.addEventListener('change', function() {
            comunaSelect.innerHTML = '<option value="">Cargando...</option>';
            if (this.value) {
                fetch('../api/comunas.php?region_id=' + this.value)
                    .then(r => r.json())
                    .then(data => {
                        comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
                        data.forEach(c => {
                            comunaSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                        });
                    });
            } else {
                comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
            }
        });
    }
});
</script>

</body>
</html>
