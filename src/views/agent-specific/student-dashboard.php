<?php
/**
 * student-dashboard.php - Student Researcher Agent Dashboard Cards
 * Displays student-specific workflow and goals
 */

require_once __DIR__ . '/../../models/Agent.php';
require_once __DIR__ . '/../../models/Workflow.php';

$agentModel = new Agent($conn);
$workflowModel = new Workflow($conn);
$user_agent = $agentModel->getByUserId($user_id);

if (!$user_agent) {
    return;
}

// Get student's goals
$query = "SELECT * FROM agent_goals WHERE user_agent_id = ? AND status != 'completed' ORDER BY created_at DESC LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_agent['id']);
$stmt->execute();
$goals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get active ideas
$query = "SELECT i.*, u.name, COUNT(DISTINCT a.id) as applicant_count
          FROM ideas i
          JOIN users u ON i.user_id = u.id
          LEFT JOIN applications a ON i.id = a.idea_id
          WHERE i.user_id = ? AND i.status IN ('open', 'in_progress')
          GROUP BY i.id
          LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_ideas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get mentor recommendations
$recommendations = $agentModel->getRecommendations($user_agent['id'], 'suggested');

// Get metrics
$query = "SELECT metric_name, metric_value FROM agent_metrics WHERE user_agent_id = ? ORDER BY recorded_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_agent['id']);
$stmt->execute();
$metrics = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!-- Active Research Goals -->
<div class="role-card">
    <h3><i class="fas fa-target" style="color: #06B6D4; margin-right: 0.5rem;"></i>Research Goals</h3>
    <?php if (!empty($goals)): ?>
        <?php foreach ($goals as $goal): ?>
        <div style="padding: 0.75rem 0; border-bottom: 1px solid #F1F5F9;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                <strong><?php echo htmlspecialchars($goal['goal_type']); ?></strong>
                <span style="font-size: 0.75rem; background: #E0F2FE; color: #0369A1; padding: 0.25rem 0.75rem; border-radius: 0.25rem;">
                    <?php echo $goal['progress']; ?>/<?php echo $goal['target_value']; ?>
                </span>
            </div>
            <div style="font-size: 0.875rem; color: #64748B; margin-bottom: 0.5rem;">
                <?php echo htmlspecialchars(substr($goal['description'], 0, 80)); ?>...
            </div>
            <div style="background: #F1F5F9; height: 4px; border-radius: 2px; overflow: hidden;">
                <div style="background: #06B6D4; height: 100%; width: <?php echo $goal['target_value'] > 0 ? round(($goal['progress'] / $goal['target_value']) * 100) : 0; ?>%;"></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 2rem; color: #94A3B8;">
            <p>No active goals yet</p>
            <a href="<?php echo BASE_URL; ?>/api/agents.php?action=add_goal" style="color: #06B6D4; text-decoration: none; font-weight: 600;">Set your first goal →</a>
        </div>
    <?php endif; ?>
</div>

<!-- My Research Ideas -->
<div class="role-card">
    <h3><i class="fas fa-flask" style="color: #06B6D4; margin-right: 0.5rem;"></i>My Ideas</h3>
    <?php if (!empty($my_ideas)): ?>
        <?php foreach ($my_ideas as $idea): ?>
        <div style="padding: 0.75rem 0; border-bottom: 1px solid #F1F5F9;">
            <a href="<?php echo BASE_URL; ?>/index.php?page=idea-detail&id=<?php echo $idea['id']; ?>" style="color: #1E293B; text-decoration: none; font-weight: 600; display: block;">
                <?php echo htmlspecialchars(substr($idea['title'], 0, 50)); ?>
            </a>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                <span><?php echo $idea['applicant_count']; ?> applicant<?php echo $idea['applicant_count'] !== 1 ? 's' : ''; ?></span> •
                <span><?php echo ucfirst($idea['status']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <div style="margin-top: 1rem;">
            <a href="<?php echo BASE_URL; ?>/index.php?page=ideas&action=create" style="display: inline-block; background: #06B6D4; color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                <i class="fas fa-plus"></i> New Idea
            </a>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 2rem; color: #94A3B8;">
            <p>No ideas yet</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=ideas&action=create" style="color: #06B6D4; text-decoration: none; font-weight: 600;">Create your first idea →</a>
        </div>
    <?php endif; ?>
</div>

<!-- Mentor Recommendations -->
<div class="role-card">
    <h3><i class="fas fa-user-tie" style="color: #8B5CF6; margin-right: 0.5rem;"></i>Mentor Matches</h3>
    <?php if (!empty($recommendations)): ?>
        <?php foreach (array_slice($recommendations, 0, 3) as $rec): ?>
        <div style="padding: 0.75rem 0; border-bottom: 1px solid #F1F5F9;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <strong style="font-size: 0.875rem;"><?php echo htmlspecialchars($rec['from_agent_type']); ?></strong>
                <span style="font-size: 0.75rem; background: #F3E8FF; color: #6D28D9; padding: 0.25rem 0.75rem; border-radius: 0.25rem;">
                    ★ <?php echo $rec['relevance_score']; ?>/10
                </span>
            </div>
            <div style="font-size: 0.825rem; color: #64748B; margin-top: 0.5rem;">
                <?php echo htmlspecialchars(substr($rec['message'], 0, 80)); ?>...
            </div>
            <button onclick="acceptRecommendation(<?php echo $rec['id']; ?>)" style="margin-top: 0.5rem; background: #8B5CF6; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 0.25rem; font-size: 0.75rem; cursor: pointer;">
                Connect
            </button>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 2rem; color: #94A3B8;">
            <p>No recommendations yet</p>
            <p style="font-size: 0.875rem;">Complete a research goal to get mentor matches</p>
        </div>
    <?php endif; ?>
</div>

<!-- Performance Metrics -->
<div class="role-card" style="grid-column: 1 / -1;">
    <h3><i class="fas fa-chart-bar" style="color: #10B981; margin-right: 0.5rem;"></i>Your Progress</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
        <?php
        $metric_totals = [];
        foreach ($metrics as $m) {
            if (!isset($metric_totals[$m['metric_name']])) {
                $metric_totals[$m['metric_name']] = 0;
            }
            $metric_totals[$m['metric_name']] += $m['metric_value'];
        }

        foreach (array_slice($metric_totals, 0, 4) as $name => $value):
        ?>
        <div style="background: #F1F5F9; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <div style="font-size: 1.75rem; font-weight: 700; color: #06B6D4;">
                <?php echo $value; ?>
            </div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.5rem; text-transform: capitalize;">
                <?php echo str_replace('_', ' ', $name); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function acceptRecommendation(recommendationId) {
        if (confirm('Connect with this mentor?')) {
            fetch('<?php echo BASE_URL; ?>/src/api/agent-recommendations.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=accept&recommendation_id=' + recommendationId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Connection request sent!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
    }
</script>
