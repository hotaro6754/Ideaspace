<?php
/**
 * Router for PHP Built-in Server and Vercel
 */

$requested_file = $_SERVER["REQUEST_URI"];
$file_path = __DIR__ . $requested_file;

// Serve static files from root or src/assets
if (preg_match('/\.(?:css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $requested_file)) {
    if (file_exists($file_path)) {
        return false;
    }
    // Try src/assets
    $asset_path = dirname(__DIR__) . $requested_file;
    if (file_exists($asset_path)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml'
        ];
        $ext = pathinfo($asset_path, PATHINFO_EXTENSION);
        header('Content-Type: ' . ($mime_types[$ext] ?? 'text/plain'));
        readfile($asset_path);
        return true;
    }
}

// All other requests go through index.php
require __DIR__ . '/index.php';
