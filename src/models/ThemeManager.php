<?php

/**
 * ThemeManager.php - Role-Specific Design System
 * Manages UI themes, layouts, and feature visibility per agent type
 */

class ThemeManager {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get theme for an agent type
     */
    public function getThemeForAgent($agent_type) {
        $query = "SELECT * FROM ui_themes WHERE agent_type = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $agent_type);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get enabled features for an agent type
     */
    public function getEnabledFeatures($agent_type) {
        $query = "SELECT * FROM role_specific_features
                  WHERE agent_type = ? AND is_enabled = TRUE
                  ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $agent_type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get dashboard layout for user agent
     */
    public function getDashboardLayout($user_agent_id) {
        $query = "SELECT * FROM agent_dashboard_layout
                  WHERE user_agent_id = ? AND is_visible = TRUE
                  ORDER BY position";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Set dashboard layout
     */
    public function setDashboardLayout($user_agent_id, $card_type, $position, $width, $is_visible) {
        $query = "INSERT INTO agent_dashboard_layout
                  (user_agent_id, card_type, position, width, is_visible)
                  VALUES (?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                  position = VALUES(position),
                  width = VALUES(width),
                  is_visible = VALUES(is_visible)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isisi", $user_agent_id, $card_type, $position, $width, $is_visible);
        return $stmt->execute();
    }

    /**
     * Get navigation menu customization
     */
    public function getNavigation($user_agent_id) {
        $query = "SELECT * FROM navigation_customization
                  WHERE user_agent_id = ? AND is_visible = TRUE
                  ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_agent_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get quick actions for agent type
     */
    public function getQuickActions($agent_type) {
        $query = "SELECT * FROM quick_actions_config
                  WHERE agent_type = ? AND is_enabled = TRUE
                  ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $agent_type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get role-specific template
     */
    public function getTemplate($agent_type, $template_type) {
        $query = "SELECT template_content, template_variables FROM role_specific_templates
                  WHERE agent_type = ? AND template_type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $agent_type, $template_type);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get view preferences for user
     */
    public function getViewPreferences($user_id) {
        $query = "SELECT * FROM view_preferences WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update view preferences
     */
    public function setViewPreferences($user_id, $preferences) {
        $query = "INSERT INTO view_preferences
                  (user_id, default_view, items_per_page, sort_by, sort_order, filter_presets)
                  VALUES (?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                  default_view = VALUES(default_view),
                  items_per_page = VALUES(items_per_page),
                  sort_by = VALUES(sort_by),
                  sort_order = VALUES(sort_order),
                  filter_presets = VALUES(filter_presets)";

        $stmt = $this->conn->prepare($query);
        $filter_presets_json = json_encode($preferences['filter_presets'] ?? []);

        $stmt->bind_param("isiss",
            $user_id,
            $preferences['default_view'],
            $preferences['items_per_page'],
            $preferences['sort_by'],
            $preferences['sort_order'],
            $filter_presets_json
        );

        return $stmt->execute();
    }

    /**
     * Get full theme configuration for user agent
     */
    public function getFullThemeConfig($user_id, $agent_type) {
        $theme = $this->getThemeForAgent($agent_type);
        $features = $this->getEnabledFeatures($agent_type);
        $quick_actions = $this->getQuickActions($agent_type);
        $view_prefs = $this->getViewPreferences($user_id);

        return [
            'theme' => $theme,
            'features' => $features,
            'quick_actions' => $quick_actions,
            'view_preferences' => $view_prefs,
            'css_variables' => $this->generateCSSVariables($theme)
        ];
    }

    /**
     * Generate CSS variables from theme
     */
    private function generateCSSVariables($theme) {
        if (!$theme) {
            return [];
        }

        return [
            '--agent-primary' => $theme['primary_color'],
            '--agent-secondary' => $theme['secondary_color'],
            '--agent-accent' => $theme['accent_color'],
            '--animation-enabled' => ($theme['animation_enabled'] ? 'all' : 'none'),
            '--spacing-scale' => $theme['spacing_scale']
        ];
    }

    /**
     * Get agent-specific dashboard config
     */
    public function getAgentDashboardConfig($user_agent_id) {
        // Get user agent info
        $query = "SELECT ua.*, at.name, u.id as user_id
                  FROM user_agents ua
                  JOIN agent_types at ON ua.agent_type_id = at.id
                  JOIN users u ON ua.user_id = u.id
                  WHERE ua.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_agent_id);
        $stmt->execute();
        $agent = $stmt->get_result()->fetch_assoc();

        if (!$agent) {
            return null;
        }

        $layout = $this->getDashboardLayout($user_agent_id);
        $theme = $this->getThemeForAgent($agent['name']);
        $quick_actions = $this->getQuickActions($agent['name']);
        $navigation = $this->getNavigation($user_agent_id);

        return [
            'agent' => $agent,
            'layout' => $layout,
            'theme' => $theme,
            'quick_actions' => $quick_actions,
            'navigation' => $navigation,
            'css_variables' => $this->generateCSSVariables($theme)
        ];
    }

    /**
     * Customize feature visibility for agent type
     */
    public function setFeatureEnabled($agent_type, $feature_name, $is_enabled) {
        $query = "UPDATE role_specific_features
                  SET is_enabled = ?
                  WHERE agent_type = ? AND feature_name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $is_enabled, $agent_type, $feature_name);
        return $stmt->execute();
    }

    /**
     * Create custom dashboard layout from template
     */
    public function applyDashboardTemplate($user_agent_id, $agent_type) {
        // Get default layout for agent type
        $default_layouts = [
            'student_researcher' => [
                ['card_type' => 'mentor_finder', 'position' => 1, 'width' => 'half'],
                ['card_type' => 'goals', 'position' => 2, 'width' => 'half'],
                ['card_type' => 'metrics', 'position' => 3, 'width' => 'full'],
                ['card_type' => 'recommendations', 'position' => 4, 'width' => 'full'],
                ['card_type' => 'timeline', 'position' => 5, 'width' => 'full']
            ],
            'faculty_advisor' => [
                ['card_type' => 'students', 'position' => 1, 'width' => 'half'],
                ['card_type' => 'mentorship_goals', 'position' => 2, 'width' => 'half'],
                ['card_type' => 'validation_queue', 'position' => 3, 'width' => 'full'],
                ['card_type' => 'metrics', 'position' => 4, 'width' => 'full'],
                ['card_type' => 'achievements', 'position' => 5, 'width' => 'full']
            ],
            'project_lead' => [
                ['card_type' => 'team_board', 'position' => 1, 'width' => 'full'],
                ['card_type' => 'milestone_tracker', 'position' => 2, 'width' => 'half'],
                ['card_type' => 'blockers', 'position' => 3, 'width' => 'half'],
                ['card_type' => 'wave_progress', 'position' => 4, 'width' => 'full'],
                ['card_type' => 'metrics', 'position' => 5, 'width' => 'full']
            ],
            'peer_reviewer' => [
                ['card_type' => 'review_queue', 'position' => 1, 'width' => 'full'],
                ['card_type' => 'quality_dashboard', 'position' => 2, 'width' => 'half'],
                ['card_type' => 'metrics', 'position' => 3, 'width' => 'half'],
                ['card_type' => 'feedback_templates', 'position' => 4, 'width' => 'full'],
                ['card_type' => 'achievements', 'position' => 5, 'width' => 'full']
            ],
            'community_member' => [
                ['card_type' => 'trending_ideas', 'position' => 1, 'width' => 'full'],
                ['card_type' => 'learning_opportunities', 'position' => 2, 'width' => 'half'],
                ['card_type' => 'my_contributions', 'position' => 3, 'width' => 'half'],
                ['card_type' => 'community_events', 'position' => 4, 'width' => 'full'],
                ['card_type' => 'connections', 'position' => 5, 'width' => 'full']
            ]
        ];

        $layouts = $default_layouts[$agent_type] ?? $default_layouts['community_member'];

        foreach ($layouts as $layout) {
            $this->setDashboardLayout($user_agent_id, $layout['card_type'], $layout['position'], $layout['width'], true);
        }

        return ['success' => true];
    }
}
?>
