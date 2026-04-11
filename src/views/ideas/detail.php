<?php
/**
 * IdeaSync - Idea Detail Page
 */

require_once __DIR__ . '/../../models/Idea.php';
require_once __DIR__ . '/../../models/IdeaRecommendation.php';

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id === 0) {
    redirect(BASE_URL . '/?page=ideas');
}

$db = new Database();
$conn = $db->connect();
$ideaModel = new Idea($conn);
$recommender = new IdeaRecommendation($conn);
$idea = $ideaModel->getById($idea_id);

if (!$idea) {
    http_response_code(404);
    redirect(BASE_URL . '/?page=ideas');
}

// Get recommendations
$perfect_team = [];
$similar_ideas = [];
try {
    $perfect_team = $recommender->findPerfectTeam($idea_id, 5);
    $similar_ideas = $recommender->getSimilarIdeas($idea_id, 5);
    $is_trending = $recommender->isTrending($idea_id);
} catch (Exception $e) {
    error_log("Recommendation error: " . $e->getMessage());
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

            <!-- TRENDING BADGE SECTION -->
            <?php if ($is_trending): ?>
            <div style="background: linear-gradient(135deg, #ff6b6b, #ff8a65); color: white; padding: 1rem; border-radius: var(--radius-lg); margin-bottom: 2rem; text-align: center;">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔥 This Idea is Trending!</div>
                <div style="font-size: 0.875rem; opacity: 0.95;">This idea is gaining serious attention from the community</div>
            </div>
            <?php endif; ?>

            <!-- PERFECT TEAM SECTION -->
            <?php if (!empty($perfect_team)): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h2>👥 Perfect Builders for This Idea</h2>
                    <p style="color: var(--color-text-secondary); margin-top: 0.5rem; font-size: 0.875rem;">These builders have the skills you need for this project</p>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                        <?php foreach ($perfect_team as $builder): ?>
                        <div style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 1rem; text-align: center;">
                            <!-- Avatar -->
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--color-accent-600); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.25rem; margin: 0 auto 0.75rem;">
                                <?php echo strtoupper(substr($builder['name'], 0, 1)); ?>
                            </div>

                            <!-- Name & Rank -->
                            <h4 style="margin: 0 0 0.25rem 0; font-size: 0.95rem;"><?php echo htmlspecialchars($builder['name']); ?></h4>
                            <p style="color: var(--color-text-secondary); font-size: 0.8rem; margin: 0 0 0.5rem 0;">
                                ⭐ <?php echo htmlspecialchars($builder['rank'] ?? 'Member'); ?> • <?php echo $builder['projects_completed']; ?> projects
                            </p>

                            <!-- Skill Match -->
                            <div style="background: var(--color-success-100); color: var(--color-success-700); padding: 0.5rem; border-radius: var(--radius-md); margin-bottom: 0.75rem; font-weight: 600; font-size: 0.875rem;">
                                <?php echo $builder['match_percentage']; ?>% Skill Match
                            </div>

                            <!-- Rating -->
                            <?php if ($builder['team_rating'] > 0): ?>
                            <p style="color: var(--color-text-secondary); font-size: 0.8rem; margin: 0 0 0.75rem 0;">
                                ★ <?php echo number_format($builder['team_rating'], 1); ?>/5.0
                            </p>
                            <?php endif; ?>

                            <!-- View Profile Button -->
                            <a href="<?php echo BASE_URL; ?>/?page=profile&id=<?php echo $builder['id']; ?>" class="btn btn-primary btn-sm" style="width: 100%;">View Profile</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SIMILAR IDEAS SECTION -->
            <?php if (!empty($similar_ideas)): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h2>Similar Ideas in <?php echo htmlspecialchars($idea['domain']); ?></h2>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($similar_ideas as $similar): ?>
                        <div style="border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 1rem; transition: all 0.2s;">
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem;">
                                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $similar['id']; ?>" style="text-decoration: none; color: inherit; hover:color:var(--color-accent-600);">
                                    <?php echo htmlspecialchars(substr($similar['title'], 0, 35)); ?>...
                                </a>
                            </h4>
                            <p style="color: var(--color-text-secondary); font-size: 0.75rem; margin: 0.5rem 0;">
                                👤 <?php echo htmlspecialchars($similar['creator_name']); ?> • ⭐ <?php echo htmlspecialchars($similar['creator_rank'] ?? 'Member'); ?>
                            </p>
                            <p style="color: var(--color-text-secondary); font-size: 0.75rem; margin: 0 0 0.75rem 0;">
                                ⬆️ <?php echo $similar['upvotes']; ?> upvotes • 👥 <?php echo $similar['applicant_count']; ?> applied
                            </p>
                            <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $similar['id']; ?>" class="btn btn-tertiary btn-sm" style="width: 100%;">View</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
