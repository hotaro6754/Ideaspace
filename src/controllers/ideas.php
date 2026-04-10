<?php
/**
 * Ideas Controller
 * Handles idea creation and management
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Idea.php';

class IdeasController {
    private $idea;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->idea = new Idea($db);
    }

    /**
     * Handle create idea
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Get POST data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $skills_needed = json_decode($_POST['skills_needed'] ?? '[]', true);
        $user_id = $_SESSION['user_id'] ?? null;

        // Validation
        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if (empty($title) || empty($description) || empty($domain)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (!is_array($skills_needed) || count($skills_needed) === 0) {
            return ['success' => false, 'error' => 'At least one skill is required'];
        }

        // Create idea
        return $this->idea->create($user_id, $title, $description, $domain, $skills_needed);
    }

    /**
     * Update an idea
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $skills_needed = json_decode($_POST['skills_needed'] ?? '[]', true);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($idea_id <= 0) {
            return ['success' => false, 'error' => 'Invalid idea ID'];
        }

        if (empty($title) || empty($description) || empty($domain)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (!is_array($skills_needed) || count($skills_needed) === 0) {
            return ['success' => false, 'error' => 'At least one skill is required'];
        }

        // Check if user is idea creator
        if (!$this->idea->isCreator($idea_id, $user_id)) {
            return ['success' => false, 'error' => 'You are not authorized to update this idea'];
        }

        // Update idea
        return $this->idea->update($idea_id, $title, $description, $domain, $skills_needed);
    }

    /**
     * Delete an idea
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($idea_id <= 0) {
            return ['success' => false, 'error' => 'Invalid idea ID'];
        }

        // Check if user is idea creator
        if (!$this->idea->isCreator($idea_id, $user_id)) {
            return ['success' => false, 'error' => 'You are not authorized to delete this idea'];
        }

        return $this->idea->delete($idea_id);
    }

    /**
     * Get ideas list
     */
    public function getList($limit = 20, $offset = 0, $filters = []) {
        return $this->idea->getAll($limit, $offset, $filters);
    }

    /**
     * Get single idea
     */
    public function getById($idea_id) {
        return $this->idea->getById($idea_id);
    }

    /**
     * Get ideas by user
     */
    public function getByUser($user_id, $limit = 20, $offset = 0) {
        return $this->idea->getByUser($user_id);
    }

    /**
     * Update idea status
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($idea_id <= 0) {
            return ['success' => false, 'error' => 'Invalid idea ID'];
        }

        if (empty($status)) {
            return ['success' => false, 'error' => 'Status is required'];
        }

        // Check if user is idea creator
        if (!$this->idea->isCreator($idea_id, $user_id)) {
            return ['success' => false, 'error' => 'You are not authorized to update this idea'];
        }

        return $this->idea->updateStatus($idea_id, $status);
    }

    /**
     * Upvote an idea
     */
    public function upvote() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($idea_id <= 0) {
            return ['success' => false, 'error' => 'Invalid idea ID'];
        }

        require_once __DIR__ . '/../models/Upvote.php';
        $upvoteModel = new Upvote($this->conn);

        // Check if user already upvoted
        if ($upvoteModel->hasUpvoted($idea_id, $user_id)) {
            // Remove upvote
            return $upvoteModel->removeUpvote($idea_id, $user_id);
        } else {
            // Add upvote
            return $upvoteModel->create($idea_id, $user_id);
        }
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$ideasCtrl = new IdeasController($conn);

// Route to appropriate method
if ($action === 'create') {
    $result = $ideasCtrl->create();
    if ($result['success']) {
        $_SESSION['message'] = 'Idea posted successfully!';
        header('Location: ' . BASE_URL . '/?page=ideas');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=ideas&action=create');
        exit();
    }
} elseif ($action === 'update') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $ideasCtrl->update();
    if ($result['success']) {
        $_SESSION['message'] = 'Idea updated successfully!';
        header('Location: ' . BASE_URL . '/?page=idea-detail&id=' . $_POST['idea_id']);
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=idea-detail&id=' . $_POST['idea_id'] . '&action=edit');
        exit();
    }
} elseif ($action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $ideasCtrl->delete();
    if ($result['success']) {
        $_SESSION['message'] = 'Idea deleted successfully!';
        header('Location: ' . BASE_URL . '/?page=ideas');
        exit();
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=idea-detail&id=' . $_POST['idea_id']);
        exit();
    }
} elseif ($action === 'update-status') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $ideasCtrl->updateStatus();
    echo json_encode($result);
    exit();
} elseif ($action === 'upvote') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $ideasCtrl->upvote();
    echo json_encode($result);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
