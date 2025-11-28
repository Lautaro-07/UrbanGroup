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
            <a href="index.php" class="flex items-center gap-2 text-2xl font-bold text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/>
                    <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/>
                </svg>
                Urban Group
            </a>

            <!-- MENU -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="index.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'home' ? 'text-blue-600' : '' ?>">
                   Inicio
                </a>

                <a href="propiedades.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'properties' ? 'text-blue-600' : '' ?>">
                   Propiedades
                </a>

                <a href="nosotros.php" 
                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition <?= ($currentPage ?? '') === 'about' ? 'text-blue-600' : '' ?>">
                   Nosotros
                </a>
            </nav>

            <!-- USUARIO -->
            <div class="flex items-center gap-2">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Panel Admin</a>
                    <?php else: ?>
                        <a href="partner/" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Mi Panel</a>
                    <?php endif; ?>

                    <a href="logout.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Cerrar</a>

                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Iniciar Sesión</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

