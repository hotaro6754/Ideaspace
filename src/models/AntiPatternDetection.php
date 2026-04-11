<?php

/**
 * AntiPatternDetection.php - Automated Anti-Pattern Detection Engine
 * Detects collaboration risks and project anti-patterns
 */

class AntiPatternDetection {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Run all anti-pattern detections for an idea
     */
    public function detectAllPatterns($idea_id) {
        $patterns_found = [];

        // Detect each pattern
        $silent_partner = $this->detectSilentPartner($idea_id);
        if ($silent_partner['found']) {
            $patterns_found[] = $silent_partner;
        }

        $scope_creep = $this->detectScopeCreep($idea_id);
        if ($scope_creep['found']) {
            $patterns_found[] = $scope_creep;
        }

        $unclear_ownership = $this->detectUnclearOwnership($idea_id);
        if ($unclear_ownership['found']) {
            $patterns_found[] = $unclear_ownership;
        }

        $knowledge_isolation = $this->detectKnowledgeIsolation($idea_id);
        if ($knowledge_isolation['found']) {
            $patterns_found[] = $knowledge_isolation;
        }

        $deadline_drift = $this->detectDeadlineDrift($idea_id);
        if ($deadline_drift['found']) {
            $patterns_found[] = $deadline_drift;
        }

        $communication_breakdown = $this->detectCommunicationBreakdown($idea_id);
        if ($communication_breakdown['found']) {
            $patterns_found[] = $communication_breakdown;
        }

        // Save detected patterns
        foreach ($patterns_found as $pattern) {
            $this->recordPattern($idea_id, $pattern);
        }

        return $patterns_found;
    }

    /**
     * Detect Silent Partner Problem
     * Collaborators assigned but not actively contributing
     */
    private function detectSilentPartner($idea_id) {
        $query = "SELECT
                    c.id,
                    c.collaborator_id,
                    u.name,
                    COUNT(DISTINCT wt.id) as tasks_assigned,
                    SUM(CASE WHEN wt.status = 'completed' THEN 1 ELSE 0 END) as tasks_completed,
                    COUNT(DISTINCT m.id) as messages_sent
                  FROM collaborations c
                  JOIN users u ON c.collaborator_id = u.id
                  LEFT JOIN wave_tasks wt ON c.collaborator_id = wt.assigned_to
                  LEFT JOIN messages m ON c.collaborator_id = m.sender_user_id
                                       AND m.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
                  WHERE c.idea_id = ? AND c.status = 'active'
                  GROUP BY c.id
                  HAVING tasks_assigned > 0 AND (tasks_completed = 0 OR messages_sent = 0)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (count($result) > 0) {
            return [
                'found' => true,
                'pattern_name' => 'Silent Partner Problem',
                'severity' => 'warning',
                'details' => [
                    'silent_partners' => count($result),
                    'affected_collaborators' => $result
                ],
                'message' => count($result) . ' collaborator(s) assigned but not actively contributing'
            ];
        }

        return ['found' => false];
    }

    /**
     * Detect Scope Creep
     * Requirements expanding beyond original scope
     */
    private function detectScopeCreep($idea_id) {
        $query = "SELECT
                    COUNT(DISTINCT skills) as original_skill_count,
                    (SELECT COUNT(*) FROM wave_tasks WHERE project_roadmap_id
                        IN (SELECT id FROM project_roadmaps WHERE idea_id = ?)
                    ) as original_task_count
                  FROM (
                    SELECT JSON_UNQUOTE(JSON_EXTRACT(skills_needed, '$[*]')) as skills
                    FROM ideas WHERE id = ?
                  ) as skill_table";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $idea_id, $idea_id);
        $stmt->execute();
        $original = $stmt->get_result()->fetch_assoc();

        // Check current state
        $query2 = "SELECT JSON_ARRAY_LENGTH(skills_needed) as current_skills
                   FROM ideas WHERE id = ?";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bind_param("i", $idea_id);
        $stmt2->execute();
        $current = $stmt2->get_result()->fetch_assoc();

        $skills_added = ($current['current_skills'] ?? 0) - ($original['original_skill_count'] ?? 0);

        // Check for task growth
        $query3 = "SELECT COUNT(*) as total_tasks FROM wave_tasks WHERE project_roadmap_id
                   IN (SELECT id FROM project_roadmaps WHERE idea_id = ?)";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->bind_param("i", $idea_id);
        $stmt3->execute();
        $current_tasks = $stmt3->get_result()->fetch_assoc();

        $tasks_added = ($current_tasks['total_tasks'] ?? 0) - ($original['original_task_count'] ?? 0);

        if ($skills_added > 3 || $tasks_added > 20) {
            return [
                'found' => true,
                'pattern_name' => 'Scope Creep',
                'severity' => 'critical',
                'details' => [
                    'skills_added' => $skills_added,
                    'tasks_added' => $tasks_added
                ],
                'message' => "Scope has grown significantly: +{$skills_added} skills, +{$tasks_added} tasks"
            ];
        }

        return ['found' => false];
    }

