<?php
/**
 * Authentication Controller
 * Handles user registration, login, and logout
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->user = new User($db);
    }

    /**
     * Handle user registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
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
        return $this->user->register($roll_number, $name, $email, $password, $branch, $year);
    }

    /**
     * Handle user login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Get POST data
        $identifier = trim($_POST['identifier'] ?? ''); // roll_number or email
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($identifier) || empty($password)) {
            return ['success' => false, 'error' => 'Roll number/Email and password are required'];
        }

        // Attempt login
        $result = $this->user->login($identifier, $password);

        if ($result['success']) {
            // Set session
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['roll_number'] = $result['user']['roll_number'];
            $_SESSION['name'] = $result['user']['name'];
            $_SESSION['user_type'] = $result['user']['user_type'];
            $_SESSION['email'] = $result['user']['email'];
        }

        return $result;
    }

    /**
     * Handle user logout
     */
    public function logout() {
        session_destroy();
        return ['success' => true];
    }
}

// Determine action
$action = $_GET['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$auth = new AuthController($conn);

// Route to appropriate method
if ($action === 'register' || $_POST['action'] === 'register') {
    $result = $auth->register();
    if ($result['success']) {
        $_SESSION['message'] = 'Registration successful! Please login.';
        header('Location: ' . BASE_URL . '/?page=login');
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
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
