<?php
/**
 * Collaboration Controller
 * Handles collaboration applications and management
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Collaboration.php';

class CollaborationController {
    private $application;
    private $collaboration;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->application = new Application($db);
        $this->collaboration = new Collaboration($db);
    }

    /**
     * Apply for an idea as a collaborator
     */
    public function apply() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // SECURITY: Verify CSRF token
        require_once __DIR__ . '/../helpers/Security.php';
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($idea_id <= 0) {
            return ['success' => false, 'error' => 'Invalid idea ID'];
        }

        if (empty($role)) {
            return ['success' => false, 'error' => 'Role is required'];
        }

        if (empty($message) || strlen($message) < 20) {
            return ['success' => false, 'error' => 'Message must be at least 20 characters'];
        }

        // Check if user already applied
        $existing = $this->application->checkExisting($idea_id, $user_id);
        if ($existing) {
            return ['success' => false, 'error' => 'You have already applied for this idea'];
        }

        return $this->application->create($idea_id, $user_id, $role, $message);
    }

    /**
     * Accept a collaboration application
     */
    public function acceptApplication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // SECURITY: Verify CSRF token
        require_once __DIR__ . '/../helpers/Security.php';
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $app_id = (int)($_POST['app_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($app_id <= 0) {
            return ['success' => false, 'error' => 'Invalid application ID'];
        }

        // Get application details
        $app = $this->application->getById($app_id);
        if (!$app) {
            return ['success' => false, 'error' => 'Application not found'];
        }

        // Check if user is idea creator
        require_once __DIR__ . '/../models/Idea.php';
        $ideaModel = new Idea($this->conn);
        if (!$ideaModel->isCreator($app['idea_id'], $user_id)) {
            return ['success' => false, 'error' => 'You are not authorized to accept this application'];
        }

        // Accept application
        $result = $this->application->updateStatus($app_id, 'accepted');
        if (!$result['success']) {
            return $result;
        }

        // Create collaboration record
        return $this->collaboration->create($app['idea_id'], $app['user_id'], $app['role']);
    }

    /**
     * Reject a collaboration application
     */
    public function rejectApplication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // SECURITY: Verify CSRF token
        require_once __DIR__ . '/../helpers/Security.php';
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $app_id = (int)($_POST['app_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($app_id <= 0) {
            return ['success' => false, 'error' => 'Invalid application ID'];
        }

        // Get application details
        $app = $this->application->getById($app_id);
        if (!$app) {
            return ['success' => false, 'error' => 'Application not found'];
        }

        // Check if user is idea creator
        require_once __DIR__ . '/../models/Idea.php';
        $ideaModel = new Idea($this->conn);
        if (!$ideaModel->isCreator($app['idea_id'], $user_id)) {
            return ['success' => false, 'error' => 'You are not authorized to reject this application'];
        }

        return $this->application->updateStatus($app_id, 'rejected', $reason);
    }

    /**
     * Get applications for user's ideas
     */
    public function getApplications($user_id) {
        if (!$user_id) {
            return [];
        }
        return $this->application->getByCreator($user_id);
    }

    /**
     * Get user's applications
     */
    public function getUserApplications($user_id) {
        if (!$user_id) {
            return [];
        }
        return $this->application->getByUser($user_id);
    }

    /**
     * Get user's collaborations
     */
    public function getCollaborations($user_id) {
        if (!$user_id) {
            return [];
        }
        return $this->collaboration->getByUser($user_id);
    }

    /**
     * Leave a collaboration
     */
    public function leaveCollaboration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $collab_id = (int)($_POST['collab_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($collab_id <= 0) {
            return ['success' => false, 'error' => 'Invalid collaboration ID'];
        }

        // Get collaboration details
        $collab = $this->collaboration->getById($collab_id);
        if (!$collab || $collab['collaborator_id'] !== $user_id) {
            return ['success' => false, 'error' => 'You are not part of this collaboration'];
        }

        return $this->collaboration->updateStatus($collab_id, 'left');
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$collabCtrl = new CollaborationController($conn);

// Route to appropriate method
if ($action === 'apply') {
    $result = $collabCtrl->apply();
    echo json_encode($result);
    exit();
} elseif ($action === 'accept') {
    $result = $collabCtrl->acceptApplication();
    if ($result['success']) {
        $_SESSION['message'] = 'Application accepted!';
    } else {
        $_SESSION['error'] = $result['error'];
    }
    echo json_encode($result);
    exit();
} elseif ($action === 'reject') {
    $result = $collabCtrl->rejectApplication();
    if ($result['success']) {
        $_SESSION['message'] = 'Application rejected!';
    } else {
        $_SESSION['error'] = $result['error'];
    }
    echo json_encode($result);
    exit();
} elseif ($action === 'leave') {
    $result = $collabCtrl->leaveCollaboration();
    if ($result['success']) {
        $_SESSION['message'] = 'You have left the collaboration';
        header('Location: ' . BASE_URL . '/?page=profile&section=collaborations');
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=profile&section=collaborations');
    }
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
