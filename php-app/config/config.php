<?php
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Urban Group');
}
if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', 'Portal Inmobiliario Profesional');
}
require_once __DIR__ . '/../includes/base_url.php';
if (!defined('SITE_URL')) {
    $serverPort = $_SERVER['SERVER_PORT'] ?? null;
    $forwardedProto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
        || $serverPort == 443
        || $forwardedProto === 'https';
    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:5000';
    define('SITE_URL', $protocol . $host);
}
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', '$');
}
if (!defined('DEFAULT_CURRENCY')) {
    define('DEFAULT_CURRENCY', 'CLP');
}
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 12);
}
if (!defined('WHATSAPP_NUMBER')) {
    define('WHATSAPP_NUMBER', '+542914125043');
}
// Database settings: choose 'pgsql', 'mysql', or 'sqlite'
if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'mysql'); // Using MySQL with phpMyAdmin
}

// MySQL settings for XAMPP / Hostinger
// These can be overridden by environment variables: MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_PORT
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');  // XAMPP default, change to Hostinger host when deploying
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'urbanpropiedades');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');  // XAMPP default, change to Hostinger user when deploying
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');  // XAMPP default (empty), set Hostinger password when deploying
}
if (!defined('DB_PORT')) {
    define('DB_PORT', '3306');
}
if (!defined('PROPERTY_TYPES')) {
    define('PROPERTY_TYPES', [
        'Casa' => 'Casa',
        'Departamento' => 'Departamento',
        'Oficina' => 'Oficina',
        'Local Comercial' => 'Local Comercial',
        'Bodega' => 'Bodega',
        'Terreno' => 'Terreno',
        'Galpón' => 'Galpón',
        'Estacionamiento' => 'Estacionamiento'
    ]);
}
if (!defined('OPERATION_TYPES')) {
    define('OPERATION_TYPES', [
        'Venta' => 'Venta',
        'Arriendo' => 'Arriendo'
    ]);
}
if (!defined('BEDROOM_OPTIONS')) {
    define('BEDROOM_OPTIONS', [
        '1' => '1 dormitorio',
        '2' => '2 dormitorios',
        '3' => '3 dormitorios',
        '4' => '4 dormitorios',
        '5+' => '5 o más dormitorios'
    ]);
}
if (!defined('PRICE_RANGES_SALE')) {
    define('PRICE_RANGES_SALE', [
        '0-100000000' => 'Hasta $100M',
        '100000000-200000000' => '$100M - $200M',
        '200000000-400000000' => '$200M - $400M',
        '400000000-800000000' => '$400M - $800M',
        '800000000+' => 'Más de $800M'
    ]);
}
if (!defined('PRICE_RANGES_RENT')) {
    define('PRICE_RANGES_RENT', [
        '0-500000' => 'Hasta $500.000',
        '500000-1000000' => '$500.000 - $1M',
        '1000000-2000000' => '$1M - $2M',
        '2000000-4000000' => '$2M - $4M',
        '4000000+' => 'Más de $4M'
    ]);
}
