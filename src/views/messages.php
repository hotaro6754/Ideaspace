<?php
ob_start();
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');

$current_user_id = $_SESSION['user_id'];
$to_id = (int)($_GET['to'] ?? 0);
$db = getConnection();

// Get Conversations List with unread counts
$query = "SELECT DISTINCT
            u.id, u.name, u.roll_number, u.branch,
            (SELECT COUNT(*) FROM messages WHERE sender_user_id = u.id AND recipient_user_id = ? AND is_read = 0) as unread_count,
            (SELECT MAX(created_at) FROM messages WHERE (sender_user_id = ? AND recipient_user_id = u.id) OR (sender_user_id = u.id AND recipient_user_id = ?)) as last_msg_time
          FROM users u
          JOIN messages m ON (m.sender_user_id = u.id OR m.recipient_user_id = u.id)
          WHERE (m.sender_user_id = ? OR m.recipient_user_id = ?) AND u.id != ?
          ORDER BY last_msg_time DESC";

$stmt = $db->prepare($query);
$stmt->bind_param("iiiiii", $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Mark as read if a conversation is open
if ($to_id > 0) {
    $db->prepare("UPDATE messages SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE sender_user_id = ? AND recipient_user_id = ? AND is_read = 0")->execute([$to_id, $current_user_id]);
}

// Get Active Chat Messages
$messages = [];
$active_recipient = null;
if ($to_id > 0) {
    $u_stmt = $db->prepare("SELECT id, name, roll_number, branch FROM users WHERE id = ?");
    $u_stmt->bind_param("i", $to_id);
    $u_stmt->execute();
    $active_recipient = $u_stmt->get_result()->fetch_assoc();

    if ($active_recipient) {
        $m_stmt = $db->prepare("SELECT * FROM messages
                                 WHERE (sender_user_id = ? AND recipient_user_id = ?)
                                    OR (sender_user_id = ? AND recipient_user_id = ?)
                                 ORDER BY created_at ASC");
        $m_stmt->bind_param("iiii", $current_user_id, $to_id, $to_id, $current_user_id);
        $m_stmt->execute();
        $messages = $m_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 h-[calc(100vh-64px)]">
    <div class="flex h-full premium-card overflow-hidden shadow-premium">
        <!-- Sidebar -->
        <div class="w-full md:w-80 border-r border-slate-100 flex flex-col bg-slate-50/50">
            <div class="p-6 border-b border-slate-100 bg-white">
                <h2 class="text-lg font-bold text-slate-900">Collaboration Hub</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Direct Messages</p>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <?php foreach($conversations as $conv): ?>
                <a href="<?php echo BASE_URL; ?>/?page=messages&to=<?php echo $conv['id']; ?>"
                   class="flex items-center gap-4 p-4 rounded-2xl transition-all <?php echo ($to_id == $conv['id']) ? 'bg-white shadow-subtle border border-slate-100' : 'hover:bg-white/60'; ?>">
                    <div class="relative">
                        <div class="h-10 w-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold">
                            <?php echo strtoupper(substr($conv['name'], 0, 1)); ?>
                        </div>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 h-5 w-5 bg-secondary text-white text-[10px] font-black flex items-center justify-center rounded-full ring-2 ring-white">
                                <?php echo $conv['unread_count']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="flex justify-between items-center mb-0.5">
                            <p class="text-sm font-bold text-slate-900 truncate"><?php echo sanitize($conv['name']); ?></p>
                            <span class="text-[8px] font-bold text-slate-400 uppercase"><?php echo date('H:i', strtotime($conv['last_msg_time'])); ?></span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo sanitize($conv['branch']); ?> Dept</p>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-12 px-6 opacity-40">
                        <i class="far fa-comment-dots text-3xl mb-4 text-slate-300"></i>
                        <p class="text-xs font-bold uppercase tracking-widest">No Active Chats</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="hidden md:flex flex-1 flex-col bg-white">
            <?php if ($active_recipient): ?>
                <!-- Chat Header -->
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold">
                            <?php echo strtoupper(substr($active_recipient['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900"><?php echo sanitize($active_recipient['name']); ?></h3>
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest"><?php echo sanitize($active_recipient['branch']); ?> DEPARTMENT</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=profile&id=<?php echo $active_recipient['id']; ?>" class="btn-outline !py-2 !px-4 !text-xs font-bold">View Profile</a>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/30" id="chat-messages" data-user-id="<?php echo $current_user_id; ?>" data-last-id="<?php echo end($messages)["id"] ?? 0; ?>">
                    <?php foreach($messages as $m): ?>
                        <div class="flex <?php echo ($m['sender_user_id'] == $current_user_id) ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-[70%] p-4 rounded-2xl text-sm font-medium shadow-subtle <?php echo ($m['sender_user_id'] == $current_user_id) ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-100'; ?>">
                                <?php echo sanitize($m['message']); ?>
                                <div class="flex items-center justify-between mt-2 gap-4">
                                    <p class="text-[9px] opacity-60 font-bold uppercase"><?php echo date('H:i', strtotime($m['created_at'])); ?></p>
                                    <?php if ($m['sender_user_id'] == $current_user_id): ?>
                                        <i class="fas fa-check-double text-[8px] <?php echo $m['is_read'] ? 'text-blue-300' : 'text-white/40'; ?>"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Input -->
                <div class="p-6 bg-white border-t border-slate-50">
                    <form action="<?php echo BASE_URL; ?>/src/controllers/messages.php?action=send" method="POST" class="flex gap-4">
                        <input type="hidden" name="recipient_id" value="<?php echo $to_id; ?>">
                        <input type="text" name="message" required class="form-input !rounded-2xl" placeholder="Write a professional message..." autocomplete="off">
                        <button type="submit" class="btn-primary !rounded-2xl !px-8">
                            <i class="fas fa-paper-plane mr-2 text-xs"></i> Send
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center opacity-40">
                    <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-300 text-3xl mb-6">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tighter">Secure Messaging</h3>
                    <p class="text-sm font-medium text-slate-500 mt-2 max-w-xs">Select a lead builder or collaborator to start a private conversation.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const msgBox = document.getElementById('chat-messages');
    if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
