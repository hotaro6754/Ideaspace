<?php
/**
 * Security Helper - CSRF protection, input validation, and security utilities
 * File: /src/helpers/Security.php
 */

class Security {
    const CSRF_TOKEN_LENGTH = 32;
    const CSRF_TOKEN_LIFETIME = 3600; // 1 hour

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token) {
        // Check if token exists in session
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        // Check if token matches
        if ($token !== $_SESSION['csrf_token']) {
            return false;
        }

        // Check if token has expired
        if (isset($_SESSION['csrf_token_time']) && time() - $_SESSION['csrf_token_time'] > self::CSRF_TOKEN_LIFETIME) {
            return false;
        }

        return true;
    }

    /**
     * Get CSRF token for forms
     */
    public static function getCsrfToken() {
        return self::generateCsrfToken();
    }

    /**
     * Sanitize HTML input (prevent XSS)
     */
    public static function sanitizeHtml($input) {
        return htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize text input
     */
    public static function sanitizeText($input) {
        return trim(strip_tags($input ?? ''));
    }

    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate URL
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
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

    /**
     * Generate secure token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Rate limit check (basic implementation)
     */
    public static function checkRateLimit($identifier, $action, $max_attempts = 5, $window_minutes = 60) {
        $cache_key = "ratelimit_{$identifier}_{$action}";

        if (!isset($_SESSION[$cache_key])) {
            $_SESSION[$cache_key] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'expires' => time() + ($window_minutes * 60)
            ];
        }

        $rate_limit = $_SESSION[$cache_key];

        // Check if window has expired
        if (time() > $rate_limit['expires']) {
            $_SESSION[$cache_key] = [
                'attempts' => 1,
                'first_attempt' => time(),
                'expires' => time() + ($window_minutes * 60)
            ];
            return true; // Not limited
        }

        // Check if limit exceeded
        if ($rate_limit['attempts'] >= $max_attempts) {
            return false; // Limited
        }

        // Increment attempts
        $_SESSION[$cache_key]['attempts']++;
        return true; // Not limited yet
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent($event_type, $user_id, $details = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event_type' => $event_type,
            'user_id' => $user_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];

        // Log to file
        $log_file = __DIR__ . '/../../logs/security.log';
        if (!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        error_log(json_encode($log_entry) . PHP_EOL, 3, $log_file);

        return true;
    }

    /**
     * Escape output (prevent XSS)
     */
    public static function escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        // Simple security headers - removed strict CSP for demo environment compatibility
        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
}
