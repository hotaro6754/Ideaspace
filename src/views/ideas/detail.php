<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idea Details - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body>
    <!-- Navigation Header -->
    <header>
        <nav>
            <a href="<?php echo BASE_URL; ?>/?page=home" class="logo">IdeaSync</a>
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>/?page=home">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/?page=ideas" class="active">Ideas</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=dashboard">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>/?page=login">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Detail Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Back Button -->
            <a href="<?php echo BASE_URL; ?>/?page=ideas" style="color: #3b82f6; text-decoration: none; font-weight: 500; margin-bottom: 2rem; display: inline-block;">← Back to Ideas</a>

            <!-- Idea Card -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem; margin-bottom: 2rem;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: #dbeafe; color: #1e40af;">AI/ML</span>
                            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: #dcfce7; color: #166534;">Open</span>
                        </div>
                        <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">AI Chatbot Builder</h1>
                        <p style="color: #6b7280;">Posted by John Doe • 2 days ago</p>
                    </div>
                    <button style="background: none; border: none; font-size: 2rem; cursor: pointer;">⭐</button>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Description</h2>
                    <p style="color: #6b7280; line-height: 1.8; margin: 0;">
                        Create a specialized chatbot for campus queries using NLP. The bot should be able to answer questions about courses, faculty, club activities, and general campus information. Build a web interface for students to interact with the chatbot and an admin panel to manage responses.
                    </p>
                </div>

                <!-- Skills & Requirements -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #e5e7eb;">
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Skills Needed</h3>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #f3f4f6; color: #6b7280;">Python</span>
                            <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #f3f4f6; color: #6b7280;">NLP</span>
                            <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.875rem; background: #f3f4f6; color: #6b7280;">React</span>
                        </div>
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Looking for</h3>
                        <p style="color: #6b7280; margin: 0;">2-3 developers with NLP experience</p>
                    </div>
                </div>

                <!-- Actions -->
                <?php if (isLoggedIn()): ?>
                    <div style="display: flex; gap: 1rem;">
                        <button style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                            Apply to Collaborate
                        </button>
                        <button style="flex: 1; padding: 0.75rem 1.5rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                            Message Author
                        </button>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #6b7280;">
                        <a href="<?php echo BASE_URL; ?>/?page=login" style="color: #3b82f6; text-decoration: none;">Sign in</a> to collaborate on this idea
                    </p>
                <?php endif; ?>
            </div>

            <!-- Applicants Section -->
            <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem;">Applicants (3)</h2>
                <div style="display: grid; gap: 1rem;">
                    <!-- Applicant -->
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.75rem;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 50%; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <p style="font-weight: 600; color: #111827; margin: 0;">Jane Smith</p>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">3rd Year CSE • Python, React</p>
                        </div>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; background: #dcfce7; color: #166534;">Accepted</span>
                    </div>
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

    <style>
        body {
            background: #f9fafb;
        }

        h1, h2, h3 {
            margin: 0;
        }

        p {
            margin: 0;
        }
    </style>
</body>
</html>
