<?php
/**
 * File Upload Controller - FIXED
 * Handles file uploads and management with security improvements
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Env.php';
require_once __DIR__ . '/../models/FileUpload.php';
require_once __DIR__ . '/../helpers/Security.php';

class FileUploadController {
    private $fileUpload;
    private $conn;
    private $uploadDir;
    private $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    private $maxFileSize;

    public function __construct($db) {
        $this->conn = $db;
        $this->fileUpload = new FileUpload($db);

        // Get upload directory from config or use default
        $this->uploadDir = Env::get('UPLOAD_DIR', __DIR__ . '/../../uploads');
        if (!is_absolute_path($this->uploadDir)) {
            $this->uploadDir = __DIR__ . '/../../' . $this->uploadDir;
        }

        // Get max file size from config or use default
        $this->maxFileSize = (int)Env::get('MAX_UPLOAD_SIZE', 10485760); // 10MB default

        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload a file - FIXED: now validates CSRF token and MIME types
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // ✅ FIX #1: VERIFY CSRF TOKEN
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request - CSRF token validation failed'];
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
            return ['success' => false, 'error' => 'File is too large (max ' . ($this->maxFileSize / 1048576) . 'MB)'];
        }

        // ✅ FIX #2: VALIDATE FILE EXTENSION
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        // ✅ FIX #3: VALIDATE MIME TYPE SERVER-SIDE (not client-provided)
        $mimeType = $this->getServerMimeType($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return ['success' => false, 'error' => 'File content type not allowed (detected: ' . $mimeType . ')'];
        }

        // Generate secure filename
        $filename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
        $filepath = $this->uploadDir . '/' . $filename;

        // Move file with restricted permissions
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Failed to save file'];
        }

        // Set restrictive permissions
        chmod($filepath, 0644);

        // Save to database
        $fileUrl = '/uploads/' . $filename; // Use relative path, not BASE_URL
        return $this->fileUpload->create($user_id, $idea_id, $file['name'], $fileUrl, $mimeType, $file['size']);
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
     * Delete a file - FIXED: now validates CSRF token and prevents path traversal
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // ✅ FIX #4: VERIFY CSRF TOKEN
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request - CSRF token validation failed'];
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

        // ✅ FIX #5: PREVENT PATH TRAVERSAL - Use realpath to resolve actual path
        $storedPath = $file['file_url'];

        // Construct safe file path from database value
        $filename = basename($storedPath); // Extract only filename
        $filepath = $this->uploadDir . '/' . $filename;

        // Verify the resolved path is within upload directory
        $realPath = realpath($filepath);
        $realUploadDir = realpath($this->uploadDir);

        if ($realPath === false || strpos($realPath, $realUploadDir) !== 0) {
            // Path is outside upload directory - potential traversal attack!
            error_log("Potential path traversal attempt: $filepath");
            return ['success' => false, 'error' => 'Invalid file path'];
        }

        // Delete physical file safely
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                error_log("Failed to delete file: $filepath");
                return ['success' => false, 'error' => 'Failed to delete file from disk'];
            }
        }

        // Delete from database
        return $this->fileUpload->delete($file_id);
    }

    /**
     * Get MIME type from file content (server-side validation)
     * Uses finfo instead of Content-Type header
     */
    private function getServerMimeType($filepath) {
        if (function_exists('mime_content_type')) {
            return mime_content_type($filepath);
        }

        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            return $mimeType ?: 'application/octet-stream';
        }

        return 'application/octet-stream';
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

/**
 * Helper function to check if path is absolute
 */
function is_absolute_path($path) {
    return (isset($path[0]) && $path[0] === '/') ||
           (isset($path[1]) && $path[1] === ':') ||
           strpos($path, '//') === 0;
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

// Return JSON for all responses
header('Content-Type: application/json');

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
