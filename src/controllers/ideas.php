<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Security.php';

$db = getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');

    $title = trim($_POST['title'] ?? '');
    $domain = trim($_POST['domain'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Process skills_raw into array
    $skills_raw = $_POST['skills_raw'] ?? '';
    $skills = array_map('trim', explode(',', $skills_raw));
    $skills = array_filter($skills); // Remove empty values

    if (empty($title) || empty($description)) {
        $_SESSION['error'] = "Title and Description are required";
        header("Location: " . BASE_URL . "/?page=ideas&action=create");
        exit();
    }

    $stmt = $db->prepare("INSERT INTO ideas (user_id, title, domain, description, skills_needed) VALUES (?, ?, ?, ?, ?)");
    $skills_json = json_encode($skills);
    $stmt->bind_param("issss", $_SESSION['user_id'], $title, $domain, $description, $skills_json);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Innovation Track created successfully!";
        header("Location: " . BASE_URL . "/?page=ideas");
    } else {
        $_SESSION['error'] = "Failed to create track: " . $db->error;
        header("Location: " . BASE_URL . "/?page=ideas&action=create");
    }
    exit();
}
?>
