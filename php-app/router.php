<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

if (strpos($path, '/assets/') === 0) {
    $assetPath = __DIR__ . $path;
    if (file_exists($assetPath)) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        }
        readfile($assetPath);
        exit;
    }
}

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
    '/partner/' => '/public/partner/index.php',
    '/partner' => '/public/partner/index.php',
    '/api/contact.php' => '/api/contact.php',
    '/api/comunas.php' => '/api/comunas.php'
];

if (isset($routes[$path])) {
    require __DIR__ . $routes[$path];
    exit;
}

http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404 - No encontrado</title></head><body style="font-family: sans-serif; text-align: center; padding: 50px;"><h1>404 - Página no encontrada</h1><p>La página que buscas no existe.</p><p><a href="/" style="color: #10b981;">Volver al inicio</a></p></body></html>';
