<?php
/**
 * IdeaSync - Professional Dashboard
 */

if (!isLoggedIn()) {
    redirect(BASE_URL . '/?page=login');
}

$current_user = getCurrentUser();
if (!$current_user) {
    redirect(BASE_URL . '/?page=login');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="container">
            <div class="flex-between">
                <a href="<?php echo BASE_URL; ?>/" class="navbar-brand">IdeaSync</a>
                <nav class="navbar-menu" id="navMenu">
                    <a href="<?php echo BASE_URL; ?>/?page=ideas">Ideas</a>
                    <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="active">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/?page=messages">Messages</a>
                    <a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a>
                </nav>
                <div class="flex gap-4" style="align-items: center;">
                    <button style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">🔔</button>
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-accent-600); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                        <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout" style="font-size: 0.875rem; color: var(--color-accent-600);">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div style="background: var(--color-bg-secondary); min-height: calc(100vh - 80px); padding: 2rem 1rem;">
        <div class="container" style="max-width: 1280px;">

            <!-- WELCOME SECTION -->
            <div class="card card-accent" style="background: var(--gradient-primary); color: white; border: none; margin-bottom: 2rem; border-left: 4px solid white;">
                <div class="card-body">
                    <h1 style="color: white; margin-bottom: 0.5rem; font-size: 2rem;">Welcome back, <?php echo htmlspecialchars($current_user['name']); ?>! 👋</h1>
                    <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">Keep building amazing things with your team</p>
                </div>
            </div>

            <!-- QUICK STATS ROW -->
            <div class="grid grid-cols-4" style="margin-bottom: 2rem; gap: 1rem;">
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">💡</div>
                        <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Your Ideas</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-accent-600);">12</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">👥</div>
                        <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Active Teams</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-accent-600);">5</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📋</div>
                        <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Applications</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-accent-600);">8</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐</div>
                        <div style="font-size: 0.875rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Builder Rank</div>
                        <div style="font-size: 1.875rem; font-weight: 700; color: var(--color-accent-600);">Builder</div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
                <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary btn-lg" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none;">
                    <span style="font-size: 1.25rem;">💡</span> Post New Idea
                </a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-tertiary btn-lg" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none;">
                    <span style="font-size: 1.25rem;">🔍</span> Browse Ideas
                </a>
                <a href="<?php echo BASE_URL; ?>/?page=messages" class="btn btn-tertiary btn-lg" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none;">
                    <span style="font-size: 1.25rem;">💬</span> Team Channels
                </a>
            </div>

            <!-- MAIN GRID -->
            <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- LEFT COLUMN -->
                <div>
                    <!-- Recent Activity -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h2>My Active Collaborations</h2>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <!-- Collaboration Item -->
                                <div style="padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h4 style="margin-bottom: 0.25rem;">AI-Powered Study Platform</h4>
                                        <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin: 0;">3 members • 5 commits</p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <span class="badge badge-primary">Active</span>
                                    </div>
                                </div>
                                <div style="padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h4 style="margin-bottom: 0.25rem;">Campus Event Planner</h4>
                                        <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin: 0;">5 members • Team lead</p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <span class="badge badge-success">On Track</span>
                                    </div>
                                </div>
                                <div style="padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h4 style="margin-bottom: 0.25rem;">Green Energy Solution</h4>
                                        <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin: 0;">4 members • Seeking funding</p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <span class="badge badge-warning">Review</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Applications -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Pending Applications</h2>
                        </div>
                        <div class="card-body">
                            <p style="color: var(--color-text-secondary); text-align: center; padding: 2rem 0;">
                                You have 2 pending applications. <a href="<?php echo BASE_URL; ?>/?page=profile&action=applications" style="color: var(--color-accent-600);">View all →</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR -->
                <div>
                    <!-- Upcoming Events -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3>Upcoming Events</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div style="padding: 1rem; background: var(--color-bg-secondary); border-radius: var(--radius-base);">
                                    <p style="margin: 0; font-weight: 600; font-size: 0.875rem;">Hackathon 2024</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--color-text-secondary);">Mar 15, 2024</p>
                                </div>
                                <div style="padding: 1rem; background: var(--color-bg-secondary); border-radius: var(--radius-base);">
                                    <p style="margin: 0; font-weight: 600; font-size: 0.875rem;">Mentorship Roundtable</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--color-text-secondary);">Mar 20, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Builder Rank Progress -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Your Progress</h3>
                        </div>
                        <div class="card-body">
                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏗️</div>
                                <div style="font-weight: 700; font-size: 1.125rem; color: var(--color-text-primary);">Builder</div>
                                <p style="font-size: 0.875rem; color: var(--color-text-secondary); margin: 0.5rem 0 0 0;">70 / 100 points to ARCHITECT</p>
                            </div>
                            <div style="background: var(--color-bg-secondary); border-radius: var(--radius-full); height: 8px; overflow: hidden; margin-bottom: 1rem;">
                                <div style="background: var(--color-accent-600); height: 100%; width: 70%;"></div>
                            </div>
                            <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.875rem;">
                                <li style="padding: 0.25rem 0;">✓ 12 ideas posted</li>
                                <li style="padding: 0.25rem 0;">✓ 5 collaborations</li>
                                <li style="padding: 0.25rem 0;">○ Reach 100 upvotes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
