<?php
/**
 * Email Service
 * Simulated for high-performance demos, but interface-ready for production
 */

class EmailService {
    public static function sendVerificationEmail($email, $name, $link) {
        // Log locally for demo verification
        $log_entry = date('Y-m-d H:i:s') . " | DEMO EMAIL [Verification] | TO: $email | NAME: $name | LINK: $link\n";
        file_put_contents(dirname(__DIR__, 2) . '/logs/emails.log', $log_entry, FILE_APPEND);

        // In demo mode, we auto-verify for convenience unless requested otherwise
        // getConnection()->query("UPDATE users SET email_verified = 1 WHERE email = '" . $email . "'");

        return true;
    }

    public static function sendPasswordResetEmail($email, $name, $link) {
        $log_entry = date('Y-m-d H:i:s') . " | DEMO EMAIL [Reset] | TO: $email | NAME: $name | LINK: $link\n";
        file_put_contents(dirname(__DIR__, 2) . '/logs/emails.log', $log_entry, FILE_APPEND);
        return true;
    }
}
?>
