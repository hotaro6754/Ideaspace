<?php
/**
 * User Model
 * Handles user-related database operations
 */

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($roll_number, $name, $email, $password, $branch, $year) {
        if (!$this->validateRollNumber($roll_number)) {
            return ['success' => false, 'error' => 'Invalid roll number format. Use LIDxxx'];
        }
        if ($this->rollNumberExists($roll_number)) {
            return ['success' => false, 'error' => 'Roll number already registered'];
        }
        if ($this->emailExists($email)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Auto-verifying for DEMO
        $query = "INSERT INTO users (roll_number, name, email, password, branch, year, user_type, email_verified)
                  VALUES (?, ?, ?, ?, ?, ?, 'builder', 1)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return ['success' => false, 'error' => 'DB Error: ' . $this->conn->error];

        $stmt->bind_param("sssssi", $roll_number, $name, $email, $password_hash, $branch, $year);

        if ($stmt->execute()) {
            return ['success' => true, 'user_id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'error' => 'Registration failed: ' . $this->conn->error];
        }
    }

    public function login($identifier, $password) {
        $query = "SELECT * FROM users WHERE roll_number = ? OR email = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'error' => 'Invalid credentials'];
    }

    private function validateRollNumber($roll_number) {
        return preg_match('/^LID\d+$/i', $roll_number);
    }

    private function rollNumberExists($roll_number) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE roll_number = ?");
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
