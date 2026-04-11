<?php
/**
 * IdeaSync - Ideas Marketplace
 */

require_once __DIR__ . '/../../models/Idea.php';

$db = new Database();
$conn = $db->connect();
$ideaModel = new Idea($conn);

// Get filter parameters
$domain = $_GET['domain'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = (int)($_GET['p'] ?? 1);
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build filters
$filters = [];
if (!empty($domain)) $filters['domain'] = $domain;
if (!empty($status)) $filters['status'] = $status;
if (!empty($search)) $filters['search'] = $search;

// Fetch ideas
$ideas = $ideaModel->getAll($per_page, $offset, $filters);
$total = $ideaModel->getTotal($filters);
$total_pages = ceil($total / $per_page);

// Domain colors
$domain_colors = [
    'Technology' => '#06B6D4',
    'Business' => '#10B981',
    'Health' => '#EC4899',
    'Education' => '#8B5CF6',
    'Environment' => '#14B8A6',
    'Other' => '#64748B'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <a href="<?php echo BASE_URL; ?>/" class="navbar-brand">IdeaSync</a>
                <nav class="navbar-menu" id="navMenu">
                    <a href="<?php echo BASE_URL; ?>/">Home</a>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="active">Ideas</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=dashboard">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a>
                    <?php endif; ?>
                </nav>
                <div class="flex gap-4" style="align-items: center;">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary btn-sm">+ Post Idea</a>
                        <a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout" class="btn btn-ghost btn-sm">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/?page=login" class="btn btn-ghost btn-sm">Sign In</a>
                        <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-sm">Join</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div style="background: var(--color-bg-secondary); min-height: calc(100vh - 80px); padding: 2rem 1rem;">
        <div class="container" style="max-width: 1280px;">

            <!-- HEADER -->
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Ideas Marketplace</h1>
                <p style="color: var(--color-text-secondary); margin: 0;">Browse <?php echo $total; ?> innovative ideas from your campus</p>
            </div>

            <!-- SEARCH & FILTERS -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <form method="GET" style="display: grid; gap: 1rem;">
                        <input type="hidden" name="page" value="ideas">

                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; align-items: end;">
                            <!-- Search -->
                            <div>
                                <label class="form-label">Search Ideas</label>
                                <input type="text" name="search" class="form-input" placeholder="Search by title or description..." value="<?php echo htmlspecialchars($search); ?>" style="margin-bottom: 0;">
                            </div>

                            <!-- Domain Filter -->
                            <div>
                                <label class="form-label">Domain</label>
                                <select name="domain" class="form-input" style="margin-bottom: 0;">
                                    <option value="">All Domains</option>
                                    <option value="Technology" <?php echo $domain === 'Technology' ? 'selected' : ''; ?>>Technology</option>
                                    <option value="Business" <?php echo $domain === 'Business' ? 'selected' : ''; ?>>Business</option>
                                    <option value="Health" <?php echo $domain === 'Health' ? 'selected' : ''; ?>>Health</option>
                                    <option value="Education" <?php echo $domain === 'Education' ? 'selected' : ''; ?>>Education</option>
                                    <option value="Environment" <?php echo $domain === 'Environment' ? 'selected' : ''; ?>>Environment</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="form-label">Status</label>
                                <select name="status" class="form-input" style="margin-bottom: 0;">
                                    <option value="">All Status</option>
                                    <option value="open" <?php echo $status === 'open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div>
                                <button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 0;">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- IDEAS GRID -->
            <?php if (!empty($ideas)): ?>
                <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <?php foreach ($ideas as $idea): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="card" style="height: 100%; transition: all var(--transition-base); cursor: pointer;">
                                <div class="card-body">
                                    <!-- Domain Badge -->
                                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;">
                                        <span class="badge" style="background-color: <?php echo $domain_colors[$idea['domain']] ?? '#64748B'; ?>25; color: <?php echo $domain_colors[$idea['domain']] ?? '#64748B'; ?>; border: 1px solid <?php echo $domain_colors[$idea['domain']] ?? '#64748B'; ?>33;">
                                            <?php echo htmlspecialchars($idea['domain']); ?>
                                        </span>
                                        <span class="badge badge-gray" style="text-transform: capitalize;"><?php echo htmlspecialchars($idea['status'] ?? 'open'); ?></span>
                                    </div>

                                    <!-- Title -->
                                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--color-text-primary); line-height: 1.4;">
                                        <?php echo htmlspecialchars(substr($idea['title'], 0, 50)); ?>
                                    </h3>

                                    <!-- Description -->
                                    <p style="color: var(--color-text-secondary); font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.5;">
                                        <?php echo htmlspecialchars(substr($idea['description'], 0, 100)); ?>...
                                    </p>

                                    <!-- Creator -->
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--color-accent-600); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                            <?php echo strtoupper(substr($idea['creator_name'] ?? 'U', 0, 1)); ?>
                                        </div>
                                        <span style="color: var(--color-text-secondary);">By <?php echo htmlspecialchars(substr($idea['creator_name'], 0, 20)); ?></span>
                                    </div>

                                    <!-- Stats -->
                                    <div style="display: flex; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border); color: var(--color-text-secondary); font-size: 0.875rem;">
                                        <span>⭐ <?php echo $idea['upvotes'] ?? 0; ?> upvotes</span>
                                        <span>👥 <?php echo $idea['application_count'] ?? 0; ?> applicants</span>
                                        <span>💬 <?php echo $idea['comment_count'] ?? 0; ?> comments</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination" style="margin-bottom: 2rem;">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo BASE_URL; ?>/?page=ideas&search=<?php echo urlencode($search); ?>&domain=<?php echo urlencode($domain); ?>&status=<?php echo urlencode($status); ?>&p=<?php echo $page - 1; ?>">← Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/?page=ideas&search=<?php echo urlencode($search); ?>&domain=<?php echo urlencode($domain); ?>&status=<?php echo urlencode($status); ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="<?php echo BASE_URL; ?>/?page=ideas&search=<?php echo urlencode($search); ?>&domain=<?php echo urlencode($domain); ?>&status=<?php echo urlencode($status); ?>&p=<?php echo $page + 1; ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <p style="font-size: 1.5rem; margin-bottom: 1rem;">📭</p>
                    <h2>No ideas found</h2>
                    <p style="color: var(--color-text-secondary); margin-bottom: 1.5rem;">Try adjusting your filters or search terms</p>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary">Post the First Idea</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary">Create Account to Post</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
