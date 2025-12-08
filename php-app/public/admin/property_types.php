<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PropertyTypeModel.php';

requireAdmin();

$propertyTypeModel = new PropertyTypeModel();

$action = $_REQUEST['action'] ?? 'list';
$id = (int)($_REQUEST['id'] ?? 0);
$name = $_POST['name'] ?? '';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        if (empty($name)) {
            $errors[] = 'El nombre del tipo de propiedad es obligatorio.';
        } else {
            if ($propertyTypeModel->create($name)) {
                $success_message = 'Tipo de propiedad creado con éxito.';
            } else {
                $errors[] = 'Error al crear el tipo de propiedad.';
            }
        }
    }

    if ($action === 'update' && $id > 0) {
        if (empty($name)) {
            $errors[] = 'El nombre no puede estar vacío.';
        } else {
            if ($propertyTypeModel->update($id, $name)) {
                $success_message = 'Tipo de propiedad actualizado con éxito.';
            } else {
                $errors[] = 'Error al actualizar.';
            }
        }
        $action = 'list'; // Go back to list view
    }
    
    if ($action === 'delete' && $id > 0) {
        // Optional: Check if any properties are using this type before deleting.
        // This requires a new method in a model, e.g., countByPropertyTypeId in PropertyModel.
        // For now, we will allow deletion.
        if ($propertyTypeModel->delete($id)) {
            $success_message = 'Tipo de propiedad eliminado con éxito.';
        } else {
            $errors[] = 'Error al eliminar. Es posible que esté en uso.';
        }
        $action = 'list'; // Go back to list view
    }
}


$propertyTypes = $propertyTypeModel->getAll();
$editType = null;
if ($action === 'edit' && $id > 0) {
    $editType = $propertyTypeModel->getById($id);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Propiedad - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50">

<header class="sticky top-0 z-50 border-b border-gray-200 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4 flex items-center justify-between">
        <a href="../index.php" class="flex items-center gap-3">
            <img src="../uploads/logo.png" alt="Urban Group" class="w-8 h-8 rounded-full shadow-sm object-cover">
            <span class="sr-only">Urban Group</span>
        </a>
        <a href="../logout.php" class="px-3 lg:px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Cerrar</a>
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
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-4 2l-4-2"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="index.php?action=properties" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Propiedades</span>
            </a>
            <!-- Secciones Especiales -->
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-slate-400 mb-2">SECCIONES ESPECIALES</p>
                <div class="space-y-1">
                    <a href="index.php?action=special_list&type=terrenos" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>Terrenos Inmo</span>
                    </a>
                    <a href="index.php?action=special_list&type=activos" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>Activos Inmo</span>
                    </a>
                    <a href="index.php?action=special_list&type=usa" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                        <span>🇺🇸 Prop. USA</span>
                    </a>
                </div>
            </div>
            <a href="index.php?action=partners" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Socios</span>
            </a>
            <a href="property_types.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path></svg>
                <span>Tipos de Propiedad</span>
            </a>
            <a href="carousel.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Carousel Inicio</span>
            </a>
            <a href="portal_clients.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Clientes Portal</span>
            </a>
        </nav>
    </aside>

    <!-- Mobile Nav -->
    <div class="lg:hidden bg-white border-b border-gray-200 px-4 py-2 flex gap-2 overflow-x-auto scrollbar-hide">
        <a href="index.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700">Dashboard</a>
        <a href="index.php?action=properties" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700">Propiedades</a>
        <a href="index.php?action=partners" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700">Socios</a>
        <a href="property_types.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-blue-600 text-white">Tipos</a>
        <a href="carousel.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700">Carousel</a>
        <a href="portal_clients.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700">Clientes Portal</a>
    </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Gestión de Tipos de Propiedad</h1>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="bg-white rounded-lg shadow p-4 lg:p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <?= $action === 'edit' ? 'Editar Tipo de Propiedad' : 'Agregar Nuevo Tipo' ?>
            </h2>
            <form method="POST" action="?action=<?= $action === 'edit' ? 'update' : 'create' ?>">
                <?php if ($action === 'edit' && $editType): ?>
                    <input type="hidden" name="id" value="<?= $editType['id'] ?>">
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-grow">
                        <label for="name" class="sr-only">Nombre del tipo</label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($editType['name'] ?? '') ?>" placeholder="Ej: Departamento" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-sm">
                            <?= $action === 'edit' ? 'Guardar Cambios' : 'Crear' ?>
                        </button>
                        <?php if ($action === 'edit'): ?>
                            <a href="property_types.php" class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- List of Property Types -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">ID</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                            <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($propertyTypes)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-500">No hay tipos de propiedad.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($propertyTypes as $type): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 lg:px-6 py-4 font-mono text-sm"><?= $type['id'] ?></td>
                                    <td class="px-4 lg:px-6 py-4 font-medium text-sm"><?= htmlspecialchars($type['name']) ?></td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="flex gap-2 justify-end">
                                            <a href="?action=edit&id=<?= $type['id'] ?>" class="inline-block px-3 py-1 bg-amber-600 text-white text-xs font-semibold rounded hover:bg-amber-700 transition whitespace-nowrap">Editar</a>
                                            <form method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tipo?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                                <button type="submit" class="inline-block px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition whitespace-nowrap">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>