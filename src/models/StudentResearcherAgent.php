<?php

/**
 * StudentResearcherAgent.php
 * Agent type: Student Researcher
 * Goals: Find mentors, validate ideas, publish findings
 */

class StudentResearcherAgent {
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
     * Find mentors (Faculty Advisors) based on expertise
     */
    public function findMentors($field_of_interest, $limit = 5) {
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic,
                         COUNT(DISTINCT ag.id) as guided_students
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_goals ag ON ua.id = ag.user_agent_id
                            AND ag.goal_type = 'guide_research'
                  WHERE at.name = 'faculty_advisor'
                  AND ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY guided_students DESC, ua.assigned_at DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Submit research for validation by mentor
     */
    public function submitForValidation($mentor_agent_id, $idea_id, $research_summary) {
        // Log action
        $this->agent->logAction(
            $this->user_agent_id,
            'submitted_research_for_validation',
            "Submitted research for mentor validation",
            $idea_id,
            null,
            ['summary' => $research_summary]
        );

        // Make recommendation to mentor
        return $this->agent->makeRecommendation(
            $this->user_agent_id,
            $mentor_agent_id,
            'review',
            "Research submission for validation: " . substr($research_summary, 0, 100),
            $idea_id,
            7
        );
    }

    /**
     * Track publications
     */
    public function recordPublication($publication_title, $publication_url, $idea_id = null) {
        $this->agent->logAction(
            $this->user_agent_id,
            'published_research',
            "Published: " . $publication_title,
            $idea_id,
            null,
            ['url' => $publication_url]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'papers_published', 1, 'count');

        // Check for achievement: "First Publication"
        return ['success' => true, 'publication_recorded' => true];
    }

    /**
     * Find collaboration opportunities that match expertise
     */
    public function findRelevantIdeas($domain, $skills) {
        $skillsJson = json_encode($skills);
        $query = "SELECT i.*, u.name, u.profile_pic,
                         SUM(CASE WHEN upvotes.id IS NOT NULL THEN 1 ELSE 0 END) as upvote_count,
                         COUNT(DISTINCT a.id) as applicant_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN upvotes ON i.id = upvotes.idea_id
                  LEFT JOIN applications a ON i.id = a.idea_id
                  WHERE i.domain = ?
                  AND i.status = 'open'
                  AND JSON_CONTAINS(i.skills_needed, ?, '$')
                  GROUP BY i.id
                  ORDER BY upvote_count DESC, i.created_at DESC
                  LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $domain, $skillsJson);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
