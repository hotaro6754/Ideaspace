<?php
/**
 * EmailService - Send professional emails for IdeaSync
 */

class EmailService {
    private $from = 'noreply@ideasync.lendi.edu.in';
    private $site_name = 'IdeaSync';
    private $base_url = 'http://localhost:8000';

    public function sendWelcomeEmail($email, $name, $roll_number) {
        $subject = "Welcome to IdeaSync - Campus Collaboration Platform";
        $message = $this->getWelcomeTemplate($name, $roll_number);
        return $this->send($email, $subject, $message);
    }

    public function sendCollaborationRequest($recipient_email, $recipient_name, $idea_title, $applicant_name, $applicant_id) {
        $subject = "New Collaboration Request: {$idea_title}";
        $message = $this->getCollaborationRequestTemplate($recipient_name, $idea_title, $applicant_name);
        return $this->send($recipient_email, $subject, $message);
    }

    public function sendCollaborationAccepted($recipient_email, $recipient_name, $idea_title, $acceptor_name) {
        $subject = "Collaboration Accepted: {$idea_title}";
        $message = $this->getCollaborationAcceptedTemplate($recipient_name, $idea_title, $acceptor_name);
        return $this->send($recipient_email, $subject, $message);
    }

    public function sendCollaborationRejected($recipient_email, $recipient_name, $idea_title) {
        $subject = "Collaboration Update: {$idea_title}";
        $message = $this->getCollaborationRejectedTemplate($recipient_name, $idea_title);
        return $this->send($recipient_email, $subject, $message);
    }

    public function sendPasswordReset($email, $name, $reset_token) {
        $reset_link = "{$this->base_url}/?page=reset-password&token={$reset_token}";
        $subject = "Reset Your {$this->site_name} Password";
        $message = $this->getPasswordResetTemplate($name, $reset_link);
        return $this->send($email, $subject, $message);
    }

    public function sendEmailVerification($email, $name, $verification_token) {
        $verify_link = "{$this->base_url}/?page=verify-email&token={$verification_token}";
        $subject = "Verify Your {$this->site_name} Email";
        $message = $this->getEmailVerificationTemplate($name, $verify_link);
        return $this->send($email, $subject, $message);
    }

    public function sendNotification($email, $name, $notification_type, $data) {
        switch ($notification_type) {
            case 'upvote':
                $subject = "Your idea got upvoted!";
                $message = $this->getUpvoteTemplate($name, $data['idea_title']);
                break;
            case 'completion':
                $subject = "Project Completed: {$data['idea_title']}";
                $message = $this->getCompletionTemplate($name, $data['idea_title']);
                break;
            default:
                return false;
        }
        return $this->send($email, $subject, $message);
    }

    private function send($to, $subject, $html_message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->site_name} <{$this->from}>\r\n";
        $headers .= "Reply-To: {$this->from}\r\n";

        return mail($to, $subject, $html_message, $headers);
    }

    private function getWelcomeTemplate($name, $roll_number) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #3B82F6;'>Welcome to IdeaSync, {$name}!</h2>
                <p>Your account has been created successfully.</p>
                <p><strong>Roll Number:</strong> {$roll_number}</p>
                <p>You can now:</p>
                <ul>
                    <li>Post your innovative ideas</li>
                    <li>Find skilled collaborators</li>
                    <li>Build your builder rank</li>
                    <li>Connect with the campus community</li>
                </ul>
                <p><a href='{$this->base_url}/?page=dashboard' style='display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px;'>Go to Dashboard</a></p>
                <hr style='border: none; border-top: 1px solid #E2E8F0; margin: 30px 0;'>
                <p style='color: #94A3B8; font-size: 12px;'>© IdeaSync - Campus Collaboration Platform</p>
            </div>
        </body>
        </html>";
    }

    private function getCollaborationRequestTemplate($recipient_name, $idea_title, $applicant_name) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #3B82F6;'>New Collaboration Request!</h2>
                <p>Hi {$recipient_name},</p>
                <p>{$applicant_name} wants to collaborate on your idea: <strong>{$idea_title}</strong></p>
                <p><a href='{$this->base_url}/?page=dashboard' style='display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px;'>View Request</a></p>
            </div>
        </body>
        </html>";
    }

    private function getCollaborationAcceptedTemplate($recipient_name, $idea_title, $acceptor_name) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #10B981;'>Collaboration Accepted!</h2>
                <p>Hi {$recipient_name},</p>
                <p>Your collaboration request for <strong>{$idea_title}</strong> has been accepted by {$acceptor_name}!</p>
                <p>You can now start working together on the project.</p>
                <p><a href='{$this->base_url}/?page=profile&section=collaborations' style='display: inline-block; padding: 10px 20px; background: #10B981; color: white; text-decoration: none; border-radius: 8px;'>View Collaboration</a></p>
            </div>
        </body>
        </html>";
    }

    private function getCollaborationRejectedTemplate($recipient_name, $idea_title) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #64748B;'>Collaboration Update</h2>
                <p>Hi {$recipient_name},</p>
                <p>Unfortunately, your collaboration request for <strong>{$idea_title}</strong> was not accepted at this time.</p>
                <p>Don't worry! There are many other great ideas to collaborate on. Keep building!</p>
                <p><a href='{$this->base_url}/?page=ideas' style='display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px;'>Explore More Ideas</a></p>
            </div>
        </body>
        </html>";
    }

    private function getPasswordResetTemplate($name, $reset_link) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2>Reset Your Password</h2>
                <p>Hi {$name},</p>
                <p>Click the button below to reset your password. This link expires in 1 hour.</p>
                <p><a href='{$reset_link}' style='display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px;'>Reset Password</a></p>
                <p style='color: #94A3B8; font-size: 12px;'>If you didn't request this, please ignore this email.</p>
            </div>
        </body>
        </html>";
    }

    private function getEmailVerificationTemplate($name, $verify_link) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2>Verify Your Email</h2>
                <p>Hi {$name},</p>
                <p>Thank you for signing up! Click the button below to verify your email address.</p>
                <p><a href='{$verify_link}' style='display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 8px;'>Verify Email</a></p>
            </div>
        </body>
        </html>";
    }

    private function getUpvoteTemplate($name, $idea_title) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #F59E0B;'>Your Idea Got Upvoted! 👍</h2>
                <p>Hi {$name},</p>
                <p>Someone upvoted your idea: <strong>{$idea_title}</strong></p>
                <p>Great work! Keep building amazing projects.</p>
            </div>
        </body>
        </html>";
    }

    private function getCompletionTemplate($name, $idea_title) {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Arial; line-height: 1.6; color: #1E293B;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #10B981;'>Project Completed! 🎉</h2>
                <p>Hi {$name},</p>
                <p>Your idea <strong>{$idea_title}</strong> has been marked as completed!</p>
                <p>Congratulations on finishing the project. You've earned +50 builder points!</p>
            </div>
        </body>
        </html>";
    }
}
?>
