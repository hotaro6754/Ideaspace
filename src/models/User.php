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

    /**
     * Register a new user
     */
    public function register($roll_number, $name, $email, $password, $branch, $year) {
        // Validate roll number format (example: LID001)
        if (!$this->validateRollNumber($roll_number)) {
            return ['success' => false, 'error' => 'Invalid roll number format'];
        }

        // Check if roll number already exists
        if ($this->rollNumberExists($roll_number)) {
            return ['success' => false, 'error' => 'Roll number already registered'];
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert into database
        $query = "INSERT INTO users (roll_number, name, email, password_hash, branch, year, user_type)
                  VALUES (?, ?, ?, ?, ?, ?, 'builder')";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error: ' . $this->conn->error];
        }

        $stmt->bind_param("ssisii", $roll_number, $name, $email, $password_hash, $branch, $year);

        if ($stmt->execute()) {
            return ['success' => true, 'user_id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'error' => 'Registration failed: ' . $stmt->error];
        }
    }

    /**
     * Login user
     */
    public function login($identifier, $password) {
        // Identifier can be roll_number or email
        $query = "SELECT id, roll_number, name, email, password_hash, user_type, profile_pic
                  FROM users
                  WHERE roll_number = ? OR email = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'roll_number' => $user['roll_number'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'user_type' => $user['user_type'],
                        'profile_pic' => $user['profile_pic']
                    ]
                ];
            } else {
                return ['success' => false, 'error' => 'Invalid password'];
            }
        } else {
            return ['success' => false, 'error' => 'User not found'];
        }
    }

    /**
     * Validate roll number format
     */
    private function validateRollNumber($roll_number) {
        // Lendi college format: LID + numbers (e.g., LID001)
        return preg_match('/^LID\d{3,}$/', $roll_number);
    }

    /**
     * Check if roll number exists
     */
    private function rollNumberExists($roll_number) {
        $query = "SELECT id FROM users WHERE roll_number = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Check if email exists
     */
    private function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        $query = "SELECT id, roll_number, name, email, branch, year, github_username, user_type, profile_pic, created_at
                  FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
