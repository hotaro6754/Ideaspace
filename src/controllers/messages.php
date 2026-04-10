<?php
/**
 * Messages Controller
 * Handles direct messaging between users
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Message.php';

class MessagesController {
    private $message;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->message = new Message($db);
    }

    /**
     * Send a message
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $recipient_id = (int)($_POST['recipient_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($recipient_id <= 0) {
            return ['success' => false, 'error' => 'Invalid recipient ID'];
        }

        if ($user_id === $recipient_id) {
            return ['success' => false, 'error' => 'You cannot message yourself'];
        }

        if (empty($content) || strlen($content) < 1) {
            return ['success' => false, 'error' => 'Message content is required'];
        }

        if (strlen($content) > 5000) {
            return ['success' => false, 'error' => 'Message is too long (max 5000 characters)'];
        }

        return $this->message->create($user_id, $recipient_id, $content);
    }

    /**
     * Get conversation between two users
     */
    public function getConversation($user_id, $other_user_id, $limit = 50, $offset = 0) {
        if (!$user_id || !$other_user_id) {
            return [];
        }
        return $this->message->getConversation($user_id, $other_user_id, $limit, $offset);
    }

    /**
     * Get user's conversations (latest message from each conversation)
     */
    public function getConversations($user_id, $limit = 20, $offset = 0) {
        if (!$user_id) {
            return [];
        }
        return $this->message->getUserConversations($user_id, $limit, $offset);
    }

    /**
     * Mark message as read
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $message_id = (int)($_POST['message_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($message_id <= 0) {
            return ['success' => false, 'error' => 'Invalid message ID'];
        }

        // Verify message recipient is current user
        $msg = $this->message->getById($message_id);
        if (!$msg || $msg['recipient_id'] !== $user_id) {
            return ['success' => false, 'error' => 'Message not found'];
        }

        return $this->message->markAsRead($message_id);
    }

    /**
     * Mark conversation as read
     */
    public function markConversationAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $sender_id = (int)($_POST['sender_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($sender_id <= 0) {
            return ['success' => false, 'error' => 'Invalid sender ID'];
        }

        return $this->message->markConversationAsRead($user_id, $sender_id);
    }

    /**
     * Delete message
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $message_id = (int)($_POST['message_id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        if ($message_id <= 0) {
            return ['success' => false, 'error' => 'Invalid message ID'];
        }

        // Verify user is sender or recipient
        $msg = $this->message->getById($message_id);
        if (!$msg || ($msg['sender_id'] !== $user_id && $msg['recipient_id'] !== $user_id)) {
            return ['success' => false, 'error' => 'Message not found'];
        }

        return $this->message->delete($message_id);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount($user_id) {
        if (!$user_id) {
            return 0;
        }
        return $this->message->getUnreadCount($user_id);
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$msgCtrl = new MessagesController($conn);

// Route to appropriate method
if ($action === 'send') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $msgCtrl->send();
    echo json_encode($result);
    exit();
} elseif ($action === 'get-conversation') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $other_user_id = (int)($_GET['other_user_id'] ?? 0);
    $page = (int)($_GET['page'] ?? 1);
    $limit = 50;
    $offset = ($page - 1) * $limit;
    $messages = $msgCtrl->getConversation($user_id, $other_user_id, $limit, $offset);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit();
} elseif ($action === 'get-conversations') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $page = (int)($_GET['page'] ?? 1);
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $conversations = $msgCtrl->getConversations($user_id, $limit, $offset);
    echo json_encode(['success' => true, 'conversations' => $conversations]);
    exit();
} elseif ($action === 'mark-read') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $msgCtrl->markAsRead();
    echo json_encode($result);
    exit();
} elseif ($action === 'mark-conversation-read') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $msgCtrl->markConversationAsRead();
    echo json_encode($result);
    exit();
} elseif ($action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $msgCtrl->delete();
    echo json_encode($result);
    exit();
} elseif ($action === 'unread-count') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $count = $msgCtrl->getUnreadCount($user_id);
    echo json_encode(['success' => true, 'count' => $count]);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
