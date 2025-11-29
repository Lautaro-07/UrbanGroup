<?php
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Urban Group');
}
if (!defined('SITE_DESCRIPTION')) {
    define('SITE_DESCRIPTION', 'Portal Inmobiliario Profesional');
}
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:5000'));
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
