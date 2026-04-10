<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Collaborations - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .collab-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .collab-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-left {
            background: #fee2e2;
            color: #991b1b;
        }
        .idea-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .idea-link:hover {
            color: #1e40af;
        }
        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <header>
        <nav>
            <a href="<?php echo BASE_URL; ?>/?page=home" class="logo">IdeaSync</a>
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>/?page=home">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/?page=ideas">Ideas</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=dashboard">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/?page=profile" class="active">Profile</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=login">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <?php
    if (!isLoggedIn()) {
        http_response_code(401);
        include __DIR__ . '/../404.php';
        exit();
    }

    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../models/Collaboration.php';
    require_once __DIR__ . '/../../models/Idea.php';

    $db = new Database();
    $conn = $db->connect();
    $collabModel = new Collaboration($conn);
    $ideaModel = new Idea($conn);

    $user_id = $_SESSION['user_id'];

    // Get user's collaborations
    $collaborations = $collabModel->getByUser($user_id);

    // Domain colors
    $domain_colors = [
        'AI/ML' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'Web Development' => ['bg' => '#dcfce7', 'text' => '#065f46'],
        'Mobile Development' => ['bg' => '#fce7f3', 'text' => '#831843'],
        'Cybersecurity' => ['bg' => '#fef3c7', 'text' => '#92400e'],
        'Cloud Computing' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
        'Data Science' => ['bg' => '#f0fdf4', 'text' => '#166534'],
        'IoT' => ['bg' => '#fef2f2', 'text' => '#7c2d12'],
        'Blockchain' => ['bg' => '#fef3c7', 'text' => '#78350f'],
        'Game Development' => ['bg' => '#f3e8ff', 'text' => '#581c87'],
        'Other' => ['bg' => '#f3f4f6', 'text' => '#374151']
    ];

    function getColorForDomain($domain) {
        global $domain_colors;
        return $domain_colors[$domain] ?? $domain_colors['Other'];
    }
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">My Collaborations</h1>
                        <p style="color: #6b7280; margin: 0.5rem 0 0 0;">Ideas you're actively working on</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=profile" class="btn btn-ghost">Back to Profile</a>
                </div>
            </div>

            <!-- Collaborations List -->
            <?php if (empty($collaborations)): ?>
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px; border: 1px dashed #e5e7eb;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div>
                    <h3 style="color: #111827; margin-bottom: 0.5rem;">No Collaborations Yet</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">Accept applications to start collaborating on ideas!</p>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-primary">Browse Ideas</a>
                </div>
            <?php else: ?>
                <?php foreach ($collaborations as $collab):
                    $idea = $ideaModel->getById($collab['idea_id']);
                    $colors = getColorForDomain($idea['domain']);
                    $initials = substr($collab['name'], 0, 1);
                ?>
                    <div class="collab-card">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="idea-link" style="font-size: 1.25rem; margin-bottom: 0.5rem; display: block;">
                                    <?php echo sanitize($idea['title']); ?>
                                </a>
                                <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                                    <span class="status-badge status-<?php echo strtolower($collab['status']); ?>">
                                        <?php echo ucfirst($collab['status']); ?>
                                    </span>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600; background: <?php echo $colors['bg']; ?>; color: <?php echo $colors['text']; ?>;">
                                        <?php echo sanitize($idea['domain']); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="color: #6b7280; font-size: 0.875rem;">Joined on</div>
                                <div style="color: #111827; font-weight: 600;"><?php echo date('M d, Y', strtotime($collab['created_at'])); ?></div>
                            </div>
                        </div>

                        <!-- Creator Info -->
                        <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div class="avatar"><?php echo strtoupper($initials); ?></div>
                                <div>
                                    <div style="font-size: 0.875rem; color: #6b7280;">Working with</div>
                                    <div style="color: #111827; font-weight: 600;"><?php echo sanitize($collab['name']); ?></div>
                                    <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo sanitize($collab['roll_number']); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Your Role -->
                        <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <div style="font-size: 0.875rem; color: #6b7280;">Your Role</div>
                            <div style="color: #111827; font-weight: 600; font-size: 1.125rem;"><?php echo sanitize($collab['role']); ?></div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 1rem;">
                            <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="btn btn-secondary btn-sm" style="flex: 1;">View Idea</a>
                            <?php if ($collab['status'] === 'active'): ?>
                                <button type="button" onclick="leaveCollaboration(<?php echo $collab['id']; ?>)" class="btn btn-danger btn-sm">Leave Team</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function leaveCollaboration(collabId) {
            if (!confirm('Are you sure you want to leave this collaboration?')) {
                return;
            }

            const formData = new FormData();
            formData.append('collab_id', collabId);

            fetch('<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=leave', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }
    </script>
</body>
</html>
