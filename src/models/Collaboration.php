<?php
/**
 * Collaboration Model
 * Handles team collaborations on ideas
 */

class Collaboration {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a collaboration entry (called when application is accepted)
     */
    public function create($idea_id, $leader_id, $collaborator_id, $role = '') {
        // Validate inputs
        if (empty($idea_id) || empty($leader_id) || empty($collaborator_id)) {
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        // Check if collaboration already exists
        $check_query = "SELECT id FROM collaborations
                       WHERE idea_id = ? AND leader_id = ? AND collaborator_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("iii", $idea_id, $leader_id, $collaborator_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'Collaboration already exists'];
        }

        // Insert collaboration
        $query = "INSERT INTO collaborations (idea_id, leader_id, collaborator_id, role, status)
                  VALUES (?, ?, ?, ?, 'active')";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("iiss", $idea_id, $leader_id, $collaborator_id, $role);

        if ($stmt->execute()) {
            // Update builder rank for collaborator
            $builderRankQuery = "UPDATE builder_rank SET collaborations = collaborations + 1
                                WHERE user_id = ?";
            $rankStmt = $this->conn->prepare($builderRankQuery);
            if ($rankStmt) {
                $rankStmt->bind_param("i", $collaborator_id);
                $rankStmt->execute();
            }

            return ['success' => true, 'collaboration_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to create collaboration'];
        }
    }

    /**
     * Get all collaborations for an idea
     */
    public function getForIdea($idea_id) {
        $query = "SELECT c.*, u.name, u.roll_number, u.branch, u.profile_pic, u.github_username
                  FROM collaborations c
                  JOIN users u ON c.collaborator_id = u.id
                  WHERE c.idea_id = ? AND c.status = 'active'
                  ORDER BY c.joined_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all collaborations for a user (both as leader and collaborator)
     */
    public function getForUser($user_id) {
        $query = "SELECT c.*, i.title as idea_title, i.domain,
                         CASE
                             WHEN c.leader_id = ? THEN u.name
                             ELSE ul.name
                         END as team_member_name,
                         CASE
                             WHEN c.leader_id = ? THEN 'leader'
                             ELSE 'collaborator'
                         END as my_role
                  FROM collaborations c
                  JOIN ideas i ON c.idea_id = i.id
                  JOIN users u ON c.collaborator_id = u.id
                  JOIN users ul ON c.leader_id = ul.id
                  WHERE (c.leader_id = ? OR c.collaborator_id = ?) AND c.status = 'active'
                  ORDER BY c.joined_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get single collaboration
     */
    public function getById($collaboration_id) {
        $query = "SELECT c.*, i.title, u1.name as leader_name, u2.name as collaborator_name
                  FROM collaborations c
                  JOIN ideas i ON c.idea_id = i.id
                  JOIN users u1 ON c.leader_id = u1.id
                  JOIN users u2 ON c.collaborator_id = u2.id
                  WHERE c.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $collaboration_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update collaboration role
     */
    public function updateRole($collaboration_id, $role) {
        if (empty($role)) {
            return ['success' => false, 'error' => 'Role cannot be empty'];
        }

        $query = "UPDATE collaborations SET role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $role, $collaboration_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update role'];
    }

    /**
     * Mark collaboration as inactive (user left the project)
     */
    public function markInactive($collaboration_id) {
        $query = "UPDATE collaborations SET status = 'inactive', left_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $collaboration_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to mark collaboration as inactive'];
    }

    /**
     * Get team statistics for an idea
     */
    public function getTeamStats($idea_id) {
        $query = "SELECT
                    COUNT(*) as total_members,
                    COUNT(DISTINCT role) as distinct_roles,
                    GROUP_CONCAT(DISTINCT role) as roles
                  FROM collaborations
                  WHERE idea_id = ? AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get top collaborators (most active)
     */
    public function getTopCollaborators($limit = 10) {
        $query = "SELECT u.id, u.name, u.roll_number, u.branch, u.profile_pic,
                         COUNT(*) as collaboration_count
                  FROM collaborations c
                  JOIN users u ON c.collaborator_id = u.id
                  WHERE c.status = 'active'
                  GROUP BY c.collaborator_id
                  ORDER BY collaboration_count DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if user is part of collaboration
     */
    public function isUserInCollaboration($collaboration_id, $user_id) {
        $query = "SELECT id FROM collaborations
                  WHERE id = ? AND (leader_id = ? OR collaborator_id = ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $collaboration_id, $user_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Get collaboration count for user
     */
    public function getCountForUser($user_id) {
        $query = "SELECT COUNT(*) as collab_count FROM collaborations
                  WHERE (leader_id = ? OR collaborator_id = ?) AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['collab_count'] ?? 0;
    }

    /**
     * Update collaboration status
     */
    public function updateStatus($collaboration_id, $status) {
        // Map common status values to valid database statuses
        if ($status === 'left' || $status === 'inactive') {
            $db_status = 'inactive';
            $left_at = 'NOW()';
        } elseif ($status === 'active') {
            $db_status = 'active';
            $left_at = 'NULL';
        } else {
            return ['success' => false, 'error' => 'Invalid status'];
        }

        if ($db_status === 'inactive') {
            $query = "UPDATE collaborations SET status = ?, left_at = NOW() WHERE id = ?";
        } else {
            $query = "UPDATE collaborations SET status = ?, left_at = NULL WHERE id = ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $db_status, $collaboration_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update collaboration status'];
    }
}
?>
