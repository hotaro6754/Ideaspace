<?php
class EmailVerification {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($user_id) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $query = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $user_id, $token, $expires);

        if ($stmt->execute()) {
            return ['success' => true, 'token' => $token];
        }
        return ['success' => false, 'error' => $this->conn->error];
    }

    public function verify($token) {
        $query = "SELECT user_id FROM email_verifications WHERE token = ? AND expires_at > CURRENT_TIMESTAMP";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            $user_id = $res['user_id'];
            $this->conn->prepare("UPDATE users SET email_verified = 1, email_verified_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$user_id]);
            $this->conn->prepare("DELETE FROM email_verifications WHERE token = ?")->execute([$token]);
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Invalid or expired token'];
    }
}
