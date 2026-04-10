<?php
/**
 * File Upload Controller
 * Handles file uploads and management
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FileUpload.php';

class FileUploadController {
    private $fileUpload;
    private $conn;
    private $uploadDir = __DIR__ . '/../../uploads';
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    private $maxFileSize = 10485760; // 10MB

    public function __construct($db) {
        $this->conn = $db;
        $this->fileUpload = new FileUpload($db);

        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload a file
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $user_id = $_SESSION['user_id'] ?? null;
        $idea_id = (int)($_POST['idea_id'] ?? 0);

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if (!isset($_FILES['file'])) {
            return ['success' => false, 'error' => 'No file provided'];
        }

        $file = $_FILES['file'];

        // Validation
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error: ' . $this->getUploadError($file['error'])];
        }

        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'error' => 'File is too large (max 10MB)'];
        }

        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('file_') . '_' . time() . '.' . $ext;
        $filepath = $this->uploadDir . '/' . $filename;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Failed to save file'];
        }

        // Save to database
        $fileUrl = BASE_URL . '/uploads/' . $filename;
        return $this->fileUpload->create($user_id, $idea_id, $file['name'], $fileUrl, $file['type'], $file['size']);
    }

    /**
     * Get files for an idea
     */
    public function getIdeaFiles($idea_id) {
        if ($idea_id <= 0) {
            return [];
        }
        return $this->fileUpload->getByIdea($idea_id);
    }

    /**
     * Get user's files
     */
    public function getUserFiles($user_id, $limit = 20, $offset = 0) {
        if (!$user_id) {
            return [];
        }
        return $this->fileUpload->getByUser($user_id, $limit, $offset);
    }

    /**
     * Delete a file
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $file_id = (int)($_POST['file_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($file_id <= 0) {
            return ['success' => false, 'error' => 'Invalid file ID'];
        }

        // Get file details
        $file = $this->fileUpload->getById($file_id);
        if (!$file) {
            return ['success' => false, 'error' => 'File not found'];
        }

        // Check authorization (user must be uploader or idea creator)
        if ($file['user_id'] !== $user_id) {
            // Check if user is idea creator
            require_once __DIR__ . '/../models/Idea.php';
            $ideaModel = new Idea($this->conn);
            if (!$ideaModel->isCreator($file['idea_id'], $user_id)) {
                return ['success' => false, 'error' => 'You are not authorized to delete this file'];
            }
        }

        // Delete physical file
        $urlParts = parse_url($file['file_url']);
        $filepath = __DIR__ . '/../../' . ltrim($urlParts['path'], '/');
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete from database
        return $this->fileUpload->delete($file_id);
    }

    /**
     * Get upload error message
     */
    private function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        return $errors[$errorCode] ?? 'Unknown error';
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
$fileCtrl = new FileUploadController($conn);

// Route to appropriate method
if ($action === 'upload') {
    $result = $fileCtrl->upload();
    echo json_encode($result);
    exit();
} elseif ($action === 'get-idea-files') {
    $idea_id = (int)($_GET['idea_id'] ?? 0);
    $files = $fileCtrl->getIdeaFiles($idea_id);
    echo json_encode(['success' => true, 'files' => $files]);
    exit();
} elseif ($action === 'get-user-files') {
    $user_id = $_SESSION['user_id'];
    $page = (int)($_GET['page'] ?? 1);
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $files = $fileCtrl->getUserFiles($user_id, $limit, $offset);
    echo json_encode(['success' => true, 'files' => $files]);
    exit();
} elseif ($action === 'delete') {
    $result = $fileCtrl->delete();
    echo json_encode($result);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
