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

requirePartner();

$propertyModel = new PropertyModel();
$photoModel = new PhotoModel();
$locationModel = new LocationModel();
$propertyTypeModel = new PropertyTypeModel();
$propertyDetailsModel = new PropertyDetailsModel();

$action = $_GET['action'] ?? 'dashboard';
$propertyId = (int)($_GET['id'] ?? 0);

$regions = $locationModel->getRegions();
$propertyTypes = $propertyTypeModel->getAll();

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
        $propertyId = $propertyModel->create([
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description']),
            'property_type' => sanitizeInput($_POST['property_type'] ?? ''),
            'operation_type' => sanitizeInput($_POST['operation_type']),
            'price' => (float)$_POST['price'],
            'currency' => 'CLP',
            'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
            'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
            'built_area' => (float)($_POST['built_area'] ?? 0),
            'total_area' => (float)($_POST['total_area'] ?? 0),
            'parking_spots' => (int)($_POST['parking_spots'] ?? 0),
            'address' => sanitizeInput($_POST['address']),
            'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
            'region_id' => !empty($_POST['region_id']) ? (int)$_POST['region_id'] : null,
            'images' => json_encode([]),
            'is_featured' => 0,
            'is_active' => 1,
            'partner_id' => $_SESSION['user_id'],
            'section_type' => sanitizeInput($_POST['section_type'] ?? 'propiedades'),
            'property_category' => sanitizeInput($_POST['property_category'] ?? '')
        ]);
        
        if ($propertyId) {
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
                'section_type' => sanitizeInput($_POST['section_type'] ?? 'propiedades'),
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
                        $fullPath = $uploadDir . $filename;
                        if (@move_uploaded_file($_FILES['property_photos']['tmp_name'][$i], $fullPath)) {
                            @chmod($fullPath, 0644);
                            $photoModel->create($propertyId, '../uploads/properties/' . $filename, $displayOrder);
                            $displayOrder++;
                        }
                    }
                }
            }
        }
        
        header('Location: index.php?action=properties');
        exit;
    }

    // ---------- CREATE SPECIAL PROPERTY (partner) ----------
    if ($postAction === 'create_special_property') {
        $specialType = sanitizeInput($_POST['special_type'] ?? 'propiedades');
        $propertyId = $propertyModel->create([
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description']),
            'property_type' => sanitizeInput($_POST['property_type'] ?? ''),
            'operation_type' => sanitizeInput($_POST['operation_type'] ?? ''),
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
            'images' => json_encode([]),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'partner_id' => $_SESSION['user_id'],
            'section_type' => $specialType,
            'property_category' => sanitizeInput($_POST['property_category'] ?? '')
        ]);

        if ($propertyId) {
            $details = [];
            if ($specialType === 'terrenos') {
                $details['land_area'] = sanitizeInput($_POST['land_area'] ?? '');
                $details['zoning'] = sanitizeInput($_POST['zoning'] ?? '');
            } elseif ($specialType === 'activos') {
                $details['asset_condition'] = sanitizeInput($_POST['asset_condition'] ?? '');
                $details['brand'] = sanitizeInput($_POST['brand'] ?? '');
            } elseif ($specialType === 'usa') {
                $details['mls_id'] = sanitizeInput($_POST['mls_id'] ?? '');
                $details['state'] = sanitizeInput($_POST['state'] ?? '');
                $details['currency'] = sanitizeInput($_POST['currency'] ?? 'USD');
            }

            $propertyDetailsModel->save($propertyId, [
                'property_category' => sanitizeInput($_POST['property_category'] ?? ''),
                'section_type' => $specialType,
                'details' => $details,
                'features' => $_POST['property_features'] ?? [],
                'costs' => $_POST['costs'] ?? []
            ]);
        }

        header('Location: ?action=special_list&type=' . urlencode($specialType));
        exit;
    }
    
    if ($postAction === 'update_property' && !empty($_POST['property_id'])) {
        $propertyId = (int)$_POST['property_id'];
        $property = $propertyModel->getById($propertyId);
        if ($property && $property['partner_id'] == $_SESSION['user_id']) {
            $propertyModel->update($propertyId, [
                'title' => sanitizeInput($_POST['title']),
                'description' => sanitizeInput($_POST['description']),
                'property_type' => sanitizeInput($_POST['property_type'] ?? ''),
                'operation_type' => sanitizeInput($_POST['operation_type']),
                'price' => (float)$_POST['price'],
                'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
                'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
                'built_area' => (float)($_POST['built_area'] ?? 0),
                'total_area' => (float)($_POST['total_area'] ?? 0),
                'parking_spots' => (int)($_POST['parking_spots'] ?? 0),
                'address' => sanitizeInput($_POST['address']),
                'comuna_id' => !empty($_POST['comuna_id']) ? (int)$_POST['comuna_id'] : null,
                'region_id' => !empty($_POST['region_id']) ? (int)$_POST['region_id'] : null,
                'images' => json_encode([]),
                'section_type' => sanitizeInput($_POST['section_type'] ?? 'propiedades'),
                'property_category' => sanitizeInput($_POST['property_category'] ?? '')
            ]);
            
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
                'section_type' => sanitizeInput($_POST['section_type'] ?? 'propiedades'),
                'details' => $detailsData,
                'features' => $featuresData,
                'costs' => $costsData
            ]);
            
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
                            $fullPath = $uploadDir . $filename;
                            if (@move_uploaded_file($_FILES['property_photos']['tmp_name'][$i], $fullPath)) {
                                @chmod($fullPath, 0644);
                                $photoModel->create($propertyId, '../uploads/properties/' . $filename, $displayOrder);
                                $displayOrder++;
                            }
                        }
                    }
                }
            }
        }
        header('Location: ?action=properties');
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
        header('Location: ?action=properties');
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
$featuredProperties = count(array_filter($myProperties, fn($p) => $p['is_featured']));

