<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Unauthorized');

    $recipient_id = (int)$_POST['recipient_id'];
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) exit('Empty message');

    $stmt = $db->prepare("INSERT INTO messages (sender_user_id, recipient_user_id, message) VALUES (?, ?, ?)");

    $msg_text = "New message from " . $_SESSION["name"];
    $n_stmt = $db->prepare("INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, message) VALUES (?, ?, 'message', ?)");
    $n_stmt->bind_param("iis", $recipient_id, $_SESSION["user_id"], $msg_text);
    $n_stmt->execute();
    $stmt->bind_param("iis", $_SESSION['user_id'], $recipient_id, $message);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/?page=messages&to=" . $recipient_id);
    } else {
        exit('Failed to send');
    }
    exit();
}
?>
