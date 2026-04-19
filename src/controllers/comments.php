<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'upvote' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Auth required');
    $idea_id = (int)$_POST['idea_id'];

    // Check if already upvoted
    $stmt = $db->prepare("SELECT id FROM upvotes WHERE idea_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $idea_id, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
        exit();
    }

    $stmt = $db->prepare("INSERT INTO upvotes (idea_id, user_id) VALUES (?, ?)");

    $up_text = $_SESSION["name"] . " upvoted your track.";
    $idea_res = $db->query("SELECT user_id FROM ideas WHERE id = $idea_id");
    $idea_owner = $idea_res->fetch_assoc()["user_id"];
    $n_stmt = $db->prepare("INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id, message) VALUES (?, ?, 'upvote', ?, ?)");
    $n_stmt->bind_param("iiis", $idea_owner, $_SESSION["user_id"], $idea_id, $up_text);
    $n_stmt->execute();
    $stmt->bind_param("ii", $idea_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $db->query("UPDATE ideas SET upvotes = upvotes + 1 WHERE id = $idea_id");
    }
    header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
    exit();
}
?>
