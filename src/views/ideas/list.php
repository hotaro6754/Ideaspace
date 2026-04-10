<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas - IdeaSync</title>
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

    <?php
    // Load Idea model
    require_once __DIR__ . '/../../models/Idea.php';

    $db = new Database();
    $conn = $db->connect();
    $ideaModel = new Idea($conn);

    // Get filter parameters
    $domain = $_GET['domain'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';

    // Build filters array
    $filters = [];
    if (!empty($domain)) $filters['domain'] = $domain;
    if (!empty($status)) $filters['status'] = $status;
    if (!empty($search)) $filters['search'] = $search;

    // Fetch ideas from database
    $ideas = $ideaModel->getAll(20, 0, $filters);

    // Color mapping for domains
    $domain_colors = [
        'AI/ML' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'Web Development' => ['bg' => '#dcfce7', 'text' => '#065f46'],
        'Mobile Development' => ['bg' => '#fce7f3', 'text' => '#831843'],
        'Cybersecurity' => ['bg' => '#fef3c7', 'text' => '#92400e'],
        'Cloud Computing' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
        'Data Science' => ['bg' => '#f0fdf4', 'text' => '#166534'],
        'IoT' => ['bg' => '#fef2f2', 'text' => '#7c2d12'],
        'Blockchain' => ['bg' => '#fef3c7', 'text' => '#78350f'],
        'Game Development' => ['bg' => '#f3e8ff', 'text' => '#581c87'],
        'Other' => ['bg' => '#f3f4f6', 'text' => '#374151']
    ];

    function getColorForDomain($domain) {
        global $domain_colors;
        return $domain_colors[$domain] ?? $domain_colors['Other'];
    }
    ?>

    <!-- Ideas Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">Explore Ideas</h1>
                    <p style="color: #6b7280;">Discover innovative projects and find teams to build with</p>
                </div>
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn btn-primary" style="gap: 0.5rem; display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border: none; text-decoration: none;">
                        <span>+</span> Post Idea
                    </a>
                <?php endif; ?>
            </div>

            <!-- Filters Section -->
            <form method="GET" style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 2rem;">
                <input type="hidden" name="page" value="ideas">
                <h3 style="margin-bottom: 1rem; color: #111827;">Filter Ideas</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <!-- Search -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">Search</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search ideas..."
                               style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;">
                    </div>

                    <!-- Domain Filter -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">Domain</label>
                        <select name="domain" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;">
                            <option value="">All Domains</option>
                            <option value="AI/ML" <?php echo ($domain === 'AI/ML') ? 'selected' : ''; ?>>AI/ML</option>
                            <option value="Web Development" <?php echo ($domain === 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                            <option value="Mobile Development" <?php echo ($domain === 'Mobile Development') ? 'selected' : ''; ?>>Mobile Development</option>
                            <option value="Cybersecurity" <?php echo ($domain === 'Cybersecurity') ? 'selected' : ''; ?>>Cybersecurity</option>
                            <option value="Cloud Computing" <?php echo ($domain === 'Cloud Computing') ? 'selected' : ''; ?>>Cloud Computing</option>
                            <option value="Data Science" <?php echo ($domain === 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                            <option value="Other" <?php echo ($domain === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #111827; font-size: 0.95rem;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 0.75rem; font-family: inherit; font-size: 1rem; color: #111827; background: white; transition: all 0.25s ease;">
                            <option value="">All Status</option>
                            <option value="open" <?php echo ($status === 'open') ? 'selected' : ''; ?>>Open</option>
                            <option value="in_progress" <?php echo ($status === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo ($status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;">
                        Filter
                    </button>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" style="padding: 0.75rem 1.5rem; background: #f3f4f6; color: #111827; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; margin-left: 0.5rem;">
                        Clear
                    </a>
                </div>
            </form>

            <!-- Ideas Grid -->
            <?php if (count($ideas) > 0): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                    <?php foreach ($ideas as $idea):
                        $colors = getColorForDomain($idea['domain']);
                        $skills = json_decode($idea['skills_needed'], true) ?? [];
                    ?>
                        <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; transition: all 0.25s ease; cursor: pointer;"
                             onclick="location.href='<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>'">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <span  style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background-color: <?php echo $colors['bg']; ?>; color: <?php echo $colors['text']; ?>;">
                                    <?php echo $idea['domain']; ?>
                                </span>
                                <button style="background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 0;" onclick="event.stopPropagation();">☆</button>
                            </div>
                            <h3 style="color: #111827; margin-bottom: 0.5rem; font-weight: 600;"><?php echo htmlspecialchars(substr($idea['title'], 0, 40)); ?></h3>
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.5;">
                                <?php echo htmlspecialchars(substr($idea['description'], 0, 100)) . '...'; ?>
                            </p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                                <?php foreach (array_slice($skills, 0, 3) as $skill): ?>
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; background: #f3f4f6; color: #6b7280;">
                                        <?php echo htmlspecialchars($skill); ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($skills) > 3): ?>
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; background: #f3f4f6; color: #6b7280;">
                                        +<?php echo count($skills) - 3; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                                <span style="color: #9ca3af; font-size: 0.875rem;">👥 <?php echo $idea['applicant_count']; ?> applicants</span>
                                <button style="padding: 0.5rem 1rem; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;" onclick="event.stopPropagation();">
                                    View
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 3rem; text-align: center;">
                    <p style="color: #9ca3af; font-size: 1.125rem;">No ideas found matching your filters.</p>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" style="color: #3b82f6; text-decoration: none; font-weight: 500; display: inline-block; margin-top: 1rem;">Clear filters</a>
                </div>
            <?php endif; ?>
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

        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px #dbeafe;
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
            transition: all 0.25s ease;
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
    </style>
</body>
</html>
