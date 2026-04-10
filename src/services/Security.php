<?php
/**
 * Logger - Comprehensive logging system for IdeaSync
 */

class Logger {
    private $log_dir = __DIR__ . '/../../logs';
    private $error_log = 'errors.log';
    private $api_log = 'api.log';
    private $admin_log = 'admin.log';
    private $activity_log = 'activity.log';

    public function __construct() {
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0755, true);
        }
    }

    /**
     * Log error
     */
    public function error($message, $context = []) {
        $this->write($this->error_log, 'ERROR', $message, $context);
    }

    /**
     * Log API request
     */
    public function api($method, $endpoint, $status, $user_id = null) {
        $message = "{$method} {$endpoint} - Status: {$status}";
        $context = [
            'user_id' => $user_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->write($this->api_log, 'API', $message, $context);
    }

    /**
     * Log admin action
     */
    public function admin($action, $target_type, $target_id, $admin_id, $reason = '') {
        $message = "Admin action: {$action} on {$target_type} #{$target_id}";
        $context = [
            'admin_id' => $admin_id,
            'reason' => $reason,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->write($this->admin_log, 'ADMIN', $message, $context);
    }

    /**
     * Log user activity
     */
    public function activity($activity, $user_id, $data = []) {
        $message = "User activity: {$activity}";
        $context = array_merge([
            'user_id' => $user_id,
            'timestamp' => date('Y-m-d H:i:s')
        ], $data);
        $this->write($this->activity_log, 'ACTIVITY', $message, $context);
    }

    /**
     * Write log entry
     */
    private function write($filename, $level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $file = $this->log_dir . '/' . $filename;

        $log_entry = "[{$timestamp}] [{$level}] {$message}";
        if (!empty($context)) {
            $log_entry .= ' | ' . json_encode($context);
        }
        $log_entry .= "\n";

        file_put_contents($file, $log_entry, FILE_APPEND);

        // Rotate logs if they exceed 10MB
        if (filesize($file) > 10 * 1024 * 1024) {
            $backup = $file . '.' . date('Y-m-d-His');
            rename($file, $backup);
            gzip_file($backup);
        }
    }

    /**
     * Get recent logs
     */
    public function getRecent($log_file, $limit = 50) {
        $file = $this->log_dir . '/' . $log_file;
        if (!file_exists($file)) {
            return [];
        }

        $lines = file($file);
        return array_slice($lines, max(0, count($lines) - $limit), $limit);
    }
}

/**
 * RateLimiter - Prevent abuse with request limiting
 */
class RateLimiter {
    private $conn;
    private $limits = [
        'login' => ['max_attempts' => 5, 'window' => 3600], // 5 per hour
        'api' => ['max_attempts' => 100, 'window' => 60], // 100 per minute
        'upload' => ['max_attempts' => 5, 'window' => 3600], // 5 per hour
        'message' => ['max_attempts' => 50, 'window' => 60] // 50 per minute
    ];

    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }

    /**
     * Check if action is allowed
     */
    public function isAllowed($user_id, $action) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "{$action}:{$user_id}:{$ip}";
        $limit = $this->limits[$action] ?? ['max_attempts' => 100, 'window' => 60];

        // Clean old entries
        $this->cleanOldEntries();

        // Count recent attempts
        $query = "SELECT COUNT(*) as count FROM rate_limit WHERE key = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $key, $limit['window']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['count'] >= $limit['max_attempts']) {
            return false;
        }

        // Record attempt
        $query = "INSERT INTO rate_limit (key, user_id, ip, action, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("siss", $key, $user_id, $ip, $action);
        $stmt->execute();

        return true;
    }

    /**
     * Get remaining attempts
     */
    public function getRemaining($user_id, $action) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "{$action}:{$user_id}:{$ip}";
        $limit = $this->limits[$action] ?? ['max_attempts' => 100, 'window' => 60];

        $query = "SELECT COUNT(*) as count FROM rate_limit WHERE key = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $key, $limit['window']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return max(0, $limit['max_attempts'] - $result['count']);
    }

    private function cleanOldEntries() {
        $query = "DELETE FROM rate_limit WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $this->conn->query($query);
    }
}

/**
 * SecurityHelper - Security utilities
 */
class SecurityHelper {
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password) {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain an uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain a lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain a number';
        }

        return empty($errors) ? ['valid' => true] : ['valid' => false, 'errors' => $errors];
    }

    /**
     * Sanitize input
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }

        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
