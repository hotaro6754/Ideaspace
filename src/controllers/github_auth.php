<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/GitHubAPI.php';
require_once __DIR__ . '/../models/User.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$code = $_GET['code'] ?? null;
if (!$code) {
    header("Location: " . BASE_URL);
    exit();
}

$client_id = Env::get('GITHUB_CLIENT_ID');
$client_secret = Env::get('GITHUB_CLIENT_SECRET');

// Exchange code for access token
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'code' => $code
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = json_decode(curl_exec($ch), true);
$access_token = $response['access_token'] ?? null;

if ($access_token) {
    // Get user info
    $ch = curl_init('https://api.github.com/user');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: token ' . $access_token,
        'User-Agent: IdeaSync-App'
    ]);
    $github_user = json_decode(curl_exec($ch), true);

    if ($github_user && isset($github_user['login'])) {
        $db = getConnection();
        if (isset($_SESSION['user_id'])) {
            // Link to existing account
            $stmt = $db->prepare("UPDATE users SET github_username = ? WHERE id = ?");
            $stmt->bind_param("si", $github_user['login'], $_SESSION['user_id']);
            $stmt->execute();
            $_SESSION['message'] = "GitHub account linked successfully!";
            header("Location: " . BASE_URL . "/?page=profile");
        } else {
            // Try to login via GitHub
            $stmt = $db->prepare("SELECT * FROM users WHERE github_username = ?");
            $stmt->bind_param("s", $github_user['login']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                header("Location: " . BASE_URL . "/?page=dashboard");
            } else {
                $_SESSION['error'] = "No IdeaSync account linked to this GitHub. Please sign in and link your account.";
                header("Location: " . BASE_URL . "/?page=login");
            }
        }
    } else {
        $_SESSION['error'] = "Failed to fetch GitHub user data.";
        header("Location: " . BASE_URL . "/?page=login");
    }
} else {
    $_SESSION['error'] = "GitHub authorization failed.";
    header("Location: " . BASE_URL . "/?page=login");
}
?>
