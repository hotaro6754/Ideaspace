<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';

$db = getConnection();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Get Unread Notification Count
$notif_res = $db->query("SELECT COUNT(*) as count FROM notifications WHERE recipient_user_id = $user_id AND is_read = 0");
$notif_count = $notif_res->fetch_assoc()['count'] ?? 0;

// 2. Get Unread Message Count
$msg_res = $db->query("SELECT COUNT(*) as count FROM messages WHERE recipient_user_id = $user_id AND is_read = 0");
$msg_count = $msg_res->fetch_assoc()['count'] ?? 0;

// 3. Get Latest Messages for active chat (if specified)
$active_messages = [];
$to_id = (int)($_GET['to'] ?? 0);
$last_id = (int)($_GET['last_id'] ?? 0);

if ($to_id > 0 && $last_id > 0) {
    $stmt = $db->prepare("SELECT * FROM messages
                           WHERE ((sender_user_id = ? AND recipient_user_id = ?)
                              OR (sender_user_id = ? AND recipient_user_id = ?))
                             AND id > ?
                           ORDER BY created_at ASC");
    $stmt->bind_param("iiiii", $user_id, $to_id, $to_id, $user_id, $last_id);
    $stmt->execute();
    $active_messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

echo json_encode([
    'success' => true,
    'unread_notifications' => $notif_count,
    'unread_messages' => $msg_count,
    'new_messages' => $active_messages
]);
?>
