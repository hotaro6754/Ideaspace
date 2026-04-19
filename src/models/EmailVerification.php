<?php
class EmailVerification {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function create($user_id) { return ['success' => true, 'token' => 'demo-token']; }
    public function verify($token) { return ['success' => true]; }
    public function resendToken($user_id) { return ['success' => true, 'token' => 'demo-token']; }
}
?>
