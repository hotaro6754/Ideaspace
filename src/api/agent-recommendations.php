<?php

/**
 * Agent Recommendations API
 * Handles agent-to-agent recommendations and smart matching
 */

require_once __DIR__ . '/../models/Agent.php';
require_once __DIR__ . '/../models/StudentResearcherAgent.php';
require_once __DIR__ . '/../models/FacultyAdvisorAgent.php';
require_once __DIR__ . '/../models/ProjectLeadAgent.php';
require_once __DIR__ . '/../models/PeerReviewerAgent.php';
require_once __DIR__ . '/../models/CommunityMemberAgent.php';

class AgentRecommendationEngine {
    private $conn;
    private $agent;

    public function __construct($db) {
        $this->conn = $db;
        $this->agent = new Agent($db);
    }

    /**
     * Generate smart recommendations based on agent type and context
     */
    public function generateRecommendations($user_agent_id) {
        $userAgent = $this->getAgentById($user_agent_id);
        if (!$userAgent) {
            return [];
        }

        $recommendations = [];

        // Route to agent-specific recommendation logic
        switch ($userAgent['name']) {
            case 'student_researcher':
                $recommendations = $this->recommendForResearcher($user_agent_id);
                break;
            case 'faculty_advisor':
                $recommendations = $this->recommendForAdvisor($user_agent_id);
                break;
            case 'project_lead':
                $recommendations = $this->recommendForProjectLead($user_agent_id);
                break;
            case 'peer_reviewer':
                $recommendations = $this->recommendForReviewer($user_agent_id);
                break;
            default:
                $recommendations = $this->recommendForCommunity($user_agent_id);
        }

        return $recommendations;
    }

    /**
     * Recommendations for Student Researchers
     * Find mentors and peer reviewers, suggest collaboration opportunities
     */
    private function recommendForResearcher($user_agent_id) {
        $recommendations = [];

        // Find faculty advisors in similar domains
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic,
                         COUNT(DISTINCT ag.id) as mentorship_count,
                         at.display_name
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_goals ag ON ua.id = ag.user_agent_id
                  WHERE at.name = 'faculty_advisor'
                  AND ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY mentorship_count ASC, RAND()
                  LIMIT 3";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $advisors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($advisors as $advisor) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $advisor['id'],
                'mentorship',
                "Perfect mentor match for your research. " . $advisor['name'] . " has guided " . $advisor['mentorship_count'] . " researchers.",
                null,
                rand(6, 9)
            );
        }

        // Find peer reviewers
        $query = "SELECT ua.* FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE at.name = 'peer_reviewer'
                  AND ua.is_active = TRUE
                  ORDER BY RAND()
                  LIMIT 2";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $reviewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($reviewers as $reviewer) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $reviewer['id'],
                'review',
                "Get expert feedback on your research before publication",
                null,
                7
            );
        }

        return $this->agent->getRecommendations($user_agent_id, 'suggested');
    }

    /**
     * Recommendations for Faculty Advisors
     * Find promising student researchers and collaboration opportunities
     */
    private function recommendForAdvisor($user_agent_id) {
        // Find student researchers without mentors
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE at.name = 'student_researcher'
                  AND ua.is_active = TRUE
                  AND ua.id NOT IN (
                      SELECT to_agent_id FROM agent_recommendations
                      WHERE from_agent_id = ? AND recommendation_type = 'mentorship'
                  )
                  ORDER BY ua.assigned_at DESC
                  LIMIT 3";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_agent_id);
        $stmt->execute();
        $researchers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($researchers as $researcher) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $researcher['id'],
                'mentorship',
                "New student researcher " . $researcher['name'] . " is looking for mentorship",
                null,
                rand(7, 9)
            );
        }

        return $this->agent->getRecommendations($user_agent_id, 'suggested');
    }

    /**
     * Recommendations for Project Leads
     * Find peer reviewers, potential collaborators, and related projects
     */
    private function recommendForProjectLead($user_agent_id) {
        // Find peer reviewers for project milestones
        $query = "SELECT ua.* FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE at.name = 'peer_reviewer'
                  AND ua.is_active = TRUE
                  ORDER BY RAND()
                  LIMIT 2";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $reviewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($reviewers as $reviewer) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $reviewer['id'],
                'review',
                "Request peer review for upcoming project milestone",
                null,
                8
            );
        }

        return $this->agent->getRecommendations($user_agent_id, 'suggested');
    }

    /**
     * Recommendations for Peer Reviewers
     * Find projects needing review and collaboration opportunities
     */
    private function recommendForReviewer($user_agent_id) {
        // Find project leads with ongoing projects
        $query = "SELECT ua.*, u.name FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE at.name = 'project_lead'
                  AND ua.is_active = TRUE
                  AND ua.id NOT IN (
                      SELECT to_agent_id FROM agent_recommendations
                      WHERE from_agent_id = ? AND recommendation_type = 'review'
                  )
                  ORDER BY RAND()
                  LIMIT 3";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_agent_id);
        $stmt->execute();
        $leads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($leads as $lead) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $lead['id'],
                'review',
                "Project " . $lead['name'] . " needs quality review for their next milestone",
                null,
                rand(6, 8)
            );
        }

        return $this->agent->getRecommendations($user_agent_id, 'suggested');
    }

    /**
     * Recommendations for Community Members
     * Find trending ideas and learning opportunities
     */
    private function recommendForCommunity($user_agent_id) {
        // Find trending ideas
        $query = "SELECT i.*, u.name FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  WHERE i.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                  ORDER BY i.upvotes DESC
                  LIMIT 3";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ideas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Find community members to connect with
        $query2 = "SELECT ua.* FROM user_agents ua
                   WHERE ua.is_active = TRUE
                   ORDER BY RAND()
                   LIMIT 2";

        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $members = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($members as $member) {
            $this->agent->makeRecommendation(
                $user_agent_id,
                $member['id'],
                'collaboration',
                "Connect and learn from this community member",
                null,
                rand(5, 7)
            );
        }

        return $this->agent->getRecommendations($user_agent_id, 'suggested');
    }

    /**
     * Accept a recommendation and log it
     */
    public function acceptRecommendation($recommendation_id) {
        $this->agent->acceptRecommendation($recommendation_id);
        return ['success' => true];
    }

    /**
     * Get agent by ID
     */
    private function getAgentById($agent_id) {
        $query = "SELECT ua.*, at.name FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  WHERE ua.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    require_once __DIR__ . '/../config/Database.php';
    $db = new Database();
    $conn = $db->connect();

    $engine = new AgentRecommendationEngine($conn);

    if ($action === 'generate') {
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            require_once __DIR__ . '/../models/Agent.php';
            $agent = new Agent($conn);
            $userAgent = $agent->getByUserId($user_id);

            if ($userAgent) {
                $recs = $engine->generateRecommendations($userAgent['id']);
                echo json_encode(['success' => true, 'recommendations' => $recs]);
            }
        }
    } elseif ($action === 'accept') {
        $recommendation_id = (int)($_POST['recommendation_id'] ?? 0);
        $result = $engine->acceptRecommendation($recommendation_id);
        echo json_encode($result);
    }
}
?>
