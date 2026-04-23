<?php
/**
 * AntiPatternDetection Model - Detects unhealthy collaboration patterns
 */

class AntiPatternDetection {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Scan an idea for all known anti-patterns
     */
    public function scanIdea($idea_id) {
        $results = [];

        $patterns = [
            'detectSilentPartner',
            'detectScopeCreep',
            'detectUnclearOwnership',
            'detectDeadlineDrift'
        ];

        foreach ($patterns as $method) {
            $found = $this->$method($idea_id);
            if ($found['found']) {
                $results[] = $found;
                $this->recordPattern($idea_id, $found);
            }
        }

        return $results;
    }

    /**
     * Detect Silent Partner
     * No activity from a team member in 7 days
     */
    private function detectSilentPartner($idea_id) {
        // Look for users in collaborations who haven't logged activity recently
        $query = "SELECT u.name, MAX(al.created_at) as last_act
                  FROM collaborations c
                  JOIN users u ON c.collaborator_id = u.id
                  LEFT JOIN activity_logs al ON u.id = al.user_id
                  WHERE c.idea_id = ? AND c.status = 'active'
                  GROUP BY u.id
                  HAVING last_act IS NULL OR last_act < DATETIME('now', '-7 days')";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            return [
                'found' => true,
                'pattern_name' => 'Silent Partner',
                'severity' => 'warning',
                'message' => "Team member " . $res['name'] . " has been inactive for over 7 days."
            ];
        }
        return ['found' => false];
    }

    /**
     * Detect Scope Creep
     * Too many requirements added after planning
     */
    private function detectScopeCreep($idea_id) {
        $query = "SELECT LENGTH(detailed_requirements) as req_len FROM project_briefs WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && $res['req_len'] > 5000) {
            return [
                'found' => true,
                'pattern_name' => 'Scope Creep',
                'severity' => 'warning',
                'message' => "Project requirements are becoming excessively complex (potential creep)."
            ];
        }
        return ['found' => false];
    }

    /**
     * Detect Unclear Ownership
     * Tasks without owners
     */
    private function detectUnclearOwnership($idea_id) {
        $query = "SELECT COUNT(*) as count FROM project_tasks WHERE idea_id = ? AND assigned_to IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && $res['count'] > 3) {
            return [
                'found' => true,
                'pattern_name' => 'Unclear Ownership',
                'severity' => 'critical',
                'message' => $res['count'] . " active tasks have no assigned owners."
            ];
        }
        return ['found' => false];
    }

    /**
     * Detect Deadline Drift
     */
    private function detectDeadlineDrift($idea_id) {
        $query = "SELECT COUNT(*) as count FROM project_tasks WHERE idea_id = ? AND due_date < CURRENT_TIMESTAMP AND status != 'done'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && $res['count'] > 0) {
            return [
                'found' => true,
                'pattern_name' => 'Deadline Drift',
                'severity' => 'critical',
                'message' => $res['count'] . " tasks are past their due dates."
            ];
        }
        return ['found' => false];
    }

    private function recordPattern($idea_id, $pattern) {
        $query = "INSERT OR IGNORE INTO detected_antipatterns (idea_id, pattern_id, severity, pattern_details)
                  VALUES (?, (SELECT id FROM antipattern_rules WHERE pattern_name = ?), ?, ?)";
        $stmt = $this->conn->prepare($query);
        $details = json_encode($pattern);
        $stmt->bind_param("isss", $idea_id, $pattern['pattern_name'], $pattern['severity'], $details);
        $stmt->execute();
    }

    public function acknowledgePattern($id) {
        $stmt = $this->conn->prepare("UPDATE detected_antipatterns SET acknowledged = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
