<?php
/**
 * Authentication Controller
 * Handles user registration, login, logout, email verification, and password reset
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../models/EmailVerification.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../models/RateLimit.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../helpers/Security.php';

class AuthController {
    private $user;
    private $authLog;
    private $emailVerification;
    private $passwordReset;
    private $rateLimit;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->user = new User($db);
        $this->authLog = new AuthLog($db);
        $this->emailVerification = new EmailVerification($db);
        $this->passwordReset = new PasswordReset($db);
        $this->rateLimit = new RateLimit($db);
    }

    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        // Get POST data
        $roll_number = trim($_POST['roll_number'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $branch = trim($_POST['branch'] ?? '');
        $year = (int)($_POST['year'] ?? 0);

        // Validation
        if (empty($roll_number) || empty($name) || empty($email) || empty($password) || empty($branch) || $year === 0) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }

        if ($password !== $password_confirm) {
            return ['success' => false, 'error' => 'Passwords do not match'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address'];
        }

        if ($year < 1 || $year > 4) {
            return ['success' => false, 'error' => 'Invalid year selected'];
        }

        // Attempt registration
        $result = $this->user->register($roll_number, $name, $email, $password, $branch, $year);

        if ($result['success']) {
            $user_id = $result['user_id'];

            // Create email verification token
            $token_result = $this->emailVerification->create($user_id);
            if ($token_result['success']) {
                // Send verification email
                $user = $this->user->getById($user_id);
                $verification_link = BASE_URL . "/?page=verify-email&token=" . $token_result['token'];

                EmailService::sendVerificationEmail($user['email'], $user['name'], $verification_link);

                // Log registration event
                $this->authLog->log($user_id, 'register', true);
            }
        }

        return $result;
    }

    /**
     * Handle user login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        // Get POST data
        $identifier = trim($_POST['identifier'] ?? ''); // roll_number or email
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($identifier) || empty($password)) {
            return ['success' => false, 'error' => 'Roll number/Email and password are required'];
        }

        // Check rate limiting
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if ($this->rateLimit->isLimited($ip_address, 'login_attempt', 5, 60)) {
            $this->authLog->log(null, 'login_failure', false, ['reason' => 'rate_limited']);
            return ['success' => false, 'error' => 'Too many login attempts. Please try again later.'];
        }

        // Record attempt
        $this->rateLimit->recordAttempt($ip_address, 'login_attempt', 60);

        // Attempt login
        $result = $this->user->login($identifier, $password);

        if ($result['success']) {
            $user = $result['user'];
            $user_id = $user['id'];

            // Check email verification
            if (!$user['email_verified']) {
                // Send new verification email
                $token_result = $this->emailVerification->resendToken($user_id);
                if ($token_result['success']) {
                    $verification_link = BASE_URL . "/?page=verify-email&token=" . $token_result['token'];
                    EmailService::sendVerificationEmail($user['email'], $user['name'], $verification_link);
                }

                $this->authLog->log($user_id, 'login_failure', false, ['reason' => 'email_not_verified']);
                return ['success' => false, 'error' => 'Please verify your email before logging in. Check your inbox for verification link.'];
            }

            // Check account status
            if (!$user['is_active']) {
                $this->authLog->log($user_id, 'login_failure', false, ['reason' => 'account_inactive']);
                return ['success' => false, 'error' => 'Account is inactive'];
            }

            if ($user['is_suspended']) {
                $this->authLog->log($user_id, 'login_failure', false, ['reason' => 'account_suspended']);
                return ['success' => false, 'error' => 'Account is suspended'];
            }

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['roll_number'] = $user['roll_number'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in_at'] = time();

            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();

            // Log successful login
            $this->authLog->log($user_id, 'login_success', true);

            // Reset rate limit on success
            $this->rateLimit->reset($ip_address, 'login_attempt');

            return ['success' => true, 'user' => $user];
        }

        // Log failed login
        $user_id = null;
        if ($result['error'] !== 'Invalid username/email or password') {
            // Try to get user ID if it's another type of error
            $user = $this->user->getByIdentifier($identifier);
            if ($user) {
                $user_id = $user['id'];
            }
        }
        $this->authLog->log($user_id, 'login_failure', false, ['reason' => 'invalid_credentials']);

        return $result;
    }

    /**
     * Handle logout
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $this->authLog->log($user_id, 'logout', true);
        }
        session_destroy();
        return ['success' => true];
    }

    /**
     * Verify email with token
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if (empty($token)) {
            return ['success' => false, 'error' => 'Verification token is required'];
        }

        return $this->emailVerification->verify($token);
    }

    /**
     * Resend verification email
     */
    public function resendVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            return ['success' => false, 'error' => 'Email is required'];
        }

        // Get user by email
        $user = $this->user->getByEmail($email);
        if (!$user) {
            return ['success' => false, 'error' => 'Email not found'];
        }

        $user_id = $user['id'];

        // Check if already verified
        if ($user['email_verified']) {
            return ['success' => false, 'error' => 'Email already verified'];
        }

        // Create and send token
        $token_result = $this->emailVerification->resendToken($user_id);
        if ($token_result['success']) {
            $verification_link = BASE_URL . "/?page=verify-email&token=" . $token_result['token'];
            EmailService::sendVerificationEmail($user['email'], $user['name'], $verification_link);
            return ['success' => true, 'message' => 'Verification email sent'];
        }

        return $token_result;
    }

    /**
     * Request password reset
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Valid email is required'];
        }

        // Check rate limiting for password reset
        $identifier = 'email:' . $email;
        if ($this->rateLimit->isLimited($identifier, 'password_reset', 3, 60)) {
            return ['success' => false, 'error' => 'Too many password reset attempts. Please try again later.'];
        }

        // Record attempt
        $this->rateLimit->recordAttempt($identifier, 'password_reset', 60);

        // Create reset token
        $reset_result = $this->passwordReset->createToken($email);

        if (isset($reset_result['token'])) {
            // Send reset email
            $user_id = $reset_result['user_id'];
            $user = $this->user->getById($user_id);
            $reset_link = BASE_URL . "/?page=reset-password&token=" . $reset_result['token'];

            EmailService::sendPasswordResetEmail($user['email'], $user['name'], $reset_link);

            $this->authLog->log($user_id, 'password_reset_requested', true);
        }

        // Always return success for security
        return ['success' => true, 'message' => 'If account exists, password reset link will be sent'];
    }

    /**
     * Reset password with token
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($token) || empty($password) || empty($password_confirm)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }

        if ($password !== $password_confirm) {
            return ['success' => false, 'error' => 'Passwords do not match'];
        }

        return $this->passwordReset->resetPassword($token, $password);
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$auth = new AuthController($conn);

// Route to appropriate method
if ($action === 'register' || $_POST['action'] === 'register') {
    $result = $auth->register();
    if ($result['success']) {
        $_SESSION['message'] = 'Registration successful! Please verify your email.';
        header('Location: ' . BASE_URL . '/?page=check-email');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=register');
        exit();
    }
} elseif ($action === 'login' || $_POST['action'] === 'login') {
    $result = $auth->login();
    if ($result['success']) {
        $_SESSION['message'] = 'Login successful!';
        header('Location: ' . BASE_URL . '/?page=dashboard');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    }
} elseif ($action === 'logout') {
    $auth->logout();
    $_SESSION['message'] = 'Logged out successfully!';
    header('Location: ' . BASE_URL . '/?page=home');
    exit();
} elseif ($action === 'verify-email' || $action === 'verify') {
    $result = $auth->verifyEmail();
    if ($result['success']) {
        $_SESSION['message'] = 'Email verified successfully! Please login.';
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=verify-email');
        exit();
    }
} elseif ($action === 'resend-verification') {
    $result = $auth->resendVerification();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
} elseif ($action === 'forgot-password') {
    $result = $auth->forgotPassword();
    if ($result['success']) {
        $_SESSION['message'] = 'If account exists, password reset link has been sent to your email.';
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=forgot-password');
        exit();
    }
} elseif ($action === 'reset-password') {
    $result = $auth->resetPassword();
    if ($result['success']) {
        $_SESSION['message'] = 'Password reset successfully! Please login with your new password.';
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=reset-password&token=' . ($_POST['token'] ?? ''));
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
