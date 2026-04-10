<?php
/**
 * EmailVerification Model - Handle email verification workflow
 * File: /src/models/EmailVerification.php
 */

class EmailVerification {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create verification token
     */
    public function create($user_id) {
        // Check if user exists and not already verified
        $user_query = "SELECT email_verified FROM users WHERE id = ?";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        if ($user['email_verified']) {
            return ['success' => false, 'error' => 'Email already verified'];
        }

        // Delete existing token
        $delete_query = "DELETE FROM email_verifications WHERE user_id = ?";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        // Create new token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + (24 * 60 * 60)); // 24 hours

        $query = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("iss", $user_id, $token, $expires_at);

        if ($stmt->execute()) {
            return ['success' => true, 'token' => $token];
        }

        return ['success' => false, 'error' => 'Failed to create verification token'];
    }

    /**
     * Verify email with token
     */
    public function verify($token) {
        // Check token exists and not expired
        $query = "SELECT user_id, expires_at FROM email_verifications WHERE token = ?";
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

        $user_id = $result['user_id'];

        // Update user
        $update_query = "UPDATE users SET email_verified = TRUE, email_verified_at = NOW() WHERE id = ?";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bind_param("i", $user_id);

        if (!$update_stmt->execute()) {
            return ['success' => false, 'error' => 'Failed to verify email'];
        }

        // Log auth event
        $log_query = "INSERT INTO auth_logs (user_id, action, ip_address, user_agent, success) VALUES (?, 'email_verified', ?, ?, TRUE)";
        $log_stmt = $this->conn->prepare($log_query);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $log_stmt->bind_param("iss", $user_id, $ip, $user_agent);
        $log_stmt->execute();

        // Delete token
        $delete_query = "DELETE FROM email_verifications WHERE user_id = ?";
        $delete_stmt = $this->conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();

        return ['success' => true, 'user_id' => $user_id];
    }

    /**
     * Check if user email is verified
     */
    public function isVerified($user_id) {
        $query = "SELECT email_verified FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result && $result['email_verified'] ? true : false;
    }

    /**
     * Resend verification email
     */
    public function resendToken($user_id) {
        // Check user hasn't verified yet
        if ($this->isVerified($user_id)) {
            return ['success' => false, 'error' => 'Email already verified'];
        }

        // Check if recently sent
        $check_query = "SELECT created_at FROM email_verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();

        if ($result && strtotime($result['created_at']) > time() - 300) { // 5 minutes
            return ['success' => false, 'error' => 'Please wait before requesting another email'];
        }

        return $this->create($user_id);
    }
}
?>
