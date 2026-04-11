<?php
/**
 * role-dashboard.php - Role-Specific Dynamic Dashboard
 * Renders different layouts and content based on agent type
 */

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    redirect(BASE_URL . '/auth/login');
}

require_once __DIR__ . '/../models/ThemeManager.php';
require_once __DIR__ . '/../models/Agent.php';

$agentModel = new Agent($conn);
$themeManager = new ThemeManager($conn);

$user_agent = $agentModel->getByUserId($user_id);
if (!$user_agent) {
    redirect(BASE_URL . '/agents/onboarding');
}

$theme_config = $themeManager->getAgentDashboardConfig($user_agent['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user_agent['display_name']; ?> Dashboard - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            <?php foreach ($theme_config['css_variables'] as $var => $value): ?>
            <?php echo $var; ?>: <?php echo $value; ?>;
            <?php endforeach; ?>
        }

        * {
            --agent-color: var(--agent-primary);
        }

        body {
            background: var(--bg-primary);
        }

        .role-header {
            background: linear-gradient(135deg, var(--agent-primary) 0%, var(--agent-secondary) 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .role-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .role-icon {
            font-size: 2.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .role-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border-left: 4px solid var(--agent-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        }

        .role-card h3 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1.125rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .quick-action-btn {
            background: var(--agent-color);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .quick-action-btn:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .metric-box {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--agent-color);
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
            text-transform: uppercase;
        }

        .section-divider {
            border-top: 2px solid var(--agent-color);
            opacity: 0.1;
            margin: 2rem 0;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 1rem;
            display: block;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="container" style="padding: 2rem;">
        <!-- Role Header -->
        <div class="role-header">
            <div class="role-title">
                <div class="role-icon">
                    <i class="<?php
                        $icons = [
                            'student_researcher' => 'fas fa-flask',
                            'faculty_advisor' => 'fas fa-chalkboard-user',
                            'project_lead' => 'fas fa-tasks',
                            'peer_reviewer' => 'fas fa-check-circle',
                            'community_member' => 'fas fa-users'
                        ];
                        echo $icons[$user_agent['name']] ?? 'fas fa-user';
                    ?>"></i>
                </div>
                <div>
                    <h1 style="margin: 0; font-size: 1.75rem;"><?php echo $user_agent['display_name']; ?> Dashboard</h1>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Welcome back! Here's your personalized workspace.</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if (!empty($theme_config['quick_actions'])): ?>
        <div class="role-card">
            <h3>Quick Actions</h3>
            <div class="quick-actions">
                <?php foreach ($theme_config['quick_actions'] as $action): ?>
                <a href="<?php echo $action['action_url']; ?>" class="quick-action-btn" title="<?php echo $action['action_name']; ?>">
                    <i class="<?php echo $action['icon']; ?>"></i>
                    <span><?php echo $action['action_name']; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dynamic Layout Based on Agent Type -->
        <div class="section-divider"></div>

        <div class="dashboard-grid">
            <!-- Agent-Specific Content Sections -->
            <?php
            switch ($user_agent['name']) {
                case 'student_researcher':
                    include __DIR__ . '/agent-specific/student-dashboard.php';
                    break;
                case 'faculty_advisor':
                    include __DIR__ . '/agent-specific/faculty-dashboard.php';
                    break;
                case 'project_lead':
                    include __DIR__ . '/agent-specific/lead-dashboard.php';
                    break;
                case 'peer_reviewer':
                    include __DIR__ . '/agent-specific/reviewer-dashboard.php';
                    break;
                default:
                    include __DIR__ . '/agent-specific/community-dashboard.php';
            }
            ?>
        </div>

        <!-- Theme Customization Info -->
        <div style="margin-top: 2rem; padding: 1rem; background: var(--bg-light); border-radius: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
            <i class="fas fa-info-circle"></i> Your dashboard is customized for your role as a <?php echo strtolower($user_agent['display_name']); ?>.
            <a href="<?php echo BASE_URL; ?>/preferences" style="color: var(--agent-color); text-decoration: none; font-weight: 600;">Customize your view →</a>
        </div>
    </div>

    <script>
        // Apply theme CSS variables
        document.documentElement.style.setProperty('--agent-primary', '<?php echo $theme_config['theme']['primary_color']; ?>');
        document.documentElement.style.setProperty('--agent-secondary', '<?php echo $theme_config['theme']['secondary_color']; ?>');
        document.documentElement.style.setProperty('--agent-accent', '<?php echo $theme_config['theme']['accent_color']; ?>');
    </script>
</body>
</html>
