<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IdeaSync - Campus Collaboration Platform</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- Navigation Header -->
    <header>
        <nav>
            <a href="<?php echo BASE_URL; ?>/?page=home" class="logo">IdeaSync</a>
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>/?page=home" class="active">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/?page=ideas">Ideas</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=dashboard">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=login">Sign In</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/?page=register">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="section bg-gradient-to-br from-blue-50 via-purple-50 to-white">
        <div class="container">
            <div class="grid grid-2">
                <div class="flex flex-center pt-8">
                    <div>
                        <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 2rem; color: #111827;">
                            Connect Ideas with Talent
                        </h1>
                        <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 2rem; line-height: 1.8;">
                            Join the campus collaboration platform where Visionaries meet Builders. Post your innovative ideas and find the perfect team to bring them to life.
                        </p>
                        <div class="flex gap-4">
                            <?php if (isLoggedIn()): ?>
                                <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="btn btn-primary btn-lg">
                                    Go to Dashboard
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-lg">
                                    Get Started
                                </a>
                                <a href="<?php echo BASE_URL; ?>/?page=login" class="btn btn-ghost btn-lg">
                                    Sign In
                                </a>
                            <?php endif; ?>
                        </div>
                        <p style="margin-top: 2rem; font-size: 0.875rem; color: #9ca3af;">
                            ✨ Join 100+ college students building together
                        </p>
                    </div>
                </div>
                <div class="flex flex-center">
                    <div style="width: 100%; max-width: 500px; height: 400px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        💡
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 4rem;">How It Works</h2>
            <div class="grid grid-3">
                <!-- Feature 1 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔍</div>
                    <h3>Post Your Idea</h3>
                    <p>Share your innovative project idea with the entire campus. Define the skills you need and what you're building.</p>
                </div>

                <!-- Feature 2 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🤝</div>
                    <h3>Find Collaborators</h3>
                    <p>Browse talented students ready to collaborate. Filter by skills, interests, and track records.</p>
                </div>

                <!-- Feature 3 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🚀</div>
                    <h3>Build Together</h3>
                    <p>Team up with builders and bring your ideas to life. Track progress and celebrate wins together.</p>
                </div>

                <!-- Feature 4 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">⭐</div>
                    <h3>GitHub Integration</h3>
                    <p>Showcase your repositories and contributions. Build credibility through your coding projects.</p>
                </div>

                <!-- Feature 5 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🏆</div>
                    <h3>Builder Rank</h3>
                    <p>Earn points and unlock ranks: INITIATE → BUILDER → ARCHITECT → LEGEND</p>
                </div>

                <!-- Feature 6 -->
                <div class="card">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                    <h3>Analytics</h3>
                    <p>Track your profile growth, collaboration stats, and project completions in your dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section bg-gradient-to-r from-blue-500 to-purple-500">
        <div class="container text-center">
            <h2 style="color: white; margin-bottom: 2rem; font-size: 2rem;">Ready to Start Collaborating?</h2>
            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 2rem; font-size: 1.125rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Join hundreds of students from Lendi Institute of Engineering & Technology who are already building amazing projects together.
            </p>
            <?php if (!isLoggedIn()): ?>
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn btn-primary btn-lg" style="background: white; color: #3b82f6; border: none;">
                    Create Your Account
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn btn-primary btn-lg" style="background: white; color: #3b82f6; border: none;">
                    Explore Ideas Now
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #111827; color: white; padding: 3rem 0; text-align: center; border-top: 1px solid #374151;">
        <div class="container">
            <p style="margin-bottom: 1rem;">© 2024 IdeaSync - Campus Collaboration Platform. Lendi Institute of Engineering & Technology.</p>
            <div style="display: flex; gap: 2rem; justify-content: center; margin-bottom: 2rem;">
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 0.875rem;">Home</a>
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 0.875rem;">About</a>
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 0.875rem;">Terms</a>
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 0.875rem;">Privacy</a>
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 0.875rem;">Contact</a>
            </div>
            <p style="color: #6b7280; font-size: 0.875rem;">Built with ❤️ for campus collaboration</p>
        </div>
    </footer>

    <style>
        body {
            background: #ffffff;
            color: #111827;
        }

        .section {
            padding: 4rem 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .grid {
            display: grid;
            gap: 2rem;
            margin: 0;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .flex {
            display: flex;
            align-items: center;
        }

        .flex-center {
            justify-content: center;
        }

        .gap-4 {
            gap: 1rem;
        }

        .pt-8 {
            padding-top: 2rem;
        }

        h1 {
            font-weight: 700;
        }

        h2 {
            font-weight: 700;
            color: #111827;
        }

        p {
            margin: 0;
        }

        .bg-gradient-to-br {
            background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
        }

        .from-blue-50 {
            --tw-gradient-from: #eff6ff;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0));
        }

        .via-purple-50 {
            --tw-gradient-via: #faf5ff;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-via), var(--tw-gradient-to, rgba(250, 245, 255, 0));
        }

        .to-white {
            --tw-gradient-to: white;
        }

        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }

        .from-blue-500 {
            --tw-gradient-from: #3b82f6;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(59, 130, 246, 0));
        }

        .to-purple-500 {
            --tw-gradient-to: #8b5cf6;
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
            border-color: #dbeafe;
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

        .btn-ghost {
            background: transparent;
            color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-ghost:hover {
            background: #dbeafe;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }
    </style>
</body>
</html>
