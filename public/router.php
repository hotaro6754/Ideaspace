<?php
/**
 * Router for PHP Built-in Server
 * Handles requests and routes to appropriate files
 */

$requested_file = $_SERVER["REQUEST_URI"];

// Strip query string
$requested_path = parse_url($requested_file, PHP_URL_PATH);

// Health check - respond immediately without routing
if ($requested_path === '/public/health.php' || $requested_path === '/health.php' || strpos($requested_path, 'health') !== false) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: text/plain");
    echo "OK";
    exit(0);
}

// Serve static files directly
if (preg_match('/\.(?:css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $requested_path)) {
    return false;
}

// Check if file exists in public directory
if ($requested_path !== "/" && file_exists(__DIR__ . $requested_path)) {
    return false;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
