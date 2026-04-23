<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/GSDWorkflow.php';
require_once __DIR__ . '/../helpers/Security.php';

$db = getConnection();
$gsd = new GSDWorkflow($db);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit();
}

if ($action === 'save_charter') {
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'error' => 'Invalid token']);
        exit();
    }

    $idea_id = (int)$_POST['idea_id'];
    $data = [
        'vision' => $_POST['vision'],
        'mission' => $_POST['mission'],
        'success_criteria' => $_POST['success_criteria'],
        'scope_limitations' => $_POST['scope_limitations']
    ];

    if ($gsd->createCharter($idea_id, $data)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save charter']);
    }
    exit();
}

if ($action === 'save_brief') {
    $idea_id = (int)$_POST['idea_id'];
    $req = $_POST['requirements'];
    $stack = $_POST['stack'];
    $risks = $_POST['risks'];

    // Update or Insert Brief
    $check = $db->prepare("SELECT id FROM project_briefs WHERE idea_id = ?");
    $check->bind_param("i", $idea_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $stmt = $db->prepare("UPDATE project_briefs SET detailed_requirements=?, technical_stack=?, risk_assessment=?, updated_at=CURRENT_TIMESTAMP WHERE idea_id=?");
        $stmt->bind_param("sssi", $req, $stack, $risks, $idea_id);
    } else {
        $stmt = $db->prepare("INSERT INTO project_briefs (idea_id, detailed_requirements, technical_stack, risk_assessment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $idea_id, $req, $stack, $risks);
    }

    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'error' => $db->error]);
    exit();
}

if ($action === 'log_decision') {
    $idea_id = (int)$_POST['idea_id'];
    $title = $_POST['title'];
    $context = $_POST['context'];
    $user_id = $_SESSION['user_id'];

    $stmt = $db->prepare("INSERT INTO decision_logs (idea_id, user_id, decision_title, decision_context) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $idea_id, $user_id, $title, $context);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/?page=decision-log&id=" . $idea_id);
    } else {
        exit('Failed to log');
    }
    exit();
}

if ($action === 'pass_gate') {
    $idea_id = (int)$_POST['idea_id'];
    $phase = $_POST['phase'];
    $comments = $_POST['comments'] ?? '';

    if ($gsd->passQualityGate($idea_id, $phase, $_SESSION['user_id'], $comments)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to pass gate']);
    }
    exit();
}

if ($action === 'get_progress') {
    $idea_id = (int)$_GET['idea_id'];
    echo json_encode(['success' => true, 'progress' => $gsd->getProgress($idea_id)]);
    exit();
}
?>
