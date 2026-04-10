<?php
/**
 * AdminAction Model
 * Handles admin actions for moderation and management
 */

class AdminAction {
    private $conn;

    // Valid action types
    const ACTION_TYPES = [
        'feature_idea',      // Feature an idea on homepage
        'remove_idea',       // Remove inappropriate idea
        'flag_user',         // Flag user for inappropriate behavior
        'verify_skills'      // Verify user skills
    ];

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create an admin action
     */
    public function create($admin_user_id, $action_type, $target_idea_id = null, $target_user_id = null, $reason = '') {
        // Validate action type
        if (!in_array($action_type, self::ACTION_TYPES)) {
            return ['success' => false, 'error' => 'Invalid action type'];
        }

        // Validate that admin user exists and is admin
        $admin_query = "SELECT user_type FROM users WHERE id = ?";
        $admin_stmt = $this->conn->prepare($admin_query);
        $admin_stmt->bind_param("i", $admin_user_id);
        $admin_stmt->execute();
        $admin = $admin_stmt->get_result()->fetch_assoc();

        if (!$admin) {
            return ['success' => false, 'error' => 'Admin user not found'];
        }

        // Insert admin action
        $query = "INSERT INTO admin_actions (admin_user_id, action_type, target_idea_id, target_user_id, reason)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("isIIs", $admin_user_id, $action_type, $target_idea_id, $target_user_id, $reason);

        if ($stmt->execute()) {
            // Handle specific action types
            $this->handleActionConsequences($action_type, $target_idea_id, $target_user_id);

            return ['success' => true, 'action_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to create admin action'];
        }
    }

    /**
     * Get all admin actions
     */
    public function getAll($limit = 50, $offset = 0, $filters = []) {
        $query = "SELECT a.*, au.name as admin_name, u.name as target_user_name, i.title as idea_title
                  FROM admin_actions a
                  JOIN users au ON a.admin_user_id = au.id
                  LEFT JOIN users u ON a.target_user_id = u.id
                  LEFT JOIN ideas i ON a.target_idea_id = i.id
                  WHERE 1=1";

        $params = [];
        $types = "";

        // Apply filters
        if (!empty($filters['action_type'])) {
            $query .= " AND a.action_type = ?";
            $params[] = &$filters['action_type'];
            $types .= "s";
        }

        if (!empty($filters['admin_user_id'])) {
            $query .= " AND a.admin_user_id = ?";
            $params[] = &$filters['admin_user_id'];
            $types .= "i";
        }

        if (!empty($filters['target_user_id'])) {
            $query .= " AND a.target_user_id = ?";
            $params[] = &$filters['target_user_id'];
            $types .= "i";
        }

        $query .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $limit_ref = &$limit;
        $offset_ref = &$offset;
        $params[] = &$limit_ref;
        $params[] = &$offset_ref;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        if (!empty($params)) {
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get admin actions by admin user
     */
    public function getByAdmin($admin_user_id, $limit = 20) {
        $query = "SELECT a.*, u.name as target_user_name, i.title as idea_title
                  FROM admin_actions a
                  LEFT JOIN users u ON a.target_user_id = u.id
                  LEFT JOIN ideas i ON a.target_idea_id = i.id
                  WHERE a.admin_user_id = ?
                  ORDER BY a.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $admin_user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get admin actions on a specific idea
     */
    public function getByIdea($idea_id) {
        $query = "SELECT a.*, au.name as admin_name
                  FROM admin_actions a
                  JOIN users au ON a.admin_user_id = au.id
                  WHERE a.target_idea_id = ?
                  ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get admin actions on a specific user
     */
    public function getByTargetUser($target_user_id) {
        $query = "SELECT a.*, au.name as admin_name
                  FROM admin_actions a
                  JOIN users au ON a.admin_user_id = au.id
                  WHERE a.target_user_id = ?
                  ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $target_user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get single admin action
     */
    public function getById($action_id) {
        $query = "SELECT a.*, au.name as admin_name, u.name as target_user_name, i.title as idea_title
                  FROM admin_actions a
                  JOIN users au ON a.admin_user_id = au.id
                  LEFT JOIN users u ON a.target_user_id = u.id
                  LEFT JOIN ideas i ON a.target_idea_id = i.id
                  WHERE a.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $action_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Handle consequences of admin actions
     */
    private function handleActionConsequences($action_type, $target_idea_id, $target_user_id) {
        switch ($action_type) {
            case 'feature_idea':
                // Mark idea as featured (would need a featured column in ideas table)
                // For now, just log the action
                break;

            case 'remove_idea':
                if ($target_idea_id) {
                    // Delete the idea
                    $query = "DELETE FROM ideas WHERE id = ?";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param("i", $target_idea_id);
                    $stmt->execute();
                }
                break;

            case 'flag_user':
                if ($target_user_id) {
                    // Flag user (could add a flagged column to users table)
                    // For now, just log the action
                }
                break;

            case 'verify_skills':
                if ($target_user_id) {
                    // Mark user as verified (could add verified column to users table)
                    // For now, just log the action
                }
                break;
        }
    }

    /**
     * Get action statistics for dashboard
     */
    public function getStatistics() {
        $query = "SELECT
                    action_type,
                    COUNT(*) as count,
                    COUNT(DISTINCT admin_user_id) as admin_count
                  FROM admin_actions
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY action_type";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get action count
     */
    public function getCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM admin_actions WHERE 1=1";

        if (!empty($filters['action_type'])) {
            $query .= " AND action_type = ?";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($filters['action_type'])) {
            $stmt->bind_param("s", $filters['action_type']);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}
?>
