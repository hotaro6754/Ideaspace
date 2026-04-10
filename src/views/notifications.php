<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .notification-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateX(4px);
        }
        .notification-item.read {
            background: #f9fafb;
            border-left-color: #d1d5db;
        }
        .notification-item.unread {
            border-left-color: #3b82f6;
            background: #eff6ff;
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }
        .notification-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .type-application {
            background: #fef3c7;
            color: #92400e;
        }
        .type-collaboration {
            background: #d1fae5;
            color: #065f46;
        }
        .type-message {
            background: #dbeafe;
            color: #1e40af;
        }
        .type-upvote {
            background: #fce7f3;
            color: #831843;
        }
        .notification-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .notification-message {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }
        .notification-meta {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        .notification-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .badge-count {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .filter-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .filter-tab {
            background: none;
            border: none;
            padding: 1rem;
            color: #6b7280;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        .filter-tab.active {
            color: #3b82f6;
        }
        .filter-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #3b82f6;
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
    if (!isLoggedIn()) {
        http_response_code(401);
        include __DIR__ . '/../404.php';
        exit();
    }

    require_once __DIR__ . '/../../config/Database.php';
    require_once __DIR__ . '/../../models/Notification.php';

    $db = new Database();
    $conn = $db->connect();
    $notifModel = new Notification($conn);

    $user_id = $_SESSION['user_id'];

    // Get all notifications
    $notifications = $notifModel->getByUser($user_id, 100, 0);

    // Filter by type if requested
    $filter_type = $_GET['type'] ?? 'all';
    $filtered_notifications = [];

    if ($filter_type === 'all') {
        $filtered_notifications = $notifications;
    } else {
        foreach ($notifications as $notif) {
            if ($notif['type'] === $filter_type) {
                $filtered_notifications[] = $notif;
            }
        }
    }

    // Count unread
    $unread_count = $notifModel->getUnreadCount($user_id);

    function getNotificationIcon($type) {
        $icons = [
            'application' => '📝',
            'collaboration' => '🤝',
            'message' => '💬',
            'upvote' => '⭐',
            'idea' => '💡',
            'other' => '🔔'
        ];
        return $icons[$type] ?? $icons['other'];
    }

    function getNotificationType($type) {
        $types = [
            'application' => 'Application',
            'collaboration' => 'Collaboration',
            'message' => 'Message',
            'upvote' => 'Upvote',
            'idea' => 'Idea',
            'other' => 'Notification'
        ];
        return $types[$type] ?? 'Notification';
    }

    function getTypeClass($type) {
        $classes = [
            'application' => 'type-application',
            'collaboration' => 'type-collaboration',
            'message' => 'type-message',
            'upvote' => 'type-upvote',
            'idea' => 'type-upvote',
            'other' => 'type-message'
        ];
        return $classes[$type] ?? 'type-message';
    }
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">Notifications
                        <?php if ($unread_count > 0): ?>
                            <span class="badge-count"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </h1>
                    <p style="color: #6b7280; margin: 0.5rem 0 0 0;">Stay updated with your activities</p>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <?php if ($unread_count > 0): ?>
                        <button class="btn btn-secondary btn-sm" onclick="markAllAsRead()">Mark All Read</button>
                    <?php endif; ?>
                    <?php if (!empty($notifications)): ?>
                        <button class="btn btn-danger btn-sm" onclick="deleteAll()">Clear All</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab <?php echo $filter_type === 'all' ? 'active' : ''; ?>" onclick="filterNotifications('all')">All</button>
                <button class="filter-tab <?php echo $filter_type === 'application' ? 'active' : ''; ?>" onclick="filterNotifications('application')">Applications</button>
                <button class="filter-tab <?php echo $filter_type === 'collaboration' ? 'active' : ''; ?>" onclick="filterNotifications('collaboration')">Collaborations</button>
                <button class="filter-tab <?php echo $filter_type === 'message' ? 'active' : ''; ?>" onclick="filterNotifications('message')">Messages</button>
                <button class="filter-tab <?php echo $filter_type === 'upvote' ? 'active' : ''; ?>" onclick="filterNotifications('upvote')">Upvotes</button>
            </div>

            <!-- Notifications List -->
            <?php if (empty($filtered_notifications)): ?>
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px; border: 1px dashed #e5e7eb;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔔</div>
                    <h3 style="color: #111827; margin-bottom: 0.5rem;">No Notifications</h3>
                    <p style="color: #6b7280;">You're all caught up!</p>
                </div>
            <?php else: ?>
                <?php foreach ($filtered_notifications as $notif): ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? 'read' : 'unread'; ?>">
                        <div class="notification-header">
                            <div>
                                <span class="notification-type <?php echo getTypeClass($notif['type']); ?>">
                                    <?php echo getNotificationType($notif['type']); ?>
                                </span>
                            </div>
                            <div style="color: #9ca3af; font-size: 0.875rem;">
                                <?php echo date('M d, Y H:i', strtotime($notif['created_at'])); ?>
                            </div>
                        </div>

                        <div class="notification-title">
                            <?php echo getNotificationIcon($notif['type']); ?>
                            <?php echo sanitize($notif['title']); ?>
                        </div>

                        <div class="notification-message">
                            <?php echo sanitize($notif['message']); ?>
                        </div>

                        <?php if (!empty($notif['related_id'])): ?>
                            <div class="notification-actions">
                                <a href="<?php echo sanitize($notif['link'] ?? '#'); ?>" class="btn btn-primary btn-sm">View</a>
                                <?php if (!$notif['is_read']): ?>
                                    <button class="btn btn-secondary btn-sm" onclick="markAsRead(<?php echo $notif['id']; ?>)">Mark as Read</button>
                                <?php endif; ?>
                                <button class="btn btn-danger btn-sm" onclick="deleteNotification(<?php echo $notif['id']; ?>)">Delete</button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterNotifications(type) {
            window.location.href = '<?php echo BASE_URL; ?>/?page=notifications&type=' + type;
        }

        function markAsRead(notifId) {
            const formData = new FormData();
            formData.append('notification_id', notifId);

            fetch('<?php echo BASE_URL; ?>/src/controllers/notifications.php?action=mark-read', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }

        function markAllAsRead() {
            const formData = new FormData();

            fetch('<?php echo BASE_URL; ?>/src/controllers/notifications.php?action=mark-all-read', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }

        function deleteNotification(notifId) {
            if (!confirm('Delete this notification?')) return;

            const formData = new FormData();
            formData.append('notification_id', notifId);

            fetch('<?php echo BASE_URL; ?>/src/controllers/notifications.php?action=delete', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }

        function deleteAll() {
            if (!confirm('Delete all notifications? This cannot be undone.')) return;

            const formData = new FormData();

            fetch('<?php echo BASE_URL; ?>/src/controllers/notifications.php?action=delete-all', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }
    </script>
</body>
</html>
