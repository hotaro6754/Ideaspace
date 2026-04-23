<?php
/**
 * GSDWorkflow Model - Implements Discuss -> Plan -> Execute -> Verify -> Ship
 */

class GSDWorkflow {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createCharter($idea_id, $data) {
        $check = $this->conn->prepare("SELECT id FROM idea_charters WHERE idea_id = ?");
        $check->bind_param("i", $idea_id);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ($exists) {
            $stmt = $this->conn->prepare("UPDATE idea_charters SET vision=?, mission=?, success_criteria=?, scope_limitations=?, updated_at=CURRENT_TIMESTAMP WHERE idea_id=?");
            $stmt->bind_param("ssssi", $data['vision'], $data['mission'], $data['success_criteria'], $data['scope_limitations'], $idea_id);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO idea_charters (idea_id, vision, mission, success_criteria, scope_limitations) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $idea_id, $data['vision'], $data['mission'], $data['success_criteria'], $data['scope_limitations']);
        }

        return $stmt->execute();
    }

    public function getCharter($idea_id) {
        $stmt = $this->conn->prepare("SELECT * FROM idea_charters WHERE idea_id = ?");
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function passQualityGate($idea_id, $phase, $user_id, $comments = '') {
        $stmt = $this->conn->prepare("INSERT INTO quality_gates (idea_id, phase_name, is_passed, passed_at, passed_by, reviewer_comments)
                                     VALUES (?, ?, 1, CURRENT_TIMESTAMP, ?, ?)");
        $stmt->bind_param("isis", $idea_id, $phase, $user_id, $comments);
        return $stmt->execute();
    }

    public function getProgress($idea_id) {
        $stmt = $this->conn->prepare("SELECT phase_name FROM quality_gates WHERE idea_id = ? AND is_passed = 1");
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $passed = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $passed_phases = array_column($passed, 'phase_name');

        $phases = ['Discuss', 'Plan', 'Execute', 'Verify', 'Ship'];
        $current = 'Discuss';
        foreach ($phases as $p) {
            if (in_array($p, $passed_phases)) {
                $current = $p;
            } else {
                break;
            }
        }
        return [
            'passed' => $passed_phases,
            'current' => $current,
            'next' => $phases[array_search($current, $phases) + 1] ?? 'Complete'
        ];
    }
}
?>
