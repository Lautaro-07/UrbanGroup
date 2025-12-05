<?php
function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $project_folder = '/UrbanGroup/php-app/public/';
    return $protocol . $host . $project_folder;
}

define('BASE_URL', get_base_url());
?>