<?php
/**
 * Agent Dashboard View
 * Professional dashboard displaying agent profile, goals, metrics, and recommendations
 */

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    redirect(BASE_URL . '/auth/login');
}

require_once __DIR__ . '/../controllers/agents.php';
$agentController = new AgentController($conn);
$dashboardData = $agentController->dashboard();

if (!$dashboardData['success']) {
    // Agent not assigned yet
    $agent_types = ['student_researcher', 'faculty_advisor', 'project_lead', 'peer_reviewer', 'community_member'];
    include __DIR__ . '/agents/onboarding.php';
    exit;
}

$agent = $dashboardData['agent'];
$goals = $dashboardData['goals'];
$metrics = $dashboardData['metrics'];
$recommendations = $dashboardData['recommendations'];
$recent_actions = $dashboardData['recent_actions'];
$achievements = $dashboardData['achievements'];

// Color mapping for agent types
$agentColors = [
    'student_researcher' => '#06B6D4',
    'faculty_advisor' => '#8B5CF6',
    'project_lead' => '#10B981',
    'peer_reviewer' => '#F59E0B',
    'community_member' => '#EF4444'
];

$agentColor = $agentColors[$agent['name']] ?? '#1E293B';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .agent-header {
            background: linear-gradient(135deg, <?php echo $agentColor; ?> 0%, <?php echo $agentColor; ?>dd 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .agent-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .goal-card {
            background: var(--white);
            border: 2px solid var(--border-light);
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .goal-card:hover {
            border-color: <?php echo $agentColor; ?>;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .goal-progress {
            margin-top: 1rem;
        }

        .progress-bar {
            background: var(--bg-light);
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .progress-fill {
            background: <?php echo $agentColor; ?>;
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .metrics-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--white);
            border: 1px solid var(--border-light);
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: <?php echo $agentColor; ?>;
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        .recommendations-card {
            background: var(--white);
            border-left: 4px solid <?php echo $agentColor; ?>;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .recommendation-item {
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .recommendation-item:hover {
            background: <?php echo $agentColor; ?>11;
            transform: translateX(4px);
        }

        .recommendation-label {
            font-size: 0.75rem;
            color: white;
            background: <?php echo $agentColor; ?>;
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            margin-bottom: 0.5rem;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .achievement-card {
            background: var(--white);
            border: 2px solid var(--border-light);
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            transition: all 0.2s ease;
        }

        .achievement-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
        }

        .achievement-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .timeline {
            position: relative;
            padding: 1rem 0;
        }

        .timeline-item {
            padding: 1rem;
            padding-left: 2.5rem;
            border-left: 2px solid var(--border-light);
            margin-bottom: 1rem;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 1.25rem;
            width: 12px;
            height: 12px;
            background: <?php echo $agentColor; ?>;
            border-radius: 50%;
            border: 2px solid white;
        }

        .timeline-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .timeline-content {
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container" style="padding: 2rem;">
        <!-- Agent Header -->
        <div class="agent-header">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="font-size: 3rem;">
                    <i class="<?php
                        $icons = [
                            'student_researcher' => 'fas fa-flask',
                            'faculty_advisor' => 'fas fa-chalkboard-user',
                            'project_lead' => 'fas fa-tasks',
                            'peer_reviewer' => 'fas fa-check-circle',
                            'community_member' => 'fas fa-users'
                        ];
                        echo $icons[$agent['name']] ?? 'fas fa-user';
                    ?>"></i>
                </div>
                <div>
                    <h1 style="margin: 0 0 0.5rem 0;"><?php echo sanitize($agent['display_name']); ?></h1>
                    <p style="margin: 0; opacity: 0.9;"><?php echo sanitize($agent['primary_goal']); ?></p>
                    <span class="agent-badge">Active since <?php echo date('M Y', strtotime($agent['assigned_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Goals Section -->
        <div>
            <h2 class="section-title">🎯 Your Goals</h2>
            <div class="goals-grid">
                <?php foreach ($goals as $goal): ?>
                <div class="goal-card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem;">
                                <?php echo ucwords(str_replace('_', ' ', $goal['goal_type'])); ?>
                            </h3>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
                                <?php echo sanitize(substr($goal['description'], 0, 100)); ?>...
                            </p>
                        </div>
                        <span style="padding: 0.25rem 0.75rem; background: var(--bg-light); border-radius: 0.25rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <?php echo ucfirst($goal['status']); ?>
                        </span>
                    </div>
                    <div class="goal-progress">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">Progress</span>
                            <span style="font-size: 0.75rem; font-weight: 600;">
                                <?php echo $goal['target_value'] > 0 ? round(($goal['progress'] / $goal['target_value']) * 100) : 0; ?>%
                            </span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(100, ($goal['progress'] / max(1, $goal['target_value'])) * 100); ?>%"></div>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                            <?php echo $goal['progress']; ?> / <?php echo $goal['target_value']; ?> <?php echo sanitize($goal['target_metric']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($goals)): ?>
                <div class="goal-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary);">
                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>No goals set yet. Set your first goal to get started!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Metrics Section -->
        <div style="margin-top: 3rem;">
            <h2 class="section-title">📊 Performance Metrics</h2>
            <div class="metrics-row">
                <?php
                $metricSummary = [];
                foreach ($metrics as $metric) {
                    if (!isset($metricSummary[$metric['metric_name']])) {
                        $metricSummary[$metric['metric_name']] = 0;
                    }
                    $metricSummary[$metric['metric_name']] += $metric['metric_value'];
                }

                foreach (array_slice($metricSummary, 0, 4) as $name => $value): ?>
                <div class="metric-card">
                    <div class="metric-value"><?php echo $value; ?></div>
                    <div class="metric-label"><?php echo str_replace('_', ' ', $name); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recommendations Section -->
        <?php if (!empty($recommendations)): ?>
        <div style="margin-top: 3rem;">
            <h2 class="section-title">💡 Recommendations for You</h2>
            <div class="recommendations-card">
                <?php foreach (array_slice($recommendations, 0, 3) as $rec): ?>
                <div class="recommendation-item" onclick="handleRecommendation(<?php echo $rec['id']; ?>, 'accepted')">
                    <div>
                        <span class="recommendation-label"><?php echo ucfirst($rec['recommendation_type']); ?></span>
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                            <?php echo sanitize($rec['message']); ?>
                        </p>
                        <div style="display: flex; gap: 1rem; margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <span style="display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-user-circle"></i>
                                From: <?php echo sanitize($rec['from_agent_type']); ?>
                            </span>
                            <span style="display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-star"></i>
                                Relevance: <?php echo $rec['relevance_score']; ?>/10
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Achievements Section -->
        <?php if (!empty($achievements)): ?>
        <div style="margin-top: 3rem;">
            <h2 class="section-title">🏆 Achievements</h2>
            <div class="achievements-grid">
                <?php foreach ($achievements as $ach): ?>
                <div class="achievement-card" title="<?php echo sanitize($ach['description']); ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-medal" style="color: #FFD700;"></i>
                    </div>
                    <div style="font-size: 0.75rem; font-weight: 600;"><?php echo sanitize($ach['achievement_name']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activity Timeline -->
        <div style="margin-top: 3rem;">
            <h2 class="section-title">📋 Recent Activity</h2>
            <div class="timeline">
                <?php foreach (array_slice($recent_actions, 0, 5) as $action): ?>
                <div class="timeline-item">
                    <div class="timeline-label"><?php echo date('M d, Y', strtotime($action['created_at'])); ?></div>
                    <div class="timeline-content">
                        <strong><?php echo str_replace('_', ' ', ucfirst($action['action_type'])); ?></strong>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: var(--text-secondary);">
                            <?php echo sanitize($action['action_description']); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function handleRecommendation(recommendationId, action) {
            // Handle recommendation acceptance
            const form = new FormData();
            form.append('recommendation_id', recommendationId);
            form.append('action', action);

            fetch('<?php echo BASE_URL; ?>/api/recommendations', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
