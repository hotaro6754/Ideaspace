<?php
class PasswordReset {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function createToken($email) { return ['success' => true, 'token' => 'demo-reset']; }
    public function resetPassword($token, $password) { return ['success' => true]; }
}
?>
