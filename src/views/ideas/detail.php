<?php
/**
 * IdeaSync - Idea Detail Page (Professional UI)
 */
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Idea.php';
require_once __DIR__ . '/../../services/HealthService.php';

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id === 0) {
    header('Location: ' . BASE_URL . '/?page=ideas');
    exit();
}

$db = new Database();
$conn = $db->connect();
$ideaModel = new Idea($conn);
$idea = $ideaModel->getById($idea_id);

if (!$idea) {
    header('Location: ' . BASE_URL . '/?page=ideas');
    exit();
}

$health = HealthService::calculateScore($idea);
$healthColor = HealthService::getHealthColor($health);
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($idea['title']); ?> | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <header class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="logo">IDEASYNC</a>
            <div class="flex gap-4 items-center">
                <a href="/?page=ideas" class="text-secondary text-sm">← Back to Feed</a>
                <?php if ($current_user): ?>
                    <div class="user-avatar"><?php echo strtoupper(substr($current_user['name'], 0, 1)); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container py-20">
        <div class="flex gap-8 items-start">
            <!-- Left Column: Content -->
            <div style="flex: 1;">
                <div class="flex gap-2 mb-4">
                    <span class="badge badge-accent"><?php echo htmlspecialchars($idea['domain']); ?></span>
                    <span class="badge"><?php echo strtoupper($idea['status'] ?? 'OPEN'); ?></span>
                </div>

                <h1 class="mb-6 tight-tracking"><?php echo htmlspecialchars($idea['title']); ?></h1>

                <div class="flex items-center gap-3 mb-12">
                    <div class="user-avatar"><?php echo strtoupper(substr($idea['name'], 0, 1)); ?></div>
                    <div>
                        <div class="text-sm font-semibold"><?php echo htmlspecialchars($idea['name']); ?></div>
                        <div class="text-xs text-muted"><?php echo $idea['branch']; ?> • <?php echo $idea['roll_number']; ?></div>
                    </div>
                </div>

                <div class="card mb-8">
                    <h3 class="mb-4">The Vision</h3>
                    <p class="text-secondary" style="white-space: pre-wrap;"><?php echo htmlspecialchars($idea['description']); ?></p>
                </div>

                <div class="card">
                    <h3 class="mb-4">Skills Required</h3>
                    <div class="flex gap-2 flex-wrap">
                        <?php
                        $skills = json_decode($idea['skills_needed'] ?? '[]', true);
                        foreach ($skills as $skill): ?>
                            <span class="badge"><?php echo htmlspecialchars($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Actions -->
            <div style="width: 320px;">
                <div class="card mb-6">
                    <h3 class="text-sm uppercase tracking-widest text-muted mb-4">Project Health</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-2xl font-bold"><?php echo $health; ?>%</span>
                        <span class="text-xs" style="color: <?php echo $healthColor; ?>;">● Optimal</span>
                    </div>
                    <div class="health-bar-container">
                        <div class="health-bar-fill" style="width: <?php echo $health; ?>%; background-color: <?php echo $healthColor; ?>;"></div>
                    </div>
                    <div class="mt-4 text-xs text-muted">
                        Score based on description quality, skills defined, and community interest.
                    </div>
                </div>

                <div class="card">
                    <div class="flex flex-col gap-4">
                        <div class="text-center pb-4 border-b border-border">
                            <div class="text-2xl font-bold mb-1"><?php echo $idea['applicant_count']; ?></div>
                            <div class="text-xs text-muted uppercase">Applicants</div>
                        </div>

                        <?php if ($current_user && $current_user['id'] == $idea['user_id']): ?>
                            <button class="btn btn-secondary w-full">Manage Applicants</button>
                            <button class="btn btn-ghost w-full">Edit Idea</button>
                        <?php else: ?>
                            <button class="btn btn-primary w-full">Apply to Build</button>
                            <button class="btn btn-secondary w-full">⭐ Upvote Idea</button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($idea['github_repo_url']): ?>
                    <a href="<?php echo htmlspecialchars($idea['github_repo_url']); ?>" target="_blank" class="card mt-6 flex items-center justify-between hover:border-accent">
                        <div class="flex items-center gap-3">
                            <i data-lucide="github" size="18"></i>
                            <span class="text-sm font-semibold">View Repository</span>
                        </div>
                        <i data-lucide="external-link" size="14" class="text-muted"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>
