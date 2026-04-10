<?php
/**
 * FileUpload Model
 * Handles file uploads for ideas and collaborations
 */

class FileUpload {
    private $conn;
    private $upload_dir = '/uploads/';
    private $max_file_size = 10485760; // 10MB in bytes
    private $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                              'application/msword', 'text/plain', 'application/json'];

    public function __construct($db, $upload_base_path = '') {
        $this->conn = $db;
        if (!empty($upload_base_path)) {
            $this->upload_dir = $upload_base_path;
        }
    }

    /**
     * Upload a file for an idea
     */
    public function uploadForIdea($idea_id, $user_id, $file, $file_type = '') {
        // Validation
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error: ' . ($file['error'] ?? 'Unknown')];
        }

        // Check file size
        if ($file['size'] > $this->max_file_size) {
            return ['success' => false, 'error' => 'File size exceeds maximum limit (10MB)'];
        }

        // Check file type
        if (!in_array($file['type'], $this->allowed_types)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        // Check if idea exists
        $idea_query = "SELECT id FROM ideas WHERE id = ?";
        $idea_stmt = $this->conn->prepare($idea_query);
        $idea_stmt->bind_param("i", $idea_id);
        $idea_stmt->execute();

        if ($idea_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Idea not found'];
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file['name']);
        $file_path = $this->upload_dir . 'ideas/' . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($this->upload_dir . 'ideas/')) {
            mkdir($this->upload_dir . 'ideas/', 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return ['success' => false, 'error' => 'Failed to save file'];
        }

        // Insert file record in database
        return $this->recordFileUpload($user_id, $idea_id, null, $filename, $file_path, $file['size'], $file_type, $file['type']);
    }

    /**
     * Upload a file for a collaboration
     */
    public function uploadForCollaboration($collaboration_id, $user_id, $file, $file_type = '') {
        // Validation
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error: ' . ($file['error'] ?? 'Unknown')];
        }

        // Check file size
        if ($file['size'] > $this->max_file_size) {
            return ['success' => false, 'error' => 'File size exceeds maximum limit (10MB)'];
        }

        // Check file type
        if (!in_array($file['type'], $this->allowed_types)) {
            return ['success' => false, 'error' => 'File type not allowed'];
        }

        // Check if collaboration exists
        $collab_query = "SELECT id FROM collaborations WHERE id = ?";
        $collab_stmt = $this->conn->prepare($collab_query);
        $collab_stmt->bind_param("i", $collaboration_id);
        $collab_stmt->execute();

        if ($collab_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Collaboration not found'];
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file['name']);
        $file_path = $this->upload_dir . 'collaborations/' . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($this->upload_dir . 'collaborations/')) {
            mkdir($this->upload_dir . 'collaborations/', 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return ['success' => false, 'error' => 'Failed to save file'];
        }

        // Insert file record in database
        return $this->recordFileUpload($user_id, null, $collaboration_id, $filename, $file_path, $file['size'], $file_type, $file['type']);
    }

    /**
     * Record file upload in database
     */
    private function recordFileUpload($user_id, $idea_id, $collaboration_id, $filename, $filepath, $filesize, $filetype, $mimetype) {
        $query = "INSERT INTO file_uploads (uploader_user_id, idea_id, collaboration_id, file_name, file_path, file_size, file_type, mime_type)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("iIIssIss", $user_id, $idea_id, $collaboration_id, $filename, $filepath, $filesize, $filetype, $mimetype);

        if ($stmt->execute()) {
            return ['success' => true, 'file_id' => $stmt->insert_id, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to record file upload'];
        }
    }

    /**
     * Get files for an idea
     */
    public function getFilesForIdea($idea_id, $limit = 50) {
        $query = "SELECT fu.*, u.name as uploader_name, u.profile_pic
                  FROM file_uploads fu
                  JOIN users u ON fu.uploader_user_id = u.id
                  WHERE fu.idea_id = ? AND fu.is_deleted = FALSE
                  ORDER BY fu.uploaded_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $idea_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get files for a collaboration
     */
    public function getFilesForCollaboration($collaboration_id, $limit = 50) {
        $query = "SELECT fu.*, u.name as uploader_name, u.profile_pic
                  FROM file_uploads fu
                  JOIN users u ON fu.uploader_user_id = u.id
                  WHERE fu.collaboration_id = ? AND fu.is_deleted = FALSE
                  ORDER BY fu.uploaded_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $collaboration_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get files uploaded by a user
     */
    public function getFilesByUser($user_id, $limit = 50, $offset = 0) {
        $query = "SELECT fu.*
                  FROM file_uploads fu
                  WHERE fu.uploader_user_id = ? AND fu.is_deleted = FALSE
                  ORDER BY fu.uploaded_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get single file
     */
    public function getById($file_id) {
        $query = "SELECT fu.*, u.name as uploader_name
                  FROM file_uploads fu
                  JOIN users u ON fu.uploader_user_id = u.id
                  WHERE fu.id = ? AND fu.is_deleted = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Delete file (soft delete)
     */
    public function delete($file_id) {
        // Get file first to delete from filesystem
        $file = $this->getById($file_id);

        if (!$file) {
            return ['success' => false, 'error' => 'File not found'];
        }

        // Delete from filesystem
        if (file_exists($file['file_path'])) {
            if (!unlink($file['file_path'])) {
                return ['success' => false, 'error' => 'Failed to delete file from storage'];
            }
        }

        // Mark as deleted in database
        $query = "UPDATE file_uploads SET is_deleted = TRUE, deleted_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $file_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete file record'];
    }

    /**
     * Get file statistics
     */
    public function getStatistics($idea_id = null, $collaboration_id = null) {
        $query = "SELECT
                    COUNT(*) as total_files,
                    SUM(file_size) as total_size,
                    COUNT(DISTINCT file_type) as unique_types,
                    MAX(uploaded_at) as latest_upload
                  FROM file_uploads
                  WHERE is_deleted = FALSE";

        if (!is_null($idea_id)) {
            $query .= " AND idea_id = ?";
        } elseif (!is_null($collaboration_id)) {
            $query .= " AND collaboration_id = ?";
        }

        $stmt = $this->conn->prepare($query);

        if (!is_null($idea_id)) {
            $stmt->bind_param("i", $idea_id);
        } elseif (!is_null($collaboration_id)) {
            $stmt->bind_param("i", $collaboration_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get file count for idea
     */
    public function getCountForIdea($idea_id) {
        $query = "SELECT COUNT(*) as file_count FROM file_uploads
                  WHERE idea_id = ? AND is_deleted = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['file_count'] ?? 0;
    }

    /**
     * Get file count for collaboration
     */
    public function getCountForCollaboration($collaboration_id) {
        $query = "SELECT COUNT(*) as file_count FROM file_uploads
                  WHERE collaboration_id = ? AND is_deleted = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $collaboration_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['file_count'] ?? 0;
    }

    /**
     * Generate unique filename
     */
    private function generateUniqueFilename($original_filename) {
        $timestamp = time();
        $random = uniqid();
        $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        return $random . '_' . $timestamp . '.' . $extension;
    }

    /**
     * Set allowed file types
     */
    public function setAllowedTypes($types) {
        if (is_array($types)) {
            $this->allowed_types = $types;
        }
    }

    /**
     * Set max file size
     */
    public function setMaxFileSize($size) {
        $this->max_file_size = intval($size);
    }

    /**
     * Alias for uploadForIdea() - for controller compatibility
     */
    public function create($user_id, $idea_id, $filename, $url, $type, $size) {
        // This is a wrapper for direct database recording without file movement
        // Used when file path is already determined
        return $this->recordFileUpload($user_id, $idea_id, null, $filename, $url, $size, $type, $type);
    }

    /**
     * Alias for getFilesForIdea() - for controller compatibility
     */
    public function getByIdea($idea_id) {
        return $this->getFilesForIdea($idea_id);
    }

    /**
     * Alias for getFilesByUser() - for controller compatibility
     */
    public function getByUser($user_id, $limit = 50, $offset = 0) {
        return $this->getFilesByUser($user_id, $limit, $offset);
    }
}
?>
