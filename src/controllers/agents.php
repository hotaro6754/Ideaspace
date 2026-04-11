<?php

/**
 * AgentController.php
 * Handles agent views and interactions
 */

require_once __DIR__ . '/../models/Agent.php';
require_once __DIR__ . '/../models/StudentResearcherAgent.php';
require_once __DIR__ . '/../models/FacultyAdvisorAgent.php';
require_once __DIR__ . '/../models/ProjectLeadAgent.php';
require_once __DIR__ . '/../models/PeerReviewerAgent.php';
require_once __DIR__ . '/../models/CommunityMemberAgent.php';

class AgentController {
    private $conn;
    private $agent;

    public function __construct($db) {
        $this->conn = $db;
        $this->agent = new Agent($db);
    }

    /**
     * View agent dashboard
     */
    public function dashboard() {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Get user's agent
        $agent = $this->agent->getByUserId($user_id);
        if (!$agent) {
            return ['success' => false, 'error' => 'No agent profile found'];
        }

        // Get goals
        $query = "SELECT * FROM agent_goals WHERE user_agent_id = ? ORDER BY status, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $agent['id']);
        $stmt->execute();
        $goals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get metrics
        $query = "SELECT * FROM agent_metrics WHERE user_agent_id = ? ORDER BY recorded_at DESC LIMIT 20";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $agent['id']);
        $stmt->execute();
        $metrics = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get recommendations
        $recommendations = $this->agent->getRecommendations($agent['id'], 'suggested');

        // Get recent actions
        $query = "SELECT * FROM agent_logs WHERE user_agent_id = ? ORDER BY created_at DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $agent['id']);
        $stmt->execute();
        $actions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get achievements
        $query = "SELECT aa.*, ach.achievement_name, ach.description, ach.badge_icon
                  FROM user_achievements aa
                  JOIN agent_achievements ach ON aa.achievement_id = ach.id
                  WHERE aa.user_agent_id = ?
                  ORDER BY aa.unlocked_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $agent['id']);
        $stmt->execute();
        $achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'success' => true,
            'agent' => $agent,
            'goals' => $goals,
            'metrics' => $metrics,
            'recommendations' => $recommendations,
            'recent_actions' => $actions,
            'achievements' => $achievements
        ];
    }

    /**
     * Get agent-specific recommendations for quick actions
     */
    public function getAgentRecommendations() {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        $agent = $this->agent->getByUserId($user_id);
        if (!$agent) {
            return ['success' => false, 'error' => 'No agent profile'];
        }

        $recommendations = $this->agent->getRecommendations($agent['id']);

        // Filter by agent type for personalized recommendations
        switch ($agent['name']) {
            case 'student_researcher':
                $next_steps = [
                    'Find a mentor from Faculty Advisors',
                    'Submit research for validation',
                    'Explore collaboration opportunities'
                ];
                break;
            case 'faculty_advisor':
                $next_steps = [
                    'Review pending student submissions',
                    'Provide mentorship guidance',
                    'Validate ideas for publication'
                ];
                break;
            case 'project_lead':
                $next_steps = [
                    'Record project milestones',
                    'Request peer review',
                    'Update team status'
                ];
                break;
            case 'peer_reviewer':
                $next_steps = [
                    'Review pending projects',
                    'Detect anti-patterns',
                    'Provide constructive feedback'
                ];
                break;
            default:
                $next_steps = [
                    'Discover learning opportunities',
                    'Support trending ideas',
                    'Join community discussions'
                ];
        }

        return [
            'success' => true,
            'recommendations' => $recommendations,
            'suggested_next_steps' => $next_steps,
            'agent_type' => $agent['name'],
            'agent_display_name' => $agent['display_name']
        ];
    }

    /**
     * View all agents and their performance
     */
    public function viewAgentCommunity() {
        $agents = $this->agent->getAllAgentsWithMetrics();

        return [
            'success' => true,
            'agents' => $agents
        ];
    }

    /**
     * Create agent assignment for new user
     */
    public function assignAgent($user_id, $agent_type) {
        return $this->agent->getOrCreateAgent($user_id, $agent_type);
    }
}
?>
