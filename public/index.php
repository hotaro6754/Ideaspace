<?php
/**
 * IdeaSync - Campus Collaboration Platform
 * Main Entry Point - LIET Edition
 */

if (php_sapi_name() === 'cli' || isset($_SERVER['REQUEST_METHOD']) &&
    (basename($_SERVER['PHP_SELF']) === 'health.php' ||
     (isset($_GET['health']) && $_GET['health'] === '1'))) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: text/plain");
    echo "OK";
    exit(0);
}

header("Content-Type: text/html; charset=utf-8", true);
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");

session_start();

// Define base paths dynamically
$protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

require_once __DIR__ . '/../src/config/Database.php';

function sanitize($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isLoggedIn()) {
        try {
            $conn = getConnection();
            if (!$conn) return null;
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error getting current user: " . $e->getMessage());
            return null;
        }
    }
    return null;
}

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

$routes = [
    'home' => 'src/views/home.php',
    'register' => 'src/views/auth/register.php',
    'login' => 'src/views/auth/login.php',
    'logout' => 'src/controllers/auth.php',
    'dashboard' => 'src/views/dashboard.php',
    'ideas' => ($action === 'create') ? 'src/views/ideas/create.php' : 'src/views/ideas/list.php',
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
];

$view_file = __DIR__ . '/../' . ($routes[$page] ?? 'src/views/home.php');

if (file_exists($view_file)) {
    include $view_file;
} else {
    http_response_code(404);
    include __DIR__ . '/../src/views/404.php';
}
?>
