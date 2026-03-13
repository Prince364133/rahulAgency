<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [
    'php_version' => PHP_VERSION,
    'curl_enabled' => extension_loaded('curl'),
    'post_data' => $_POST,
    'server_method' => $_SERVER['REQUEST_METHOD']
];

header('Content-Type: application/json');
echo json_encode($results);
?>
