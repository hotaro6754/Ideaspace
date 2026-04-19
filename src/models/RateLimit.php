<?php
class RateLimit {
    public function isLimited($id, $key, $max, $window) { return false; }
    public function recordAttempt($id, $key, $window) { return true; }
    public function reset($id, $key) { return true; }
}
?>
