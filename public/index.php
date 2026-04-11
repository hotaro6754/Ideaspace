<?php
/**
 * IdeaSync - Campus Collaboration Platform
 * Main Entry Point
 */

// CRITICAL: Set content type FIRST before any output
header("Content-Type: text/html; charset=utf-8", true);
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");

session_start();

// Define base paths dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

// Include core files
require_once __DIR__ . '/../src/config/Database.php';

// Helper function for secure output
function sanitize($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper function for URL redirection
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user info
function getCurrentUser() {
    if (isLoggedIn()) {
        global $conn;
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}

// Simple routing
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

// Routes mapping
$routes = [
    'home' => 'src/views/home.php',
    'register' => 'src/views/auth/register.php',
    'login' => 'src/views/auth/login.php',
    'logout' => 'src/controllers/auth.php',
    'dashboard' => 'src/views/dashboard.php',
    'ideas' => ($_GET['action'] === 'create') ? 'src/views/ideas/create.php' : 'src/views/ideas/list.php',
    'idea-detail' => 'src/views/ideas/detail.php',
    'profile' => 'src/views/profile.php',
    'profile-applications' => 'src/views/profile/applications.php',
    'profile-collaborations' => 'src/views/profile/collaborations.php',
    'leaderboard' => 'src/views/leaderboard.php',
    'messages' => 'src/views/messages.php',
    'notifications' => 'src/views/notifications.php',
    'admin' => 'src/views/admin/dashboard.php',
    'admin-users' => 'src/views/admin/users.php',
    'admin-reports' => 'src/views/admin/reports.php',
    'agents' => 'src/views/agents/dashboard.php',
    'agents-onboarding' => 'src/views/agents/onboarding.php',
    'workflow' => 'src/views/workflow.php',
    'role-dashboard' => 'src/views/role-dashboard.php',
];

// Determine which file to load
$view_file = __DIR__ . '/../' . ($routes[$page] ?? 'src/views/home.php');

if (file_exists($view_file)) {
    include $view_file;
} else {
    http_response_code(404);
    include __DIR__ . '/../src/views/404.php';
}
?>
