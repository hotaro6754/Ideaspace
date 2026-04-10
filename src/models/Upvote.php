<?php
/**
 * Upvote Model
 * Handles community upvoting system for ideas
 */

class Upvote {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Add upvote to an idea
     */
    public function addUpvote($idea_id, $user_id) {
        // Check if idea exists
        $idea_query = "SELECT id FROM ideas WHERE id = ?";
        $idea_stmt = $this->conn->prepare($idea_query);
        $idea_stmt->bind_param("i", $idea_id);
        $idea_stmt->execute();

        if ($idea_stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Idea not found'];
        }

        // Check if user already upvoted
        $check_query = "SELECT id FROM upvotes WHERE idea_id = ? AND user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ii", $idea_id, $user_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'You have already upvoted this idea'];
        }

        // Insert upvote
        $insert_query = "INSERT INTO upvotes (idea_id, user_id) VALUES (?, ?)";
        $insert_stmt = $this->conn->prepare($insert_query);

        if (!$insert_stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $insert_stmt->bind_param("ii", $idea_id, $user_id);

        if ($insert_stmt->execute()) {
            // Update idea upvotes count
            $this->updateIdeaUpvoteCount($idea_id);

            // Get idea creator for notification
            $creator_query = "SELECT user_id FROM ideas WHERE id = ?";
            $creator_stmt = $this->conn->prepare($creator_query);
            $creator_stmt->bind_param("i", $idea_id);
            $creator_stmt->execute();
            $idea = $creator_stmt->get_result()->fetch_assoc();

            // Create notification
            if ($idea && $idea['user_id'] != $user_id) {
                $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id)
                               VALUES (?, ?, 'upvote', ?)";
                $notif_stmt = $this->conn->prepare($notif_query);
                $notif_stmt->bind_param("iii", $idea['user_id'], $user_id, $idea_id);
                $notif_stmt->execute();
            }

            return ['success' => true, 'upvote_id' => $insert_stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to add upvote'];
        }
    }

    /**
     * Remove upvote from an idea
     */
    public function removeUpvote($idea_id, $user_id) {
        $query = "DELETE FROM upvotes WHERE idea_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $user_id);

        if ($stmt->execute()) {
            // Update idea upvotes count
            $this->updateIdeaUpvoteCount($idea_id);
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to remove upvote'];
        }
    }

    /**
     * Check if user has upvoted an idea
     */
    public function hasUpvoted($idea_id, $user_id) {
        $query = "SELECT id FROM upvotes WHERE idea_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Get upvote count for an idea
     */
    public function getCount($idea_id) {
        $query = "SELECT COUNT(*) as upvote_count FROM upvotes WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['upvote_count'] ?? 0;
    }

    /**
     * Get all upvoters for an idea
     */
    public function getUpvoters($idea_id, $limit = 10) {
        $query = "SELECT u.id, u.name, u.roll_number, u.profile_pic, u.branch
                  FROM upvotes uv
                  JOIN users u ON uv.user_id = u.id
                  WHERE uv.idea_id = ?
                  ORDER BY uv.upvoted_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get ideas upvoted by a user
     */
    public function getIdeasByUser($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT i.*, u.name, u.roll_number, COUNT(DISTINCT uv.id) as upvote_count
                  FROM upvotes uv
                  JOIN ideas i ON uv.idea_id = i.id
                  JOIN users u ON i.user_id = u.id
                  WHERE uv.user_id = ?
                  GROUP BY i.id
                  ORDER BY uv.upvoted_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get trending ideas by upvotes (last 7 days)
     */
    public function getTrendingIdeas($limit = 10) {
        $query = "SELECT i.*, u.name, u.roll_number, COUNT(DISTINCT uv.id) as upvote_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN upvotes uv ON i.id = uv.idea_id
                          AND uv.upvoted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY i.id
                  ORDER BY upvote_count DESC, i.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update idea upvotes count in ideas table
     */
    private function updateIdeaUpvoteCount($idea_id) {
        $count = $this->getCount($idea_id);
        $query = "UPDATE ideas SET upvotes = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $count, $idea_id);
        $stmt->execute();
    }
}
?>
