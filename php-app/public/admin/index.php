<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PropertyModel.php';
require_once __DIR__ . '/../../includes/UserModel.php';
require_once __DIR__ . '/../../includes/LocationModel.php';

requireAdmin();

$propertyModel = new PropertyModel();
$userModel = new UserModel();
$locationModel = new LocationModel();

$action = $_GET['action'] ?? 'dashboard';
$propertyId = (int)($_GET['id'] ?? 0);

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
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'partner_id' => 0
        ]);
        header('Location: /admin/?action=properties');
        exit;
    }
    
    if ($postAction === 'update_property' && !empty($_POST['property_id'])) {
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
            'images' => json_encode($images),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
        header('Location: /admin/?action=properties');
        exit;
    }
    
    if ($postAction === 'delete_property' && !empty($_POST['property_id'])) {
        $propertyModel->delete((int)$_POST['property_id']);
        header('Location: /admin/?action=properties');
        exit;
    }
    
    if ($postAction === 'toggle_featured' && !empty($_POST['property_id'])) {
        $property = $propertyModel->getById((int)$_POST['property_id']);
        $propertyModel->update((int)$_POST['property_id'], ['is_featured' => $property['is_featured'] ? 0 : 1]);
        header('Location: /admin/?action=properties');
        exit;
    }
    
    if ($postAction === 'create_partner') {
        $userModel->create([
            'username' => sanitizeInput($_POST['username']),
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'name' => sanitizeInput($_POST['name']),
            'email' => sanitizeInput($_POST['email']),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'role' => 'partner',
            'is_active' => 1
        ]);
        header('Location: /admin/?action=partners');
        exit;
    }
    
    if ($postAction === 'delete_partner' && !empty($_POST['user_id'])) {
        $userModel->delete((int)$_POST['user_id']);
        header('Location: /admin/?action=partners');
        exit;
    }
}

$properties = $propertyModel->getAll([], null);
$partners = $userModel->getPartners();
$regions = $locationModel->getRegions();

$totalProperties = count($properties);
$totalPartners = count($partners);
$featuredCount = count(array_filter($properties, fn($p) => $p['is_featured']));
$activeProperties = count(array_filter($properties, fn($p) => $p['is_active']));

