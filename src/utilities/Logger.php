<?php
/**
 * Logger Class
 * Handles error, API, and admin action logging
 */

class Logger {
    private static $instance = null;
    private $log_dir = __DIR__ . '/../../logs';

    private function __construct() {
        // Ensure logs directory exists
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0755, true);
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Log errors to /logs/errors.log
     */
    public function logError($message, $context = []) {
        $log_data = $this->formatLogEntry('ERROR', $message, $context);
        $this->writeToFile('errors.log', $log_data);
        error_log($log_data);
    }

    /**
     * Log API requests to /logs/api.log
     */
    public function logAPI($endpoint, $method = 'GET', $user_id = null, $response_code = 200, $duration_ms = 0) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $context = [
            'endpoint' => $endpoint,
            'method' => $method,
            'user_id' => $user_id ?? 'GUEST',
            'ip' => $ip,
            'response_code' => $response_code,
            'duration_ms' => $duration_ms
        ];

        $log_data = $this->formatLogEntry('API', "API Request: $method $endpoint", $context);
        $this->writeToFile('api.log', $log_data);
    }

    /**
     * Log admin actions to /logs/admin.log
     */
    public function logAdminAction($admin_id, $action, $details = []) {
        $context = array_merge(['action' => $action], $details);
        $log_data = $this->formatLogEntry('ADMIN', "Admin Action by User#$admin_id: $action", $context);
        $this->writeToFile('admin.log', $log_data);
    }

    /**
     * Log security events to /logs/security.log
     */
    public function logSecurityEvent($event, $severity = 'INFO', $context = []) {
        $context = array_merge(['ip' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'], $context);
        $log_data = $this->formatLogEntry('SECURITY', "$severity: $event", $context);
        $this->writeToFile('security.log', $log_data);
    }

    /**
     * Log database queries (for debugging)
     */
    public function logQuery($query, $params = [], $execution_time_ms = 0) {
        $context = [
            'query' => $query,
            'params' => $params,
            'execution_time_ms' => $execution_time_ms
        ];

        $log_data = $this->formatLogEntry('DATABASE', 'Query Executed', $context);
        $this->writeToFile('database.log', $log_data);
    }

    /**
     * Format log entry with timestamp and context
     */
    private function formatLogEntry($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s.u');
        $context_str = !empty($context) ? ' | ' . json_encode($context) : '';
        return "[$timestamp] [$level] $message$context_str\n";
    }

    /**
     * Write to log file
     */
    private function writeToFile($filename, $content) {
        $file_path = $this->log_dir . '/' . $filename;

        // Append to file with lock
        $handle = fopen($file_path, 'a');
        if ($handle) {
            if (flock($handle, LOCK_EX)) {
                fwrite($handle, $content);
                fflush($handle);
                flock($handle, LOCK_UN);
            }
            fclose($handle);
        }
    }

    /**
     * Get recent log entries
     */
    public function getRecentLogs($filename, $lines = 50) {
        $file_path = $this->log_dir . '/' . $filename;

        if (!file_exists($file_path)) {
            return [];
        }

        $file = new SplFileObject($file_path, 'r');
        $file->seek(PHP_INT_MAX);
        $last_line = $file->key();

        $lines_to_read = min($lines, $last_line + 1);
        $start_line = max(0, $last_line - $lines_to_read + 1);

        $logs = [];
        for ($i = $start_line; $i <= $last_line; $i++) {
            $file->seek($i);
            $logs[] = $file->current();
        }

        return array_reverse($logs);
    }

    /**
     * Clear old logs (older than X days)
     */
    public function clearOldLogs($days = 30) {
        $cutoff_time = time() - ($days * 86400);

        if (!is_dir($this->log_dir)) {
            return;
        }

        $files = scandir($this->log_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $file_path = $this->log_dir . '/' . $file;
            if (is_file($file_path) && filemtime($file_path) < $cutoff_time) {
                unlink($file_path);
            }
        }
    }
}
?>
