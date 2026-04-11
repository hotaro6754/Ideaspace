<?php

/**
 * /api/workflow.php - Workflow Phase Management API
 * Handles workflow submissions, phase advances, and task updates
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Workflow.php';
require_once __DIR__ . '/../models/Agent.php';

$db = new Database();
$conn = $db->connect();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$action = $_POST['action'] ?? '';
$idea_id = (int)($_POST['idea_id'] ?? 0);

if ($idea_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid idea ID']);
    exit;
}

$workflow = new Workflow($conn);

try {
    switch ($action) {
        case 'save_charter':
            // Save idea charter (Discuss phase)
            $result = $workflow->createCharter(
                $idea_id,
                $_POST['problem_statement'] ?? '',
                $_POST['proposed_solution'] ?? '',
                explode(',', $_POST['success_criteria'] ?? ''),
                (int)($_POST['team_size'] ?? 3),
                $_POST['effort_estimate'] ?? 'medium'
            );
            echo json_encode($result);
            break;

        case 'save_brief':
            // Save project brief (Plan phase)
            $result = $workflow->createBrief(
                $idea_id,
                $_POST['description'] ?? '',
                explode(',', $_POST['objectives'] ?? ''),
                $_POST['scope'] ?? '',
                $_POST['constraints'] ?? '',
                $_POST['assumptions'] ?? '',
                explode(',', $_POST['dependencies'] ?? ''),
                $_POST['risk_assessment'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'add_wave_tasks':
            // Add wave tasks (Execute phase)
            $tasks = json_decode($_POST['tasks'] ?? '[]', true);
            if (!is_array($tasks)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid tasks JSON']);
                exit;
            }

            // Get roadmap
            $query = "SELECT id FROM project_roadmaps WHERE idea_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $idea_id);
            $stmt->execute();
            $roadmap = $stmt->get_result()->fetch_assoc();

            if (!$roadmap) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No roadmap found. Complete Plan phase first.']);
                exit;
            }

            $wave_number = (int)($_POST['wave_number'] ?? 1);
            $result = $workflow->addWaveTasks($roadmap['id'], $wave_number, $tasks);

            if ($result['success']) {
                $progress = $workflow->getWaveProgress($roadmap['id']);
                echo json_encode(['success' => true, 'wave' => $wave_number, 'progress' => $progress]);
            } else {
                echo json_encode($result);
            }
            break;

        case 'update_task_status':
            // Update task progress
            $task_id = (int)($_POST['task_id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';
            $actual_hours = (int)($_POST['actual_hours'] ?? 0);

            if ($workflow->updateTaskStatus($task_id, $status, $actual_hours)) {
                echo json_encode(['success' => true, 'status' => $status]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update task']);
            }
            break;

        case 'advance':
            // Advance to next phase
            $reason = $_POST['reason'] ?? '';

            // Check readiness
            $current = $workflow->getCurrentPhase($idea_id);
            if (!$current) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Workflow not initialized']);
                exit;
            }

            $readiness = $workflow->checkPhaseReadiness($idea_id, $current['current_phase']);
            if (!$readiness['ready']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Phase requirements not met',
                    'missing' => $readiness['missing_requirements']
                ]);
                exit;
            }

            // Advance
            $next_phase_map = [
                'discuss' => 'plan',
                'plan' => 'execute',
                'execute' => 'verify',
                'verify' => 'ship'
            ];

            $next_phase = $next_phase_map[$current['current_phase']] ?? null;
            if (!$next_phase) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Already at final phase']);
                exit;
            }

            $result = $workflow->transitionPhase($idea_id, $next_phase, $user_id, $reason);
            echo json_encode($result);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
