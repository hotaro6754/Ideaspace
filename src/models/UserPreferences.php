<?php
/**
 * UserPreferences Model - User settings and notification preferences
 * File: /src/models/UserPreferences.php
 */

class UserPreferences {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get or create user preferences
     */
    public function getOrCreate($user_id) {
        $query = "SELECT * FROM user_preferences WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return $result;
        }

        // Create default preferences
        $insert_query = "INSERT INTO user_preferences (user_id) VALUES (?)";
        $insert_stmt = $this->conn->prepare($insert_query);
        $insert_stmt->bind_param("i", $user_id);
        $insert_stmt->execute();

        return $this->getOrCreate($user_id);
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications($user_id, $notifications) {
        $allowed_fields = [
            'email_notifications',
            'email_on_application',
            'email_on_acceptance',
            'email_on_message',
            'email_on_upvote',
            'email_on_comment'
        ];

        $updates = [];
        $params = [];
        $param_types = "";

        foreach ($allowed_fields as $field) {
            if (isset($notifications[$field])) {
                $updates[] = "$field = ?";
                $params[] = (bool)$notifications[$field];
                $param_types .= "i";
            }
        }

        if (empty($updates)) {
            return ['success' => false, 'error' => 'No valid fields to update'];
        }

        $params[] = $user_id;
        $param_types .= "i";

        $query = "UPDATE user_preferences SET " . implode(", ", $updates) . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param($param_types, ...$params);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update preferences'];
    }

    /**
     * Update profile visibility preferences
     */
    public function updatePrivacy($user_id, $profile_visibility, $ideas_visibility) {
        if (!in_array($profile_visibility, ['public', 'private'])) {
            return ['success' => false, 'error' => 'Invalid profile visibility'];
        }
        if (!in_array($ideas_visibility, ['public', 'private'])) {
            return ['success' => false, 'error' => 'Invalid ideas visibility'];
        }

        $query = "UPDATE user_preferences SET profile_visibility = ?, ideas_visibility = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $profile_visibility, $ideas_visibility, $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update privacy settings'];
    }

    /**
     * Update theme preference
     */
    public function updateTheme($user_id, $theme) {
        if (!in_array($theme, ['light', 'dark'])) {
            return ['success' => false, 'error' => 'Invalid theme'];
        }

        $query = "UPDATE user_preferences SET theme = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $theme, $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update theme'];
    }

    /**
     * Update language preference
     */
    public function updateLanguage($user_id, $language) {
        if (strlen($language) > 10) {
            return ['success' => false, 'error' => 'Invalid language code'];
        }

        $query = "UPDATE user_preferences SET language = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $language, $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update language'];
    }
}
?>
