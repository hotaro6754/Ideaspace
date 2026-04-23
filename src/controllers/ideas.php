<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../services/AnalyticsService.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/?page=login");
        exit();
    }

    $title = trim($_POST['title'] ?? '');
    $domain = trim($_POST['domain'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Process skills_raw into array
    $skills_raw = $_POST['skills_raw'] ?? '';
    $skills = array_map('trim', explode(',', $skills_raw));
    $skills = array_filter($skills); // Remove empty values

    if (empty($title) || empty($description)) {
        $_SESSION['error'] = "Title and Description are required";
        header("Location: " . BASE_URL . "/?page=ideas&action=create");
        exit();
    }

    $stmt = $db->prepare("INSERT INTO ideas (user_id, title, domain, description, skills_needed) VALUES (?, ?, ?, ?, ?)");
    $skills_json = json_encode($skills);
    $stmt->bind_param("issss", $_SESSION['user_id'], $title, $domain, $description, $skills_json);

    if ($stmt->execute()) {
AnalyticsService::logEvent('idea_created', ['user_id' => $_SESSION['user_id'], 'domain' => $domain, 'title' => $title]);
        $_SESSION['message'] = "Innovation Track created successfully!";
        header("Location: " . BASE_URL . "/?page=ideas");
    } else {
        $_SESSION['error'] = "Failed to create track: " . $db->error;
        header("Location: " . BASE_URL . "/?page=ideas&action=create");
    }
    exit();
}
?>
