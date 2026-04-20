<?php
class PasswordReset {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function createToken($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $email, $token, $expires);

        if ($stmt->execute()) {
            return ['success' => true, 'token' => $token];
        }
        return ['success' => false, 'error' => $this->conn->error];
    }

    public function resetPassword($token, $password) {
        $query = "SELECT email FROM password_resets WHERE token = ? AND expires_at > CURRENT_TIMESTAMP";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            $email = $res['email'];
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->conn->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hash, $email]);
            $this->conn->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Invalid or expired token'];
    }
}
