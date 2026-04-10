<?php
/**
 * Notifications Controller
 * Handles notifications management
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationsController {
    private $notification;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->notification = new Notification($db);
    }

    /**
     * Get user notifications
     */
    public function getNotifications($user_id, $limit = 20, $offset = 0) {
        if (!$user_id) {
            return [];
        }
        return $this->notification->getByUser($user_id, $limit, $offset);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($user_id) {
        if (!$user_id) {
            return 0;
        }
        return $this->notification->getUnreadCount($user_id);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $notification_id = (int)($_POST['notification_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($notification_id <= 0) {
            return ['success' => false, 'error' => 'Invalid notification ID'];
        }

        // Verify notification belongs to user
        $notif = $this->notification->getById($notification_id);
        if (!$notif || $notif['user_id'] !== $user_id) {
            return ['success' => false, 'error' => 'Notification not found'];
        }

        return $this->notification->markAsRead($notification_id);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        return $this->notification->markAllAsRead($user_id);
    }

    /**
     * Delete notification
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $notification_id = (int)($_POST['notification_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($notification_id <= 0) {
            return ['success' => false, 'error' => 'Invalid notification ID'];
        }

        // Verify notification belongs to user
        $notif = $this->notification->getById($notification_id);
        if (!$notif || $notif['user_id'] !== $user_id) {
            return ['success' => false, 'error' => 'Notification not found'];
        }

        return $this->notification->delete($notification_id);
    }

    /**
     * Delete all notifications
     */
    public function deleteAll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        return $this->notification->deleteAll($user_id);
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Check authentication for state-changing actions
if (in_array($action, ['mark-read', 'mark-all-read', 'delete', 'delete-all'])) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
}

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$notifCtrl = new NotificationsController($conn);

// Route to appropriate method
if ($action === 'get-notifications') {
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $page = (int)($_GET['page'] ?? 1);
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $notifications = $notifCtrl->getNotifications($user_id, $limit, $offset);
    echo json_encode(['success' => true, 'notifications' => $notifications]);
    exit();
} elseif ($action === 'unread-count') {
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $count = $notifCtrl->getUnreadCount($user_id);
    echo json_encode(['success' => true, 'count' => $count]);
    exit();
} elseif ($action === 'mark-read') {
    $result = $notifCtrl->markAsRead();
    echo json_encode($result);
    exit();
} elseif ($action === 'mark-all-read') {
    $result = $notifCtrl->markAllAsRead();
    echo json_encode($result);
    exit();
} elseif ($action === 'delete') {
    $result = $notifCtrl->delete();
    echo json_encode($result);
    exit();
} elseif ($action === 'delete-all') {
    $result = $notifCtrl->deleteAll();
    echo json_encode($result);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
