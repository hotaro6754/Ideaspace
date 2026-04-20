<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/IdeaComment.php';
require_once __DIR__ . '/../helpers/Security.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$db = getConnection();
$commentModel = new IdeaComment($db);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Auth required');

    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        exit('Invalid token');
    }

    $idea_id = (int)$_POST['idea_id'];
    $content = trim($_POST['content']);
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    $res = $commentModel->create($idea_id, $_SESSION['user_id'], $content, $parent_id);

    if ($res['success']) {
        header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
    }
    exit();
}

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
    $stmt->bind_param("ii", $idea_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $db->query("UPDATE ideas SET upvotes = upvotes + 1 WHERE id = $idea_id");

        $up_text = $_SESSION["name"] . " upvoted your track.";
        $idea_res = $db->query("SELECT user_id FROM ideas WHERE id = $idea_id");
        $idea_owner = $idea_res->fetch_assoc()["user_id"];

        $n_stmt = $db->prepare("INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id, message) VALUES (?, ?, 'upvote', ?, ?)");
        $n_stmt->bind_param("iiis", $idea_owner, $_SESSION["user_id"], $idea_id, $up_text);
        $n_stmt->execute();
    }
    header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
    exit();
}
?>
