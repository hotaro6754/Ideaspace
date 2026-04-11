<?php

/**
 * ProjectLeadAgent.php
 * Agent type: Project Lead
 * Goals: Execute collaborative projects, manage teams, hit milestones
 */

class ProjectLeadAgent {
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
     * Get active team collaborations
     */
    public function getActiveTeams() {
        $query = "SELECT c.*, i.title, i.domain,
                         COUNT(DISTINCT collab.id) as team_size,
                         i.status as project_status
                  FROM collaborations c
                  JOIN ideas i ON c.idea_id = i.id
                  LEFT JOIN collaborations collab ON c.idea_id = collab.idea_id
                                                   AND collab.status = 'active'
                  WHERE (c.leader_id = ? OR c.collaborator_id = ?)
                  AND c.status = 'active'
                  GROUP BY c.id
                  ORDER BY c.joined_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->user_agent_id, $this->user_agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Record milestone completion
     */
    public function recordMilestone($idea_id, $milestone_name, $milestone_description) {
        $this->agent->logAction(
            $this->user_agent_id,
            'milestone_completed',
            "Completed milestone: {$milestone_name}",
            $idea_id,
            null,
            ['description' => $milestone_description]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'milestones_completed', 1, 'count');

        return ['success' => true, 'milestone_recorded' => true];
    }

    /**
     * Find peer reviewers for project
     */
    public function findPeerReviewers($idea_id, $limit = 3) {
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic,
                         COUNT(DISTINCT am.id) as reviews_given
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_metrics am ON ua.id = am.user_agent_id
                            AND am.metric_name = 'reviews_provided'
                  WHERE at.name = 'peer_reviewer'
                  AND ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY reviews_given DESC, RAND()
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Request peer review of project
     */
    public function requestPeerReview($peer_reviewer_agent_id, $idea_id, $review_scope) {
        $this->agent->logAction(
            $this->user_agent_id,
            'requested_peer_review',
            "Requested peer review for milestone",
            $idea_id,
            null,
            ['scope' => $review_scope]
        );

        // Make recommendation to peer reviewer
        return $this->agent->makeRecommendation(
            $this->user_agent_id,
            $peer_reviewer_agent_id,
            'review',
            "Project milestone review request: " . $review_scope,
            $idea_id,
            7
        );
    }

    /**
     * Track project completion
     */
    public function completeProject($idea_id, $completion_summary) {
        $this->agent->logAction(
            $this->user_agent_id,
            'project_completed',
            "Project completed successfully",
            $idea_id,
            null,
            ['summary' => $completion_summary]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'projects_completed', 1, 'count');

        return ['success' => true, 'project_completed' => true];
    }
}
?>
