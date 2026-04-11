<?php
/**
 * Router for PHP Built-in Server
 * Handles requests and routes to appropriate files
 */

$requested_file = $_SERVER["REQUEST_URI"];

// Serve static files directly
if (preg_match('/\.(?:css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $requested_file)) {
    return false;
}

// All other requests go through index.php
if ($requested_file !== "/" && file_exists(__DIR__ . $requested_file)) {
    return false;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
