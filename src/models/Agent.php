<?php

/**
 * Agent.php - Base Agent Model
 * Defines the agent system for campus collaboration
 */

class Agent {
    private $conn;
    private $table = 'user_agents';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get or create an agent for a user
     */
    public function getOrCreateAgent($user_id, $agent_type_name) {
        // Get agent type ID
        $query = "SELECT id FROM agent_types WHERE name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $agent_type_name);
        $stmt->execute();
        $agent_type = $stmt->get_result()->fetch_assoc();

        if (!$agent_type) {
            return ['success' => false, 'error' => 'Invalid agent type'];
        }

        // Check if user already has this agent
        $query = "SELECT * FROM user_agents WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            return ['success' => true, 'agent' => $existing];
        }

        // Create new agent
        $query = "INSERT INTO user_agents (user_id, agent_type_id, primary_goal, communication_style)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $primary_goal = $this->getPrimaryGoal($agent_type_name);
        $communication_style = $this->getCommunicationStyle($agent_type_name);

        $stmt->bind_param("iiss", $user_id, $agent_type['id'], $primary_goal, $communication_style);

        if ($stmt->execute()) {
            return ['success' => true, 'agent_id' => $this->conn->insert_id];
        }

        return ['success' => false, 'error' => 'Failed to create agent'];
    }

    /**
     * Get agent by user ID
     */
    public function getByUserId($user_id) {
        $query = "SELECT ua.*, at.display_name, at.color_code, at.icon
                  FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE ua.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all agents with metrics
     */
    public function getAllAgentsWithMetrics() {
        $query = "SELECT ua.*, at.display_name, at.color_code, at.icon,
                         COUNT(DISTINCT ag.id) as goal_count,
                         COUNT(DISTINCT am.id) as metric_count
                  FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_goals ag ON ua.id = ag.user_agent_id
                  LEFT JOIN agent_metrics am ON ua.id = am.user_agent_id
                  WHERE ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY ua.assigned_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a goal to an agent
     */
    public function addGoal($user_agent_id, $goal_type, $description, $target_metric, $target_value) {
        $query = "INSERT INTO agent_goals (user_agent_id, goal_type, description, target_metric, target_value)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssi", $user_agent_id, $goal_type, $description, $target_metric, $target_value);

        if ($stmt->execute()) {
            return ['success' => true, 'goal_id' => $this->conn->insert_id];
        }

        return ['success' => false, 'error' => 'Failed to add goal'];
    }

    /**
     * Update goal progress
     */
    public function updateGoalProgress($goal_id, $progress) {
        $query = "UPDATE agent_goals SET progress = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $progress, $goal_id);
        return $stmt->execute();
    }

    /**
     * Complete a goal
     */
    public function completeGoal($goal_id) {
        $query = "UPDATE agent_goals SET status = 'completed', completed_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $goal_id);
        return $stmt->execute();
    }

    /**
     * Record a metric
     */
    public function recordMetric($user_agent_id, $metric_name, $metric_value, $metric_type = 'count') {
        $query = "INSERT INTO agent_metrics (user_agent_id, metric_name, metric_value, metric_type)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isds", $user_agent_id, $metric_name, $metric_value, $metric_type);
        return $stmt->execute();
    }

    /**
     * Log an agent action
     */
    public function logAction($user_agent_id, $action_type, $action_description, $idea_id = null, $user_id = null, $metadata = null) {
        $query = "INSERT INTO agent_logs (user_agent_id, action_type, action_description, related_idea_id, related_user_id, metadata)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $metadata_json = $metadata ? json_encode($metadata) : null;
        $stmt->bind_param("ississ", $user_agent_id, $action_type, $action_description, $idea_id, $user_id, $metadata_json);

        return $stmt->execute();
    }

    /**
     * Make a recommendation from one agent to another
     */
    public function makeRecommendation($from_agent_id, $to_agent_id, $recommendation_type, $message, $idea_id = null, $relevance_score = 5) {
        $query = "INSERT INTO agent_recommendations (from_agent_id, to_agent_id, idea_id, recommendation_type, message, relevance_score)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iissi", $from_agent_id, $to_agent_id, $idea_id, $recommendation_type, $message, $relevance_score);

        if ($stmt->execute()) {
            return ['success' => true, 'recommendation_id' => $this->conn->insert_id];
        }

        return ['success' => false, 'error' => 'Failed to create recommendation'];
    }

    /**
     * Get recommendations for an agent
     */
    public function getRecommendations($user_agent_id, $status = 'suggested') {
        $query = "SELECT ar.*,
                         ua_from.user_id as from_user_id,
                         ua_to.user_id as to_user_id,
                         at_from.display_name as from_agent_type,
                         at_to.display_name as to_agent_type
                  FROM agent_recommendations ar
                  JOIN user_agents ua_from ON ar.from_agent_id = ua_from.id
                  JOIN user_agents ua_to ON ar.to_agent_id = ua_to.id
                  JOIN agent_types at_from ON ua_from.agent_type_id = at_from.id
                  JOIN agent_types at_to ON ua_to.agent_type_id = at_to.id
                  WHERE ar.to_agent_id = ? AND ar.action_status = ?
                  ORDER BY ar.relevance_score DESC, ar.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_agent_id, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Accept a recommendation
     */
    public function acceptRecommendation($recommendation_id) {
        $query = "UPDATE agent_recommendations SET action_status = 'accepted', responded_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $recommendation_id);
        return $stmt->execute();
    }

    /**
     * Helper: Get primary goal by agent type
     */
    private function getPrimaryGoal($agent_type) {
        $goals = [
            'student_researcher' => 'Find mentors, validate ideas, and publish research findings',
            'faculty_advisor' => 'Guide student research and validate ideas for academic merit',
            'project_lead' => 'Execute collaborative projects and achieve milestones',
            'peer_reviewer' => 'Provide constructive feedback and ensure quality standards',
            'community_member' => 'Learn from others and contribute feedback to the community'
        ];
        return $goals[$agent_type] ?? 'Collaborate in campus innovation community';
    }

    /**
     * Helper: Get communication style by agent type
     */
    private function getCommunicationStyle($agent_type) {
        $styles = [
            'student_researcher' => 'academic',
            'faculty_advisor' => 'mentoring',
            'project_lead' => 'task-focused',
            'peer_reviewer' => 'critical',
            'community_member' => 'conversational'
        ];
        return $styles[$agent_type] ?? 'collaborative';
    }
}
?>
