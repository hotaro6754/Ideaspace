<?php
/**
 * Workflow Dashboard View
 * Displays idea progression through Discuss → Plan → Execute → Verify → Ship phases
 */

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id <= 0) {
    redirect(BASE_URL . '/ideas');
}

require_once __DIR__ . '/../controllers/workflow.php';
$workflowController = new WorkflowController($conn);
$status = $workflowController->getWorkflowStatus($idea_id);

if (!$status['success']) {
    include __DIR__ . '/404.php';
    exit;
}

$current_phase = $status['current_phase']['current_phase'];
$phase_readiness = $status['readiness'];
$phase_history = $status['phase_history'];

// Phase information
$phases = [
    'discuss' => [
        'title' => 'Discuss',
        'icon' => 'fas fa-comments',
        'color' => '#06B6D4',
        'description' => 'Lightweight spec - problem statement, solution, team agreement',
        'order' => 1
    ],
    'plan' => [
        'title' => 'Plan',
        'icon' => 'fas fa-clipboard',
        'color' => '#8B5CF6',
        'description' => 'Detailed specification - objectives, scope, risks, roadmap',
        'order' => 2
    ],
    'execute' => [
        'title' => 'Execute',
        'icon' => 'fas fa-cogs',
        'color' => '#10B981',
        'description' => 'Wave-based task execution - atomic work, parallel progress',
        'order' => 3
    ],
    'verify' => [
        'title' => 'Verify',
        'icon' => 'fas fa-check-double',
        'color' => '#F59E0B',
        'description' => 'Quality assurance - verification report, peer review',
        'order' => 4
    ],
    'ship' => [
        'title' => 'Ship',
        'icon' => 'fas fa-rocket',
        'color' => '#EF4444',
        'description' => 'Final deliverables - documentation, deployment, completion',
        'order' => 5
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workflow - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .phase-timeline {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 3rem;
            overflow-x: auto;
            padding: 1rem 0;
        }

        .phase-step {
            flex: 1;
            min-width: 150px;
            text-align: center;
            position: relative;
        }

        .phase-step::after {
            content: '';
            position: absolute;
            top: 2.5rem;
            left: 100%;
            width: 1.5rem;
            height: 3px;
            background: var(--border-light);
        }

        .phase-step:last-child::after {
            display: none;
        }

        .phase-step.active::after {
            background: var(--phase-color);
        }

        .phase-circle {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 0.75rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #64748B;
            border: 3px solid var(--border-light);
            background: white;
            transition: all 0.3s ease;
        }

        .phase-step.completed .phase-circle {
            background: var(--phase-color);
            color: white;
            border-color: var(--phase-color);
        }

        .phase-step.active .phase-circle {
            background: var(--phase-color);
            color: white;
            border-color: var(--phase-color);
            transform: scale(1.1);
            box-shadow: 0 0 0 8px rgba(var(--phase-rgb), 0.1);
        }

        .phase-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .phase-step.active .phase-label {
            color: var(--phase-color);
        }

        .phase-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid var(--border-light);
        }

        .phase-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-light);
            padding-bottom: 1rem;
        }

        .phase-icon {
            font-size: 2rem;
            color: var(--phase-color);
        }

        .phase-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .phase-description {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .requirements-checklist {
            background: var(--bg-light);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .requirement-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            background: white;
            border-radius: 0.5rem;
            border-left: 3px solid var(--border-light);
        }

        .requirement-item.completed {
            border-left-color: #10B981;
        }

        .requirement-item.required {
            border-left-color: #EF4444;
        }

        .requirement-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid var(--border-light);
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.25rem;
            color: white;
        }

        .requirement-item.completed .requirement-checkbox {
            background: #10B981;
            border-color: #10B981;
        }

        .requirement-text {
            flex: 1;
        }

        .requirement-name {
            font-weight: 600;
            color: var(--text-primary);
            display: block;
        }

        .requirement-desc {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .phase-form {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-light);
            border-radius: 0.5rem;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            border-top: 2px solid var(--border-light);
            padding-top: 1.5rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--phase-color);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: var(--phase-color);
            border: 2px solid var(--phase-color);
        }

        .progress-bar {
            height: 8px;
            background: var(--bg-light);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: var(--phase-color);
            transition: width 0.3s ease;
        }

        .history-timeline {
            position: relative;
            padding: 1rem 0;
        }

        .history-item {
            padding: 1rem;
            padding-left: 2.5rem;
            border-left: 2px solid var(--border-light);
            position: relative;
            margin-bottom: 1rem;
        }

        .history-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 1.5rem;
            width: 12px;
            height: 12px;
            background: var(--phase-color);
            border-radius: 50%;
            border: 2px solid white;
        }

        .history-date {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .history-content {
            margin-top: 0.25rem;
        }

        .wave-status {
            background: var(--bg-light);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .wave-header {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .wave-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.75rem;
        }

        .metric {
            background: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--phase-color);
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="container" style="padding: 2rem;">
        <!-- Phase Timeline -->
        <div class="phase-timeline">
            <?php foreach ($phases as $phase_key => $phase_info): ?>
            <div class="phase-step <?php echo ($phase_key === $current_phase ? 'active' : ''); ?> <?php echo ($phase_info['order'] < $phases[$current_phase]['order'] ? 'completed' : ''); ?>"
                 style="--phase-color: <?php echo $phase_info['color']; ?>;">
                <div class="phase-circle">
                    <i class="<?php echo $phase_info['icon']; ?>"></i>
                </div>
                <div class="phase-label"><?php echo $phase_info['title']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Current Phase Content -->
        <div class="phase-content" style="--phase-color: <?php echo $phases[$current_phase]['color']; ?>;">
            <div class="phase-header">
                <div class="phase-icon">
                    <i class="<?php echo $phases[$current_phase]['icon']; ?>"></i>
                </div>
                <div>
                    <p class="phase-title"><?php echo $phases[$current_phase]['title']; ?> Phase</p>
                    <p class="phase-description"><?php echo $phases[$current_phase]['description']; ?></p>
                </div>
            </div>

            <!-- Requirements Checklist -->
            <div class="requirements-checklist">
                <h3 style="margin: 0 0 1rem 0;">Phase Requirements</h3>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php
                        $parts = explode('/', $phase_readiness['progress']);
                        echo ($parts[0] / $parts[1]) * 100;
                    ?>%"></div>
                </div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    <?php echo $phase_readiness['progress']; ?> requirements completed
                </p>

                <?php foreach ($status['phase_requirements'] as $req): ?>
                <div class="requirement-item <?php echo (in_array($req['requirement_name'], $phase_readiness['missing_requirements']) ? '' : 'completed'); ?> <?php echo ($req['is_mandatory'] ? 'required' : ''); ?>">
                    <div class="requirement-checkbox">
                        <?php if (!in_array($req['requirement_name'], $phase_readiness['missing_requirements'])): ?>
                        <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </div>
                    <div class="requirement-text">
                        <span class="requirement-name"><?php echo $req['requirement_name']; ?></span>
                        <span class="requirement-desc"><?php echo $req['requirement_description']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Phase-Specific Content -->
            <div class="phase-form">
                <?php if ($current_phase === 'discuss'): ?>
                    <h3>Create Idea Charter</h3>
                    <form onsubmit="submitPhase(event, 'discuss')">
                        <div class="form-group">
                            <label>Problem Statement</label>
                            <textarea name="problem_statement" placeholder="What problem does your idea solve?" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Proposed Solution</label>
                            <textarea name="proposed_solution" placeholder="How will you solve it?" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Success Criteria</label>
                            <textarea name="success_criteria" placeholder="How will you know it's successful? (comma-separated)" required></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Team Size Estimate</label>
                                <input type="number" name="team_size" min="1" max="20" value="3" required>
                            </div>
                            <div class="form-group">
                                <label>Effort Estimate</label>
                                <select name="effort_estimate" required>
                                    <option value="small">Small (1-2 weeks)</option>
                                    <option value="medium" selected>Medium (1-2 months)</option>
                                    <option value="large">Large (2-4 months)</option>
                                    <option value="extra-large">Extra Large (4+ months)</option>
                                </select>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Save Charter & Advance</button>
                        </div>
                    </form>

                <?php elseif ($current_phase === 'plan'): ?>
                    <h3>Create Project Brief & Roadmap</h3>
                    <form onsubmit="submitPhase(event, 'plan')">
                        <div class="form-group">
                            <label>Project Description</label>
                            <textarea name="description" placeholder="Detailed description of the project" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Objectives (comma-separated)</label>
                            <textarea name="objectives" placeholder="Main objectives to achieve" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Scope</label>
                            <textarea name="scope" placeholder="What's included and excluded" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Constraints</label>
                            <textarea name="constraints" placeholder="Time, budget, technical constraints" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Risk Assessment</label>
                            <textarea name="risk_assessment" placeholder="Identified risks and mitigations" required></textarea>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Save Brief & Advance</button>
                        </div>
                    </form>

                <?php elseif ($current_phase === 'execute'): ?>
                    <h3>Wave-Based Task Execution</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">Break your project into waves of parallel atomic tasks</p>
                    <form onsubmit="submitPhase(event, 'execute')">
                        <div class="form-group">
                            <label>Wave Number</label>
                            <input type="number" name="wave_number" min="1" value="1" required>
                        </div>
                        <div class="form-group">
                            <label>Tasks (JSON format)</label>
                            <textarea name="tasks" placeholder='[{"title":"Task 1","description":"...","assigned_to":1,"priority":"high","estimated_hours":8}]' required></textarea>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Add Wave Tasks</button>
                        </div>
                    </form>

                <?php elseif ($current_phase === 'verify'): ?>
                    <h3>Quality Verification & Peer Review</h3>
                    <form onsubmit="submitPhase(event, 'verify')">
                        <div class="form-group">
                            <label>Verification Report</label>
                            <textarea name="verification_report" placeholder="Document proving all objectives were met" required></textarea>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Submit for Review</button>
                        </div>
                    </form>

                <?php elseif ($current_phase === 'ship'): ?>
                    <h3>Final Deliverables & Deployment</h3>
                    <form onsubmit="submitPhase(event, 'ship')">
                        <div class="form-group">
                            <label>Final Deliverables</label>
                            <textarea name="deliverables" placeholder="What are you shipping? Links, files, etc." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Lessons Learned</label>
                            <textarea name="lessons" placeholder="What did you learn? What would you do differently?"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Next Steps</label>
                            <textarea name="next_steps" placeholder="Follow-up work, maintenance, evolution"></textarea>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Mark as Complete</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Advance Phase Button -->
            <?php if ($current_phase !== 'ship' && $phase_readiness['ready']): ?>
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border-light);">
                <button onclick="advancePhase(<?php echo $idea_id; ?>)" class="btn-primary" style="width: 100%;">
                    Advance to <?php echo $phases[$phases[$current_phase]['order'] < 5 ? array_keys($phases)[$phases[$current_phase]['order']] : $current_phase]['title']; ?> Phase →
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Phase History -->
        <div class="phase-content">
            <h2 style="margin: 0 0 1.5rem 0;">Phase History</h2>
            <div class="history-timeline">
                <?php if (empty($phase_history)): ?>
                <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">No phase transitions yet</p>
                <?php else: ?>
                    <?php foreach ($phase_history as $event): ?>
                    <div class="history-item">
                        <div class="history-date"><?php echo date('M d, Y H:i', strtotime($event['transition_date'])); ?></div>
                        <div class="history-content">
                            <strong><?php echo ucfirst($event['from_phase']); ?> → <?php echo ucfirst($event['to_phase']); ?></strong>
                            <?php if ($event['name']): ?>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">by <?php echo $event['name']; ?></div>
                            <?php endif; ?>
                            <?php if ($event['reason']): ?>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;"><?php echo $event['reason']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function submitPhase(event, phase) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('phase', phase);
            formData.append('idea_id', <?php echo $idea_id; ?>);

            fetch('<?php echo BASE_URL; ?>/api/workflow', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Phase updated! You can now advance.');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function advancePhase(ideaId) {
            fetch('<?php echo BASE_URL; ?>/api/workflow', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=advance&idea_id=' + ideaId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to advance'));
                }
            });
        }
    </script>
</body>
</html>
