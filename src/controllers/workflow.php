<?php

/**
 * WorkflowController.php
 * Manages idea progression through workflow phases
 */

require_once __DIR__ . '/../models/Workflow.php';

class WorkflowController {
    private $conn;
    private $workflow;

    public function __construct($db) {
        $this->conn = $db;
        $this->workflow = new Workflow($db);
    }

    /**
     * Get idea workflow status
     */
    public function getWorkflowStatus($idea_id) {
        $phase = $this->workflow->getCurrentPhase($idea_id);
        $requirements = $this->workflow->getPhaseRequirements($phase['current_phase']);
        $readiness = $this->workflow->checkPhaseReadiness($idea_id, $phase['current_phase']);
        $history = $this->workflow->getPhaseHistory($idea_id);

        return [
            'success' => true,
            'current_phase' => $phase,
            'phase_requirements' => $requirements,
            'readiness' => $readiness,
            'phase_history' => $history
        ];
    }

    /**
     * Discuss Phase: Create idea charter
     */
    public function discussPhase($idea_id, $data) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Create charter
        $charter = $this->workflow->createCharter(
            $idea_id,
            $data['problem_statement'] ?? '',
            $data['proposed_solution'] ?? '',
            explode(',', $data['success_criteria'] ?? ''),
            (int)($data['team_size'] ?? 3),
            $data['effort_estimate'] ?? 'medium'
        );

        if ($charter['success']) {
            // Save charter as phase document
            $content = "## Problem Statement\n" . $data['problem_statement'] . "\n\n";
            $content .= "## Proposed Solution\n" . $data['proposed_solution'] . "\n\n";
            $content .= "## Success Criteria\n" . $data['success_criteria'];

            $this->workflow->createPhaseDocument($idea_id, 'discuss', 'charter', $content, $user_id);

            return ['success' => true, 'phase' => 'discuss'];
        }

        return $charter;
    }

    /**
     * Plan Phase: Create project brief and roadmap
     */
    public function planPhase($idea_id, $data) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Create brief
        $brief = $this->workflow->createBrief(
            $idea_id,
            $data['description'] ?? '',
            explode(',', $data['objectives'] ?? ''),
            $data['scope'] ?? '',
            $data['constraints'] ?? '',
            $data['assumptions'] ?? '',
            explode(',', $data['dependencies'] ?? ''),
            $data['risk_assessment'] ?? ''
        );

        if ($brief['success']) {
            // Create roadmap
            $roadmap_phases = json_decode($data['roadmap'] ?? '[]', true);
            $roadmap = $this->workflow->createRoadmap($idea_id, $roadmap_phases);

            return ['success' => true, 'brief' => $brief, 'roadmap' => $roadmap, 'phase' => 'plan'];
        }

        return $brief;
    }

    /**
     * Execute Phase: Manage wave tasks
     */
    public function executePhase($idea_id, $data) {
        // Get roadmap
        $query = "SELECT id FROM project_roadmaps WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $roadmap = $stmt->get_result()->fetch_assoc();

        if (!$roadmap) {
            return ['success' => false, 'error' => 'No roadmap found'];
        }

        // Add wave tasks
        $wave_number = (int)($data['wave_number'] ?? 1);
        $tasks = json_decode($data['tasks'] ?? '[]', true);

        $result = $this->workflow->addWaveTasks($roadmap['id'], $wave_number, $tasks);

        if ($result['success']) {
            // Get wave progress
            $progress = $this->workflow->getWaveProgress($roadmap['id']);
            return ['success' => true, 'wave' => $wave_number, 'progress' => $progress];
        }

        return $result;
    }

    /**
     * Update task progress
     */
    public function updateTaskProgress($task_id, $status, $actual_hours = null) {
        if ($this->workflow->updateTaskStatus($task_id, $status, $actual_hours)) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update task'];
    }

    /**
     * Verify Phase: Submit verification report
     */
    public function verifyPhase($idea_id, $data) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Create verification document
        $report_content = $data['verification_report'] ?? '';
        $doc = $this->workflow->createPhaseDocument($idea_id, 'verify', 'verification_report', $report_content, $user_id);

        if ($doc['success']) {
            // Check readiness for ship phase
            $readiness = $this->workflow->checkPhaseReadiness($idea_id, 'verify');
            return ['success' => true, 'document' => $doc, 'readiness' => $readiness, 'phase' => 'verify'];
        }

        return $doc;
    }

    /**
     * Ship Phase: Mark project as complete
     */
    public function shipPhase($idea_id, $data) {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Create final documentation
        $content = "## Final Deliverables\n" . ($data['deliverables'] ?? '') . "\n\n";
        $content .= "## Lessons Learned\n" . ($data['lessons'] ?? '') . "\n\n";
        $content .= "## Next Steps\n" . ($data['next_steps'] ?? '');

        $doc = $this->workflow->createPhaseDocument($idea_id, 'ship', 'final_documentation', $content, $user_id);

        if ($doc['success']) {
            // Update idea status to completed
            $query = "UPDATE ideas SET status = 'completed' WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $idea_id);
            $stmt->execute();

            return ['success' => true, 'phase' => 'ship', 'idea_status' => 'completed'];
        }

        return $doc;
    }

    /**
     * Transition to next phase
     */
    public function advancePhase($idea_id, $reason = '') {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Check current phase readiness
        $current = $this->workflow->getCurrentPhase($idea_id);
        $readiness = $this->workflow->checkPhaseReadiness($idea_id, $current['current_phase']);

        if (!$readiness['ready']) {
            return [
                'success' => false,
                'error' => 'Phase requirements not met',
                'missing' => $readiness['missing_requirements']
            ];
        }

        // Determine next phase
        $next_phase_map = [
            'discuss' => 'plan',
            'plan' => 'execute',
            'execute' => 'verify',
            'verify' => 'ship'
        ];

        $next_phase = $next_phase_map[$current['current_phase']] ?? null;

        if (!$next_phase) {
            return ['success' => false, 'error' => 'Already at final phase'];
        }

        // Transition
        return $this->workflow->transitionPhase($idea_id, $next_phase, $user_id, $reason);
    }

    /**
     * Get detailed roadmap with progress
     */
    public function getDetailedRoadmap($idea_id) {
        // Get roadmap
        $query = "SELECT * FROM project_roadmaps WHERE idea_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idea_id);
        $stmt->execute();
        $roadmap = $stmt->get_result()->fetch_assoc();

        if (!$roadmap) {
            return ['success' => false, 'error' => 'No roadmap found'];
        }

        // Get wave progress
        $progress = $this->workflow->getWaveProgress($roadmap['id']);

        // Get all tasks
        $all_tasks = $this->workflow->getWaveTasks($roadmap['id']);

        return [
            'success' => true,
            'roadmap' => json_decode($roadmap['roadmap_json'], true),
            'wave_progress' => $progress,
            'all_tasks' => $all_tasks
        ];
    }
}
?>
