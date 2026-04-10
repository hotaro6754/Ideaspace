<?php
/**
 * PasswordReset Model - Handle password reset workflow
 * File: /src/models/PasswordReset.php
 */

class PasswordReset {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create password reset token
     */
    public function createToken($email) {
        // Check user exists
        $user_query = "SELECT id FROM users WHERE email = ?";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->bind_param("s", $email);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();

        if (!$user) {
            // For security, don't reveal if email exists
            return ['success' => true, 'message' => 'If account exists, reset link will be sent'];
        }

        $user_id = $user['id'];

        // Delete previous tokens
        $delete_query = "DELETE FROM password_resets WHERE user_id = ? AND expires_at > NOW()";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        // Create new token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + (2 * 60 * 60)); // 2 hours

        $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => true, 'message' => 'If account exists, reset link will be sent'];
        }

        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $stmt->execute();

        return ['success' => true, 'token' => $token, 'user_id' => $user_id, 'message' => 'Reset link created'];
    }

    /**
     * Validate reset token
     */
    public function validateToken($token) {
        $query = "SELECT user_id, expires_at FROM password_resets WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return ['success' => false, 'error' => 'Invalid or expired token'];
        }

        if (strtotime($result['expires_at']) < time()) {
            return ['success' => false, 'error' => 'Token has expired'];
        }

        return ['success' => true, 'user_id' => $result['user_id']];
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $new_password) {
        if (strlen($new_password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }

        // Validate token
        $validation = $this->validateToken($token);
        if (!$validation['success']) {
            return $validation;
        }

        $user_id = $validation['user_id'];

        // Hash password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Update user password
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $this->conn->prepare($update_query);
        if (!$update_stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if (!$update_stmt->execute()) {
            return ['success' => false, 'error' => 'Failed to reset password'];
        }

        // Log auth event
        $log_query = "INSERT INTO auth_logs (user_id, action, ip_address, user_agent, success) VALUES (?, 'password_reset', ?, ?, TRUE)";
        $log_stmt = $this->conn->prepare($log_query);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $log_stmt->bind_param("iss", $user_id, $ip, $user_agent);
        $log_stmt->execute();

        // Delete all reset tokens for this user
        $delete_query = "DELETE FROM password_resets WHERE user_id = ?";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        // Invalidate all sessions
        $session_query = "UPDATE sessions SET is_active = FALSE WHERE user_id = ?";
        $session_stmt = $this->conn->prepare($session_query);
        $session_stmt->bind_param("i", $user_id);
        $session_stmt->execute();

        return ['success' => true, 'message' => 'Password reset successfully'];
    }

    /**
     * Get pending reset token for user
     */
    public function getPendingToken($user_id) {
        $query = "SELECT token, expires_at FROM password_resets WHERE user_id = ? AND expires_at > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Delete expired tokens
     */
    public function deleteExpired() {
        $query = "DELETE FROM password_resets WHERE expires_at < NOW()";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return ['success' => true, 'deleted' => $stmt->affected_rows];
        }
        return ['success' => false, 'error' => 'Failed to delete expired tokens'];
    }
}
?>
