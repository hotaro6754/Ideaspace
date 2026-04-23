<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ProjectAgent.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

if ($action === 'get_suggestions') {
    $idea_id = (int)$_GET['idea_id'];
    $type = $_GET['type'] ?? 'researcher';
    $agent = new ProjectAgent($db, $type);

    echo json_encode([
        'success' => true,
        'persona' => $agent->getPersona(),
        'suggestions' => $agent->getSuggestions($idea_id),
        'health' => $agent->analyzeIdeaHealth($idea_id)
    ]);
    exit();
}
?>