    /**
     * Detect Unclear Ownership
     * Tasks without clear owner
     */
    private function detectUnclearOwnership($idea_id) {
        $query = "SELECT COUNT(*) as unowned_tasks FROM wave_tasks wt
                  JOIN project_roadmaps pr ON wt.project_roadmap_id = pr.id
                  WHERE pr.idea_id = ? AND wt.assigned_to IS NULL AND wt.status IN ('pending', 'in_progress')";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['unowned_tasks'] > 0) {
            return [
                'found' => true,
                'pattern_name' => 'Unclear Ownership',
                'severity' => 'warning',
                'details' => ['unowned_tasks' => $result['unowned_tasks']],
                'message' => $result['unowned_tasks'] . ' task(s) without clear ownership'
            ];
        }

        return ['found' => false];
    }

    /**
     * Detect Knowledge Isolation
     * Critical knowledge held by single person
     */
    private function detectKnowledgeIsolation($idea_id) {
        $query = "SELECT
                    COUNT(DISTINCT al.related_user_id) as users_with_actions,
                    SUM(CASE WHEN user_id_count = 1 THEN 1 ELSE 0 END) as isolated_knowledge_areas
                  FROM agent_logs al
                  LEFT JOIN (
                    SELECT action_type, COUNT(DISTINCT user_id) as user_id_count
                    FROM (SELECT al.action_type, al.user_agent_id as user_id FROM agent_logs al) sub
                    GROUP BY action_type
                  ) sub ON al.action_type = sub.action_type
                  WHERE al.related_idea_id = ?";

        // Simplified detection
        $query = "SELECT
                    COUNT(DISTINCT rc.reviewer_id) as reviewers,
                    COUNT(DISTINCT CASE WHEN rc.resolved = 0 THEN 1 END) as unresolved_issues
                  FROM review_comments rc
                  WHERE rc.idea_id = ?
                  AND rc.category IN ('documentation', 'knowledge_transfer')";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $low_reviewer_count = ($result['reviewers'] ?? 0) < 2;
        $has_knowledge_issues = ($result['unresolved_issues'] ?? 0) > 0;

        if ($low_reviewer_count && $has_knowledge_issues) {
            return [
                'found' => true,
                'pattern_name' => 'Knowledge Isolation',
                'severity' => 'critical',
                'details' => ['reviewers' => $result['reviewers'], 'issues' => $result['unresolved_issues']],
                'message' => 'Knowledge not adequately distributed across team'
            ];
        }

        return ['found' => false];
    }

    /**
     * Detect Deadline Drift
     * Milestones consistently delayed
     */
    private function detectDeadlineDrift($idea_id) {
        $query = "SELECT
                    COUNT(*) as total_milestones,
                    SUM(CASE WHEN actual_completion_date > target_date THEN 1 ELSE 0 END) as delayed_milestones,
                    AVG(DATEDIFF(actual_completion_date, target_date)) as avg_delay_days
                  FROM milestones
                  WHERE idea_id = ? AND actual_completion_date IS NOT NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $delay_ratio = $result['total_milestones'] > 0 ? $result['delayed_milestones'] / $result['total_milestones'] : 0;

        if ($delay_ratio >= 0.5 && $result['avg_delay_days'] > 3) {
            return [
                'found' => true,
                'pattern_name' => 'Deadline Drift',
                'severity' => 'critical',
                'details' => [
                    'delay_ratio' => round($delay_ratio * 100) . '%',
                    'avg_delay' => round($result['avg_delay_days']) . ' days'
                ],
                'message' => round($delay_ratio * 100) . '% of milestones delayed by avg ' . round($result['avg_delay_days']) . ' days'
            ];
        }

        return ['found' => false];
    }

    /**
     * Detect Communication Breakdown
     * Low message frequency
     */
    private function detectCommunicationBreakdown($idea_id) {
        $query = "SELECT
                    COUNT(*) as message_count,
                    MAX(created_at) as last_message,
                    DATEDIFF(NOW(), MAX(created_at)) as days_since_message
                  FROM messages m
                  WHERE sender_user_id IN (
                    SELECT user_id FROM collaborations WHERE idea_id = ?
                  )";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (($result['days_since_message'] ?? 7) > 7 && ($result['message_count'] ?? 0) < 5) {
            return [
                'found' => true,
                'pattern_name' => 'Communication Breakdown',
                'severity' => 'warning',
                'details' => [
                    'days_since_message' => $result['days_since_message'],
                    'message_count' => $result['message_count']
                ],
                'message' => 'Low communication frequency detected (' . $result['message_count'] . ' messages, last ' . $result['days_since_message'] . ' days ago)'
            ];
        }

        return ['found' => false];
    }

    /**
     * Record detected pattern
     */
    private function recordPattern($idea_id, $pattern) {
        $query = "SELECT id FROM antipattern_rules WHERE pattern_name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $pattern['pattern_name']);
        $stmt->execute();
        $rule = $stmt->get_result()->fetch_assoc();

        if (!$rule) {
            return; // Pattern rule not found
        }

        $pattern_details = json_encode($pattern['details']);
        $insert_query = "INSERT INTO detected_antipatterns (idea_id, pattern_id, severity, pattern_details)
                         VALUES (?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE pattern_details = VALUES(pattern_details), detected_at = NOW()";

        $insert_stmt = $this->conn->prepare($insert_query);
        $insert_stmt->bind_param("iiss", $idea_id, $rule['id'], $pattern['severity'], $pattern_details);
        $insert_stmt->execute();

        // Also create alert
        $alert_query = "INSERT INTO pattern_alerts (idea_id, pattern_name, alert_message, severity, suggested_actions)
                        VALUES (?, ?, ?, ?, ?)";

        $alert_stmt = $this->conn->prepare($alert_query);
        $suggestions = json_encode(['schedule_check_in', 'reassign_tasks', 'provide_support']);
        $alert_stmt->bind_param("issss", $idea_id, $pattern['pattern_name'], $pattern['message'], $pattern['severity'], $suggestions);
        $alert_stmt->execute();
    }

    /**
     * Get detected patterns for idea
     */
    public function getDetectedPatterns($idea_id) {
        $query = "SELECT da.*, ar.mitigation_strategy
                  FROM detected_antipatterns da
                  JOIN antipattern_rules ar ON da.pattern_id = ar.id
                  WHERE da.idea_id = ? AND da.acknowledged = FALSE
                  ORDER BY da.severity DESC, da.detected_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Acknowledge pattern (mark as seen)
     */
    public function acknowledgePattern($pattern_id) {
        $query = "UPDATE detected_antipatterns SET acknowledged = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $pattern_id);
        return $stmt->execute();
    }

    /**
     * Mark pattern as resolved
     */
    public function resolvePattern($pattern_id, $action_taken) {
        $query = "UPDATE detected_antipatterns SET resolved_at = NOW(), action_taken = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $action_taken, $pattern_id);
        return $stmt->execute();
    }

    /**
     * Record collaboration metrics
     */
    public function recordCollaborationMetrics($idea_id, $metrics) {
        $query = "INSERT INTO collaboration_metrics
                  (idea_id, metric_date, total_collaborators, active_collaborators,
                   inactive_count, communication_frequency, task_completion_rate)
                  VALUES (?, NOW(), ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiidd",
            $idea_id,
            $metrics['total_collaborators'],
            $metrics['active_collaborators'],
            $metrics['inactive_count'],
            $metrics['communication_frequency'],
            $metrics['task_completion_rate']
        );

        return $stmt->execute();
    }
}
?>
