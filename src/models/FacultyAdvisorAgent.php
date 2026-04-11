<?php

/**
 * FacultyAdvisorAgent.php
 * Agent type: Faculty Advisor
 * Goals: Guide student research, validate ideas, provide mentorship
 */

class FacultyAdvisorAgent {
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
     * Get list of student researchers to mentor
     */
    public function getStudentResearchers($limit = 10) {
        $query = "SELECT ua.*, u.name, u.email, u.profile_pic,
                         COUNT(DISTINCT ag.id) as pending_goals
                  FROM user_agents ua
                  JOIN users u ON ua.user_id = u.id
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  LEFT JOIN agent_goals ag ON ua.id = ag.user_agent_id
                            AND ag.status = 'pending'
                  WHERE at.name = 'student_researcher'
                  AND ua.is_active = TRUE
                  GROUP BY ua.id
                  ORDER BY pending_goals DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Validate an idea from academic perspective
     */
    public function validateIdea($idea_id, $validation_score, $feedback) {
        // Log validation action
        $this->agent->logAction(
            $this->user_agent_id,
            'validated_idea',
            "Validated idea with score: {$validation_score}/10",
            $idea_id,
            null,
            ['feedback' => $feedback, 'score' => $validation_score]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'ideas_validated', 1, 'count');

        return ['success' => true, 'validation_recorded' => true];
    }

    /**
     * Mentor a student researcher
     */
    public function mentorStudent($student_agent_id, $research_focus, $guidance_points) {
        $this->agent->logAction(
            $this->user_agent_id,
            'mentored_student',
            "Provided mentorship to student researcher",
            null,
            null,
            ['focus' => $research_focus, 'guidance_count' => count($guidance_points)]
        );

        // Record metric
        $this->agent->recordMetric($this->user_agent_id, 'students_guided', 1, 'count');

        // Make recommendation for next steps
        return $this->agent->makeRecommendation(
            $this->user_agent_id,
            $student_agent_id,
            'mentorship',
            "Based on your research, consider: " . implode(', ', $guidance_points),
            null,
            8
        );
    }

    /**
     * Approve a student's idea for publication
     */
    public function approveForPublication($idea_id, $publication_notes) {
        $this->agent->logAction(
            $this->user_agent_id,
            'approved_publication',
            "Approved idea for academic publication",
            $idea_id,
            null,
            ['notes' => $publication_notes]
        );

        return ['success' => true, 'approved' => true];
    }
}
?>
