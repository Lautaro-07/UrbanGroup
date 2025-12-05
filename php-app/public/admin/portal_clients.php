<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/PortalClientModel.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$portalClientModel = new PortalClientModel();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'toggle':
            $id = (int)$_POST['id'];
            $portalClientModel->toggleStatus($id);
            $message = 'Estado del cliente actualizado';
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $portalClientModel->delete($id);
            $message = 'Cliente eliminado correctamente';
            break;
    }
}

$clients = $portalClientModel->getAll();

// Group clients by registered sections
$clientsBySection = [
    'all' => $clients,
    'normal' => [],
    'terrenos' => [],
    'activos' => [],
    'usa' => []
];

foreach ($clients as $c) {
    $regs = isset($c['registered_sections']) ? trim($c['registered_sections']) : '';
    $hasCompanyData = !empty($c['razon_social']);
    
    // Si tienen datos de empresa, se muestran en terrenos, activos y USA
    if ($hasCompanyData) {
        $clientsBySection['terrenos'][] = $c;
        $clientsBySection['activos'][] = $c;
        $clientsBySection['usa'][] = $c;
    } else {
        // Si no tienen empresa, se clasifican por registered_sections
        if (empty($regs)) {
            $clientsBySection['normal'][] = $c;
        } else {
            // Split by comma for potential multiple sections
            $parts = array_map('trim', explode(',', $regs));
            $validSections = ['terrenos', 'activos', 'usa'];
            $foundSection = false;
            
            foreach ($parts as $section) {
                if (!empty($section) && in_array($section, $validSections)) {
                    $clientsBySection[$section][] = $c;
                    $foundSection = true;
                }
            }
            
            // If no valid section found, add to normal
            if (!$foundSection) {
                $clientsBySection['normal'][] = $c;
            }
        }
    }
}

$tab = $_GET['tab'] ?? 'all';
if (!array_key_exists($tab, $clientsBySection)) $tab = 'all';
// clients to display based on tab
$displayClients = $clientsBySection[$tab];

$pageTitle = 'Clientes del Portal';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Urban Group Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver al Panel
                    </a>
                    <h1 class="text-xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                </div>
                <span class="text-sm text-gray-500"><?= count($clients) ?> cliente(s) registrado(s)</span>
            </div>
        </div>
        <!-- Enlaces de navegación -->
        <div class="max-w-7xl mx-auto px-4 py-2 border-t flex gap-2 overflow-x-auto scrollbar-hide">
            <a href="index.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Dashboard</a>
            <a href="index.php?action=properties" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Propiedades</a>
            <a href="index.php?action=partners" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Socios</a>
            <a href="property_types.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Tipos</a>
            <a href="carousel.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-gray-100 text-gray-700 hover:bg-gray-200">Carousel</a>
            <a href="portal_clients.php" class="px-3 py-2 text-xs font-medium rounded-lg whitespace-nowrap bg-blue-600 text-white">Clientes Portal</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-4 border-b">
                        <nav class="flex gap-2">
                            <a href="?tab=all" class="px-3 py-1 rounded-lg text-sm <?= $tab==='all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Todos (<?= count($clientsBySection['all']) ?>)</a>
                            <a href="?tab=normal" class="px-3 py-1 rounded-lg text-sm <?= $tab==='normal' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Clientes Normales (<?= count($clientsBySection['normal']) ?>)</a>
                            <a href="?tab=terrenos" class="px-3 py-1 rounded-lg text-sm <?= $tab==='terrenos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Terrenos (<?= count($clientsBySection['terrenos']) ?>)</a>
                            <a href="?tab=activos" class="px-3 py-1 rounded-lg text-sm <?= $tab==='activos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">Activos (<?= count($clientsBySection['activos']) ?>)</a>
                            <a href="?tab=usa" class="px-3 py-1 rounded-lg text-sm <?= $tab==='usa' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' ?>">USA (<?= count($clientsBySection['usa']) ?>)</a>
                        </nav>
                    </div>
            <?php if (empty($clients)): ?>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay clientes registrados</h3>
                    <p class="text-gray-500">Los clientes del portal aparecerán aquí cuando se registren</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">RUT</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Empresa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Contacto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Secciones</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Registro</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($displayClients as $client): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($client['nombre_completo']) ?></p>
                                            <p class="text-sm text-gray-500"><?= htmlspecialchars($client['alias']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        <?= htmlspecialchars($client['rut']) ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($client['razon_social']) ?></p>
                                            <p class="text-sm text-gray-500"><?= htmlspecialchars($client['representante_legal']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm">
                                            <p class="text-gray-900"><?= htmlspecialchars($client['email']) ?></p>
                                            <p class="text-gray-500"><?= htmlspecialchars($client['celular']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <?php 
                                            $sections = isset($client['registered_sections']) ? trim($client['registered_sections']) : '';
                                            if (empty($sections)) {
                                                echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Clientes Normales</span>';
                                            } else {
                                                $sectionMap = ['terrenos' => 'Terrenos', 'activos' => 'Activos', 'usa' => 'USA'];
                                                $parts = array_map('trim', explode(',', $sections));
                                                foreach ($parts as $section) {
                                                    if (!empty($section) && isset($sectionMap[$section])) {
                                                        $colorMap = [
                                                            'terrenos' => 'bg-blue-100 text-blue-700',
                                                            'activos' => 'bg-purple-100 text-purple-700',
                                                            'usa' => 'bg-green-100 text-green-700'
                                                        ];
                                                        echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' . $colorMap[$section] . ' mr-1">' . $sectionMap[$section] . '</span>';
                                                    }
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $client['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= $client['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        <?= date('d/m/Y', strtotime($client['created_at'])) ?>
                                        <?php if ($client['last_login_at']): ?>
                                            <p class="text-xs text-gray-400">Último acceso: <?= date('d/m/Y H:i', strtotime($client['last_login_at'])) ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-2">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                <button type="submit" class="text-xs <?= $client['status'] === 'active' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' ?> px-2 py-1 rounded hover:opacity-80 transition">
                                                    <?= $client['status'] === 'active' ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('¿Eliminar este cliente?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                <button type="submit" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-medium text-blue-900 mb-2">Información sobre Clientes del Portal</h3>
            <p class="text-sm text-blue-700">
                Los clientes del portal son usuarios que se registran para acceder a las secciones exclusivas: 
                <strong>Terrenos Inmobiliarios</strong>, <strong>Activos Inmobiliarios</strong> y <strong>Propiedades USA</strong>.
                Al registrarse, aceptan la comisión del 2% + IVA en caso de compraventa.
            </p>
        </div>
    </div>
</body>
</html>
