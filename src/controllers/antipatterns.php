<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AntiPatternDetection.php';

$db = getConnection();
$detector = new AntiPatternDetection($db);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

if ($action === 'scan') {
    $idea_id = (int)$_GET['idea_id'];
    $patterns = $detector->scanIdea($idea_id);
    echo json_encode(['success' => true, 'patterns' => $patterns]);
    exit();
}

if ($action === 'acknowledge') {
    $pattern_id = (int)$_POST['id'];
    if ($detector->acknowledgePattern($pattern_id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
?>
