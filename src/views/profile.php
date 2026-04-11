<?php
/**
 * IdeaSync - Professional Builder Profile
 */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/GitHubAPI.php';

$user_id = (int)($_GET['id'] ?? $_SESSION['user_id'] ?? 0);
if ($user_id === 0) {
    header('Location: ' . BASE_URL . '/?page=login');
    exit();
}

$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: ' . BASE_URL . '/?page=ideas');
    exit();
}

// GitHub Data (Mocked or Fetched)
$github = null;
if ($user['github_username']) {
    try {
        $ghApi = new GitHubAPI();
        $github = $ghApi->getUserData($user['github_username']);
    } catch (Exception $e) {
        // Fallback
    }
}

function getTierName($tier) {
    return ['INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND'][$tier-1] ?? 'INITIATE';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name']); ?> Profile | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <header class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="logo">IDEASYNC</a>
            <div class="flex gap-4 items-center">
                <a href="/?page=feed" class="text-secondary text-sm">Feed</a>
                <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
            </div>
        </div>
    </header>

    <main class="container py-20">
        <div class="flex gap-12">
            <!-- Left: Stats & Info -->
            <div style="width: 300px;">
                <div class="text-center mb-8">
                    <div class="mx-auto mb-4" style="width: 96px; height: 96px; border-radius: 50%; background-color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; border: 4px solid var(--bg-secondary);">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <h2 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p class="text-muted text-sm mb-4"><?php echo $user['roll_number']; ?> • <?php echo $user['branch']; ?></p>
                    <span class="badge badge-accent"><?php echo getTierName($user['tier']); ?></span>
                </div>

                <div class="card mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xs uppercase tracking-widest text-muted">Builder Score</span>
                        <span class="text-accent font-bold"><?php echo $user['total_points']; ?></span>
                    </div>
                    <div class="health-bar-container">
                        <div class="health-bar-fill" style="width: <?php echo ($user['total_points'] % 200) / 2; ?>%; background-color: var(--accent);"></div>
                    </div>
                    <p class="text-[10px] text-muted mt-2"><?php echo 200 - ($user['total_points'] % 200); ?> points to next tier</p>
                </div>

                <div class="flex flex-col gap-2">
                    <button class="btn btn-secondary w-full">Edit Profile</button>
                    <button class="btn btn-ghost w-full">Share Profile</button>
                </div>
            </div>

            <!-- Right: Repos & Projects -->
            <div style="flex: 1;">
                <?php if ($github): ?>
                    <div class="mb-12">
                        <h3 class="mb-6 flex items-center gap-2">
                            <i data-lucide="github" size="20"></i>
                            GitHub Activity
                        </h3>
                        <div class="grid grid-2">
                            <?php foreach ($github['topRepos'] as $repo): ?>
                                <a href="<?php echo $repo['url']; ?>" target="_blank" class="card hover:border-accent">
                                    <div class="flex justify-between mb-2">
                                        <h4 class="text-sm font-bold"><?php echo htmlspecialchars($repo['name']); ?></h4>
                                        <div class="flex items-center gap-1 text-xs text-muted">
                                            <i data-lucide="star" size="12"></i>
                                            <?php echo $repo['stars']; ?>
                                        </div>
                                    </div>
                                    <p class="text-xs text-secondary mb-4 line-clamp-2"><?php echo htmlspecialchars($repo['description'] ?: 'No description'); ?></p>
                                    <div class="flex items-center gap-2">
                                        <span class="badge text-[10px]"><?php echo $repo['language']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-6">Top Languages</h3>
                        <div class="flex gap-4">
                            <?php foreach ($github['languages'] as $lang): ?>
                                <div class="flex-1">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span><?php echo $lang['lang']; ?></span>
                                        <span class="text-muted"><?php echo $lang['percentage']; ?>%</span>
                                    </div>
                                    <div class="health-bar-container" style="height: 4px;">
                                        <div class="health-bar-fill" style="width: <?php echo $lang['percentage']; ?>%; background-color: var(--accent);"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card p-20 text-center">
                        <i data-lucide="github" class="mx-auto mb-4 text-muted" size="48"></i>
                        <h3 class="mb-2">Connect GitHub</h3>
                        <p class="text-secondary mb-6">Sync your repositories to show off your building skills.</p>
                        <button class="btn btn-primary">Connect Account</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
