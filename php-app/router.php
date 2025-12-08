<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Servir archivos directamente si existen (assets, uploads, images)
if (strpos($path, '/assets/') === 0) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}
if (strpos($path, '/uploads/') === 0) {
    $file = __DIR__ . $path;
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

// Servir API directamente
if (strpos($path, '/api/') === 0 && strpos($path, '.php') === false) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file)) {
        return false;
    }
}

// APIs PHP
if (strpos($path, '/api/') === 0 && strpos($path, '.php') !== false) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Rutas principales
$routes = [
    '/' => '/public/index.php',
    '/index.php' => '/public/index.php',
    '/propiedades.php' => '/public/propiedades.php',
    '/propiedad.php' => '/public/propiedad.php',
    '/nosotros.php' => '/public/nosotros.php',
    '/login.php' => '/public/login.php',
    '/logout.php' => '/public/logout.php',
    '/admin/' => '/public/admin/index.php',
    '/admin' => '/public/admin/index.php',
    '/admin/carousel.php' => '/public/admin/carousel.php',
    '/admin/portal_clients.php' => '/public/admin/portal_clients.php',
    '/partner/' => '/public/partner/index.php',
    '/partner' => '/public/partner/index.php',
    '/portal_register.php' => '/public/portal_register.php',
    '/portal_login.php' => '/public/portal_login.php',
    '/portal_logout.php' => '/public/portal_logout.php',
    '/terrenos.php' => '/public/terrenos.php',
    '/activos.php' => '/public/activos.php',
    '/usa.php' => '/public/usa.php',
    '/proyectos_usa.php' => '/public/proyectos_usa.php',
    '/propiedad_usa.php' => '/public/propiedad_usa.php',
    '/api/contact.php' => '/public/api/contact.php',
    '/api/comunas.php' => '/public/api/comunas.php',
    '/api/get-comunas.php' => '/public/api/get-comunas.php',
    '/api/download-property-pdf.php' => '/public/api/download-property-pdf.php'
];

if (isset($routes[$path])) {
    require __DIR__ . $routes[$path];
    exit;
}

// Fallback: Try to find PHP file in public directory
if (preg_match('/^\/[a-zA-Z0-9_-]+\.php$/', $path)) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Fallback: Try to find PHP file in admin directory
if (preg_match('/^\/admin\/[a-zA-Z0-9_-]+\.php$/', $path)) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Fallback: Try to find PHP file in partner directory
if (preg_match('/^\/partner\/[a-zA-Z0-9_-]+\.php$/', $path)) {
    $file = __DIR__ . '/public' . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title></head><body style="font-family: sans-serif; text-align: center; padding: 50px;"><h1>404 - No encontrado</h1><p><a href="/">Volver</a></p></body></html>';
