<?php
/**
 * ContentReport Model - Report inappropriate content for moderation
 * File: /src/models/ContentReport.php
 */

class ContentReport {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create content report
     */
    public function create($reporter_id, $reported_type, $reported_id, $reason, $description = null) {
        $valid_types = ['idea', 'comment', 'message', 'user'];
        $valid_reasons = ['spam', 'inappropriate', 'offensive', 'plagiarism', 'other'];

        if (!in_array($reported_type, $valid_types)) {
            return ['success' => false, 'error' => 'Invalid report type'];
        }

        if (!in_array($reason, $valid_reasons)) {
            return ['success' => false, 'error' => 'Invalid reason'];
        }

        if ($description && strlen($description) > 1000) {
            return ['success' => false, 'error' => 'Description too long (max 1000 characters)'];
        }

        // Check if already reported
        $check_query = "SELECT id FROM content_reports
                       WHERE reporter_id = ? AND reported_type = ? AND reported_id = ?
                       AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("isi", $reporter_id, $reported_type, $reported_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'You already reported this content in the last 24 hours'];
        }

        $query = "INSERT INTO content_reports (reporter_id, reported_type, reported_id, reason, description)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("isiss", $reporter_id, $reported_type, $reported_id, $reason, $description);

        if ($stmt->execute()) {
            $report_id = $stmt->insert_id;

            // Create notification for admins
            $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, message)
                           SELECT id, ?, 'report', ?
                           FROM users WHERE role = 'admin'";
            $notif_message = "New report: {$reported_type} #{$reported_id} - {$reason}";
            $notif_stmt = $this->conn->prepare($notif_query);
            $notif_stmt->bind_param("is", $reporter_id, $notif_message);
            $notif_stmt->execute();

            return ['success' => true, 'report_id' => $report_id];
        }

        return ['success' => false, 'error' => 'Failed to create report'];
    }

    /**
     * Get pending reports (for admin)
     */
    public function getPending($limit = 50, $offset = 0) {
        $query = "SELECT cr.*, u.name as reporter_name, u.roll_number
                  FROM content_reports cr
                  LEFT JOIN users u ON cr.reporter_id = u.id
                  WHERE cr.status = 'pending'
                  ORDER BY cr.created_at DESC
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
     * Update report status
     */
    public function updateStatus($report_id, $status, $admin_id, $admin_notes = null) {
        $valid_statuses = ['pending', 'under_review', 'resolved', 'dismissed'];

        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'error' => 'Invalid status'];
        }

        $query = "UPDATE content_reports
                  SET status = ?, resolved_by = ?, resolved_at = NOW(), admin_notes = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("sisi", $status, $admin_id, $admin_notes, $report_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update report'];
    }

    /**
     * Get reports for specific content
     */
    public function getForContent($reported_type, $reported_id) {
        $query = "SELECT cr.*, u.name as reporter_name
                  FROM content_reports cr
                  LEFT JOIN users u ON cr.reporter_id = u.id
                  WHERE cr.reported_type = ? AND cr.reported_id = ?
                  ORDER BY cr.created_at DESC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("si", $reported_type, $reported_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get report stats
     */
    public function getStats() {
        $query = "SELECT
                    COUNT(*) as total_reports,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed
                  FROM content_reports";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
