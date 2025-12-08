<?php
function get_base_url() {
    // Handle CLI execution
    if (php_sapi_name() === 'cli') {
        return '/UrbanGroup/php-app/public/';
    }
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 0) == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // For XAMPP local development
    return $protocol . $host . '/UrbanGroup/php-app/public/';
}

if (!defined('BASE_URL')) {
    define('BASE_URL', get_base_url());
}
?>