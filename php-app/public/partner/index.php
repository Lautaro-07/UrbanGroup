<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PropertyModel.php';
require_once __DIR__ . '/../../includes/LocationModel.php';

requirePartner();

$propertyModel = new PropertyModel();
$locationModel = new LocationModel();

$action = $_GET['action'] ?? 'dashboard';
$propertyId = (int)($_GET['id'] ?? 0);

$regions = $locationModel->getRegions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'create_property') {
        $images = array_filter(explode("\n", trim($_POST['images'] ?? '')));
        
        $propertyModel->create([
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description']),
            'property_type' => sanitizeInput($_POST['property_type']),
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
            'images' => json_encode($images),
            'is_featured' => 0,
            'is_active' => 1,
            'partner_id' => $_SESSION['user_id']
        ]);
        header('Location: index.php?action=properties');
        exit;
    }
    
    if ($postAction === 'update_property' && !empty($_POST['property_id'])) {
        $property = $propertyModel->getById((int)$_POST['property_id']);
        if ($property && $property['partner_id'] == $_SESSION['user_id']) {
            $images = array_filter(explode("\n", trim($_POST['images'] ?? '')));
            
            $propertyModel->update((int)$_POST['property_id'], [
                'title' => sanitizeInput($_POST['title']),
                'description' => sanitizeInput($_POST['description']),
                'property_type' => sanitizeInput($_POST['property_type']),
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
                'images' => json_encode($images)
            ]);
        }
        header('Location: ?action=properties');
        exit;
    }
    
    if ($postAction === 'delete_property' && !empty($_POST['property_id'])) {
        $property = $propertyModel->getById((int)$_POST['property_id']);
        if ($property && $property['partner_id'] == $_SESSION['user_id']) {
            $propertyModel->delete((int)$_POST['property_id']);
        }
        header('Location: ?action=properties');
        exit;
    }
}

$myProperties = $propertyModel->getByPartnerId($_SESSION['user_id']);
$totalProperties = count($myProperties);
$activeProperties = count(array_filter($myProperties, fn($p) => $p['is_active']));
$featuredProperties = count(array_filter($myProperties, fn($p) => $p['is_featured']));

$editProperty = null;
if ($action === 'edit' && $propertyId) {
    $editProperty = $propertyModel->getById($propertyId);
    if (!$editProperty || $editProperty['partner_id'] != $_SESSION['user_id']) {
        header('Location: ?action=properties');
        exit;
    }
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
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8">
        <?php if ($action === 'dashboard'): ?>
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
                            <a href="../propiedad.php?id=<?= $property['id'] ?>" class="group">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        <img src="<?= getFirstImage($property['images']) ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= getFirstImage($property['images']) ?>" class="w-12 h-10 object-cover rounded" alt="">
                                            <div>
                                                <p class="font-medium text-gray-900"><?= htmlspecialchars(truncateText($property['title'], 40)) ?></p>
                                                <p class="text-xs text-gray-600"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= ucfirst($property['property_type']) ?></td>
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
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="<?= $action === 'edit' ? 'update_property' : 'create_property' ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="property_id" value="<?= $editProperty['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Propiedad *</label>
                            <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <?php foreach (PROPERTY_TYPES as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= ($editProperty['property_type'] ?? '') === $key ? 'selected' : '' ?>><?= $value ?></option>
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
                            <select name="region_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= $region['id'] ?>" <?= ($editProperty['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>><?= htmlspecialchars($region['name']) ?></option>
                                <?php endforeach; ?>
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
                            <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URLs de Imágenes (una por línea)</label>
                            <textarea name="images" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://ejemplo.com/imagen1.jpg&#10;https://ejemplo.com/imagen2.jpg"><?= implode("\n", getImages($editProperty['images'] ?? '[]')) ?></textarea>
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

</body>
</html>
