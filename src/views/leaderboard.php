<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 1rem;
            color: #6b7280;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        .tab-btn.active {
            color: #3b82f6;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #3b82f6;
        }
        .leaderboard-row {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .leaderboard-row:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .rank-badge {
            font-size: 1.5rem;
            font-weight: 700;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 1.5rem;
            min-width: 50px;
        }
        .rank-1 {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
        }
        .rank-2 {
            background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
            color: white;
        }
        .rank-3 {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
        }
        .rank-other {
            background: #f3f4f6;
            color: #6b7280;
        }
        .user-info {
            flex: 1;
        }
        .user-name {
            font-weight: 600;
            color: #111827;
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }
        .user-detail {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        .stats {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }
        .stat {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #3b82f6;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #9ca3af;
            text-transform: uppercase;
            font-weight: 600;
        }
        .achievement {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }
        .achievement-icon {
            font-size: 1.75rem;
        }
        .achievement-name {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
    </style>
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
    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../models/BuilderRank.php';

    $db = new Database();
    $conn = $db->connect();

    // Fetch leaderboard data
    $query = "SELECT br.*, u.name, u.roll_number, u.profile_pic, u.branch,
                     COUNT(DISTINCT i.id) as ideas_posted,
                     COUNT(DISTINCT c.id) as collaborations
              FROM builder_ranks br
              JOIN users u ON br.user_id = u.id
              LEFT JOIN ideas i ON u.id = i.user_id
              LEFT JOIN collaborations c ON u.id = c.collaborator_id AND c.status = 'active'
              GROUP BY br.id
              ORDER BY br.rank DESC
              LIMIT 50";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $leaderboard = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get top builders
    $builderQuery = "SELECT u.id, u.name, u.roll_number, u.profile_pic, u.branch,
                            COUNT(DISTINCT c.id) as collaborations,
                            COUNT(DISTINCT CASE WHEN c.status = 'completed' THEN c.id END) as completed_projects
                     FROM users u
                     LEFT JOIN collaborations c ON u.id = c.collaborator_id
                     GROUP BY u.id
                     HAVING collaborations > 0
                     ORDER BY collaborations DESC
                     LIMIT 10";
    $stmt = $conn->prepare($builderQuery);
    $stmt->execute();
    $topBuilders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get top visionaries
    $visionaryQuery = "SELECT u.id, u.name, u.roll_number, u.profile_pic, u.branch,
                             COUNT(DISTINCT i.id) as ideas_posted,
                             COUNT(DISTINCT c.id) as collaborators_gathered
                      FROM users u
                      LEFT JOIN ideas i ON u.id = i.user_id
                      LEFT JOIN collaborations c ON i.id = c.idea_id AND c.status = 'active'
                      GROUP BY u.id
                      HAVING ideas_posted > 0
                      ORDER BY ideas_posted DESC
                      LIMIT 10";
    $stmt = $conn->prepare($visionaryQuery);
    $stmt->execute();
    $topVisionaries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    function getRankBadgeClass($rank) {
        if ($rank === 1) return 'rank-1';
        if ($rank === 2) return 'rank-2';
        if ($rank === 3) return 'rank-3';
        return 'rank-other';
    }

    function getRankEmoji($rank) {
        if ($rank === 1) return '🥇';
        if ($rank === 2) return '🥈';
        if ($rank === 3) return '🥉';
        return $rank;
    }
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; font-weight: 700; color: #111827; margin: 0; margin-bottom: 0.5rem;">🏆 Leaderboard</h1>
                <p style="color: #6b7280; font-size: 1.125rem; margin: 0;">Celebrate the top builders and visionaries on IdeaSync</p>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('overall')">Overall Rankings</button>
                <button class="tab-btn" onclick="showTab('builders')">Top Builders</button>
                <button class="tab-btn" onclick="showTab('visionaries')">Top Visionaries</button>
            </div>

            <!-- Overall Rankings Tab -->
            <div id="overall-tab" class="tab-content">
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Overall Rankings</h2>
                    <?php foreach ($leaderboard as $index => $user): ?>
                        <div class="leaderboard-row">
                            <div class="rank-badge <?php echo getRankBadgeClass($index + 1); ?>">
                                <?php echo getRankEmoji($index + 1); ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo sanitize($user['name']); ?></div>
                                <div class="user-detail"><?php echo sanitize($user['roll_number']); ?> • <?php echo sanitize($user['branch']); ?></div>
                                <div class="stats">
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['points']; ?></div>
                                        <div class="stat-label">Points</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['ideas_posted']; ?></div>
                                        <div class="stat-label">Ideas</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['collaborations']; ?></div>
                                        <div class="stat-label">Collaborations</div>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; min-width: 200px;">
                                <a href="<?php echo BASE_URL; ?>/?page=profile&user_id=<?php echo $user['user_id']; ?>" class="btn btn-secondary btn-sm">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Builders Tab -->
            <div id="builders-tab" class="tab-content" style="display: none;">
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Top Builders 🔨</h2>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">Users with the most active collaborations</p>
                    <?php foreach ($topBuilders as $index => $user): ?>
                        <div class="leaderboard-row">
                            <div class="rank-badge <?php echo getRankBadgeClass($index + 1); ?>">
                                <?php echo getRankEmoji($index + 1); ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo sanitize($user['name']); ?></div>
                                <div class="user-detail"><?php echo sanitize($user['roll_number']); ?> • <?php echo sanitize($user['branch']); ?></div>
                                <div class="stats">
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['collaborations']; ?></div>
                                        <div class="stat-label">Collaborations</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['completed_projects']; ?></div>
                                        <div class="stat-label">Completed</div>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; min-width: 200px;">
                                <a href="<?php echo BASE_URL; ?>/?page=profile&user_id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Visionaries Tab -->
            <div id="visionaries-tab" class="tab-content" style="display: none;">
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Top Visionaries 💡</h2>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">Users with the most innovative ideas</p>
                    <?php foreach ($topVisionaries as $index => $user): ?>
                        <div class="leaderboard-row">
                            <div class="rank-badge <?php echo getRankBadgeClass($index + 1); ?>">
                                <?php echo getRankEmoji($index + 1); ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?php echo sanitize($user['name']); ?></div>
                                <div class="user-detail"><?php echo sanitize($user['roll_number']); ?> • <?php echo sanitize($user['branch']); ?></div>
                                <div class="stats">
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['ideas_posted']; ?></div>
                                        <div class="stat-label">Ideas</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?php echo (int)$user['collaborators_gathered']; ?></div>
                                        <div class="stat-label">Collaborators</div>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; min-width: 200px;">
                                <a href="<?php echo BASE_URL; ?>/?page=profile&user_id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').style.display = 'block';

            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
