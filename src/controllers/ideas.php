<?php
/**
 * Ideas Controller - Enhanced
 */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Idea.php';
require_once __DIR__ . '/../services/PointsService.php';

session_start();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$conn = getConnection();
$ideaModel = new Idea($conn);

if ($action === 'create') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/?page=login');
        exit();
    }

    $title = $_POST['title'];
    $desc = $_POST['description'];
    $domain = $_POST['domain'];
    $skills = json_decode($_POST['skills_needed'], true);
    $user_id = $_SESSION['user_id'];

    $result = $ideaModel->create($user_id, $title, $desc, $domain, $skills);

    if ($result['success']) {
        // Award points
        PointsService::awardPoints($user_id, 'post_idea', $result['idea_id'], 'idea');
        header('Location: ' . BASE_URL . '/?page=idea-detail&id=' . $result['idea_id']);
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ' . BASE_URL . '/?page=ideas&action=create');
    }
    exit();
}

// Redirect if no action
header('Location: ' . BASE_URL . '/?page=feed');
