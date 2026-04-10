<?php
/**
 * IdeaComment Model - Comments on ideas with threaded replies
 * File: /src/models/IdeaComment.php
 */

class IdeaComment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Add comment to idea
     */
    public function create($idea_id, $user_id, $content, $parent_comment_id = null) {
        if (empty($content) || strlen($content) < 3) {
            return ['success' => false, 'error' => 'Comment must be at least 3 characters'];
        }

        if (strlen($content) > 5000) {
            return ['success' => false, 'error' => 'Comment too long (max 5000 characters)'];
        }

        // Check if idea exists
        $idea_query = "SELECT id FROM ideas WHERE id = ?";
        $idea_stmt = $this->conn->prepare($idea_query);
        $idea_stmt->bind_param("i", $idea_id);
        $idea_stmt->execute();

        if ($idea_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Idea not found'];
        }

        // Insert comment
        $query = "INSERT INTO idea_comments (idea_id, user_id, content, parent_comment_id)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("iisi", $idea_id, $user_id, $content, $parent_comment_id);

        if ($stmt->execute()) {
            $comment_id = $stmt->insert_id;

            // Update comment count
            $update_query = "UPDATE ideas SET comment_count = comment_count + 1 WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("i", $idea_id);
            $update_stmt->execute();

            // Create notification
            $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id)
                           SELECT user_id, ?, 'comment', ? FROM ideas WHERE id = ? LIMIT 1";
            $notif_stmt = $this->conn->prepare($notif_query);
            $notif_stmt->bind_param("iii", $user_id, $idea_id, $idea_id);
            $notif_stmt->execute();

            return ['success' => true, 'comment_id' => $comment_id];
        }

        return ['success' => false, 'error' => 'Failed to create comment'];
    }

    /**
     * Get comments for idea
     */
    public function getForIdea($idea_id, $limit = 50, $offset = 0) {
        $query = "SELECT c.*, u.name, u.profile_pic, u.roll_number,
                         (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id) as likes_count,
                         (SELECT COUNT(*) FROM idea_comments WHERE parent_comment_id = c.id) as reply_count
                  FROM idea_comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.idea_id = ? AND c.is_deleted = FALSE AND c.parent_comment_id IS NULL
                  ORDER BY c.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iii", $idea_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get replies to a comment
     */
    public function getReplies($parent_comment_id) {
        $query = "SELECT c.*, u.name, u.profile_pic, u.roll_number
                  FROM idea_comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.parent_comment_id = ? AND c.is_deleted = FALSE
                  ORDER BY c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $parent_comment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Like a comment
     */
    public function likeComment($comment_id, $user_id) {
        // Check if already liked
        $check_query = "SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ii", $comment_id, $user_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'Already liked'];
        }

        // Add like
        $insert_query = "INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)";
        $insert_stmt = $this->conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $comment_id, $user_id);

        if ($insert_stmt->execute()) {
            // Update comment likes count
            $update_query = "UPDATE idea_comments SET likes_count = likes_count + 1 WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("i", $comment_id);
            $update_stmt->execute();

            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * Delete comment (soft delete)
     */
    public function delete($comment_id, $user_id) {
        // Check ownership
        $check_query = "SELECT user_id FROM idea_comments WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $comment_id);
        $check_stmt->execute();
        $comment = $check_stmt->get_result()->fetch_assoc();

        if (!$comment || $comment['user_id'] != $user_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        // Soft delete
        $delete_query = "UPDATE idea_comments SET is_deleted = TRUE, deleted_at = NOW() WHERE id = ?";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $comment_id);

        return $delete_stmt->execute()
            ? ['success' => true]
            : ['success' => false];
    }

    /**
     * Edit comment
     */
    public function edit($comment_id, $user_id, $content) {
        if (strlen($content) < 3 || strlen($content) > 5000) {
            return ['success' => false, 'error' => 'Invalid content length'];
        }

        // Check ownership
        $check_query = "SELECT user_id FROM idea_comments WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $comment_id);
        $check_stmt->execute();
        $comment = $check_stmt->get_result()->fetch_assoc();

        if (!$comment || $comment['user_id'] != $user_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        // Update
        $update_query = "UPDATE idea_comments SET content = ?, is_edited = TRUE, edited_at = NOW() WHERE id = ?";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bind_param("si", $content, $comment_id);

        return $update_stmt->execute()
            ? ['success' => true]
            : ['success' => false];
    }

    /**
     * Get comment count for idea
     */
    public function getCountForIdea($idea_id) {
        $query = "SELECT COUNT(*) as count FROM idea_comments WHERE idea_id = ? AND is_deleted = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }
}
?>
