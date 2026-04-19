<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'rsvp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Auth required');
    $event_id = (int)$_POST['event_id'];

    $stmt = $db->prepare("INSERT OR REPLACE INTO event_rsvps (event_id, user_id, status) VALUES (?, ?, 'attending')");
    $stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
    $stmt->execute();

    header("Location: " . BASE_URL . "/?page=dashboard");
    exit();
}
?>
