<?php

/**
 * CommunityMemberAgent.php
 * Agent type: Community Member
 * Goals: Learn, contribute feedback, support community growth
 */

class CommunityMemberAgent {
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
     * Discover learning opportunities
     */
    public function discoverOpportunities($interests = '', $limit = 10) {
        $query = "SELECT i.*, u.name, u.profile_pic,
                         COUNT(DISTINCT c.id) as team_size,
                         COUNT(DISTINCT upv.id) as upvote_count,
                         GROUP_CONCAT(DISTINCT c.role) as roles_available
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN collaborations c ON i.id = c.idea_id AND c.status = 'active'
                  LEFT JOIN upvotes upv ON i.id = upv.idea_id
                  WHERE i.status IN ('open', 'in_progress')
                  GROUP BY i.id
                  ORDER BY upvote_count DESC, i.created_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Upvote and support an idea
     */
    public function supportIdea($idea_id) {
        $this->agent->logAction(
            $this->user_agent_id,
            'supported_idea',
            "Upvoted and expressed support",
            $idea_id
        );

        // Record community engagement metric
        $this->agent->recordMetric($this->user_agent_id, 'ideas_supported', 1, 'count');

        return ['success' => true, 'support_recorded' => true];
    }

    /**
     * Contribute feedback to idea
     */
    public function contributeFeedback($idea_id, $feedback_text) {
        $this->agent->logAction(
            $this->user_agent_id,
            'contributed_feedback',
            "Added constructive feedback to idea",
            $idea_id,
            null,
            ['feedback' => substr($feedback_text, 0, 200)]
        );

        // Record engagement metric
        $this->agent->recordMetric($this->user_agent_id, 'feedback_contributed', 1, 'count');
        $this->agent->recordMetric($this->user_agent_id, 'community_engagement_score', 5, 'rating');

        return ['success' => true, 'feedback_recorded' => true];
    }

    /**
     * Join a community discussion/channel
     */
    public function joinDiscussion($channel_id, $discussion_topic) {
        $this->agent->logAction(
            $this->user_agent_id,
            'joined_discussion',
            "Joined discussion: {$discussion_topic}",
            null,
            null,
            ['channel_id' => $channel_id, 'topic' => $discussion_topic]
        );

        // Track community participation
        $this->agent->recordMetric($this->user_agent_id, 'discussions_joined', 1, 'count');

        return ['success' => true, 'joined' => true];
    }

    /**
     * Recommend ideas to community members
     */
    public function recommendIdeas($similar_idea_ids = []) {
        foreach ($similar_idea_ids as $idea_id) {
            $this->agent->logAction(
                $this->user_agent_id,
                'recommended_idea',
                "Recommended idea to community",
                $idea_id
            );
        }

        // Track recommendations given
        $this->agent->recordMetric(
            $this->user_agent_id,
            'ideas_recommended',
            count($similar_idea_ids),
            'count'
        );

        return ['success' => true, 'recommendations_recorded' => true];
    }

    /**
     * Get community insights and trending ideas
     */
    public function getCommunityInsights() {
        $query = "SELECT
                    COUNT(DISTINCT i.id) as total_ideas,
                    COUNT(DISTINCT u.id) as total_members,
                    COUNT(DISTINCT c.id) as active_collaborations,
                    AVG(i.upvotes) as avg_idea_support
                  FROM ideas i
                  CROSS JOIN users u
                  LEFT JOIN collaborations c ON c.status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $insights = $stmt->get_result()->fetch_assoc();

        // Get trending ideas
        $query2 = "SELECT i.*, u.name, u.profile_pic,
                          COUNT(DISTINCT upv.id) as upvote_count,
                          COUNT(DISTINCT c.id) as collaborator_count
                   FROM ideas i
                   JOIN users u ON i.user_id = u.id
                   LEFT JOIN upvotes upv ON i.id = upv.idea_id
                   LEFT JOIN collaborations c ON i.id = c.idea_id
                   WHERE i.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                   GROUP BY i.id
                   ORDER BY upvote_count DESC
                   LIMIT 5";

        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $trending = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'community_insights' => $insights,
            'trending_ideas' => $trending
        ];
    }

    /**
     * Learn from top performers
     */
    public function getTopPerformers($limit = 5) {
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic,
                         at.display_name as agent_type,
                         COUNT(DISTINCT am.id) as metric_count,
                         SUM(am.metric_value) as total_contribution
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_metrics am ON ua.id = am.user_agent_id
                  WHERE ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY total_contribution DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
