<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= $pageTitle ?? 'Urban Group' ?> - Portal Inmobiliario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hover-elevate { transition: all 0.3s ease; }
        .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-white">
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4">
        <div class="flex items-center justify-between">

            <!-- LOGO -->
            <a href="<?= BASE_URL ?>index.php" class="flex items-center gap-3">
                <img src="<?= BASE_URL ?>uploads/logo.png" alt="Urban Group" class="w-9 h-9 rounded-full shadow-sm object-cover">
                <span class="sr-only">Urban Group</span>
            </a>

            <!-- MENU -->
            <nav class="hidden lg:flex items-center gap-6">
                <a href="<?= BASE_URL ?>index.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'home' ? 'text-blue-600' : '' ?>">
                   Inicio
                </a>

                <a href="<?= BASE_URL ?>propiedades.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'properties' ? 'text-blue-600' : '' ?>">
                   Propiedades
                </a>

                <a href="<?= BASE_URL ?>nosotros.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'about' ? 'text-blue-600' : '' ?>">
                   Nosotros
                </a>

                     <a href="<?= BASE_URL ?>portal_login.php?section=terrenos" 
                         class="text-sm font-medium text-gray-700 hover:text-green-600 transition <?= ($currentPage ?? '') === 'terrenos' ? 'text-green-600' : '' ?>">
                         Terrenos Inmobiliarios
                     </a>

                     <a href="<?= BASE_URL ?>portal_login.php?section=activos" 
                         class="text-sm font-medium text-gray-700 hover:text-purple-600 transition <?= ($currentPage ?? '') === 'activos' ? 'text-purple-600' : '' ?>">
                         Activos Inmobiliarios
                     </a>

                     <a href="<?= BASE_URL ?>portal_login.php?section=usa" 
                         class="text-sm font-medium text-gray-700 hover:text-red-600 transition <?= ($currentPage ?? '') === 'usa' ? 'text-red-600' : '' ?>">
                         Propiedades USA
                     </a>
            </nav>

            <!-- USUARIO -->
            <div class="flex items-center gap-2">
                <?php if (!empty(
                    
                    
                    
                    $_SESSION['portal_client_id']
                )): ?>
                    <!-- Cliente portal: mostrar sólo Mis Favoritos -->
                    <a href="<?= BASE_URL ?>cliente/favoritos.php" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Mis Favoritos</a>
                <?php elseif (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="<?= BASE_URL ?>admin/" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Panel Admin</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>partner/" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Mi Panel</a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>logout.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Cerrar</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Iniciar Sesión</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

