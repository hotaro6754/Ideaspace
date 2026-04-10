<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
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
        redirect(BASE_URL . '/?page=login');
    }

    $current_user = getCurrentUser();
    if (!$current_user) {
        redirect(BASE_URL . '/?page=login');
    }
    ?>

    <!-- Profile Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Profile Header -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem;">
                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; flex-shrink: 0;">
                    👤
                </div>
                <div style="flex: 1;">
                    <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($current_user['name']); ?>
                    </h1>
                    <p style="color: #6b7280; margin-bottom: 1rem; display: grid; gap: 0.5rem;">
                        <span>Roll: <?php echo htmlspecialchars($current_user['roll_number']); ?></span>
                        <span><?php echo htmlspecialchars($current_user['branch']); ?> • Year <?php echo htmlspecialchars($current_user['year']); ?></span>
                    </p>
                </div>
                <a href="#" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.25s ease;">
                    Edit Profile
                </a>
            </div>

            <!-- Profile Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Quick Links -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Quick Links</h2>
                    <div style="display: grid; gap: 1rem;">
                        <a href="<?php echo BASE_URL; ?>/?page=profile-applications" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem; text-decoration: none; transition: all 0.3s ease;" class="quick-link">
                            <div style="font-size: 1.5rem;">📝</div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">My Applications</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">View collaboration requests</div>
                            </div>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/?page=profile-collaborations" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem; text-decoration: none; transition: all 0.3s ease;" class="quick-link">
                            <div style="font-size: 1.5rem;">🤝</div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">My Collaborations</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">Teams you're working on</div>
                            </div>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/?page=leaderboard" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem; text-decoration: none; transition: all 0.3s ease;" class="quick-link">
                            <div style="font-size: 1.5rem;">🏆</div>
                            <div>
                                <div style="font-weight: 600; color: #111827;">Leaderboard</div>
                                <div style="color: #9ca3af; font-size: 0.875rem;">View top builders</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Skills Section -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Skills</h2>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #dbeafe; color: #1e40af; font-weight: 500;">Python</span>
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #dbeafe; color: #1e40af; font-weight: 500;">JavaScript</span>
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #dbeafe; color: #1e40af; font-weight: 500;">React</span>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem;">Add more skills to your profile</p>
                </div>
            </div>

            <!-- GitHub Stats -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">GitHub Stats</h2>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <span style="color: #6b7280;">Repositories</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                            <span style="color: #6b7280;">Followers</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #6b7280;">Languages</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #111827;">-</span>
                        </div>
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin-top: 1rem;">Link your GitHub to sync stats</p>
                </div>
            </div>

            <!-- Projects Section -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Recent Projects</h2>
                <div style="text-align: center; padding: 3rem; color: #9ca3af;">
                    <p style="font-size: 1rem;">No projects yet. Start collaborating!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #111827; color: white; padding: 2rem 0; text-align: center; border-top: 1px solid #374151; margin-top: 2rem;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <p style="margin: 0; font-size: 0.875rem;">© 2024 IdeaSync - Built for campus collaboration</p>
        </div>
    </footer>
</body>
</html>
