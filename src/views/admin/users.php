<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .admin-table thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        .admin-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
        }
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .admin-table tbody tr:hover {
            background: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .user-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .user-type-user {
            background: #dbeafe;
            color: #1e40af;
        }
        .user-type-moderator {
            background: #fce7f3;
            color: #831843;
        }
        .user-type-admin {
            background: #fef3c7;
            color: #92400e;
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

    // Get all users
    $query = "SELECT id, name, roll_number, email, branch, year, user_type, created_at, is_active
              FROM users
              ORDER BY created_at DESC
              LIMIT 100";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get statistics
    $statsQuery = "SELECT
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN user_type = 'admin' THEN 1 END) as admins,
                    COUNT(CASE WHEN user_type = 'moderator' THEN 1 END) as moderators,
                    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_users
                   FROM users";

    $stmt = $conn->prepare($statsQuery);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">User Management</h1>
                    <p style="color: #6b7280; margin: 0.5rem 0 0 0;">Manage platform users and permissions</p>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=admin" class="btn btn-ghost">Back to Admin</a>
            </div>

            <!-- Statistics -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem;">
                    <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #3b82f6;"><?php echo $stats['total_users']; ?></div>
                </div>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem;">
                    <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Users</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #10b981;"><?php echo $stats['active_users']; ?></div>
                </div>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem;">
                    <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Admins</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;"><?php echo $stats['admins']; ?></div>
                </div>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem;">
                    <div style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Moderators</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #ec4899;"><?php echo $stats['moderators']; ?></div>
                </div>
            </div>

            <!-- Users Table -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Roll Number</th>
                            <th>Email</th>
                            <th>Branch</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo sanitize($user['name']); ?></td>
                                <td><?php echo sanitize($user['roll_number']); ?></td>
                                <td style="font-size: 0.875rem; color: #6b7280;"><?php echo sanitize($user['email']); ?></td>
                                <td><?php echo sanitize($user['branch']); ?></td>
                                <td>
                                    <span class="user-type-badge user-type-<?php echo strtolower($user['user_type']); ?>">
                                        <?php echo ucfirst($user['user_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.875rem; color: #6b7280;">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/?page=profile&user_id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
