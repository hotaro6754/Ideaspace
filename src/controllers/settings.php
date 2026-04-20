<?php
/**
 * Settings Controller
 * Handles user preferences and settings
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Security.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

class SettingsController {
    private $userModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/?page=login");
            exit();
        }

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Invalid security token.";
            header("Location: " . BASE_URL . "/?page=profile-edit");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $github_username = trim($_POST['github_username'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $academic_role = $_POST['academic_role'] ?? 'builder';
        $interests = trim($_POST['interests'] ?? '');

        $query = "UPDATE users SET name = ?, github_username = ?, bio = ?, academic_role = ?, interests = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $github_username, $bio, $academic_role, $interests, $user_id);

        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            $_SESSION['message'] = "Profile updated successfully!";
            header("Location: " . BASE_URL . "/?page=profile");
        } else {
            $_SESSION['error'] = "Failed to update profile.";
            header("Location: " . BASE_URL . "/?page=profile-edit");
        }
        exit();
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$db = getConnection();
$controller = new SettingsController($db);

if ($action === 'update_profile') {
    $controller->updateProfile();
}
?>
