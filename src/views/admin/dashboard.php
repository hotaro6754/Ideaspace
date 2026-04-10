<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- Navigation Header -->
    <header>
        <nav>
            <a href="<?php echo BASE_URL; ?>/?page=home" class="logo">IdeaSync</a>
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>/?page=home">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=dashboard">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/?page=admin" class="active">Admin</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout">Logout</a></li>
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

    <!-- Admin Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 2rem;">Admin Dashboard</h1>

            <!-- Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</p>
                    <p style="font-size: 2rem; font-weight: 700; color: #111827;">247</p>
                </div>
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Ideas</p>
                    <p style="font-size: 2rem; font-weight: 700; color: #111827;">34</p>
                </div>
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Collaborations</p>
                    <p style="font-size: 2rem; font-weight: 700; color: #111827;">18</p>
                </div>
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Completed Projects</p>
                    <p style="font-size: 2rem; font-weight: 700; color: #111827;">5</p>
                </div>
            </div>

            <!-- Management Sections -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                <!-- User Management -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">👥</div>
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">User Management</h3>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage user accounts</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=admin-users" class="btn btn-primary" style="margin-top: auto; width: 100%; text-align: center;">View Users</a>
                </div>

                <!-- Reports & Analytics -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">📊</div>
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">Reports</h3>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">View analytics & metrics</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=admin-reports" class="btn btn-primary" style="margin-top: auto; width: 100%; text-align: center;">View Reports</a>
                </div>

                <!-- Moderation -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">⚖️</div>
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin: 0;">Moderation</h3>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Review flagged content</p>
                        </div>
                    </div>
                    <button class="btn btn-secondary" style="margin-top: auto; width: 100%; text-align: center;">Coming Soon</button>
                </div>
            </div>

            <!-- Management Sections -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <!-- Recent Ideas -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Recent Ideas</h2>
                    <div style="space: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem; border-left: 3px solid #3b82f6;">
                            <p style="font-weight: 600; color: #111827; margin: 0; margin-bottom: 0.25rem;">AI Chatbot Builder</p>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">By John Doe • 2 days ago</p>
                        </div>
                        <div style="padding: 1rem; background: #f9fafb; border-radius: 0.5rem; border-left: 3px solid #3b82f6;">
                            <p style="font-weight: 600; color: #111827; margin: 0; margin-bottom: 0.25rem;">Campus Marketplace</p>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">By Jane Smith • 5 days ago</p>
                        </div>
                    </div>
                </div>

                <!-- Flagged Content -->
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Flagged Items</h2>
                    <div style="text-align: center; padding: 2rem; color: #9ca3af;">
                        <p style="margin: 0;">No flagged items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #111827; color: white; padding: 2rem 0; text-align: center; border-top: 1px solid #374151; margin-top: 2rem;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <p style="margin: 0; font-size: 0.875rem;">© 2024 IdeaSync Admin Panel</p>
        </div>
    </footer>

    <style>
        body {
            background: #f9fafb;
        }

        h1, h2 {
            margin: 0;
        }

        p {
            margin: 0;
        }
    </style>
</body>
</html>
