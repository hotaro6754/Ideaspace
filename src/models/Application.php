<?php
/**
 * Application Model
 * Handles collaboration application logic
 */

class Application {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a collaboration application
     */
    public function create($idea_id, $user_id, $message = '') {
        // Check if idea exists
        $idea_query = "SELECT id, user_id FROM ideas WHERE id = ?";
        $idea_stmt = $this->conn->prepare($idea_query);
        $idea_stmt->bind_param("i", $idea_id);
        $idea_stmt->execute();
        $idea_result = $idea_stmt->get_result();

        if ($idea_result->num_rows === 0) {
            return ['success' => false, 'error' => 'Idea not found'];
        }

        $idea = $idea_result->fetch_assoc();

        // Prevent self-application
        if ($idea['user_id'] == $user_id) {
            return ['success' => false, 'error' => 'You cannot apply to your own idea'];
        }

        // Check for duplicate application
        $check_query = "SELECT id FROM applications WHERE idea_id = ? AND user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ii", $idea_id, $user_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'You have already applied to this idea'];
        }

        // Insert application
        $insert_query = "INSERT INTO applications (idea_id, user_id, message, status)
                         VALUES (?, ?, ?, 'pending')";
        $insert_stmt = $this->conn->prepare($insert_query);

        if (!$insert_stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $insert_stmt->bind_param("iis", $idea_id, $user_id, $message);

        if ($insert_stmt->execute()) {
            // Create notification for idea creator
            $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id)
                           VALUES (?, ?, 'application', ?)";
            $notif_stmt = $this->conn->prepare($notif_query);
            $notif_stmt->bind_param("iii", $idea['user_id'], $user_id, $idea_id);
            $notif_stmt->execute();

            return ['success' => true, 'application_id' => $insert_stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to submit application'];
        }
    }

    /**
     * Get applications for an idea
     */
    public function getForIdea($idea_id) {
        $query = "SELECT a.*, u.name, u.roll_number, u.branch, u.year, u.profile_pic
                  FROM applications a
                  JOIN users u ON a.user_id = u.id
                  WHERE a.idea_id = ?
                  ORDER BY a.applied_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get applications by user
     */
    public function getByUser($user_id) {
        $query = "SELECT a.*, i.title, i.domain, u.name as creator_name
                  FROM applications a
                  JOIN ideas i ON a.idea_id = i.id
                  JOIN users u ON i.user_id = u.id
                  WHERE a.user_id = ?
                  ORDER BY a.applied_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update application status
     */
    public function updateStatus($application_id, $status) {
        $valid_statuses = ['pending', 'accepted', 'rejected', 'withdrawn'];
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'error' => 'Invalid status'];
        }

        $query = "UPDATE applications SET status = ?, responded_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $application_id);

        if ($stmt->execute()) {
            // If accepted, create collaboration
            if ($status === 'accepted') {
                $app_query = "SELECT idea_id, user_id FROM applications WHERE id = ?";
                $app_stmt = $this->conn->prepare($app_query);
                $app_stmt->bind_param("i", $application_id);
                $app_stmt->execute();
                $app = $app_stmt->get_result()->fetch_assoc();

                $idea_query = "SELECT user_id FROM ideas WHERE id = ?";
                $idea_stmt = $this->conn->prepare($idea_query);
                $idea_stmt->bind_param("i", $app['idea_id']);
                $idea_stmt->execute();
                $idea = $idea_stmt->get_result()->fetch_assoc();

                // Create collaboration entry
                $collab_query = "INSERT INTO collaborations (idea_id, leader_id, collaborator_id, status)
                                VALUES (?, ?, ?, 'active')";
                $collab_stmt = $this->conn->prepare($collab_query);
                $collab_stmt->bind_param("iii", $app['idea_id'], $idea['user_id'], $app['user_id']);
                $collab_stmt->execute();
            }

            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to update status'];
        }
    }

    /**
     * Get single application
     */
    public function getById($application_id) {
        $query = "SELECT a.*, u.name, u.roll_number
                  FROM applications a
                  JOIN users u ON a.user_id = u.id
                  WHERE a.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Check if application already exists
     */
    public function checkExisting($idea_id, $user_id) {
        $query = "SELECT id, status FROM applications WHERE idea_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return ['exists' => true, 'application_id' => $result['id'], 'status' => $result['status']];
        }
        return ['exists' => false];
    }

    /**
     * Get all applications created by a user (for creators viewing their submissions)
     */
    public function getByCreator($user_id) {
        $query = "SELECT a.*, i.title, i.domain, u.name as applicant_name, u.branch, u.year
                  FROM applications a
                  JOIN ideas i ON a.idea_id = i.id
                  JOIN users u ON a.user_id = u.id
                  WHERE i.user_id = ?
                  ORDER BY a.applied_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
