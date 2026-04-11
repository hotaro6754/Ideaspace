<?php
/**
 * IdeaSync - Idea Detail Page
 */

require_once __DIR__ . '/../../models/Idea.php';

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id === 0) {
    redirect(BASE_URL . '/?page=ideas');
}

$db = new Database();
$conn = $db->connect();
$ideaModel = new Idea($conn);
$idea = $ideaModel->getById($idea_id);

if (!$idea) {
    http_response_code(404);
    redirect(BASE_URL . '/?page=ideas');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($idea['title']); ?> - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body style="background: var(--color-bg-secondary);">
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
                    <?php endif; ?>
                </nav>
                <div class="flex gap-4" style="align-items: center;">
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-ghost btn-sm" style="color: var(--color-accent-600);">← Back to Ideas</a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div style="padding: 2rem 1rem; min-height: calc(100vh - 80px);">
        <div class="container" style="max-width: 900px;">

            <!-- IDEA CARD -->
            <div class="card" style="margin-bottom: 2rem; box-shadow: var(--shadow-lg);">
                <div class="card-body">
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                        <div style="flex: 1;">
                            <!-- Badges -->
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                                <span class="badge badge-primary"><?php echo htmlspecialchars($idea['domain']); ?></span>
                                <span class="badge badge-gray" style="text-transform: capitalize;"><?php echo htmlspecialchars($idea['status'] ?? 'open'); ?></span>
                            </div>

                            <!-- Title -->
                            <h1 style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--color-text-primary);"><?php echo htmlspecialchars($idea['title']); ?></h1>

                            <!-- Creator -->
                            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--color-text-secondary);">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-accent-600); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    <?php echo strtoupper(substr($idea['creator_name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--color-text-primary);"><?php echo htmlspecialchars($idea['creator_name']); ?></div>
                                    <div style="font-size: 0.875rem;">Posted <?php echo date('M j, Y', strtotime($idea['created_at'])); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                            <button class="btn btn-primary btn-lg" style="margin: 0;">⭐ Upvote</button>
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-secondary btn-lg" style="margin: 0;">📋 Apply</button>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-secondary btn-lg" style="margin: 0; text-decoration: none;">📋 Apply</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding: 1rem 0; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Upvotes</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-accent-600);"><?php echo $idea['upvotes'] ?? 0; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Applicants</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-accent-600);"><?php echo $idea['application_count'] ?? 0; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Comments</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-accent-600);"><?php echo $idea['comment_count'] ?? 0; ?></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div style="margin-bottom: 2rem;">
                        <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-text-primary);">About This Idea</h2>
                        <p style="color: var(--color-text-secondary); line-height: 1.7; font-size: 1rem;">
                            <?php echo nl2br(htmlspecialchars($idea['description'])); ?>
                        </p>
                    </div>

                    <!-- Requirements -->
                    <?php if ($idea['required_skills']): ?>
                        <div style="margin-bottom: 2rem;">
                            <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-text-primary);">Required Skills</h2>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                <?php foreach (explode(',', $idea['required_skills']) as $skill): ?>
                                    <span class="badge badge-primary"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Timeline -->
                    <?php if ($idea['timeline']): ?>
                        <div style="margin-bottom: 2rem;">
                            <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-text-primary);">Timeline</h2>
                            <p style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($idea['timeline']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- COMMENTS SECTION -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h2>Comments & Discussion</h2>
                </div>
                <div class="card-body">
                    <p style="color: var(--color-text-secondary); text-align: center; padding: 2rem 0;">
                        💬 Be the first to comment on this idea!
                    </p>

                    <?php if (isLoggedIn()): ?>
                        <div style="padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                            <h3 style="margin-bottom: 1rem;">Add a Comment</h3>
                            <form style="display: flex; flex-direction: column; gap: 1rem;">
                                <textarea class="form-input" placeholder="Share your thoughts or ask questions..." style="min-height: 100px;"></textarea>
                                <button type="submit" class="btn btn-primary" style="align-self: flex-start;">Post Comment</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div style="padding-top: 1.5rem; border-top: 1px solid var(--color-border); text-align: center;">
                            <p style="color: var(--color-text-secondary); margin-bottom: 1rem;">Sign in to comment on this idea</p>
                            <a href="<?php echo BASE_URL; ?>/?page=login" class="btn btn-primary">Sign In</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
