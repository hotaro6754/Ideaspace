<?php

/**
 * /api/agents.php - Agent Management API
 * Handles agent assignment and role selection
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
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
$agent = new Agent($conn);

try {
    switch ($action) {
        case 'assign':
            // Assign agent type to user
            $agent_type = $_POST['agent_type'] ?? '';

            if (empty($agent_type)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Agent type required']);
                exit;
            }

            $result = $agent->getOrCreateAgent($user_id, $agent_type);

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'agent_id' => $result['agent_id'] ?? null,
                    'message' => 'Agent assigned successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
            break;

        case 'get_current':
            // Get current user's agent
            $user_agent = $agent->getByUserId($user_id);

            if ($user_agent) {
                echo json_encode(['success' => true, 'agent' => $user_agent]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'No agent assigned']);
            }
            break;

        case 'add_goal':
            // Add a goal to user's agent
            $user_agent = $agent->getByUserId($user_id);

            if (!$user_agent) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No agent assigned']);
                exit;
            }

            $goal_type = $_POST['goal_type'] ?? '';
            $description = $_POST['description'] ?? '';
            $target_metric = $_POST['target_metric'] ?? '';
            $target_value = (int)($_POST['target_value'] ?? 0);

            $result = $agent->addGoal(
                $user_agent['id'],
                $goal_type,
                $description,
                $target_metric,
                $target_value
            );

            echo json_encode($result);
            break;

        case 'record_metric':
            // Record a metric for user's agent
            $user_agent = $agent->getByUserId($user_id);

            if (!$user_agent) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No agent assigned']);
                exit;
            }

            $metric_name = $_POST['metric_name'] ?? '';
            $metric_value = (int)($_POST['metric_value'] ?? 0);
            $metric_type = $_POST['metric_type'] ?? 'count';

            $result = $agent->recordMetric($user_agent['id'], $metric_name, $metric_value, $metric_type);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to record metric']);
            }
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
