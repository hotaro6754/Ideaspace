<?php
/**
 * RateLimit Model - Rate limiting for brute force protection
 * File: /src/models/RateLimit.php
 */

class RateLimit {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Check if action is rate limited
     */
    public function isLimited($identifier, $action, $max_attempts = 5, $window_minutes = 60) {
        $query = "SELECT attempt_count, first_attempt_at, expires_at FROM rate_limits
                  WHERE identifier = ? AND action = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false; // Fail open - don't block if DB error
        }

        $stmt->bind_param("ss", $identifier, $action);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return false; // No attempts yet
        }

        // Check if window has expired
        if (strtotime($result['expires_at']) < time()) {
            // Reset the counter
            $this->reset($identifier, $action);
            return false;
        }

        return $result['attempt_count'] >= $max_attempts;
    }

    /**
     * Record attempt
     */
    public function recordAttempt($identifier, $action, $window_minutes = 60) {
        $expires_at = date('Y-m-d H:i:s', time() + ($window_minutes * 60));

        $query = "INSERT INTO rate_limits (identifier, action, attempt_count, first_attempt_at, expires_at)
                  VALUES (?, ?, 1, NOW(), ?)
                  ON DUPLICATE KEY UPDATE
                    attempt_count = attempt_count + 1,
                    expires_at = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssss", $identifier, $action, $expires_at, $expires_at);
        return $stmt->execute();
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts($identifier, $action, $max_attempts = 5) {
        $query = "SELECT attempt_count, expires_at FROM rate_limits
                  WHERE identifier = ? AND action = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return $max_attempts;
        }

        $stmt->bind_param("ss", $identifier, $action);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result || strtotime($result['expires_at']) < time()) {
            return $max_attempts;
        }

        return max(0, $max_attempts - $result['attempt_count']);
    }

    /**
     * Reset counter for identifier
     */
    public function reset($identifier, $action) {
        $query = "DELETE FROM rate_limits WHERE identifier = ? AND action = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ss", $identifier, $action);
        return $stmt->execute();
    }

    /**
     * Reset all counters for identifier
     */
    public function resetAll($identifier) {
        $query = "DELETE FROM rate_limits WHERE identifier = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $identifier);
        return $stmt->execute();
    }

    /**
     * Get rate limit status for identifier
     */
    public function getStatus($identifier) {
        $query = "SELECT action, attempt_count, expires_at FROM rate_limits
                  WHERE identifier = ? AND expires_at > NOW()
                  ORDER BY action";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Clean up expired entries
     */
    public function cleanupExpired() {
        $query = "DELETE FROM rate_limits WHERE expires_at < NOW()";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return ['success' => true, 'deleted' => $stmt->affected_rows];
        }

        return ['success' => false, 'error' => 'Failed to cleanup'];
    }

    /**
     * Get top offenders
     */
    public function getTopOffenders($limit = 10) {
        $query = "SELECT identifier, action, COUNT(*) as count, MAX(expires_at) as last_attempt
                  FROM rate_limits
                  WHERE expires_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                  GROUP BY identifier, action
                  ORDER BY count DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