$editProperty = null;
if ($action === 'edit' && $propertyId) {
    $editProperty = $propertyModel->getById($propertyId);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - UrbanPropiedades</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<header class="sticky top-0 z-50 border-b border-gray-200 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 text-xl lg:text-2xl font-bold text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 lg:w-8 lg:h-8" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
                <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
            </svg>
            <span class="hidden sm:inline">Urban</span>
        </a>
        <a href="/logout.php" class="px-3 lg:px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Cerrar</a>
    </div>
</header>

<div class="flex h-screen flex-col lg:flex-row">
    <!-- Sidebar -->
    <aside class="hidden lg:flex flex-col w-64 bg-slate-900 text-white border-r border-slate-700 overflow-y-auto">
        <div class="p-6 border-b border-slate-700">
            <h3 class="text-xs font-semibold text-slate-400 mb-2">ADMINISTRACIÓN</h3>
            <p class="text-sm font-medium truncate"><?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        
        <nav class="flex-1 p-4 space-y-1">
            <a href="/admin/" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'dashboard' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-4 2l-4-2"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="/admin/?action=properties" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'properties' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Propiedades</span>
            </a>
            <a href="/admin/?action=partners" class="flex items-center gap-3 px-4 py-3 rounded-lg <?= $action === 'partners' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?> transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Socios</span>
            </a>
        </nav>
    </aside>

    <!-- Mobile Nav -->
    <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-2 flex gap-2 overflow-x-auto scrollbar-hide">
        <a href="/admin/" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap <?= $action === 'dashboard' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Dashboard</a>
        <a href="/admin/?action=properties" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap <?= $action === 'properties' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Propiedades</a>
        <a href="/admin/?action=partners" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap <?= $action === 'partners' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Socios</a>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <?php if ($action === 'dashboard'): ?>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-4 lg:p-6 border-l-4 border-blue-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs lg:text-sm font-medium">Total Propiedades</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1"><?= $totalProperties ?></p>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 lg:p-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs lg:text-sm font-medium">Activas</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1"><?= $activeProperties ?></p>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 lg:p-6 border-l-4 border-amber-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs lg:text-sm font-medium">Destacadas</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1"><?= $featuredCount ?></p>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 lg:p-6 border-l-4 border-indigo-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs lg:text-sm font-medium">Socios</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1"><?= $totalPartners ?></p>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Properties Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 lg:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Últimas Propiedades</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Propiedad</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Precio</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($properties, 0, 5) as $property): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex items-center gap-2 lg:gap-3">
                                            <img src="<?= getFirstImage($property['images']) ?>" class="w-10 h-8 lg:w-12 lg:h-10 object-cover rounded flex-shrink-0" alt="">
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate text-sm"><?= htmlspecialchars(truncateText($property['title'], 30)) ?></p>
                                                <p class="text-xs text-gray-600 truncate"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-sm"><?= ucfirst($property['property_type']) ?></td>
                                    <td class="px-4 lg:px-6 py-4 font-medium text-sm"><?= formatPrice($property['price']) ?></td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex gap-1 flex-wrap">
                                            <?php if ($property['is_featured']): ?>
                                                <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded">⭐</span>
                                            <?php endif; ?>
                                            <span class="inline-block px-2 py-1 <?= $property['is_active'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> text-xs font-semibold rounded">
                                                <?= $property['is_active'] ? 'Activa' : 'Inactiva' ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($action === 'properties'): ?>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Gestión de Propiedades</h1>
                <a href="/admin/?action=add" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">+ Agregar</a>
            </div>
            
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Propiedad</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Precio</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex items-center gap-2 lg:gap-3">
                                            <img src="<?= getFirstImage($property['images']) ?>" class="w-10 h-8 lg:w-12 lg:h-10 object-cover rounded flex-shrink-0" alt="">
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate text-sm"><?= htmlspecialchars(truncateText($property['title'], 30)) ?></p>
                                                <p class="text-xs text-gray-600 truncate"><?= htmlspecialchars($property['comuna_name'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-sm"><?= ucfirst($property['property_type']) ?></td>
                                    <td class="px-4 lg:px-6 py-4 font-medium text-sm"><?= formatPrice($property['price']) ?></td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="toggle_featured">
                                            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                            <button type="submit" class="inline-block px-2 py-1 <?= $property['is_featured'] ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700' ?> text-xs font-semibold rounded hover:opacity-75 transition whitespace-nowrap">
                                                <?= $property['is_featured'] ? '⭐ Destacada' : '☆ Normal' ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex gap-1 flex-wrap">
                                            <a href="/propiedad.php?id=<?= $property['id'] ?>" target="_blank" class="inline-block px-2 lg:px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition whitespace-nowrap">Ver</a>
                                            <a href="/admin/?action=edit&id=<?= $property['id'] ?>" class="inline-block px-2 lg:px-3 py-1 bg-amber-600 text-white text-xs font-semibold rounded hover:bg-amber-700 transition whitespace-nowrap">Editar</a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_property">
                                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                                <button type="submit" onclick="return confirm('¿Eliminar propiedad?')" class="inline-block px-2 lg:px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition whitespace-nowrap">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <div class="flex items-center gap-2 mb-8">
                <a href="/admin/?action=properties" class="px-3 py-2 text-gray-600 hover:text-gray-900">←</a>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900"><?= $action === 'edit' ? 'Editar Propiedad' : 'Agregar Nueva Propiedad' ?></h1>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 lg:p-8 max-w-4xl">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="<?= $action === 'edit' ? 'update_property' : 'create_property' ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="property_id" value="<?= $editProperty['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Título de la Propiedad *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editProperty['title'] ?? '') ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Propiedad *</label>
                            <select name="property_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Seleccionar</option>
                                <?php foreach (PROPERTY_TYPES as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= ($editProperty['property_type'] ?? '') === $key ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Operación *</label>
                            <select name="operation_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="Venta" <?= ($editProperty['operation_type'] ?? '') === 'Venta' ? 'selected' : '' ?>>Venta</option>
                                <option value="Arriendo" <?= ($editProperty['operation_type'] ?? '') === 'Arriendo' ? 'selected' : '' ?>>Arriendo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio (CLP) *</label>
                            <input type="number" name="price" value="<?= $editProperty['price'] ?? '' ?>" required step="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Región</label>
                            <select name="region_id" id="regionSelectAdmin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Seleccionar</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= $region['id'] ?>" <?= ($editProperty['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>><?= htmlspecialchars($region['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comuna</label>
                            <select name="comuna_id" id="comunaSelectAdmin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Seleccionar</option>
                                <?php if (!empty($editProperty['region_id'])): ?>
                                    <?php $comunas = $locationModel->getComunas($editProperty['region_id']); foreach ($comunas as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($editProperty['comuna_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; endif; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dormitorios</label>
                            <input type="number" name="bedrooms" value="<?= $editProperty['bedrooms'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Baños</label>
                            <input type="number" name="bathrooms" value="<?= $editProperty['bathrooms'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Construcción (m²)</label>
                            <input type="number" name="built_area" value="<?= $editProperty['built_area'] ?? 0 ?>" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total (m²)</label>
                            <input type="number" name="total_area" value="<?= $editProperty['total_area'] ?? 0 ?>" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estacionamientos</label>
                            <input type="number" name="parking_spots" value="<?= $editProperty['parking_spots'] ?? 0 ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($editProperty['address'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"><?= htmlspecialchars($editProperty['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">URLs de Imágenes (una por línea)</label>
                            <textarea name="images" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none font-mono text-xs" placeholder="https://ejemplo.com/imagen1.jpg&#10;https://ejemplo.com/imagen2.jpg"><?= implode("\n", getImages($editProperty['images'] ?? '[]')) ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2 space-y-3">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_featured" <?= ($editProperty['is_featured'] ?? 0) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Marcar como Destacada</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" <?= ($editProperty['is_active'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Propiedad Activa</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">
                            <?= $action === 'edit' ? 'Guardar Cambios' : 'Crear Propiedad' ?>
                        </button>
                        <a href="/admin/?action=properties" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($action === 'partners'): ?>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Gestión de Socios</h1>
                <button onclick="document.getElementById('addPartnerModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">+ Agregar Socio</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <?php foreach ($partners as $partner): ?>
                    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($partner['name']) ?></h3>
                                <p class="text-xs text-gray-600">@<?= htmlspecialchars($partner['username']) ?></p>
                            </div>
                            <span class="inline-block px-2 py-1 <?= $partner['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' ?> text-xs font-semibold rounded whitespace-nowrap flex-shrink-0">
                                <?= $partner['is_active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2 truncate"><?= htmlspecialchars($partner['email']) ?></p>
                        <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($partner['phone'] ?? '-') ?></p>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_partner">
                            <input type="hidden" name="user_id" value="<?= $partner['id'] ?>">
                            <button type="submit" onclick="return confirm('¿Eliminar socio?')" class="w-full px-3 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition text-sm">Eliminar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Partner Modal -->
            <div id="addPartnerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Agregar Nuevo Socio</h2>
                        <button onclick="document.getElementById('addPartnerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>
                    <form method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="create_partner">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                            <input type="text" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                            <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">Crear</button>
                            <button type="button" onclick="document.getElementById('addPartnerModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
