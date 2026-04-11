<?php
/**
 * IdeaSync - Main Entry Point
 */
session_start();

// Define base paths
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', '/src/assets');

require_once __DIR__ . '/../src/config/Database.php';

// Auth Helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$page = $_GET['page'] ?? 'home';

$routes = [
    'home' => 'src/views/home.php',
    'feed' => 'src/views/ideas/list.php',
    'ideas' => ($_GET['action'] === 'create') ? 'src/views/ideas/create.php' : 'src/views/ideas/list.php',
    'idea-detail' => 'src/views/ideas/detail.php',
    'login' => 'src/views/auth/login.php',
    'register' => 'src/views/auth/register.php',
    'profile' => 'src/views/profile.php',
    'leaderboard' => 'src/views/leaderboard.php',
    'forge' => 'src/views/forge.php',
];

$view_file = __DIR__ . '/../' . ($routes[$page] ?? 'src/views/home.php');

if (file_exists($view_file)) {
    include $view_file;
} else {
    include __DIR__ . '/../src/views/404.php';
}