$editProperty = null;
$editPropertyDetails = ['details' => [], 'features' => [], 'costs' => []];
if ($action === 'edit' && $propertyId) {
    $editProperty = $propertyModel->getById($propertyId);
    if (!$editProperty || $editProperty['partner_id'] != $_SESSION['user_id']) {
        header('Location: ?action=properties');
        exit;
    }
    $editPropertyDetails = $propertyDetailsModel->getByPropertyId($propertyId);
}

$propertyCategories = PropertyDetailsModel::getPropertyCategories();
$sectionTypes = PropertyDetailsModel::getSectionTypes();

// If a quick-add section is provided, preselect it when opening the add form
$sectionParam = $_GET['section'] ?? null;
if ($action === 'add' && $sectionParam && in_array($sectionParam, array_keys($sectionTypes))) {
    $editProperty = ['section_type' => $sectionParam];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Socio - UrbanPropiedades</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <a href="../index.php" class="flex items-center gap-2 text-2xl font-bold text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
            </svg>
            Urban Group
        </a>
        <div class="flex items-center gap-4">
            <a href="?action=special_list&type=terrenos" class="px-3 py-2 text-sm font-medium border border-gray-200 rounded hover:bg-gray-50">Terrenos</a>
            <a href="?action=add_special&type=terrenos" class="px-3 py-2 text-sm font-medium bg-blue-600 text-white rounded hover:bg-blue-700">Agregar Terreno</a>
            <a href="?action=special_list&type=activos" class="px-3 py-2 text-sm font-medium border border-gray-200 rounded hover:bg-gray-50">Activos</a>
            <a href="?action=add_special&type=activos" class="px-3 py-2 text-sm font-medium bg-green-600 text-white rounded hover:bg-green-700">Agregar Activo</a>
            <a href="?action=special_list&type=usa" class="px-3 py-2 text-sm font-medium border border-gray-200 rounded hover:bg-gray-50">Prop. USA</a>
            <a href="?action=add_special&type=usa" class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded hover:bg-indigo-700">Agregar USA</a>
            <a href="../index.php" class="px-4 py-2 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Ir al Sitio</a>
            <a href="../logout.php" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Cerrar</a>
        </div>
    </div>
</header>

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white">
        <div class="p-6 border-b border-slate-700">
            <h3 class="text-xs font-semibold text-slate-400 mb-2">MI CUENTA</h3>
            <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        
        <nav class="p-4 space-y-1">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'dashboard' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-4 2l-4-2"/>
                </svg>
                Dashboard
            </a>
            <a href="?action=properties" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= in_array($action, ['properties', 'edit']) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Mis Propiedades
            </a>
            <a href="?action=add" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'add' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Propiedad
            </a>
            <!-- Secciones Especiales -->
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-slate-400 mb-2">SECCIONES ESPECIALES</p>
                <div class="space-y-1">
                    <a href="?action=special_list&type=terrenos" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>📍 Terrenos</span>
                    </a>
                    <a href="?action=special_list&type=activos" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>⚙️ Activos</span>
                    </a>
                    <a href="?action=special_list&type=usa" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>🇺🇸 Prop. USA</span>
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
        <?php if ($action === 'special_list' && isset($_GET['type'])):
            $specialType = $_GET['type'];
            $filtered = array_values(array_filter($myProperties, fn($p) => ($p['section_type'] ?? 'propiedades') === $specialType));
        ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-6"><?= htmlspecialchars(ucfirst($specialType)) ?> - Mis Propiedades</h1>
            <div class="mb-4">
                <a href="?action=add_special&type=<?= urlencode($specialType) ?>" class="px-4 py-2 bg-blue-600 text-white rounded">Agregar <?= htmlspecialchars(ucfirst($specialType)) ?></a>
                <a href="?action=properties" class="ml-3 px-3 py-2 border rounded">Volver a Todas</a>
            </div>
            <?php if (empty($filtered)): ?>
                <div class="bg-white rounded-lg shadow p-6 text-center">No tienes propiedades en esta sección.</div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Propiedad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered as $property): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4"><?= htmlspecialchars($property['title']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($property['property_type'] ?? '') ?></td>
                                    <td class="px-6 py-4 font-bold"><?= formatPrice($property['price']) ?></td>
                                    <td class="px-6 py-4"><?= $property['is_active'] ? 'Activa' : 'Inactiva' ?></td>
                                    <td class="px-6 py-4">
                                        <a href="?action=edit&id=<?= $property['id'] ?>" class="text-blue-600">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php elseif ($action === 'add_special' && isset($_GET['type'])):
            $specialType = $_GET['type'];
            $editProperty = $editProperty ?? ['section_type' => $specialType];
        ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Agregar <?= htmlspecialchars(ucfirst($specialType)) ?></h1>
            <div class="bg-white rounded-lg shadow p-6">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create_special_property">
                    <input type="hidden" name="special_type" value="<?= htmlspecialchars($specialType) ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Título</label>
                            <input name="title" required class="w-full px-4 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Precio</label>
                            <input name="price" type="number" step="any" class="w-full px-4 py-2 border rounded" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Comuna</label>
                            <select name="comuna_id" class="w-full px-4 py-2 border rounded">
                                <option value="">Seleccionar</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php if ($specialType === 'terrenos'): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Superficie (m2)</label>
                                <input name="land_area" class="w-full px-4 py-2 border rounded" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Zonificación</label>
                                <input name="zoning" class="w-full px-4 py-2 border rounded" />
                            </div>
                        </div>
                    <?php elseif ($specialType === 'activos'): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Condición</label>
                                <input name="asset_condition" class="w-full px-4 py-2 border rounded" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Marca</label>
                                <input name="brand" class="w-full px-4 py-2 border rounded" />
                            </div>
                        </div>
                    <?php elseif ($specialType === 'usa'): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">MLS ID</label>
                                <input name="mls_id" class="w-full px-4 py-2 border rounded" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Estado (State)</label>
                                <input name="state" class="w-full px-4 py-2 border rounded" />
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Crear <?= htmlspecialchars(ucfirst($specialType)) ?></button>
                        <a href="?action=special_list&type=<?= urlencode($specialType) ?>" class="ml-3 px-3 py-2 border rounded">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'dashboard'): ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Mi Dashboard</h1>
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Mis Propiedades</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $totalProperties ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Activas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $activeProperties ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-amber-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Destacadas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $featuredProperties ?></p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions: Add property by section -->
            <div class="bg-white rounded-lg shadow p-4 lg:p-6 mb-8">
                <h3 class="text-lg font-semibold mb-3">Agregar Propiedad Rápida</h3>
                <div class="flex gap-3">
                    <a href="?action=add&section=terrenos" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo Terreno</a>
                    <a href="?action=add&section=activos" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Nuevo Activo</a>
                    <a href="?action=add&section=usa" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Nueva Propiedad USA</a>
                </div>
            </div>

            <!-- Recent Properties -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Mis Propiedades Recientes</h2>
                    <a href="?action=add" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">+ Nueva</a>
                </div>
                
                <?php if (empty($myProperties)): ?>
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No tienes propiedades</h3>
                        <p class="text-gray-600 mb-4">Agrega tu primera propiedad para empezar a publicar.</p>
                        <a href="?action=add" class="inline-block px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">Agregar Propiedad</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                        <?php foreach (array_slice($myProperties, 0, 3) as $property): ?>
                            <?php 
                            $propPhotos = $photoModel->getByPropertyId($property['id']);
                            $thumbImg = !empty($propPhotos) ? $propPhotos[0]['photo_url'] : getFirstImage($property['images']);
                            ?>
                            <a href="../propiedad.php?id=<?= $property['id'] ?>" class="group">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        <img src="<?= getPropertyPhotoUrl($thumbImg) ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 line-clamp-1"><?= htmlspecialchars(truncateText($property['title'], 40)) ?></h3>
                                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                        <p class="text-sm font-bold text-gray-900 mt-2"><?= formatPrice($property['price']) ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($action === 'properties'): ?>
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mis Propiedades</h1>
                <a href="?action=add" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">+ Nueva Propiedad</a>
            </div>
            
            <?php if (empty($myProperties)): ?>
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No tienes propiedades</h3>
                    <p class="text-gray-600">Agrega tu primera propiedad</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Propiedad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myProperties as $property): ?>
                                <?php 
                                $propPhotos = $photoModel->getByPropertyId($property['id']);
                                $thumbImg2 = !empty($propPhotos) ? $propPhotos[0]['photo_url'] : getFirstImage($property['images']);
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= getPropertyPhotoUrl($thumbImg2) ?>" class="w-12 h-10 object-cover rounded" alt="">
                                            <div>
                                                <p class="font-medium text-gray-900"><?= htmlspecialchars(truncateText($property['title'], 40)) ?></p>
                                                <p class="text-xs text-gray-600"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($property['property_type'] ?? 'N/A') ?></td>
                                    <td class="px-6 py-4 font-medium"><?= formatPrice($property['price']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-2 py-1 <?= $property['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> text-xs font-semibold rounded">
                                            <?= $property['is_active'] ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 space-x-2">
                                        <a href="?action=edit&id=<?= $property['id'] ?>" class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition">Editar</a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_property">
                                            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                            <button type="submit" onclick="return confirm('¿Eliminar propiedad?')" class="inline-block px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-8"><?= $action === 'edit' ? 'Editar Propiedad' : 'Agregar Nueva Propiedad' ?></h1>
            
            <div class="bg-white rounded-lg shadow p-8 max-w-4xl">
                <form method="POST" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $action === 'edit' ? 'update_property' : 'create_property' ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="property_id" value="<?= $editProperty['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-blue-800 mb-3">Clasificación de la Propiedad</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sección *</label>
                                <select name="section_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                                    <?php foreach ($sectionTypes as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= (($editProperty['section_type'] ?? 'propiedades') === $key) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría de Propiedad *</label>
                                <select name="property_category" id="propertyCategorySelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white" onchange="updateDynamicFields()">
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($propertyCategories as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= (($editProperty['property_category'] ?? ($editPropertyDetails['property_category'] ?? '')) === $key) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                      <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Propiedad *</label>
                            <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Seleccionar</option>
                                <?php foreach ($propertyTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type['name']) ?>" <?= (isset($editProperty['property_type']) && $editProperty['property_type'] === $type['name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Operación *</label>
                            <select name="operation_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Venta" <?= ($editProperty['operation_type'] ?? '') === 'Venta' ? 'selected' : '' ?>>Venta</option>
                                <option value="Arriendo" <?= ($editProperty['operation_type'] ?? '') === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                            <input type="number" name="price" value="<?= $editProperty['price'] ?? '' ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Región</label>
                            <select name="region_id" id="regionSelectPartner" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= $region['id'] ?>" <?= ($editProperty['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>><?= htmlspecialchars($region['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comuna</label>
                            <select name="comuna_id" id="comunaSelectPartner" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar</option>
                                <?php if (!empty($editProperty['region_id'])): ?>
                                    <?php $comunas = $locationModel->getComunas($editProperty['region_id']); foreach ($comunas as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($editProperty['comuna_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; endif; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dormitorios</label>
                            <input type="number" name="bedrooms" value="<?= $editProperty['bedrooms'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Baños</label>
                            <input type="number" name="bathrooms" value="<?= $editProperty['bathrooms'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Construida (m²)</label>
                            <input type="number" name="built_area" value="<?= $editProperty['built_area'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Superficie Total (m²)</label>
                            <input type="number" name="total_area" value="<?= $editProperty['total_area'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estacionamientos</label>
                            <input type="number" name="parking_spots" value="<?= $editProperty['parking_spots'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($editProperty['address'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fotos de la Propiedad (hasta 12)</label>
                            <?php if ($action === 'edit' && isset($editProperty['id'])): ?>
                                <?php $propertyPhotos = $photoModel->getByPropertyId($editProperty['id']); ?>
                                <?php if (!empty($propertyPhotos)): ?>
                                    <div class="mb-4">
                                        <p class="text-xs font-medium text-gray-600 mb-2">Fotos Actuales (<?= count($propertyPhotos) ?>):</p>
                                        <div class="space-y-3">
                                            <?php foreach ($propertyPhotos as $index => $photo): ?>
                                                <div class="flex gap-2 items-center bg-gray-50 p-2 rounded-lg">
                                                    <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden border border-gray-200">
                                                        <img src="<?= getPropertyPhotoUrl($photo['photo_url']) ?>" alt="Foto <?= $index + 1 ?>" class="w-full h-full object-cover">
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs text-gray-600">Orden: <span class="font-bold"><?= $index + 1 ?></span></p>
                                                        <p class="text-xs text-gray-500 truncate"><?= basename($photo['photo_url']) ?></p>
                                                    </div>
                                                    <div class="flex flex-col gap-1">
                                                        <?php if ($index > 0): ?>
                                                            <a href="?action=edit&id=<?= $editProperty['id'] ?>&move_photo=<?= $photo['id'] ?>&move_to=<?= $index - 1 ?>" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 text-center">↑</a>
                                                        <?php endif; ?>
                                                        <button type="button" onclick="deletePhoto(<?= $photo['id'] ?>, this)" class="px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 text-center">Eliminar</button>
                                                        <?php if ($index < count($propertyPhotos) - 1): ?>
                                                            <a href="?action=edit&id=<?= $editProperty['id'] ?>&move_photo=<?= $photo['id'] ?>&move_to=<?= $index + 1 ?>" class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 text-center">↓</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition">
                                <input type="file" name="property_photos[]" multiple accept="image/jpeg,image/png" class="hidden" id="propertyPhotosInput">
                                <label for="propertyPhotosInput" class="cursor-pointer block">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700">Haz clic para subir fotos</p>
                                    <p class="text-xs text-gray-500">JPG o PNG (máx 12 fotos)</p>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-2" id="photoCount">Ninguna foto seleccionada</p>
                        </div>
                        
                        <script>
                        document.getElementById('propertyPhotosInput').addEventListener('change', function() {
                            const count = this.files.length;
                            document.getElementById('photoCount').textContent = count === 0 ? 'Ninguna foto seleccionada' : count + ' foto(s) seleccionada(s)';
                        });
                        </script>
                    </div>
                    
                    <div id="dynamicFieldsSection" class="mt-6 border-t border-gray-200 pt-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Datos Específicos de la Propiedad</h3>
                        <div id="dynamicFieldsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                        
                        <div id="dynamicCostsSection" class="mt-6 hidden">
                            <h4 class="text-md font-semibold text-gray-800 mb-3">Costos</h4>
                            <div id="dynamicCostsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                        </div>
                        
                        <div id="dynamicFeaturesSection" class="mt-6 hidden">
                            <h4 class="text-md font-semibold text-gray-800 mb-3">Características (SI/NO)</h4>
                            <div id="dynamicFeaturesContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"></div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            <?= $action === 'edit' ? 'Guardar Cambios' : 'Crear Propiedad' ?>
                        </button>
                        <a href="?action=properties" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function deletePhoto(photoId, button) {
    if (!confirm('¿Eliminar esta foto?')) return;
    
    fetch('?delete_photo_ajax=1&photo_id=' + photoId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'delete_photo_ajax=1&photo_id=' + photoId
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const photoDiv = button.closest('.flex.gap-2.items-center');
                photoDiv.style.opacity = '0';
                photoDiv.style.transition = 'opacity 0.3s ease';
                setTimeout(() => photoDiv.remove(), 300);
            }
        });
}
</script>

<script>
    const regionSelect = document.getElementById('regionSelectPartner');
    const comunaSelect = document.getElementById('comunaSelectPartner');

    if (regionSelect && comunaSelect) {
        regionSelect.addEventListener('change', function() {
            const regionId = this.value;
            comunaSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!regionId) {
                comunaSelect.innerHTML = '<option value="">Seleccionar</option>';
                return;
            }

            fetch(`../../api/comunas.php?region_id=${regionId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccionar</option>';
                    if (data && data.length > 0) {
                        data.forEach(comuna => {
                            options += `<option value="${comuna.id}">${comuna.name}</option>`;
                        });
                    }
                    comunaSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error('Error fetching comunas:', error);
                    comunaSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    }
</script>

<script>
const categoryFields = {
    'casa': {
        details: [
            {name: 'n_pisos', label: 'N° de Pisos', type: 'number'},
            {name: 'superficie_construida', label: 'Superficie Construida (m²)', type: 'number'},
            {name: 'superficie_total', label: 'Superficie Total (m²)', type: 'number'},
            {name: 'n_dormitorios', label: 'N° Dormitorios', type: 'number'},
            {name: 'n_banos', label: 'N° Baños', type: 'number'},
            {name: 'n_estacionamientos', label: 'N° Estacionamientos', type: 'number'},
            {name: 'orientacion', label: 'Orientación', type: 'select', options: ['Norte', 'Sur', 'Este', 'Oeste', 'Noreste', 'Noroeste', 'Sureste', 'Suroeste']},
            {name: 'ano_construccion', label: 'Año de Construcción', type: 'number'}
        ],
        features: ['Sala de Estar', 'Comedor', 'Living', 'Cocina', 'Logia', 'Bodega', 'Piscina', 'Quincho', 'Jardín', 'Portón Eléctrico', 'Calefacción Central', 'Aire Acondicionado', 'Alarma', 'Citófono'],
        costs: ['contribuciones', 'gastos_comunes']
    },
    'departamento': {
        details: [
            {name: 'piso', label: 'Piso del Departamento', type: 'number'},
            {name: 'superficie_util', label: 'Superficie Útil (m²)', type: 'number'},
            {name: 'superficie_terraza', label: 'Superficie Terraza (m²)', type: 'number'},
            {name: 'n_dormitorios', label: 'N° Dormitorios', type: 'number'},
            {name: 'n_banos', label: 'N° Baños', type: 'number'},
            {name: 'n_estacionamientos', label: 'N° Estacionamientos', type: 'number'},
            {name: 'n_bodegas', label: 'N° Bodegas', type: 'number'},
            {name: 'orientacion', label: 'Orientación', type: 'select', options: ['Norte', 'Sur', 'Este', 'Oeste', 'Noreste', 'Noroeste', 'Sureste', 'Suroeste']},
            {name: 'ano_construccion', label: 'Año de Construcción', type: 'number'}
        ],
        features: ['Sala de Estar', 'Comedor', 'Living', 'Cocina Americana', 'Logia', 'Terraza', 'Balcón', 'Calefacción Central', 'Aire Acondicionado', 'Gimnasio', 'Piscina Común', 'Sala de Eventos', 'Conserjería 24hrs', 'Citófono'],
        costs: ['contribuciones', 'gastos_comunes']
    },
    'oficina': {
        details: [
            {name: 'piso', label: 'Piso', type: 'number'},
            {name: 'superficie_util', label: 'Superficie Útil (m²)', type: 'number'},
            {name: 'n_banos', label: 'N° Baños', type: 'number'},
            {name: 'n_estacionamientos', label: 'N° Estacionamientos', type: 'number'},
            {name: 'ano_construccion', label: 'Año de Construcción', type: 'number'}
        ],
        features: ['Recepción', 'Sala de Reuniones', 'Cocina/Kitchenette', 'Bodega', 'Aire Acondicionado', 'Calefacción', 'Piso Flotante', 'Cielo Modular', 'Ascensor', 'Conserjería'],
        costs: ['contribuciones', 'gastos_comunes']
    },
    'bodega': {
        details: [
            {name: 'superficie_cubierta', label: 'Superficie Cubierta (m²)', type: 'number'},
            {name: 'superficie_patio', label: 'Superficie Patio (m²)', type: 'number'},
            {name: 'altura_util', label: 'Altura Útil (m)', type: 'number'},
            {name: 'capacidad_carga', label: 'Capacidad de Carga (kg/m²)', type: 'number'},
            {name: 'n_accesos', label: 'N° Accesos Vehiculares', type: 'number'}
        ],
        features: ['Galpón', 'Oficinas', 'Baños', 'Portón Industrial', 'Andén de Carga', 'Sistema Contra Incendios', 'Vigilancia 24hrs', 'Patio de Maniobras'],
        costs: ['contribuciones', 'gastos_comunes']
    },
    'local_comercial': {
        details: [
            {name: 'superficie_local', label: 'Superficie Local (m²)', type: 'number'},
            {name: 'superficie_bodega', label: 'Superficie Bodega (m²)', type: 'number'},
            {name: 'frente_vitrina', label: 'Frente/Vitrina (m)', type: 'number'},
            {name: 'n_banos', label: 'N° Baños', type: 'number'},
            {name: 'n_estacionamientos', label: 'N° Estacionamientos', type: 'number'}
        ],
        features: ['Vitrina', 'Bodega', 'Baño Clientes', 'Baño Personal', 'Cortina Metálica', 'Aire Acondicionado', 'Sistema Seguridad', 'Acceso Discapacitados'],
        costs: ['contribuciones', 'gastos_comunes', 'arriendo_mensual']
    },
    'parcela_con_casa': {
        details: [
            {name: 'superficie_terreno', label: 'Superficie Terreno (m²)', type: 'number'},
            {name: 'superficie_construida', label: 'Superficie Construida (m²)', type: 'number'},
            {name: 'n_dormitorios', label: 'N° Dormitorios', type: 'number'},
            {name: 'n_banos', label: 'N° Baños', type: 'number'},
            {name: 'tipo_agua', label: 'Tipo de Agua', type: 'select', options: ['APR', 'Pozo', 'Canal', 'Red Pública']},
            {name: 'tipo_electricidad', label: 'Electricidad', type: 'select', options: ['Monofásica', 'Trifásica', 'Solar', 'Sin conexión']}
        ],
        features: ['Casa Principal', 'Casa Cuidador', 'Galpón', 'Bodega', 'Quincho', 'Piscina', 'Huerto', 'Frutales', 'Corral', 'Riego Tecnificado', 'Derechos de Agua'],
        costs: ['contribuciones']
    },
    'parcela_sin_casa': {
        details: [
            {name: 'superficie_terreno', label: 'Superficie Terreno (m²)', type: 'number'},
            {name: 'tipo_suelo', label: 'Tipo de Suelo', type: 'select', options: ['Agrícola', 'Forestal', 'Mixto', 'Residencial']},
            {name: 'acceso_agua', label: 'Acceso a Agua', type: 'select', options: ['Sí', 'No', 'Factible']},
            {name: 'acceso_electricidad', label: 'Acceso a Electricidad', type: 'select', options: ['Sí', 'No', 'Factible']},
            {name: 'acceso_camino', label: 'Tipo de Acceso/Camino', type: 'select', options: ['Pavimentado', 'Ripio', 'Tierra', 'Servidumbre']}
        ],
        features: ['Derechos de Agua', 'Factibilidad Construcción', 'Cerco Perimetral', 'Portón de Acceso', 'Árboles', 'Vista Panorámica'],
        costs: ['contribuciones']
    },
    'terreno_industrial': {
        details: [
            {name: 'superficie_terreno', label: 'Superficie Terreno (m²)', type: 'number'},
            {name: 'frente_calle', label: 'Frente a Calle (m)', type: 'number'},
            {name: 'fondo_terreno', label: 'Fondo del Terreno (m)', type: 'number'},
            {name: 'uso_suelo', label: 'Uso de Suelo', type: 'select', options: ['Industrial', 'Bodegaje', 'Comercial', 'Mixto']},
            {name: 'capacidad_electrica', label: 'Capacidad Eléctrica (kVA)', type: 'number'}
        ],
        features: ['Urbanizado', 'Cierre Perimetral', 'Portón Vehicular', 'Alcantarillado', 'Gas Natural', 'Fibra Óptica', 'Guardianía'],
        costs: ['contribuciones']
    },
    'fundo': {
        details: [
            {name: 'superficie_hectareas', label: 'Superficie (Hectáreas)', type: 'number'},
            {name: 'superficie_regadio', label: 'Superficie Regadío (Ha)', type: 'number'},
            {name: 'superficie_secano', label: 'Superficie Secano (Ha)', type: 'number'},
            {name: 'derechos_agua', label: 'Derechos de Agua (L/s)', type: 'number'},
            {name: 'n_casas', label: 'N° Casas en el Predio', type: 'number'}
        ],
        features: ['Casa Patronal', 'Casa Cuidador', 'Galpones', 'Bodegas', 'Corrales', 'Sistema Riego', 'Maquinaria', 'Plantaciones', 'Ganado', 'Reservorio Agua'],
        costs: ['contribuciones']
    },
    'derechos_llave': {
        details: [
            {name: 'tipo_negocio', label: 'Tipo de Negocio', type: 'text'},
            {name: 'anos_funcionamiento', label: 'Años de Funcionamiento', type: 'number'},
            {name: 'facturacion_mensual', label: 'Facturación Mensual Promedio', type: 'number'},
            {name: 'n_empleados', label: 'N° de Empleados', type: 'number'},
            {name: 'superficie_local', label: 'Superficie Local (m²)', type: 'number'}
        ],
        features: ['Clientela Establecida', 'Marca Registrada', 'Página Web', 'Redes Sociales', 'Mobiliario', 'Equipamiento', 'Stock', 'Contratos Vigentes', 'Licencias/Patentes'],
        costs: ['arriendo_mensual', 'gastos_operacionales', 'patente_comercial']
    },
    'terreno_inmobiliario': {
        details: [
            {name: 'superficie_terreno', label: 'Superficie Terreno (m²)', type: 'number'},
            {name: 'frente_calle', label: 'Frente a Calle (m)', type: 'number'},
            {name: 'fondo_terreno', label: 'Fondo del Terreno (m)', type: 'number'},
            {name: 'uso_suelo', label: 'Uso de Suelo Permitido', type: 'select', options: ['Residencial', 'Comercial', 'Mixto', 'Industrial']},
            {name: 'coeficiente_constructibilidad', label: 'Coeficiente Constructibilidad', type: 'number'}
        ],
        features: ['Urbanizado', 'Factibilidad Agua', 'Factibilidad Eléctrica', 'Factibilidad Gas', 'Factibilidad Alcantarillado', 'Cerco', 'Esquina'],
        costs: ['contribuciones']
    }
};

const existingDetails = <?= json_encode($editPropertyDetails['details'] ?? []) ?>;
const existingFeatures = <?= json_encode($editPropertyDetails['features'] ?? []) ?>;
const existingCosts = <?= json_encode($editPropertyDetails['costs'] ?? []) ?>;

function updateDynamicFields() {
    const category = document.getElementById('propertyCategorySelect')?.value;
    const section = document.getElementById('dynamicFieldsSection');
    const container = document.getElementById('dynamicFieldsContainer');
    const costsSection = document.getElementById('dynamicCostsSection');
    const costsContainer = document.getElementById('dynamicCostsContainer');
    const featuresSection = document.getElementById('dynamicFeaturesSection');
    const featuresContainer = document.getElementById('dynamicFeaturesContainer');
    
    if (!category || !categoryFields[category]) {
        if (section) section.classList.add('hidden');
        return;
    }
    
    const config = categoryFields[category];
    section.classList.remove('hidden');
    
    container.innerHTML = '';
    config.details.forEach(field => {
        const existingValue = existingDetails[field.name] || '';
        let inputHtml = '';
        
        if (field.type === 'select') {
            inputHtml = `<select name="details[${field.name}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccionar</option>
                ${field.options.map(opt => `<option value="${opt}" ${existingValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>`;
        } else {
            inputHtml = `<input type="${field.type}" name="details[${field.name}]" value="${existingValue}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" ${field.type === 'number' ? 'step="any"' : ''}>`;
        }
        
        container.innerHTML += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">${field.label}</label>
                ${inputHtml}
            </div>
        `;
    });
    
    if (config.costs && config.costs.length > 0) {
        costsSection.classList.remove('hidden');
        costsContainer.innerHTML = '';
        const costLabels = {
            'contribuciones': 'Contribuciones (UF/año)',
            'gastos_comunes': 'Gastos Comunes (CLP/mes)',
            'arriendo_mensual': 'Arriendo Mensual (CLP)',
            'gastos_operacionales': 'Gastos Operacionales (CLP/mes)',
            'patente_comercial': 'Patente Comercial (CLP/año)'
        };
        config.costs.forEach(cost => {
            const existingValue = existingCosts[cost] || '';
            costsContainer.innerHTML += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">${costLabels[cost] || cost}</label>
                    <input type="number" name="costs[${cost}]" value="${existingValue}" step="any" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            `;
        });
    } else {
        costsSection.classList.add('hidden');
    }
    
    if (config.features && config.features.length > 0) {
        featuresSection.classList.remove('hidden');
        featuresContainer.innerHTML = '';
        config.features.forEach(feature => {
            const isChecked = existingFeatures.includes(feature);
            featuresContainer.innerHTML += `
                <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="property_features[]" value="${feature}" ${isChecked ? 'checked' : ''} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700">${feature}</span>
                </label>
            `;
        });
    } else {
        featuresSection.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateDynamicFields();
});
</script>

</body>
</html>
