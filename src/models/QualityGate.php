<?php

/**
 * QualityGate.php - Quality Gates & Approval System
 * Manages milestone approvals, quality checks, andblockers
 */

class QualityGate {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a milestone
     */
    public function createMilestone($idea_id, $title, $description, $target_date) {
        $query = "INSERT INTO milestones (idea_id, title, description, target_date)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $idea_id, $title, $description, $target_date);

        if ($stmt->execute()) {
            return ['success' => true, 'milestone_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to create milestone'];
    }

    /**
     * Complete a milestone
     */
    public function completeMilestone($milestone_id) {
        $query = "UPDATE milestones SET status = 'completed', actual_completion_date = NOW(), completion_percentage = 100 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $milestone_id);
        return $stmt->execute();
    }

    /**
     * Request approval for milestone
     */
    public function requestMilestoneApproval($milestone_id, $required_approvals = 2) {
        $query = "INSERT INTO milestone_approvals (milestone_id, required_approvals, approval_status)
                  VALUES (?, ?, 'pending')
                  ON DUPLICATE KEY UPDATE required_approvals = VALUES(required_approvals)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $milestone_id, $required_approvals);
        return $stmt->execute();
    }

    /**
     * Approve a milestone
     */
    public function approveMilestone($milestone_id, $approver_id, $approval_type = 'approve', $comments = '') {
        // Record approval
        $query = "INSERT INTO approvals (milestone_id, approver_id, approval_type, comments)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiss", $milestone_id, $approver_id, $approval_type, $comments);
        $stmt->execute();

        // Update approval count
        $query2 = "UPDATE milestone_approvals
                   SET received_approvals = (
                       SELECT COUNT(*) FROM approvals WHERE milestone_id = ? AND approval_type = 'approve'
                   )
                   WHERE milestone_id = ?";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bind_param("ii", $milestone_id, $milestone_id);
        $stmt2->execute();

        // Check if approval status should change
        $this->updateApprovalStatus($milestone_id);

