<?php
/**
 * Agent Onboarding View
 * Helps new users select their agent type
 */

$agentTypeInfo = [
    'student_researcher' => [
        'title' => 'Student Researcher',
        'icon' => 'fas fa-flask',
        'color' => '#06B6D4',
        'description' => 'Find mentors, validate ideas, and publish research findings',
        'goals' => [
            'Find faculty mentors in your field',
            'Publish research papers',
            'Validate your ideas academically'
        ],
        'workflows' => ['Discuss', 'Research', 'Validate', 'Publish']
    ],
    'faculty_advisor' => [
        'title' => 'Faculty Advisor',
        'icon' => 'fas fa-chalkboard-user',
        'color' => '#8B5CF6',
        'description' => 'Guide student research, validate ideas, and provide mentorship',
        'goals' => [
            'Mentor student researchers',
            'Validate ideas for academic merit',
            'Guide publication process'
        ],
        'workflows' => ['Receive', 'Guide', 'Validate', 'Approve']
    ],
    'project_lead' => [
        'title' => 'Project Lead',
        'icon' => 'fas fa-tasks',
        'color' => '#10B981',
        'description' => 'Execute collaborative projects, manage teams, and hit milestones',
        'goals' => [
            'Build collaborative teams',
            'Complete project milestones',
            'Ship finished projects'
        ],
        'workflows' => ['Plan', 'Execute', 'Review', 'Complete']
    ],
    'peer_reviewer' => [
        'title' => 'Peer Reviewer',
        'icon' => 'fas fa-check-circle',
        'color' => '#F59E0B',
        'description' => 'Provide constructive feedback and ensure quality standards',
        'goals' => [
            'Review project implementations',
            'Detect anti-patterns',
            'Guide quality improvements'
        ],
        'workflows' => ['Review', 'Analyze', 'Feedback', 'Track']
    ],
    'community_member' => [
        'title' => 'Community Member',
        'icon' => 'fas fa-users',
        'color' => '#EF4444',
        'description' => 'Learn, contribute feedback, and support community growth',
        'goals' => [
            'Discover learning opportunities',
            'Support community ideas',
            'Build connections'
        ],
        'workflows' => ['Learn', 'Engage', 'Support', 'Grow']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Onboarding - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .onboarding-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .onboarding-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
            max-width: 600px;
        }

        .onboarding-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .onboarding-header p {
            font-size: 1.125rem;
            opacity: 0.8;
            line-height: 1.6;
        }

        .agent-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin-bottom: 2rem;
        }

        .agent-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .agent-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .agent-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--agent-color);
        }

        .agent-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--agent-color);
        }

        .agent-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            color: #1E293B;
        }

        .agent-card p {
            color: #64748B;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .agent-goals {
            text-align: left;
            background: var(--agent-color);
            background-opacity: 0.05;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .agent-goals li {
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            list-style: none;
            padding-left: 1.5rem;
            position: relative;
        }

        .agent-goals li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--agent-color);
            font-weight: bold;
        }

        .select-btn {
            background: var(--agent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .select-btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .workflows {
            display: flex;
            gap: 0.25rem;
            margin-top: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .workflow-badge {
            font-size: 0.7rem;
            background: #F1F5F9;
            color: #475569;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        @media (max-width: 768px) {
            .onboarding-header h1 {
                font-size: 1.75rem;
            }

            .agent-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="onboarding-container">
        <div class="onboarding-header">
            <h1>Welcome to IdeaSync Agent System</h1>
            <p>Select your role to get personalized recommendations, track goals, and collaborate effectively with your campus community.</p>
        </div>

        <div class="agent-cards">
            <?php foreach ($agentTypeInfo as $agentType => $info): ?>
            <div class="agent-card" style="--agent-color: <?php echo $info['color']; ?>">
                <div class="agent-icon">
                    <i class="<?php echo $info['icon']; ?>"></i>
                </div>
                <h3><?php echo $info['title']; ?></h3>
                <p><?php echo $info['description']; ?></p>

                <div class="agent-goals">
                    <ul>
                        <?php foreach ($info['goals'] as $goal): ?>
                        <li><?php echo $goal; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="workflows">
                    <?php foreach ($info['workflows'] as $workflow): ?>
                    <span class="workflow-badge"><?php echo $workflow; ?></span>
                    <?php endforeach; ?>
                </div>

                <button class="select-btn" onclick="selectAgent('<?php echo $agentType; ?>')">
                    Select This Role
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; color: white; opacity: 0.7;">
            <p style="font-size: 0.875rem;">You can change your role later from your profile settings</p>
        </div>
    </div>

    <script>
        function selectAgent(agentType) {
            const form = new FormData();
            form.append('agent_type', agentType);

            fetch('<?php echo BASE_URL; ?>/api/agents/assign', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Redirect to agent dashboard
                    window.location.href = '<?php echo BASE_URL; ?>/agents/dashboard';
                } else {
                    alert('Error selecting agent type: ' + data.error);
                }
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
</body>
</html>
