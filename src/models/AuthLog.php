<?php
class AuthLog {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function log($user_id, $event, $success, $meta = []) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
        $stmt = $this->conn->prepare("INSERT INTO auth_logs (user_id, event, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $event, $ip, $ua);
        $stmt->execute();
    }
}
?>
