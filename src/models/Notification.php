<?php
/**
 * Notification Model
 * Handles user notifications for application events, collaborations, and interactions
 */

class Notification {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new notification
     */
    public function create($recipient_user_id, $notification_type, $actor_user_id = null, $related_idea_id = null, $message = '') {
        // Validate notification type
        $valid_types = ['application', 'acceptance', 'rejection', 'upvote', 'message'];
        if (!in_array($notification_type, $valid_types)) {
            return ['success' => false, 'error' => 'Invalid notification type'];
        }

        $query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id, message)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("iisIs", $recipient_user_id, $actor_user_id, $notification_type, $related_idea_id, $message);

        if ($stmt->execute()) {
            return ['success' => true, 'notification_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to create notification'];
        }
    }

    /**
     * Get notifications for a user
     */
    public function getForUser($user_id, $limit = 20, $offset = 0, $unread_only = false) {
        $query = "SELECT n.*, u.name as actor_name, u.profile_pic as actor_pic, i.title as idea_title
                  FROM notifications n
                  LEFT JOIN users u ON n.actor_user_id = u.id
                  LEFT JOIN ideas i ON n.related_idea_id = i.id
                  WHERE n.recipient_user_id = ?";

        if ($unread_only) {
            $query .= " AND n.is_read = FALSE";
        }

        $query .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get unread notification count for user
     */
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as unread_count FROM notifications
                  WHERE recipient_user_id = ? AND is_read = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['unread_count'] ?? 0;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id) {
        $query = "UPDATE notifications SET is_read = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notification_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update notification'];
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($user_id) {
        $query = "UPDATE notifications SET is_read = TRUE WHERE recipient_user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update notifications'];
    }

    /**
     * Get single notification
     */
    public function getById($notification_id) {
        $query = "SELECT n.*, u.name as actor_name, u.profile_pic as actor_pic, i.title as idea_title
                  FROM notifications n
                  LEFT JOIN users u ON n.actor_user_id = u.id
                  LEFT JOIN ideas i ON n.related_idea_id = i.id
                  WHERE n.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Delete notification
     */
    public function delete($notification_id) {
        $query = "DELETE FROM notifications WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $notification_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete notification'];
    }

    /**
     * Delete all notifications for a user
     */
    public function deleteAllForUser($user_id) {
        $query = "DELETE FROM notifications WHERE recipient_user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete notifications'];
    }

    /**
     * Get notification statistics for admin dashboard
     */
    public function getStatistics($user_id) {
        $query = "SELECT
                    notification_type,
                    COUNT(*) as count,
                    SUM(CASE WHEN is_read = FALSE THEN 1 ELSE 0 END) as unread
                  FROM notifications
                  WHERE recipient_user_id = ?
                  GROUP BY notification_type";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Alias for getForUser() - for controller compatibility
     */
    public function getByUser($user_id, $limit = 20, $offset = 0) {
        return $this->getForUser($user_id, $limit, $offset);
    }

    /**
     * Alias for deleteAllForUser() - for controller compatibility
     */
    public function deleteAll($user_id) {
        return $this->deleteAllForUser($user_id);
    }
}
?>
