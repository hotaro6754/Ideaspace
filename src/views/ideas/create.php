<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post an Idea - IdeaSync</title>
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
                    <li><a href="<?php echo BASE_URL; ?>/?page=profile">Profile</a></li>
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

    <!-- Form Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" style="color: #3b82f6; text-decoration: none; font-weight: 500;">← Back to Ideas</a>
                <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 1rem 0 0.5rem 0;">Share Your Idea</h1>
                <p style="color: #6b7280;">Post an innovative project and find talented collaborators</p>
            </div>

            <!-- Error Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #ef4444; flex-shrink: 0; margin-top: 0.125rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span style="color: #991b1b; font-size: 0.95rem;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="<?php echo BASE_URL; ?>/src/controllers/ideas.php" style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 2rem;">
                <input type="hidden" name="action" value="create">

                <!-- Title -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">
                        Idea Title <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="title" placeholder="e.g., AI Chatbot for Campus Queries"
                           required minlength="10" maxlength="200"
                           style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;" />
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0 0;">Between 10 and 200 characters</p>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">
                        Description <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="description" placeholder="Describe your idea in detail. What problem does it solve? What's your vision?"
                              required minlength="50" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease; resize: vertical; min-height: 150px;"></textarea>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0 0;">Minimum 50 characters. Be descriptive!</p>
                </div>

                <!-- Domain -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">
                        Domain <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="domain" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;">
                        <option value="">Select a domain</option>
                        <option value="AI/ML">AI/ML</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Mobile Development">Mobile Development</option>
                        <option value="Cybersecurity">Cybersecurity</option>
                        <option value="Cloud Computing">Cloud Computing</option>
                        <option value="Data Science">Data Science</option>
                        <option value="IoT">IoT</option>
                        <option value="Blockchain">Blockchain</option>
                        <option value="Game Development">Game Development</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Skills Needed -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">
                        Skills Needed <span style="color: #ef4444;">*</span>
                    </label>
                    <div id="skills-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" class="skill-input" placeholder="e.g., Python"
                                   style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;" />
                            <button type="button" onclick="addSkillField()" style="padding: 0.75rem 1rem; background: #f3f4f6; color: #111827; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                                + Add
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="skills_needed" id="skills-hidden" value="[]">
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0 0;">Add at least one skill</p>
                </div>

                <!-- Team Size -->
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">
                        Team Size Needed <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="team_size" min="1" max="10" value="2" required
                           style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;" />
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0 0;">How many team members are you looking for?</p>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                    <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                        Post Idea
                    </button>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" style="flex: 1; padding: 0.75rem 1.5rem; background: #f3f4f6; color: #111827; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        Cancel
                    </a>
                </div>
            </form>

            <!-- Tips -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.75rem; padding: 1.5rem; margin-top: 2rem;">
                <h3 style="color: #1e40af; font-weight: 600; margin-bottom: 1rem;">💡 Tips for a Great Idea Post</h3>
                <ul style="color: #1e40af; margin: 0; padding-left: 1.25rem;">
                    <li style="margin-bottom: 0.5rem;">Be clear and specific about what you want to build</li>
                    <li style="margin-bottom: 0.5rem;">Include technical requirements and technologies</li>
                    <li style="margin-bottom: 0.5rem;">Explain the impact or problem your idea solves</li>
                    <li style="margin-bottom: 0.5rem;">Be realistic about timeline and scope</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #111827; color: white; padding: 2rem 0; text-align: center; border-top: 1px solid #374151; margin-top: 2rem;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <p style="margin: 0; font-size: 0.875rem;">© 2024 IdeaSync - Built for campus collaboration</p>
        </div>
    </footer>

    <script>
        function addSkillField() {
            const container = document.getElementById('skills-container');
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.gap = '0.5rem';
            div.innerHTML = `
                <input type="text" class="skill-input" placeholder="e.g., React"
                       style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;" />
                <button type="button" onclick="removeSkillField(this)" style="padding: 0.75rem 1rem; background: #fee2e2; color: #991b1b; border: 2px solid #fecaca; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }

        function removeSkillField(btn) {
            btn.parentElement.remove();
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const skills = Array.from(document.querySelectorAll('.skill-input'))
                .map(input => input.value.trim())
                .filter(skill => skill.length > 0);

            if (skills.length === 0) {
                e.preventDefault();
                alert('Please add at least one skill');
                return;
            }

            document.getElementById('skills-hidden').value = JSON.stringify(skills);
        });
    </script>

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

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        ul {
            list-style-type: disc;
        }

        li {
            margin-bottom: 0.5rem;
        }
    </style>
</body>
</html>
