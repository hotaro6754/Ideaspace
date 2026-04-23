<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../models/EmailVerification.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../helpers/Security.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$db = getConnection();
$userModel = new User($db);
$authLog = new AuthLog($db);
$emailVerify = new EmailVerification($db);
$passReset = new PasswordReset($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'register') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token.";
        header("Location: " . BASE_URL . "/?page=register");
        exit();
    }

    $res = $userModel->register($_POST['roll_number'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['branch'], $_POST['year']);
    if ($res['success']) {
        $verifyRes = $emailVerify->create($res['user_id']);
        if ($verifyRes['success']) {
            EmailService::sendVerificationEmail($_POST['email'], $_POST['name'], BASE_URL . "/?page=verify&token=" . $verifyRes['token']);
        }
        $_SESSION['message'] = "Profile initialized! Please check your email for verification.";
        header("Location: " . BASE_URL . "/?page=login");
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=register");
    }
    exit();
}

if ($action === 'login') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token.";
        header("Location: " . BASE_URL . "/?page=login");
        exit();
    }

    $identifier = $_POST['identifier'];
    if (!Security::checkRateLimit($identifier, 'login')) {
        $_SESSION['error'] = "Too many login attempts. Please try again later.";
        header("Location: " . BASE_URL . "/?page=login");
        exit();
    }

    $res = $userModel->login($identifier, $_POST['password']);
    if ($res['success']) {
        if (!($res['user']['is_active'] ?? true)) {
            $_SESSION['error'] = "Your account is deactivated. Please contact support.";
            header("Location: " . BASE_URL . "/?page=login");
            exit();
        }
        if ($res['user']['is_suspended'] ?? false) {
            $_SESSION['error'] = "Your account is suspended: " . ($res['user']['suspension_reason'] ?? 'Administrative action');
            header("Location: " . BASE_URL . "/?page=login");
            exit();
        }

        $_SESSION['user_id'] = $res['user']['id'];
        $_SESSION['name'] = $res['user']['name'];
        $_SESSION['email'] = $res['user']['email'];
        $_SESSION['is_admin'] = (bool)($res['user']['is_admin'] ?? false);

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

if ($action === 'forgot-password') {
    $email = $_POST['email'];
    $res = $passReset->createToken($email);
    if ($res['success']) {
        EmailService::sendPasswordResetEmail($email, "User", BASE_URL . "/?page=reset-password&token=" . $res['token']);
        $_SESSION['message'] = "Password reset link sent to your email.";
    } else {
        $_SESSION['error'] = "Failed to send reset link.";
    }
    header("Location: " . BASE_URL . "/?page=login");
    exit();
}

if ($action === 'reset-password') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Invalid security token.";
        header("Location: " . BASE_URL . "/?page=reset-password&token=" . $_POST['token']);
        exit();
    }

    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: " . BASE_URL . "/?page=reset-password&token=" . $token);
        exit();
    }

    $res = $passReset->resetPassword($token, $password);
    if ($res['success']) {
        $_SESSION['message'] = "Password updated successfully. You can now sign in.";
        header("Location: " . BASE_URL . "/?page=login");
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=reset-password&token=" . $token);
    }
    exit();
}

if ($action === 'verify') {
    $token = $_GET['token'] ?? '';
    $res = $emailVerify->verify($token);
    if ($res['success']) {
        $_SESSION['message'] = "Email verified! You can now access all features.";
    } else {
        $_SESSION['error'] = $res['error'];
    }
    header("Location: " . BASE_URL . "/?page=login");
    exit();
}

if ($action === 'logout') {
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
}
?>
