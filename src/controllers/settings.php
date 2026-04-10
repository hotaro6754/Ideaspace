<?php
/**
 * Settings Controller
 * Handles user preferences and settings
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/UserPreferences.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Security.php';

class SettingsController {
    private $preferencesModel;
    private $userModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->preferencesModel = new UserPreferences($db);
        $this->userModel = new User($db);
    }

    /**
     * Get user preferences
     */
    public function getPreferences() {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        $user_id = $_SESSION['user_id'];
        $preferences = $this->preferencesModel->getOrCreate($user_id);

        return ['success' => true, 'preferences' => $preferences];
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $notifications = [
            'email_notifications' => isset($_POST['email_notifications']),
            'email_on_application' => isset($_POST['email_on_application']),
            'email_on_acceptance' => isset($_POST['email_on_acceptance']),
            'email_on_message' => isset($_POST['email_on_message']),
            'email_on_upvote' => isset($_POST['email_on_upvote']),
            'email_on_comment' => isset($_POST['email_on_comment'])
        ];

        return $this->preferencesModel->updateNotifications($user_id, $notifications);
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $profile_visibility = $_POST['profile_visibility'] ?? 'public';
        $ideas_visibility = $_POST['ideas_visibility'] ?? 'public';

        return $this->preferencesModel->updatePrivacy($user_id, $profile_visibility, $ideas_visibility);
    }

    /**
     * Update theme preference
     */
    public function updateTheme() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $theme = $_POST['theme'] ?? 'light';

        return $this->preferencesModel->updateTheme($user_id, $theme);
    }

    /**
     * Update language preference
     */
    public function updateLanguage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $language = $_POST['language'] ?? 'en';

        return $this->preferencesModel->updateLanguage($user_id, $language);
    }

    /**
     * Update profile information
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        if (empty($name)) {
            return ['success' => false, 'error' => 'Name is required'];
        }

        if (strlen($bio) > 500) {
            return ['success' => false, 'error' => 'Bio too long (max 500 characters)'];
        }

        $query = "UPDATE users SET name = ?, bio = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("ssi", $name, $bio, $user_id);

        if ($stmt->execute()) {
            // Update session
            $_SESSION['name'] = $name;
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }

        return ['success' => false, 'error' => 'Failed to update profile'];
    }

    /**
     * Change password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $new_password_confirm = $_POST['new_password_confirm'] ?? '';

        if (empty($current_password) || empty($new_password)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($new_password) < 8) {
            return ['success' => false, 'error' => 'New password must be at least 8 characters'];
        }

        if ($new_password !== $new_password_confirm) {
            return ['success' => false, 'error' => 'Passwords do not match'];
        }

        // Get user
        $user = $this->userModel->getById($user_id);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Update password
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }

        return ['success' => false, 'error' => 'Failed to change password'];
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$settings = new SettingsController($conn);

// Return JSON for AJAX requests
header('Content-Type: application/json');

// Route to appropriate method
if ($action === 'getPreferences') {
    echo json_encode($settings->getPreferences());
} elseif ($action === 'updateNotifications') {
    echo json_encode($settings->updateNotifications());
} elseif ($action === 'updatePrivacy') {
    echo json_encode($settings->updatePrivacy());
} elseif ($action === 'updateTheme') {
    echo json_encode($settings->updateTheme());
} elseif ($action === 'updateLanguage') {
    echo json_encode($settings->updateLanguage());
} elseif ($action === 'updateProfile') {
    echo json_encode($settings->updateProfile());
} elseif ($action === 'changePassword') {
    echo json_encode($settings->changePassword());
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
exit();
?>
