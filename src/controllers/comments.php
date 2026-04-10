<?php
/**
 * Comments Controller
 * Handles idea comments with threading support
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/IdeaComment.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../helpers/Security.php';

class CommentsController {
    private $commentModel;
    private $activityLog;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->commentModel = new IdeaComment($db);
        $this->activityLog = new ActivityLog($db);
    }

    /**
     * Create a comment on an idea
     */
    public function create() {
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
        $idea_id = (int)($_POST['idea_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $parent_comment_id = (int)($_POST['parent_comment_id'] ?? 0);
        $parent_comment_id = $parent_comment_id > 0 ? $parent_comment_id : null;

        if ($idea_id === 0) {
            return ['success' => false, 'error' => 'Idea ID is required'];
        }

        $result = $this->commentModel->create($idea_id, $user_id, $content, $parent_comment_id);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'comment', 'create', $idea_id, ['comment_id' => $result['comment_id']]);
        }

        return $result;
    }

    /**
     * Get comments for an idea
     */
    public function getForIdea() {
        $idea_id = (int)($_GET['idea_id'] ?? $_POST['idea_id'] ?? 0);
        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? $_POST['offset'] ?? 0);

        if ($idea_id === 0) {
            return ['success' => false, 'error' => 'Idea ID is required'];
        }

        // Validate pagination
        $limit = min($limit, 100);
        $offset = max($offset, 0);

        $comments = $this->commentModel->getForIdea($idea_id, $limit, $offset);

        return [
            'success' => true,
            'comments' => $comments,
            'count' => count($comments)
        ];
    }

    /**
     * Get replies to a comment
     */
    public function getReplies() {
        $parent_comment_id = (int)($_GET['comment_id'] ?? $_POST['comment_id'] ?? 0);

        if ($parent_comment_id === 0) {
            return ['success' => false, 'error' => 'Comment ID is required'];
        }

        $replies = $this->commentModel->getReplies($parent_comment_id);

        return [
            'success' => true,
            'replies' => $replies,
            'count' => count($replies)
        ];
    }

    /**
     * Like a comment
     */
    public function like() {
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
        $comment_id = (int)($_POST['comment_id'] ?? 0);

        if ($comment_id === 0) {
            return ['success' => false, 'error' => 'Comment ID is required'];
        }

        return $this->commentModel->likeComment($comment_id, $user_id);
    }

    /**
     * Edit a comment
     */
    public function edit() {
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
        $comment_id = (int)($_POST['comment_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($comment_id === 0) {
            return ['success' => false, 'error' => 'Comment ID is required'];
        }

        $result = $this->commentModel->edit($comment_id, $user_id, $content);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'comment', 'update', $comment_id);
        }

        return $result;
    }

    /**
     * Delete a comment
     */
    public function delete() {
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
        $comment_id = (int)($_POST['comment_id'] ?? 0);

        if ($comment_id === 0) {
            return ['success' => false, 'error' => 'Comment ID is required'];
        }

        $result = $this->commentModel->delete($comment_id, $user_id);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'comment', 'delete', $comment_id);
        }

        return $result;
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$comments = new CommentsController($conn);

// Return JSON for AJAX requests
header('Content-Type: application/json');

// Route to appropriate method
if ($action === 'create') {
    echo json_encode($comments->create());
} elseif ($action === 'get') {
    echo json_encode($comments->getForIdea());
} elseif ($action === 'getReplies') {
    echo json_encode($comments->getReplies());
} elseif ($action === 'like') {
    echo json_encode($comments->like());
} elseif ($action === 'edit') {
    echo json_encode($comments->edit());
} elseif ($action === 'delete') {
    echo json_encode($comments->delete());
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
exit();
?>
