<?php
/**
 * Auth Controller - Enhanced
 */
require_once __DIR__ . '/../config/Database.php';

session_start();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$conn = getConnection();

if ($action === 'register') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $roll = $_POST['roll_number'];
    $branch = $_POST['branch'];
    $year = (int)$_POST['year'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, roll_number, branch, year, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $name, $email, $roll, $branch, $year, $pass);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name'] = $name;
        header('Location: ' . BASE_URL . '/?page=feed');
    } else {
        $_SESSION['error'] = "Registration failed. Email or Roll Number might already exist.";
        header('Location: ' . BASE_URL . '/?page=register');
    }
    exit();
}

if ($action === 'login') {
    $identifier = $_POST['identifier'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email = ? OR roll_number = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header('Location: ' . BASE_URL . '/?page=feed');
    } else {
        $_SESSION['error'] = "Invalid credentials.";
        header('Location: ' . BASE_URL . '/?page=login');
    }
    exit();
}

if ($action === 'logout') {
    session_destroy();
    header('Location: ' . BASE_URL . '/');
    exit();
}
