<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IdeaSync</title>
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
                <li><a href="<?php echo BASE_URL; ?>/?page=dashboard" class="active">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a></li>
                <li><a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout">Logout</a></li>
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

    <!-- Dashboard Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container">
            <!-- Welcome Section -->
            <div style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem;">
                <h1 style="color: white; margin-bottom: 0.5rem;">Welcome back, <?php echo htmlspecialchars($current_user['name']); ?>! 👋</h1>
                <p style="color: rgba(255, 255, 255, 0.9); margin: 0;">
                    Make progress on your ideas and collaborations
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-3" style="margin-bottom: 2rem;">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">💡</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">Post an Idea</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Share your innovative project</p>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Create Idea
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔍</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">Browse Ideas</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Find projects to collaborate on</p>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View Ideas
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">👤</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">My Profile</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Update your skills and info</p>
                    <a href="<?php echo BASE_URL; ?>/?page=profile" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        Edit Profile
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📝</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">My Applications</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">View collaboration requests</p>
                    <a href="<?php echo BASE_URL; ?>/?page=profile-applications" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🤝</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">My Collaborations</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Teams you're working on</p>
                    <a href="<?php echo BASE_URL; ?>/?page=profile-collaborations" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🏆</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">Leaderboard</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">View top builders and visionaries</p>
                    <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">💬</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">Messages</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Direct messaging with collaborators</p>
                    <a href="<?php echo BASE_URL; ?>/?page=messages" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View
                    </a>
                </div>

                <div class="card" style="text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔔</div>
                    <h3 style="color: #111827; margin-bottom: 1rem;">Notifications</h3>
                    <p style="color: #6b7280; margin: 0; margin-bottom: 1rem;">Stay updated with activities</p>
                    <a href="<?php echo BASE_URL; ?>/?page=notifications" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        View
                    </a>
                </div>
            </div>

            <!-- User Info Section -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem; margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem; color: #111827;">Your Information</h2>
                <div class="grid grid-2">
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Roll Number</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #111827;"><?php echo htmlspecialchars($current_user['roll_number']); ?></p>
                    </div>
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Email</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #111827;"><?php echo htmlspecialchars($current_user['email']); ?></p>
                    </div>
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Branch</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #111827;"><?php echo htmlspecialchars($current_user['branch']); ?></p>
                    </div>
                    <div>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem;">Year</p>
                        <p style="font-size: 1.125rem; font-weight: 600; color: #111827;">Year <?php echo htmlspecialchars($current_user['year']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Activity Section -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                <h2 style="margin-bottom: 1.5rem; color: #111827;">Recent Activity</h2>
                <div style="text-align: center; padding: 2rem; color: #9ca3af;">
                    <p style="font-size: 1rem;">No activity yet. Start by posting an idea or browsing projects!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #111827; color: white; padding: 2rem 0; text-align: center; border-top: 1px solid #374151; margin-top: 2rem;">
        <div class="container">
            <p style="margin: 0; font-size: 0.875rem;">© 2024 IdeaSync - Built for campus collaboration</p>
        </div>
    </footer>

    <style>
        body {
            background: #f9fafb;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.25s ease-out;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        h1, h2, h3 {
            margin: 0;
        }

        p {
            margin: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.25s ease-out;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #111827;
            border-color: #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            border-color: #3b82f6;
        }
    </style>
</body>
</html>
