<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../models/EmailVerification.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/RateLimit.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../helpers/Security.php';

$db = getConnection();
$userModel = new User($db);
$authLog = new AuthLog($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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
        header("Location: " . BASE_URL . "/?page=dashboard");
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=login");
    }
    exit();
}

if ($action === 'logout') {
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
}
?>
