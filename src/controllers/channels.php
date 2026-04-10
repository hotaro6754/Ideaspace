<?php
/**
 * Channels Controller
 * Handles team-based messaging channels (not group chat)
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Channel.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../helpers/Security.php';

class ChannelsController {
    private $channelModel;
    private $activityLog;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->channelModel = new Channel($db);
        $this->activityLog = new ActivityLog($db);
    }

    /**
     * Create a new channel
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $collaboration_id = (int)($_POST['collaboration_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $_POST['type'] ?? 'custom';

        if ($collaboration_id === 0) {
            return ['success' => false, 'error' => 'Collaboration ID is required'];
        }

        $result = $this->channelModel->create($collaboration_id, $name, $description, $type, $user_id);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'collaboration', 'create', $collaboration_id, ['channel_id' => $result['channel_id']]);
        }

        return $result;
    }

    /**
     * Get channels for a collaboration
     */
    public function getForCollaboration() {
        $collaboration_id = (int)($_GET['collaboration_id'] ?? $_POST['collaboration_id'] ?? 0);

        if ($collaboration_id === 0) {
            return ['success' => false, 'error' => 'Collaboration ID is required'];
        }

        $channels = $this->channelModel->getForCollaboration($collaboration_id);

        return [
            'success' => true,
            'channels' => $channels,
            'count' => count($channels)
        ];
    }

    /**
     * Get channel details
     */
    public function getChannel() {
        $channel_id = (int)($_GET['channel_id'] ?? $_POST['channel_id'] ?? 0);

        if ($channel_id === 0) {
            return ['success' => false, 'error' => 'Channel ID is required'];
        }

        $channel = $this->channelModel->getById($channel_id);

        if (!$channel) {
            return ['success' => false, 'error' => 'Channel not found'];
        }

        return ['success' => true, 'channel' => $channel];
    }

    /**
     * Add message to channel
     */
    public function addMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $channel_id = (int)($_POST['channel_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $attachments = $_POST['attachments'] ?? null;

        if ($channel_id === 0) {
            return ['success' => false, 'error' => 'Channel ID is required'];
        }

        $result = $this->channelModel->addMessage($channel_id, $user_id, $content, $attachments);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'message', 'create', $channel_id, ['message_id' => $result['message_id']]);
        }

        return $result;
    }

    /**
     * Get messages from channel
     */
    public function getMessages() {
        $channel_id = (int)($_GET['channel_id'] ?? $_POST['channel_id'] ?? 0);
        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? $_POST['offset'] ?? 0);

        if ($channel_id === 0) {
            return ['success' => false, 'error' => 'Channel ID is required'];
        }

        // Validate pagination
        $limit = min($limit, 100);
        $offset = max($offset, 0);

        $messages = $this->channelModel->getMessages($channel_id, $limit, $offset);

        return [
            'success' => true,
            'messages' => $messages,
            'count' => count($messages)
        ];
    }

    /**
     * Add reaction to a message
     */
    public function addReaction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $message_id = (int)($_POST['message_id'] ?? 0);
        $emoji = trim($_POST['emoji'] ?? '');

        if ($message_id === 0) {
            return ['success' => false, 'error' => 'Message ID is required'];
        }

        if (empty($emoji)) {
            return ['success' => false, 'error' => 'Emoji is required'];
        }

        return $this->channelModel->addReaction($message_id, $user_id, $emoji);
    }

    /**
     * Get unread count for user in channel
     */
    public function getUnreadCount() {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        $user_id = $_SESSION['user_id'];
        $channel_id = (int)($_GET['channel_id'] ?? $_POST['channel_id'] ?? 0);

        if ($channel_id === 0) {
            return ['success' => false, 'error' => 'Channel ID is required'];
        }

        $count = $this->channelModel->getUnreadCount($channel_id, $user_id);

        return ['success' => true, 'unread_count' => $count];
    }

    /**
     * Mark messages as read
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $channel_id = (int)($_POST['channel_id'] ?? 0);
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($channel_id === 0 || $message_id === 0) {
            return ['success' => false, 'error' => 'Channel ID and Message ID are required'];
        }

        return $this->channelModel->markAsRead($channel_id, $user_id, $message_id);
    }

    /**
     * Delete message
     */
    public function deleteMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($message_id === 0) {
            return ['success' => false, 'error' => 'Message ID is required'];
        }

        return $this->channelModel->deleteMessage($message_id, $user_id);
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$channels = new ChannelsController($conn);

// Return JSON for AJAX requests
header('Content-Type: application/json');

// Route to appropriate method
if ($action === 'create') {
    echo json_encode($channels->create());
} elseif ($action === 'getForCollaboration') {
    echo json_encode($channels->getForCollaboration());
} elseif ($action === 'get') {
    echo json_encode($channels->getChannel());
} elseif ($action === 'addMessage') {
    echo json_encode($channels->addMessage());
} elseif ($action === 'getMessages') {
    echo json_encode($channels->getMessages());
} elseif ($action === 'addReaction') {
    echo json_encode($channels->addReaction());
} elseif ($action === 'getUnreadCount') {
    echo json_encode($channels->getUnreadCount());
} elseif ($action === 'markAsRead') {
    echo json_encode($channels->markAsRead());
} elseif ($action === 'deleteMessage') {
    echo json_encode($channels->deleteMessage());
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
exit();
?>
