<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'apply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');

    $idea_id = (int)$_POST['idea_id'];
    $message = trim($_POST['message'] ?? '');

    $stmt = $db->prepare("INSERT INTO applications (idea_id, user_id, message) VALUES (?, ?, ?)");

    $app_text = $_SESSION["name"] . " applied to your track: " . $idea_id;
    $idea_res = $db->query("SELECT user_id FROM ideas WHERE id = $idea_id");
    $idea_owner = $idea_res->fetch_assoc()["user_id"];
    $n_stmt = $db->prepare("INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, related_idea_id, message) VALUES (?, ?, 'application', ?, ?)");
    $n_stmt->bind_param("iiis", $idea_owner, $_SESSION["user_id"], $idea_id, $app_text);
    $n_stmt->execute();
    $stmt->bind_param("iis", $idea_id, $_SESSION['user_id'], $message);

    if ($stmt->execute()) {
        $db->query("UPDATE ideas SET applicant_count = applicant_count + 1 WHERE id = $idea_id");
        $_SESSION['message'] = "Collaboration request sent!";
    } else {
        $_SESSION['error'] = "You have already applied to this track.";
    }
    header("Location: " . BASE_URL . "/?page=idea-detail&id=" . $idea_id);
    exit();
}
?>

if ($action === 'respond' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');

    $app_id = (int)$_POST['app_id'];
    $status = $_POST['status']; // accepted or rejected

    $stmt = $db->prepare("UPDATE applications SET status = ?, responded_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("si", $status, $app_id);

    if ($stmt->execute()) {
        // Create notification for applicant
        $app_res = $db->query("SELECT user_id, idea_id FROM applications WHERE id = $app_id");
        $app_data = $app_res->fetch_assoc();
        $applicant_id = $app_data['user_id'];
        $idea_id = $app_data['idea_id'];

        $msg = ($status === 'accepted') ? "Your application was ACCEPTED!" : "Your application was declined.";
        $n_stmt = $db->prepare("INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, message) VALUES (?, ?, 'acceptance', ?)");
        $n_stmt->bind_param("iis", $applicant_id, $_SESSION['user_id'], $msg);
        $n_stmt->execute();

        if ($status === 'accepted') {
            // Add to collaborations table
            $c_stmt = $db->prepare("INSERT INTO collaborations (idea_id, leader_id, collaborator_id, status) VALUES (?, ?, ?, 'active')");
            $c_stmt->bind_param("iii", $idea_id, $_SESSION['user_id'], $applicant_id);
            $c_stmt->execute();
            $db->query("UPDATE ideas SET total_collaborators = total_collaborators + 1 WHERE id = $idea_id");
        }
    }
    header("Location: " . BASE_URL . "/?page=profile-applications");
    exit();
}
