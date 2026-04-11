<?php
/**
 * IdeaSync - Campus Collaboration Platform
 * Main Entry Point
 */

// CRITICAL: Respond to health checks FIRST, before anything else
// This allows Railway health checks to pass without database access
if (php_sapi_name() === 'cli' || isset($_SERVER['REQUEST_METHOD']) &&
    (basename($_SERVER['PHP_SELF']) === 'health.php' ||
     (isset($_GET['health']) && $_GET['health'] === '1'))) {
    header("HTTP/1.1 200 OK");
    header("Content-Type: text/plain");
    echo "OK";
    exit(0);
}

// Set content type FIRST before any output
header("Content-Type: text/html; charset=utf-8", true);
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");

session_start();

// Define base paths dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

// Include core files with error handling
try {
    require_once __DIR__ . '/../src/config/Database.php';
} catch (Exception $e) {
    // Database connection failed - log but continue
    error_log("Database initialization error: " . $e->getMessage());
}

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
        try {
            global $conn;
            if (!$conn) {
                return null;
            }
            $user_id = $_SESSION['user_id'];
            $query = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                return null;
            }
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
