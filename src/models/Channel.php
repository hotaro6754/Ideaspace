<?php
/**
 * Channel Model - Team-based messaging channels (not group chat)
 * File: /src/models/Channel.php
 */

class Channel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create channel for collaboration
     */
    public function create($collaboration_id, $name, $description, $type = 'custom', $created_by) {
        if (empty($name) || strlen($name) < 2) {
            return ['success' => false, 'error' => 'Channel name required'];
        }

        // Check if collaboration exists and user is member
        $collab_query = "SELECT id FROM collaborations WHERE id = ? AND (leader_id = ? OR collaborator_id = ?)";
        $collab_stmt = $this->conn->prepare($collab_query);
        $collab_stmt->bind_param("iii", $collaboration_id, $created_by, $created_by);
        $collab_stmt->execute();

        if ($collab_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        // Create channel
        $insert_query = "INSERT INTO channels (collaboration_id, name, description, type, created_by)
                        VALUES (?, ?, ?, ?, ?)";

        $insert_stmt = $this->conn->prepare($insert_query);
        if (!$insert_stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $insert_stmt->bind_param("isssi", $collaboration_id, $name, $description, $type, $created_by);

        if ($insert_stmt->execute()) {
            $channel_id = $insert_stmt->insert_id;

            // Add creator as member
            $member_query = "INSERT INTO channel_members (channel_id, user_id) VALUES (?, ?)";
            $member_stmt = $this->conn->prepare($member_query);
            $member_stmt->bind_param("ii", $channel_id, $created_by);
            $member_stmt->execute();

            // Add all collaboration members
            $members_query = "SELECT DISTINCT leader_id as user_id FROM collaborations WHERE id = ?
                            UNION
                            SELECT DISTINCT collaborator_id FROM collaborations WHERE id = ?";
            $members_stmt = $this->conn->prepare($members_query);
            $members_stmt->bind_param("ii", $collaboration_id, $collaboration_id);
            $members_stmt->execute();

            $members = $members_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($members as $member) {
                $add_query = "INSERT IGNORE INTO channel_members (channel_id, user_id) VALUES (?, ?)";
                $add_stmt = $this->conn->prepare($add_query);
                $add_stmt->bind_param("ii", $channel_id, $member['user_id']);
                $add_stmt->execute();
            }

            return ['success' => true, 'channel_id' => $channel_id];
        }

        return ['success' => false, 'error' => 'Failed to create channel'];
    }

    /**
     * Get channels for collaboration
     */
    public function getForCollaboration($collaboration_id) {
        $query = "SELECT c.*, (SELECT COUNT(*) FROM channel_messages WHERE channel_id = c.id) as message_count
                  FROM channels c
                  WHERE c.collaboration_id = ? AND c.is_archived = FALSE
                  ORDER BY c.type DESC, c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $collaboration_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get channel by ID
     */
    public function getById($channel_id) {
        $query = "SELECT * FROM channels WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $channel_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Add message to channel
     */
    public function addMessage($channel_id, $sender_id, $content, $attachments = null) {
        if (empty($content)) {
            return ['success' => false, 'error' => 'Message cannot be empty'];
        }

        // Check user is channel member
        $member_query = "SELECT id FROM channel_members WHERE channel_id = ? AND user_id = ?";
        $member_stmt = $this->conn->prepare($member_query);
        $member_stmt->bind_param("ii", $channel_id, $sender_id);
        $member_stmt->execute();

        if ($member_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Not a channel member'];
        }

        $attachments_json = $attachments ? json_encode($attachments) : null;

        $insert_query = "INSERT INTO channel_messages (channel_id, sender_id, content, attachments)
                        VALUES (?, ?, ?, ?)";

        $insert_stmt = $this->conn->prepare($insert_query);
        if (!$insert_stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $insert_stmt->bind_param("iiss", $channel_id, $sender_id, $content, $attachments_json);

        if ($insert_stmt->execute()) {
            return ['success' => true, 'message_id' => $insert_stmt->insert_id];
        }

        return ['success' => false, 'error' => 'Failed to send message'];
    }

    /**
     * Get messages for channel
     */
    public function getMessages($channel_id, $limit = 50, $offset = 0) {
        $query = "SELECT m.*, u.name, u.profile_pic, u.roll_number,
                         (SELECT COUNT(*) FROM channel_message_reactions WHERE message_id = m.id) as reaction_count
                  FROM channel_messages m
                  JOIN users u ON m.sender_id = u.id
                  WHERE m.channel_id = ? AND m.is_deleted = FALSE
                  ORDER BY m.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iii", $channel_id, $limit, $offset);
        $stmt->execute();
        $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Reverse to show chronological order
        return array_reverse($messages);
    }

    /**
     * Add message reaction (emoji)
     */
    public function addReaction($message_id, $user_id, $emoji) {
        $check_query = "SELECT id FROM channel_message_reactions
                       WHERE message_id = ? AND user_id = ? AND emoji = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("iis", $message_id, $user_id, $emoji);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'Already reacted with this emoji'];
        }

        $insert_query = "INSERT INTO channel_message_reactions (message_id, user_id, emoji)
                       VALUES (?, ?, ?)";

        $insert_stmt = $this->conn->prepare($insert_query);
        $insert_stmt->bind_param("iis", $message_id, $user_id, $emoji);

        return $insert_stmt->execute()
            ? ['success' => true]
            : ['success' => false];
    }

    /**
     * Get unread message count for user
     */
    public function getUnreadCount($channel_id, $user_id) {
        $query = "SELECT COUNT(*) as count FROM channel_messages
                  WHERE channel_id = ? AND id > COALESCE(
                    (SELECT last_read_message_id FROM channel_members
                     WHERE channel_id = ? AND user_id = ?), 0)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $channel_id, $channel_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($channel_id, $user_id, $message_id) {
        $query = "UPDATE channel_members
                  SET last_read_message_id = ?
                  WHERE channel_id = ? AND user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $message_id, $channel_id, $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false];
    }

    /**
     * Delete message (soft delete)
     */
    public function deleteMessage($message_id, $user_id) {
        // Check ownership
        $check_query = "SELECT sender_id FROM channel_messages WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $message_id);
        $check_stmt->execute();
        $message = $check_stmt->get_result()->fetch_assoc();

        if (!$message || $message['sender_id'] != $user_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        $delete_query = "UPDATE channel_messages SET is_deleted = TRUE, deleted_at = NOW() WHERE id = ?";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $message_id);

        return $delete_stmt->execute()
            ? ['success' => true]
            : ['success' => false];
    }
}
?>
