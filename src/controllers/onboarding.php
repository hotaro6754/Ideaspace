<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: " . BASE_URL . "/?page=login");
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'complete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token.";
        header("Location: " . BASE_URL . "/?page=onboarding");
        exit();
    }

    $role = $_POST['academic_role'] ?? '';
    $interests = $_POST['interests'] ?? [];

    if (empty($role) || empty($interests)) {
        $_SESSION['error'] = "Please select your role and at least one interest.";
        header("Location: " . BASE_URL . "/?page=onboarding");
        exit();
    }

    $db = getConnection();
    $interests_json = json_encode($interests);

    $stmt = $db->prepare("UPDATE users SET academic_role = ?, interests = ? WHERE id = ?");
    $stmt->bind_param("ssi", $role, $interests_json, $user_id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/?page=dashboard");
    } else {
        $_SESSION['error'] = "Failed to save profile settings.";
        header("Location: " . BASE_URL . "/?page=onboarding");
    }
    exit();
}
?>
