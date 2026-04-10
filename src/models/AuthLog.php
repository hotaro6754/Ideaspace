<?php
/**
 * AuthLog Model - Track all authentication events
 * File: /src/models/AuthLog.php
 */

class AuthLog {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Log authentication event
     */
    public function log($user_id, $action, $success = false, $details = []) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $details_json = json_encode($details);

        $query = "INSERT INTO auth_logs (user_id, action, ip_address, user_agent, details, success)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("issssi", $user_id, $action, $ip_address, $user_agent, $details_json, $success);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false];
    }

    /**
     * Get user's auth history
     */
    public function getUserHistory($user_id, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM auth_logs
                  WHERE user_id = ?
                  ORDER BY created_at DESC
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
     * Get failed login attempts (for security)
     */
    public function getFailedAttempts($user_id, $hours = 1) {
        $query = "SELECT COUNT(*) as count FROM auth_logs
                  WHERE user_id = ? AND action = 'login_failure'
                  AND created_at > DATE_SUB(NOW(), INTERVAL ? HOUR)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $hours);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * Check for suspicious activity
     */
    public function checkSuspiciousActivity($ip_address, $hours = 24) {
        $query = "SELECT COUNT(*) as count FROM auth_logs
                  WHERE ip_address = ? AND success = FALSE
                  AND created_at > DATE_SUB(NOW(), INTERVAL ? HOUR)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $ip_address, $hours);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * Get admin audit trail
     */
    public function getAuditTrail($limit = 100, $offset = 0) {
        $query = "SELECT al.*, u.name, u.roll_number FROM auth_logs al
                  LEFT JOIN users u ON al.user_id = u.id
                  ORDER BY al.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
