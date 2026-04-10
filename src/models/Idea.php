<?php
/**
 * Idea Model
 * Handles idea-related database operations
 */

class Idea {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new idea
     */
    public function create($user_id, $title, $description, $domain, $skills_needed) {
        // Validation
        if (empty($title) || empty($description) || empty($domain)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($title) < 10 || strlen($title) > 200) {
            return ['success' => false, 'error' => 'Title must be between 10 and 200 characters'];
        }

        if (strlen($description) < 50) {
            return ['success' => false, 'error' => 'Description must be at least 50 characters'];
        }

        // Convert skills array to JSON
        $skills_json = json_encode($skills_needed);

        // Insert into database
        $query = "INSERT INTO ideas (user_id, title, description, domain, skills_needed, status)
                  VALUES (?, ?, ?, ?, ?, 'open')";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("issss", $user_id, $title, $description, $domain, $skills_json);

        if ($stmt->execute()) {
            return ['success' => true, 'idea_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Failed to create idea: ' . $stmt->error];
        }
    }

    /**
     * Get all ideas with pagination
     */
    public function getAll($limit = 20, $offset = 0, $filters = []) {
        $query = "SELECT i.*, u.name, u.roll_number, COUNT(DISTINCT a.id) as applicant_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN applications a ON i.id = a.idea_id AND a.status = 'accepted'
                  WHERE 1=1";

        // Apply filters
        if (!empty($filters['domain'])) {
            $query .= " AND i.domain = ?";
        }

        if (!empty($filters['status'])) {
            $query .= " AND i.status = ?";
        }

        if (!empty($filters['search'])) {
            $query .= " AND (i.title LIKE ? OR i.description LIKE ?)";
        }

        $query .= " GROUP BY i.id ORDER BY i.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        // Bind parameters dynamically
        $params = [];
        $types = "";

        if (!empty($filters['domain'])) {
            $domain = $filters['domain'];
            $params[] = &$domain;
            $types .= "s";
        }

        if (!empty($filters['status'])) {
            $status = $filters['status'];
            $params[] = &$status;
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $search = "%" . $filters['search'] . "%";
            $params[] = &$search;
            $params[] = &$search;
            $types .= "ss";
        }

        $params[] = &$limit;
        $params[] = &$offset;
        $types .= "ii";

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get idea by ID
     */
    public function getById($idea_id) {
        $query = "SELECT i.*, u.name, u.roll_number, u.profile_pic,
                         COUNT(DISTINCT a.id) as applicant_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN applications a ON i.id = a.idea_id
                  WHERE i.id = ?
                  GROUP BY i.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get ideas by user
     */
    public function getByUser($user_id) {
        $query = "SELECT i.*, COUNT(DISTINCT a.id) as applicant_count
                  FROM ideas i
                  LEFT JOIN applications a ON i.id = a.idea_id
                  WHERE i.user_id = ?
                  GROUP BY i.id
                  ORDER BY i.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update idea status
     */
    public function updateStatus($idea_id, $status) {
        $valid_statuses = ['open', 'in_progress', 'completed', 'abandoned'];
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'error' => 'Invalid status'];
        }

        $query = "UPDATE ideas SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $idea_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update status'];
    }

    /**
     * Check if user is idea creator
     */
    public function isCreator($idea_id, $user_id) {
        $query = "SELECT id FROM ideas WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Update an idea
     */
    public function update($idea_id, $title, $description, $domain, $skills_needed) {
        // Validation
        if (empty($title) || empty($description) || empty($domain)) {
            return ['success' => false, 'error' => 'All fields are required'];
        }

        if (strlen($title) < 10 || strlen($title) > 200) {
            return ['success' => false, 'error' => 'Title must be between 10 and 200 characters'];
        }

        if (strlen($description) < 50) {
            return ['success' => false, 'error' => 'Description must be at least 50 characters'];
        }

        // Convert skills array to JSON
        $skills_json = json_encode($skills_needed);

        // Update database
        $query = "UPDATE ideas SET title = ?, description = ?, domain = ?, skills_needed = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("ssssi", $title, $description, $domain, $skills_json, $idea_id);

        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to update idea: ' . $stmt->error];
        }
    }

    /**
     * Delete an idea
     */
    public function delete($idea_id) {
        // Delete related records first
        $query = "DELETE FROM applications WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();

        $query = "DELETE FROM collaborations WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();

        $query = "DELETE FROM upvotes WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();

        $query = "DELETE FROM file_uploads WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();

        // Delete the idea
        $query = "DELETE FROM ideas WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete idea'];
    }
}
?>
