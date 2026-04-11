<?php

/**
 * PeerReviewerAgent.php
 * Agent type: Peer Reviewer
 * Goals: Provide constructive feedback, quality assurance, anti-pattern detection
 */

class PeerReviewerAgent {
    private $agent;
    private $user_agent_id;
    private $conn;

    public function __construct($db, $user_agent_id) {
        $this->conn = $db;
        $this->user_agent_id = $user_agent_id;
        require_once __DIR__ . '/Agent.php';
        $this->agent = new Agent($db);
    }

    /**
     * Review a project/idea and detect anti-patterns
     */
    public function reviewProject($idea_id, $review_findings) {
        $anti_patterns = $this->detectAntiPatterns($idea_id);

        $this->agent->logAction(
            $this->user_agent_id,
            'project_reviewed',
            "Completed project review",
            $idea_id,
            null,
            [
                'findings' => $review_findings,
                'anti_patterns_detected' => count($anti_patterns),
                'patterns' => $anti_patterns
            ]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'reviews_provided', 1, 'count');

        return ['success' => true, 'anti_patterns' => $anti_patterns];
    }

    /**
     * Detect collaboration anti-patterns
     */
    private function detectAntiPatterns($idea_id) {
        $patterns = [];

        // Check for Silent Partner problem (active collaborators)
        $query = "SELECT COUNT(*) as collaborator_count FROM collaborations
                  WHERE idea_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['collaborator_count'] > 5) {
            $patterns[] = 'silent_partner_risk: Too many collaborators may lead to unclear roles';
        }

        // Check for scope creep (too many skills)
        $query = "SELECT skills_needed FROM ideas WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $idea = $stmt->get_result()->fetch_assoc();

        if ($idea) {
            $skills = json_decode($idea['skills_needed'], true);
            if (count($skills) > 8) {
                $patterns[] = 'scope_creep: Project requires too many different skills (risk of overscoping)';
            }
        }

        // Check for deadline drift (no milestones)
        $query = "SELECT COUNT(*) as milestone_count FROM agent_logs
                  WHERE related_idea_id = ? AND action_type = 'milestone_completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['milestone_count'] == 0) {
            $patterns[] = 'deadline_drift: No recorded milestones (risk of untracked progress)';
        }

        return $patterns;
    }

    /**
     * Provide constructive feedback
     */
    public function provideFeedback($idea_id, $feedback_type, $feedback_text, $severity = 'medium') {
        $this->agent->logAction(
            $this->user_agent_id,
            'feedback_provided',
            "Provided {$feedback_type} feedback",
            $idea_id,
            null,
            ['text' => $feedback_text, 'severity' => $severity]
        );

        // Record feedback quality metric
        $quality_score = strlen($feedback_text) > 100 ? 8 : 5; // More detailed = higher quality
        $this->agent->recordMetric($this->user_agent_id, 'feedback_quality_score', $quality_score, 'rating');

        return ['success' => true, 'feedback_recorded' => true];
    }

    /**
     * Get ideas pending review
     */
    public function getIdeasPendingReview($limit = 5) {
        $query = "SELECT i.*, u.name, u.profile_pic,
                         COUNT(DISTINCT c.id) as team_size,
                         COUNT(DISTINCT al.id) as action_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN collaborations c ON i.id = c.idea_id AND c.status = 'active'
                  LEFT JOIN agent_logs al ON i.id = al.related_idea_id
                  WHERE i.status IN ('in_progress', 'completed')
                  AND i.id NOT IN (
                      SELECT DISTINCT related_idea_id FROM agent_logs
                      WHERE action_type = 'project_reviewed'
                      AND DATE_ADD(created_at, INTERVAL 7 DAY) > NOW()
                  )
                  GROUP BY i.id
                  ORDER BY i.updated_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
