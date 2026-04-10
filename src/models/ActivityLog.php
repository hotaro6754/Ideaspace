<?php
/**
 * ActivityLog Model - Track all user activities for analytics
 * File: /src/models/ActivityLog.php
 */

class ActivityLog {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Log user activity
     */
    public function log($user_id, $entity_type, $action, $entity_id = null, $details = []) {
        $valid_entities = ['idea', 'application', 'collaboration', 'message', 'comment', 'upvote', 'profile'];
        $valid_actions = ['create', 'update', 'delete', 'view'];

        if (!in_array($entity_type, $valid_entities)) {
            return ['success' => false, 'error' => 'Invalid entity type'];
        }

        if (!in_array($action, $valid_actions)) {
            return ['success' => false, 'error' => 'Invalid action'];
        }

        $details_json = json_encode($details);

        $query = "INSERT INTO activity_logs (user_id, entity_type, entity_id, action, details)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("isiss", $user_id, $entity_type, $entity_id, $action, $details_json);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to log activity'];
    }

    /**
     * Get user activity history
     */
    public function getUserHistory($user_id, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM activity_logs
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
     * Get activity by entity type
     */
    public function getByEntityType($user_id, $entity_type, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM activity_logs
                  WHERE user_id = ? AND entity_type = ?
                  ORDER BY created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("isii", $user_id, $entity_type, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get activity stats for user dashboard
     */
    public function getStats($user_id, $days = 7) {
        $query = "SELECT entity_type, COUNT(*) as count
                  FROM activity_logs
                  WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? DAY)
                  GROUP BY entity_type
                  ORDER BY count DESC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $user_id, $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get recent activity across platform (for admin dashboard)
     */
    public function getRecentActivity($limit = 100, $offset = 0) {
        $query = "SELECT al.*, u.name, u.roll_number
                  FROM activity_logs al
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

    /**
     * Delete old activity logs (data cleanup)
     */
    public function deleteOlderThan($days = 90) {
        $query = "DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);

        if ($stmt->execute()) {
            return ['success' => true, 'deleted_rows' => $stmt->affected_rows];
        }

        return ['success' => false, 'error' => 'Failed to delete logs'];
    }
}
?>
