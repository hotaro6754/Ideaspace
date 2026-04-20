<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventRsvp.php';
require_once __DIR__ . '/../helpers/Security.php';

if (!defined('BASE_URL')) {
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    define('BASE_URL', $protocol . '://' . $host);
}

$db = getConnection();
$eventModel = new Event($db);
$rsvpModel = new EventRsvp($db);
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Auth required');
    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) exit('Invalid token');

    $res = $eventModel->create(
        $_SESSION['user_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['start_time'],
        $_POST['end_time'],
        $_POST['event_type'],
        $_POST['location'] ?? null
    );

    if ($res['success']) {
        $_SESSION['message'] = "Event created successfully!";
        header("Location: " . BASE_URL . "/?page=events");
    } else {
        $_SESSION['error'] = $res['error'];
        header("Location: " . BASE_URL . "/?page=events");
    }
    exit();
}

if ($action === 'rsvp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) exit('Auth required');
    $event_id = (int)$_POST['event_id'];
    $status = $_POST['status'] ?? 'attending';

    $res = $rsvpModel->rsvp($event_id, $_SESSION['user_id'], $status);

    if ($res['success']) {
        $_SESSION['message'] = "RSVP confirmed!";
    } else {
        $_SESSION['error'] = $res['error'];
    }
    header("Location: " . BASE_URL . "/?page=events");
    exit();
}
?>
