<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <style>
        .messages-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 1.5rem;
            height: calc(100vh - 150px);
        }
        @media (max-width: 768px) {
            .messages-container {
                grid-template-columns: 1fr;
            }
            .conversation-panel {
                display: none;
            }
            .conversation-panel.active {
                display: flex;
            }
        }
        .conversations-list {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .conversation-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .conversation-item:hover {
            background: #f9fafb;
        }
        .conversation-item.active {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
        }
        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        .conversation-name {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .conversation-preview {
            color: #9ca3af;
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conversation-panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .conversation-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: space-between;
        }
        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .message-group {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .message-group.own {
            justify-content: flex-end;
        }
        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .message-bubble.other {
            background: #f3f4f6;
            color: #111827;
        }
        .message-bubble.own {
            background: #3b82f6;
            color: white;
        }
        .message-time {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }
        .message-input-area {
            padding: 1rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
        }
        .message-input {
            flex: 1;
            display: flex;
            gap: 0.5rem;
        }
        .message-input input {
            flex: 1;
        }
        .empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #9ca3af;
            height: 100%;
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
                    <li><a href="<?php echo BASE_URL; ?>/?page=dashboard" class="active">Dashboard</a></li>
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
    require_once __DIR__ . '/../../models/Message.php';
    require_once __DIR__ . '/../../models/User.php';

    $db = new Database();
    $conn = $db->connect();
    $msgModel = new Message($conn);
    $userModel = new User($conn);

    $user_id = $_SESSION['user_id'];

    // Get user's conversations
    $conversations = $msgModel->getUserConversations($user_id, 20, 0);

    // Get selected conversation
    $selected_user_id = (int)($_GET['user_id'] ?? 0);
    $messages = [];
    $selected_user = null;

    if ($selected_user_id > 0) {
        $messages = $msgModel->getConversation($user_id, $selected_user_id, 50, 0);
        $selected_user = $userModel->getById($selected_user_id);

        // Mark conversation as read
        $msgModel->markConversationAsRead($user_id, $selected_user_id);
    }
    ?>

    <!-- Container -->
    <div style="background: #f9fafb; min-height: calc(100vh - 80px); padding: 2rem;">
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
            <!-- Header -->
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">Messages</h1>
                <p style="color: #6b7280;">Direct messaging with collaborators</p>
            </div>

            <!-- Messages Container -->
            <div class="messages-container">
                <!-- Conversations List -->
                <div class="conversations-list">
                    <?php if (empty($conversations)): ?>
                        <div class="empty-state" style="height: 100%; justify-content: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">💬</div>
                            <p style="text-align: center;">No conversations yet. Start collaborating to message users!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv):
                            // Determine other user ID
                            $other_id = ($conv['sender_id'] === $user_id) ? $conv['recipient_id'] : $conv['sender_id'];
                            $other_user = $userModel->getById($other_id);
                            $initials = substr($other_user['name'], 0, 1);
                            $is_active = ($selected_user_id === $other_id);
                        ?>
                            <div class="conversation-item <?php echo $is_active ? 'active' : ''; ?>" onclick="selectConversation(<?php echo $other_id; ?>)">
                                <div class="avatar"><?php echo strtoupper($initials); ?></div>
                                <div class="conversation-info">
                                    <div class="conversation-name"><?php echo sanitize($other_user['name']); ?></div>
                                    <div class="conversation-preview"><?php echo sanitize(substr($conv['content'], 0, 50)); ?>...</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Conversation Panel -->
                <div class="conversation-panel <?php echo $selected_user ? 'active' : ''; ?>">
                    <?php if ($selected_user): ?>
                        <!-- Header -->
                        <div class="conversation-header">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div class="avatar" style="width: 40px; height: 40px; font-size: 1rem;"><?php echo strtoupper(substr($selected_user['name'], 0, 1)); ?></div>
                                <div>
                                    <div style="font-weight: 600; color: #111827;"><?php echo sanitize($selected_user['name']); ?></div>
                                    <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo sanitize($selected_user['roll_number']); ?></div>
                                </div>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/?page=profile&user_id=<?php echo $selected_user['id']; ?>" class="btn btn-ghost btn-sm">View Profile</a>
                        </div>

                        <!-- Messages -->
                        <div class="messages-area" id="messages-area">
                            <?php if (empty($messages)): ?>
                                <div style="text-align: center; color: #9ca3af; margin: auto;">
                                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">👋</div>
                                    <p>Start a conversation!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messages as $msg):
                                    $is_own = ($msg['sender_id'] === $user_id);
                                ?>
                                    <div class="message-group <?php echo $is_own ? 'own' : ''; ?>">
                                        <div>
                                            <div class="message-bubble <?php echo $is_own ? 'own' : 'other'; ?>">
                                                <?php echo sanitize($msg['content']); ?>
                                            </div>
                                            <div class="message-time"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Input Area -->
                        <div class="message-input-area">
                            <div class="message-input">
                                <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off">
                                <button class="btn btn-primary btn-sm" onclick="sendMessage(<?php echo $selected_user_id; ?>)">Send</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">💭</div>
                            <p>Select a conversation to start messaging</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectConversation(userId) {
            window.location.href = '<?php echo BASE_URL; ?>/?page=messages&user_id=' + userId;
        }

        function sendMessage(recipientId) {
            const content = document.getElementById('message-input').value.trim();
            if (!content) return;

            const formData = new FormData();
            formData.append('recipient_id', recipientId);
            formData.append('content', content);

            fetch('<?php echo BASE_URL; ?>/src/controllers/messages.php?action=send', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    document.getElementById('message-input').value = '';
                    // Reload messages
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }

        // Allow sending with Enter key
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                const btn = document.querySelector('.message-input-area .btn');
                if (btn) btn.click();
            }
        });

        // Auto-scroll to bottom
        const messagesArea = document.getElementById('messages-area');
        if (messagesArea) {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    </script>
</body>
</html>
