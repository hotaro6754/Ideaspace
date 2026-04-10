<?php
/**
 * Message Model
 * Handles direct messaging between users
 */

class Message {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Send a message
     */
    public function send($sender_user_id, $recipient_user_id, $message_text) {
        // Validation
        if (empty($message_text)) {
            return ['success' => false, 'error' => 'Message cannot be empty'];
        }

        if (strlen($message_text) > 5000) {
            return ['success' => false, 'error' => 'Message is too long (max 5000 characters)'];
        }

        // Check if both users exist
        $user_query = "SELECT id FROM users WHERE id = ? OR id = ?";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->bind_param("ii", $sender_user_id, $recipient_user_id);
        $user_stmt->execute();

        if ($user_stmt->get_result()->num_rows < 2) {
            return ['success' => false, 'error' => 'One or both users do not exist'];
        }

        // Prevent self-messaging
        if ($sender_user_id == $recipient_user_id) {
            return ['success' => false, 'error' => 'Cannot send message to yourself'];
        }

        // Insert message
        $query = "INSERT INTO messages (sender_user_id, recipient_user_id, message)
                  VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("iis", $sender_user_id, $recipient_user_id, $message_text);

        if ($stmt->execute()) {
            // Create notification for recipient
            $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type)
                           VALUES (?, ?, 'message')";
            $notif_stmt = $this->conn->prepare($notif_query);
            $notif_stmt->bind_param("ii", $recipient_user_id, $sender_user_id);
            $notif_stmt->execute();

            return ['success' => true, 'message_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to send message'];
        }
    }

    /**
     * Get conversation between two users
     */
    public function getConversation($user_id1, $user_id2, $limit = 50, $offset = 0) {
        $query = "SELECT m.*, u.name as sender_name, u.profile_pic
                  FROM messages m
                  JOIN users u ON m.sender_user_id = u.id
                  WHERE (m.sender_user_id = ? AND m.recipient_user_id = ?)
                     OR (m.sender_user_id = ? AND m.recipient_user_id = ?)
                  ORDER BY m.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iiiiii", $user_id1, $user_id2, $user_id2, $user_id1, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_reverse($result); // Return in chronological order
    }

    /**
     * Get all conversations for a user (list of last message with each contact)
     */
    public function getConversations($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT
                    CASE
                        WHEN m.sender_user_id = ? THEN m.recipient_user_id
                        ELSE m.sender_user_id
                    END as contact_id,
                    u.name as contact_name,
                    u.profile_pic,
                    m.message as last_message,
                    m.created_at as last_message_at,
                    m.is_read,
                    COUNT(CASE WHEN m.recipient_user_id = ? AND m.is_read = FALSE THEN 1 END) as unread_count
                  FROM messages m
                  JOIN users u ON (
                    CASE
                        WHEN m.sender_user_id = ? THEN m.recipient_user_id
                        ELSE m.sender_user_id
                    END = u.id
                  )
                  WHERE m.sender_user_id = ? OR m.recipient_user_id = ?
                  GROUP BY contact_id
                  ORDER BY m.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iiiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get single message
     */
    public function getById($message_id) {
        $query = "SELECT m.*, u.name as sender_name, u.profile_pic
                  FROM messages m
                  JOIN users u ON m.sender_user_id = u.id
                  WHERE m.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Mark message as read
     */
    public function markAsRead($message_id) {
        $query = "UPDATE messages SET is_read = TRUE, read_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $message_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to mark message as read'];
    }

    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead($user_id, $contact_id) {
        $query = "UPDATE messages
                  SET is_read = TRUE, read_at = NOW()
                  WHERE recipient_user_id = ? AND sender_user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $contact_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to mark conversation as read'];
    }

    /**
     * Get unread message count for user
     */
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as unread_count FROM messages
                  WHERE recipient_user_id = ? AND is_read = FALSE";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['unread_count'] ?? 0;
    }

    /**
     * Delete a message (soft delete)
     */
    public function delete($message_id) {
        // Note: This could be a soft delete or hard delete
        // For now, implementing as deletion from UI (hard delete)
        $query = "DELETE FROM messages WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $message_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete message'];
    }

    /**
     * Search messages
     */
    public function search($user_id, $search_term, $limit = 20) {
        $search = "%" . $search_term . "%";

        $query = "SELECT m.*, u.name as contact_name, u.profile_pic,
                         CASE
                             WHEN m.sender_user_id = ? THEN m.recipient_user_id
                             ELSE m.sender_user_id
                         END as contact_id
                  FROM messages m
                  JOIN users u ON (
                    CASE
                        WHEN m.sender_user_id = ? THEN m.recipient_user_id
                        ELSE m.sender_user_id
                    END = u.id
                  )
                  WHERE (m.sender_user_id = ? OR m.recipient_user_id = ?)
                    AND m.message LIKE ?
                  ORDER BY m.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iiiiisi", $user_id, $user_id, $user_id, $user_id, $search, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get message statistics for user
     */
    public function getStatistics($user_id) {
        $query = "SELECT
                    COUNT(*) as total_messages,
                    SUM(CASE WHEN sender_user_id = ? THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN recipient_user_id = ? AND is_read = FALSE THEN 1 ELSE 0 END) as unread,
                    COUNT(DISTINCT CASE WHEN sender_user_id = ? THEN recipient_user_id ELSE sender_user_id END) as total_contacts
                  FROM messages
                  WHERE sender_user_id = ? OR recipient_user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Alias for send() - for controller compatibility
     */
    public function create($sender_id, $recipient_id, $content) {
        return $this->send($sender_id, $recipient_id, $content);
    }

    /**
     * Alias for getConversations() - for controller compatibility
     */
    public function getUserConversations($user_id, $limit = 20, $offset = 0) {
        return $this->getConversations($user_id, $limit, $offset);
    }
}
?>
