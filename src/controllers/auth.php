<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../models/EmailVerification.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/RateLimit.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../helpers/Security.php';

// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$db = getConnection();
$userModel = new User($db);
$authLog = new AuthLog($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$page = $_GET['page'] ?? '';

if ($action === 'register') {
    $res = $userModel->register($_POST['roll_number'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['branch'], $_POST['year']);
    if ($res['success']) {
        $_SESSION['message'] = "Profile initialized! You can now sign in.";
        header("Location: " . BASE_URL . "/?page=login");
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=register");
    }
    exit();
}

if ($action === 'login') {
    $res = $userModel->login($_POST['identifier'], $_POST['password']);
    if ($res['success']) {
        $_SESSION['user_id'] = $res['user']['id'];
        $_SESSION['name'] = $res['user']['name'];
        $_SESSION['email'] = $res['user']['email'];
        // Check if role/interests are set, if not redirect to onboarding
        if (empty($res['user']['academic_role']) || empty($res['user']['interests'])) {
             header("Location: " . BASE_URL . "/?page=onboarding");
        } else {
             header("Location: " . BASE_URL . "/?page=dashboard");
        }
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=login");
    }
    exit();
}

if ($action === 'logout' || $page === 'logout') {
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
}
?>
