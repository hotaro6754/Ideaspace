<?php
/**
 * IdeaSync - Enhanced Ideas Feed
 */
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Idea.php';
require_once __DIR__ . '/../../services/HealthService.php';

$db = new Database();
$conn = $db->connect();
$ideaModel = new Idea($conn);

$filters = [
    'domain' => $_GET['domain'] ?? null,
    'status' => $_GET['status'] ?? 'open',
    'search' => $_GET['search'] ?? null
];

$ideas = $ideaModel->getAll(20, 0, $filters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas Feed | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .health-bar-bg { height: 4px; background: var(--border); border-radius: 2px; overflow: hidden; margin-top: 4px; }
        .health-bar-fill { height: 100%; transition: width 0.3s ease; }
    </style>
</head>
<body class="bg-primary text-primary">

    <div class="flex">
        <!-- Sidebar placeholder -->
        <aside style="width: 240px; border-right: 1px solid var(--border); min-height: 100vh; padding: 2rem;">
            <h2 class="mb-8 text-accent font-bold">IDEASYNC</h2>
            <nav class="flex flex-col gap-4">
                <a href="/?page=ideas" class="text-primary">Feed</a>
                <a href="/?page=ideas&action=create" class="text-secondary">Post Idea</a>
                <a href="/?page=leaderboard" class="text-secondary">Leaderboard</a>
                <a href="/?page=forge" class="text-secondary">Forge</a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Explore Ideas</h1>
                <a href="/?page=ideas&action=create" class="btn btn-primary">+ Post Idea</a>
            </header>

            <div class="grid grid-2">
                <?php foreach ($ideas as $idea):
                    $health = HealthService::calculateScore($idea);
                    $healthColor = HealthService::getHealthColor($health);
                ?>
                    <div class="card p-5 relative">
                        <div class="flex justify-between items-start mb-4">
                            <span class="badge badge-web"><?php echo htmlspecialchars($idea['domain']); ?></span>
                            <?php if ($idea['is_iic_featured']): ?>
                                <span class="text-gold text-xs">⭐ IIC</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="mb-2 text-lg font-semibold"><a href="/?page=idea-detail&id=<?php echo $idea['id']; ?>"><?php echo htmlspecialchars($idea['title']); ?></a></h3>
                        <p class="text-secondary text-sm mb-6 line-clamp-2"><?php echo htmlspecialchars($idea['description']); ?></p>

                        <div class="flex items-center gap-2 mb-6">
                            <div class="user-avatar"><?php echo strtoupper(substr($idea['name'], 0, 1)); ?></div>
                            <span class="text-xs text-secondary"><?php echo htmlspecialchars($idea['name']); ?> • <?php echo $idea['branch']; ?></span>
                        </div>

                        <div class="border-t border-border pt-4 flex justify-between items-end">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-muted"><?php echo $idea['applicant_count']; ?> applicants</span>
                                    <span class="text-xs text-muted">Health: <?php echo $health; ?>%</span>
                                </div>
                                <div class="health-bar-bg">
                                    <div class="health-bar-fill" style="width: <?php echo $health; ?>%; background: <?php echo $healthColor; ?>;"></div>
                                </div>
                            </div>
                            <a href="/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="btn btn-secondary btn-sm ml-4">Apply →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
