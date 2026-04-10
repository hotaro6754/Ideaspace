<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Reports - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }
        .stat-change {
            font-size: 0.875rem;
            color: #10b981;
        }
        .section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
        }
        .chart-placeholder {
            height: 300px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1.125rem;
        }
        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .list-item:last-child {
            border-bottom: none;
        }
        .domain-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .domain-name {
            font-weight: 600;
            color: #111827;
            min-width: 150px;
        }
        .progress-bar {
            flex: 1;
            height: 24px;
            background: #f3f4f6;
            border-radius: 6px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
            transition: width 0.3s ease;
        }
        .progress-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            min-width: 60px;
            text-align: right;
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
    if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        include __DIR__ . '/../404.php';
        exit();
    }

    require_once __DIR__ . '/../../config/Database.php';

    $db = new Database();
    $conn = $db->connect();

    // Overall statistics
    $statsQuery = "SELECT
                    COUNT(DISTINCT u.id) as total_users,
                    COUNT(DISTINCT i.id) as total_ideas,
                    COUNT(DISTINCT a.id) as total_applications,
                    COUNT(DISTINCT c.id) as total_collaborations,
                    COUNT(DISTINCT m.id) as total_messages
                   FROM users u
                   LEFT JOIN ideas i ON u.id = i.user_id
                   LEFT JOIN applications a ON 1=1
                   LEFT JOIN collaborations c ON 1=1
                   LEFT JOIN messages m ON 1=1";

    $stmt = $conn->prepare($statsQuery);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();

    // Ideas by domain
    $domainQuery = "SELECT domain, COUNT(*) as count
                    FROM ideas
                    GROUP BY domain
                    ORDER BY count DESC";

    $stmt = $conn->prepare($domainQuery);
    $stmt->execute();
    $domainStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $maxDomain = !empty($domainStats) ? $domainStats[0]['count'] : 1;

    // Ideas by status
    $statusQuery = "SELECT status, COUNT(*) as count
                    FROM ideas
                    GROUP BY status";

    $stmt = $conn->prepare($statusQuery);
    $stmt->execute();
    $statusStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Applications by status
    $appStatusQuery = "SELECT status, COUNT(*) as count
                       FROM applications
                       GROUP BY status";

    $stmt = $conn->prepare($appStatusQuery);
    $stmt->execute();
    $appStatusStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Top branches by user count
    $branchQuery = "SELECT branch, COUNT(*) as count
                    FROM users
                    GROUP BY branch
                    ORDER BY count DESC
                    LIMIT 10";

    $stmt = $conn->prepare($branchQuery);
    $stmt->execute();
    $branchStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $maxBranch = !empty($branchStats) ? $branchStats[0]['count'] : 1;
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">Analytics & Reports</h1>
                    <p style="color: #6b7280; margin: 0.5rem 0 0 0;">Platform usage and performance metrics</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=admin" class="btn btn-ghost">Back to Admin</a>
            </div>

            <!-- Key Metrics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">👥 Total Users</div>
                    <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="stat-change">↑ Active platform users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">💡 Total Ideas</div>
                    <div class="stat-value"><?php echo number_format($stats['total_ideas']); ?></div>
                    <div class="stat-change">Ideas posted on platform</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">📝 Applications</div>
                    <div class="stat-value"><?php echo number_format($stats['total_applications']); ?></div>
                    <div class="stat-change">Collaboration applications</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">🤝 Collaborations</div>
                    <div class="stat-value"><?php echo number_format($stats['total_collaborations']); ?></div>
                    <div class="stat-change">Active collaborations</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">💬 Messages</div>
                    <div class="stat-value"><?php echo number_format($stats['total_messages']); ?></div>
                    <div class="stat-change">Direct messages sent</div>
                </div>
            </div>

            <!-- Ideas by Domain -->
            <div class="section">
                <div class="section-title">📊 Ideas by Domain</div>
                <div>
                    <?php foreach ($domainStats as $domain): ?>
                        <div class="list-item">
                            <div class="domain-bar">
                                <div class="domain-name"><?php echo sanitize($domain['domain']); ?></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($domain['count'] / $maxDomain * 100); ?>%"></div>
                                </div>
                            </div>
                            <div class="progress-label"><?php echo $domain['count']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
                <!-- Ideas Status -->
                <div class="section">
                    <div class="section-title">📈 Ideas by Status</div>
                    <div>
                        <?php
                        $statusLabels = [
                            'open' => '🟢 Open',
                            'in_progress' => '🔵 In Progress',
                            'completed' => '✅ Completed',
                            'abandoned' => '❌ Abandoned'
                        ];
                        ?>
                        <?php foreach ($statusStats as $status): ?>
                            <div class="list-item">
                                <div style="font-weight: 600; color: #111827;">
                                    <?php echo $statusLabels[$status['status']] ?? $status['status']; ?>
                                </div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">
                                    <?php echo $status['count']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Applications Status -->
                <div class="section">
                    <div class="section-title">📋 Applications by Status</div>
                    <div>
                        <?php
                        $appStatusLabels = [
                            'pending' => '⏳ Pending',
                            'accepted' => '✅ Accepted',
                            'rejected' => '❌ Rejected'
                        ];
                        ?>
                        <?php foreach ($appStatusStats as $status): ?>
                            <div class="list-item">
                                <div style="font-weight: 600; color: #111827;">
                                    <?php echo $appStatusLabels[$status['status']] ?? $status['status']; ?>
                                </div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">
                                    <?php echo $status['count']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Users by Branch -->
            <div class="section">
                <div class="section-title">🏫 Users by Branch</div>
                <div>
                    <?php foreach ($branchStats as $branch): ?>
                        <div class="list-item">
                            <div class="domain-bar">
                                <div class="domain-name"><?php echo sanitize($branch['branch']); ?></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($branch['count'] / $maxBranch * 100); ?>%"></div>
                                </div>
                            </div>
                            <div class="progress-label"><?php echo $branch['count']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Time-based analytics would go here -->
            <div class="section">
                <div class="section-title">📅 Growth Trends</div>
                <div class="chart-placeholder">
                    Chart visualization could be implemented here with Chart.js or similar library
                </div>
            </div>
        </div>
    </div>
</body>
</html>
