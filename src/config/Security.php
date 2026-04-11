<?php

/**
 * Security.php - Security Utilities
 * Provides CSRF token verification and other security functions
 */

class Security {

    /**
     * Generate CSRF token for session
     */
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Get CSRF token for form output
     */
    public static function getToken() {
        return self::generateToken();
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token = null) {
        // If no token provided, try to get from POST
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? '';
        }

        $session_token = $_SESSION['csrf_token'] ?? '';

        // Use hash_equals for timing-safe comparison
        return !empty($token) && !empty($session_token) && hash_equals($session_token, $token);
    }

    /**
     * Sanitize input
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Generate password hash
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password) {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain number';
        }

        if (!preg_match('/[!@#$%^&*]/', $password)) {
            $errors[] = 'Password must contain special character (!@#$%^&*)';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }

    /**
     * Rate limiting check
     */
    public static function checkRateLimit($identifier, $limit = 10, $window = 60) {
        $key = 'rate_limit_' . md5($identifier);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $window];
        }

        if (time() > $_SESSION[$key]['reset_time']) {
            $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $window];
        }

        $_SESSION[$key]['count']++;

        return $_SESSION[$key]['count'] <= $limit;
    }

    /**
     * Sanitize filename for upload
     */
    public static function sanitizeFilename($filename) {
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

        // Limit length
        $filename = substr($filename, 0, 255);

        return $filename;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $max_size = 5242880, $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']) {
        $errors = [];

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file uploaded';
            return ['valid' => false, 'errors' => $errors];
        }

        if ($file['size'] > $max_size) {
            $errors[] = 'File exceeds maximum size of ' . ($max_size / 1024 / 1024) . 'MB';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            $errors[] = 'File type not allowed. Allowed: ' . implode(', ', $allowed_types);
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Escape for JSON
     */
    public static function jsonEscape($string) {
        return json_encode($string, JSON_UNESCAPED_SLASHES);
    }
}

?>