        return ['success' => true];
    }

    /**
     * Update approval status
     */
    private function updateApprovalStatus($milestone_id) {
        $query = "SELECT required_approvals, received_approvals FROM milestone_approvals WHERE milestone_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $milestone_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['received_approvals'] >= $result['required_approvals']) {
            $query2 = "UPDATE milestone_approvals SET approval_status = 'approved', approved_at = NOW() WHERE milestone_id = ?";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bind_param("i", $milestone_id);
            $stmt2->execute();
        }
    }

    /**
     * Record a quality check result
     */
    public function recordQualityCheck($idea_id, $check_name, $check_type, $status, $details, $checked_by) {
        $details_json = json_encode($details);
        $query = "INSERT INTO quality_checks (idea_id, check_name, check_type, status, details, checked_by, checked_at)
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssis", $idea_id, $check_name, $check_type, $status, $details_json, $checked_by);

        if ($stmt->execute()) {
            return ['success' => true, 'check_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to record quality check'];
    }

    /**
     * Get quality checks for an idea
     */
    public function getQualityChecks($idea_id) {
        $query = "SELECT * FROM quality_checks WHERE idea_id = ? ORDER BY check_type, checked_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a blocker
     */
    public function addBlocker($idea_id, $blocker_type, $title, $description, $severity, $reported_by) {
        $query = "INSERT INTO blockers (idea_id, blocker_type, title, description, severity, reported_by, owner_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $owner_id = $reported_by; // Default: reporter owns the issue
        $stmt->bind_param("issisii", $idea_id, $blocker_type, $title, $description, $severity, $reported_by, $owner_id);

        if ($stmt->execute()) {
            return ['success' => true, 'blocker_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add blocker'];
    }

    /**
     * Resolve a blocker
     */
    public function resolveBlocker($blocker_id, $resolution) {
        $query = "UPDATE blockers SET status = 'resolved', resolution = ?, resolved_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $resolution, $blocker_id);
        return $stmt->execute();
    }

    /**
     * Get active blockers for an idea
     */
    public function getBlockers($idea_id, $status = null) {
        $query = "SELECT b.*, u_reported.name as reported_by_name, u_owner.name as owner_name
                  FROM blockers b
                  LEFT JOIN users u_reported ON b.reported_by = u_reported.id
                  LEFT JOIN users u_owner ON b.owner_id = u_owner.id
                  WHERE b.idea_id = ?";

        if ($status) {
            $query .= " AND b.status = ?";
        }

        $query .= " ORDER BY b.severity DESC, b.created_at DESC";

        $stmt = $this->conn->prepare($query);

        if ($status) {
            $stmt->bind_param("is", $idea_id, $status);
        } else {
            $stmt->bind_param("i", $idea_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add review comment
     */
    public function addReviewComment($reviewer_id, $idea_id, $review_type, $comment_text, $severity, $category) {
        $query = "INSERT INTO review_comments (reviewer_id, idea_id, review_type, comment_text, severity, category)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssss", $reviewer_id, $idea_id, $review_type, $comment_text, $severity, $category);

        if ($stmt->execute()) {
            return ['success' => true, 'comment_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add comment'];
    }

    /**
     * Get review comments for an idea
     */
    public function getReviewComments($idea_id) {
        $query = "SELECT rc.*, u.name, u.profile_pic
                  FROM review_comments rc
                  JOIN users u ON rc.reviewer_id = u.id
                  WHERE rc.idea_id = ?
                  ORDER BY rc.severity DESC, rc.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Record quality metric
     */
    public function recordQualityMetric($idea_id, $metric_name, $metric_value, $target_value, $unit = '%') {
        $query = "INSERT INTO quality_metrics (idea_id, metric_name, metric_value, target_value, unit)
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isdds", $idea_id, $metric_name, $metric_value, $target_value, $unit);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to record metric'];
    }

    /**
     * Get quality metrics for an idea
     */
    public function getQualityMetrics($idea_id) {
        $query = "SELECT * FROM quality_metrics WHERE idea_id = ? ORDER BY measured_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get quality gate dashboard
     */
    public function getQualityGateDashboard($idea_id) {
        // Get milestones
        $milestones_query = "SELECT * FROM milestones WHERE idea_id = ? ORDER BY target_date";
        $stmt = $this->conn->prepare($milestones_query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $milestones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get blockers
        $blockers = $this->getBlockers($idea_id);

        // Get quality checks
        $checks = $this->getQualityChecks($idea_id);

        // Get review comments
        $comments = $this->getReviewComments($idea_id);

        // Get metrics
        $metrics = $this->getQualityMetrics($idea_id);

        // Calculate overall quality score
        $quality_score = $this->calculateQualityScore($idea_id, $checks, $blockers, $metrics);

        return [
            'milestones' => $milestones,
            'blockers' => $blockers,
            'quality_checks' => $checks,
            'review_comments' => $comments,
            'quality_metrics' => $metrics,
            'overall_quality_score' => $quality_score
        ];
    }

    /**
     * Calculate overall quality score
     */
    private function calculateQualityScore($idea_id, $checks, $blockers, $metrics) {
        $score = 100;

        // Deduct for failed checks
        $failed_checks = array_filter($checks, fn($c) => $c['status'] === 'failed');
        $score -= count($failed_checks) * 10;

        // Deduct for critical blockers
        $critical_blockers = array_filter($blockers, fn($b) => $b['severity'] === 'critical' && $b['status'] === 'open');
        $score -= count($critical_blockers) * 15;

        // Deduct for high blockers
        $high_blockers = array_filter($blockers, fn($b) => $b['severity'] === 'high' && $b['status'] === 'open');
        $score -= count($high_blockers) * 5;

        // Add points for metrics meeting targets
        foreach ($metrics as $m) {
            if ($m['metric_value'] >= $m['target_value']) {
                $score += 5;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Grant gate approval credential
     */
    public function grantApprovalCredential($user_id, $gate_name, $privilege_level = 'approver', $domain = null) {
        $query = "INSERT INTO gate_credentials (user_id, gate_name, privilege_level, verify_domain)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE privilege_level = VALUES(privilege_level)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $gate_name, $privilege_level, $domain);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false];
    }

    /**
     * Check if user can approve a gate
     */
    public function canApproveGate($user_id, $gate_name) {
        $query = "SELECT * FROM gate_credentials
                  WHERE user_id = ?
                  AND gate_name = ?
                  AND privilege_level IN ('approver', 'admin')
                  AND (expires_at IS NULL OR expires_at > NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_id, $gate_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }
}
?>
