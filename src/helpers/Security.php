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
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize text input
     */
    public static function sanitizeText($input) {
        return trim(strip_tags($input));
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
     * In production, use Redis or memcached for better performance
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

        // Log to file (optional, can be replaced with database logging)
        $log_file = __DIR__ . '/../../logs/security.log';
        if (!file_exists(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        error_log(json_encode($log_entry) . PHP_EOL, 3, $log_file);

        return true;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowed_extensions = [], $max_size = 5242880) { // 5MB default
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error'];
        }

        // Check file size
        if ($file['size'] > $max_size) {
            return ['success' => false, 'error' => 'File size exceeds limit'];
        }

        // Check file extension
        if (!empty($allowed_extensions)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime_type, $allowed_extensions)) {
                return ['success' => false, 'error' => 'File type not allowed'];
            }
        }

        return ['success' => true];
    }

    /**
     * Escape output (prevent XSS)
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get client IP address
     */
    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    /**
     * Check if HTTPS is enabled
     */
    public static function isHttpsEnabled() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               $_SERVER['SERVER_PORT'] == 443 ||
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }

    /**
     * Set security headers - ENHANCED
     */
    public static function setSecurityHeaders() {
        // Force HTTPS in production
        if (!self::isHttpsEnabled() && Env::get('APP_ENV') === 'production') {
            header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
            exit();
        }

        // Strict Content Security Policy (no unsafe-inline)
        $nonce = bin2hex(random_bytes(16));
        $_SESSION['csp_nonce'] = $nonce;

        $csp = "default-src 'self'; " .
               "script-src 'self' 'nonce-$nonce' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'nonce-$nonce' https://cdn.jsdelivr.net; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' https: data:; " .
               "connect-src 'self' https:; " .
               "frame-ancestors 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "upgrade-insecure-requests;";

        header("Content-Security-Policy: $csp");

        // X-Frame-Options (prevent clickjacking) - STRICT
        header("X-Frame-Options: DENY");

        // X-Content-Type-Options (prevent MIME type sniffing)
        header("X-Content-Type-Options: nosniff");

        // X-XSS-Protection (legacy XSS protection)
        header("X-XSS-Protection: 1; mode=block");

        // Referrer-Policy (strict)
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Permissions-Policy (formerly Feature-Policy)
        header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()");

        // Additional security headers
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        header("X-Permitted-Cross-Domain-Policies: none");
    }
}
?>
