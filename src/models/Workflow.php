<?php

/**
 * Workflow.php - Specification-Driven Workflow Management
 * Implements Discuss → Plan → Execute → Verify → Ship phases
 */

class Workflow {
    private $conn;
    private $table = 'workflow_phases';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create workflow for an idea
     */
    public function initializeWorkflow($idea_id) {
        // Create workflow phase entry
        $query = "INSERT INTO workflow_phases (idea_id, current_phase) VALUES (?, 'discuss')
                  ON DUPLICATE KEY UPDATE id = id";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        return $stmt->execute();
    }

    /**
     * Get current phase for an idea
     */
    public function getCurrentPhase($idea_id) {
        $query = "SELECT * FROM workflow_phases WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create idea charter (Discuss phase)
     */
    public function createCharter($idea_id, $problem_statement, $proposed_solution, $success_criteria, $team_size, $effort) {
        $query = "INSERT INTO idea_charters
                  (idea_id, problem_statement, proposed_solution, success_criteria, team_size_estimate, effort_estimate)
                  VALUES (?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                  problem_statement = VALUES(problem_statement),
                  proposed_solution = VALUES(proposed_solution),
                  success_criteria = VALUES(success_criteria),
                  updated_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $success_json = json_encode($success_criteria);
        $stmt->bind_param("isssi", $idea_id, $problem_statement, $proposed_solution, $success_json, $team_size, $effort);

        if ($stmt->execute()) {
            return ['success' => true, 'charter_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to create charter'];
    }

    /**
     * Create project brief (Plan phase)
     */
    public function createBrief($idea_id, $description, $objectives, $scope, $constraints, $assumptions, $dependencies, $risk_assessment) {
        $query = "INSERT INTO project_briefs
                  (idea_id, description, objectives, scope, constraints, assumptions, dependencies, risk_assessment)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                  description = VALUES(description),
                  objectives = VALUES(objectives),
                  scope = VALUES(scope),
                  updated_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $objectives_json = json_encode($objectives);
        $dependencies_json = json_encode($dependencies);

        $stmt->bind_param("isssssss",
            $idea_id,
            $description,
            $objectives_json,
            $scope,
            $constraints,
            $assumptions,
            $dependencies_json,
            $risk_assessment
        );

        if ($stmt->execute()) {
            return ['success' => true, 'brief_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to create brief'];
    }

    /**
     * Create project roadmap (Plan → Execute)
     */
    public function createRoadmap($idea_id, $roadmap_phases) {
        $query = "INSERT INTO project_roadmaps (idea_id, roadmap_json) VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE
                  roadmap_json = VALUES(roadmap_json),
                  updated_at = NOW()";

        $stmt = $this->conn->prepare($query);
        $roadmap_json = json_encode($roadmap_phases);
        $stmt->bind_param("is", $idea_id, $roadmap_json);

        if ($stmt->execute()) {
            // Get the roadmap ID
            $query2 = "SELECT id FROM project_roadmaps WHERE idea_id = ?";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bind_param("i", $idea_id);
            $stmt2->execute();
            $result = $stmt2->get_result()->fetch_assoc();

            return ['success' => true, 'roadmap_id' => $result['id']];
        }
        return ['success' => false, 'error' => 'Failed to create roadmap'];
    }

    /**
     * Add wave tasks (Execute phase)
     */
    public function addWaveTasks($roadmap_id, $wave_number, $tasks) {
        $success = true;
        foreach ($tasks as $task) {
            $query = "INSERT INTO wave_tasks
                      (project_roadmap_id, wave_number, task_title, description, assigned_to, priority, estimated_hours, dependencies)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $dependencies_json = json_encode($task['dependencies'] ?? []);

            $stmt->bind_param("iississs",
                $roadmap_id,
                $wave_number,
                $task['title'],
                $task['description'] ?? '',
                $task['assigned_to'] ?? null,
                $task['priority'] ?? 'medium',
                $task['estimated_hours'] ?? 0,
                $dependencies_json
            );

            if (!$stmt->execute()) {
                $success = false;
            }
        }

        return ['success' => $success];
    }

    /**
     * Update task status
     */
    public function updateTaskStatus($task_id, $status, $actual_hours = null) {
        $query = "UPDATE wave_tasks
                  SET status = ?, actual_hours = COALESCE(?, actual_hours)";

        if ($status === 'completed') {
            $query .= ", completed_at = NOW()";
        }

        $query .= " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $status, $actual_hours, $task_id);

        return $stmt->execute();
    }

    /**
     * Transition idea to next phase
     */
    public function transitionPhase($idea_id, $to_phase, $transitioned_by, $reason = '') {
        // Get current phase
        $current = $this->getCurrentPhase($idea_id);
        if (!$current) {
            return ['success' => false, 'error' => 'Workflow not initialized'];
        }

        // Validate phase transition
        $valid_transitions = [
            'discuss' => ['plan'],
            'plan' => ['execute'],
            'execute' => ['verify'],
            'verify' => ['ship'],
            'ship' => []
        ];

        if (!in_array($to_phase, $valid_transitions[$current['current_phase']] ?? [])) {
            return ['success' => false, 'error' => "Cannot transition from {$current['current_phase']} to {$to_phase}"];
        }

        // Create transition record
        $query = "INSERT INTO workflow_transitions (idea_id, from_phase, to_phase, transitioned_by, reason)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issss", $idea_id, $current['current_phase'], $to_phase, $transitioned_by, $reason);
        $stmt->execute();

        // Update current phase
        $query2 = "UPDATE workflow_phases SET current_phase = ?, entered_phase_at = NOW() WHERE idea_id = ?";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bind_param("si", $to_phase, $idea_id);

        if ($stmt2->execute()) {
            return ['success' => true, 'new_phase' => $to_phase];
        }

        return ['success' => false, 'error' => 'Failed to transition phase'];
    }

    /**
     * Get phase requirements checklist
     */
    public function getPhaseRequirements($phase) {
        $query = "SELECT * FROM phase_requirements WHERE phase = ? ORDER BY is_mandatory DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $phase);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if phase requirements are met
     */
    public function checkPhaseReadiness($idea_id, $phase) {
        $requirements = $this->getPhaseRequirements($phase);
        $missing = [];

        foreach ($requirements as $req) {
            if ($req['is_mandatory']) {
                // Check if requirement is fulfilled
                $fulfilled = $this->isRequirementFulfilled($idea_id, $phase, $req['requirement_name']);
                if (!$fulfilled) {
                    $missing[] = $req['requirement_name'];
                }
            }
        }

        return [
            'ready' => count($missing) === 0,
            'missing_requirements' => $missing,
            'progress' => (count($requirements) - count($missing)) . '/' . count($requirements)
        ];
    }

    /**
     * Helper: Check if requirement is fulfilled
     */
    private function isRequirementFulfilled($idea_id, $phase, $requirement_name) {
        $query = "SELECT COUNT(*) as count FROM phase_documents
                  WHERE idea_id = ? AND phase = ? AND document_type = ?";
        $stmt = $this->conn->prepare($query);
        $doc_type = strtolower(str_replace(' ', '_', $requirement_name));
        $stmt->bind_param("iss", $idea_id, $phase, $doc_type);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }

    /**
     * Get wave tasks for execution phase
     */
    public function getWaveTasks($project_roadmap_id, $wave_number = null) {
        $query = "SELECT * FROM wave_tasks WHERE project_roadmap_id = ?";

        if ($wave_number !== null) {
            $query .= " AND wave_number = ?";
        }

        $query .= " ORDER BY wave_number, priority DESC, created_at";

        $stmt = $this->conn->prepare($query);

        if ($wave_number !== null) {
            $stmt->bind_param("ii", $project_roadmap_id, $wave_number);
        } else {
            $stmt->bind_param("i", $project_roadmap_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get wave progress
     */
    public function getWaveProgress($project_roadmap_id) {
        $query = "SELECT
                    wave_number,
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                    SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked_tasks,
                    SUM(estimated_hours) as estimated_hours,
                    SUM(actual_hours) as actual_hours
                  FROM wave_tasks
                  WHERE project_roadmap_id = ?
                  GROUP BY wave_number
                  ORDER BY wave_number";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_roadmap_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create phase document
     */
    public function createPhaseDocument($idea_id, $phase, $document_type, $content, $author_id) {
        $query = "INSERT INTO phase_documents
                  (idea_id, phase, document_type, document_content, author_id, document_version)
                  VALUES (?, ?, ?, ?, ?, 1)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssi", $idea_id, $phase, $document_type, $content, $author_id);

        if ($stmt->execute()) {
            return ['success' => true, 'document_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to create document'];
    }

    /**
     * Get phase history
     */
    public function getPhaseHistory($idea_id) {
        $query = "SELECT wt.*, u.name, u.profile_pic
                  FROM workflow_transitions wt
                  LEFT JOIN users u ON wt.transitioned_by = u.id
                  WHERE wt.idea_id = ?
                  ORDER BY wt.transition_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
