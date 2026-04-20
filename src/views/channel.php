<?php
if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');
$user_id = $_SESSION['user_id'];
$channel_id = (int)($_GET['id'] ?? 0);
$db = getConnection();

// Verify access
$stmt = $db->prepare("SELECT c.*, col.idea_id FROM channels c JOIN collaborations col ON c.collaboration_id = col.id JOIN channel_members cm ON c.id = cm.channel_id WHERE c.id = ? AND cm.user_id = ?");
$stmt->bind_param("ii", $channel_id, $user_id);
$stmt->execute();
$channel = $stmt->get_result()->fetch_assoc();

if (!$channel) redirect(BASE_URL . '/?page=profile-collaborations');

$msg_stmt = $db->prepare("SELECT m.*, u.name FROM channel_messages m JOIN users u ON m.sender_id = u.id WHERE m.channel_id = ? ORDER BY m.created_at ASC");
$msg_stmt->bind_param("i", $channel_id);
$msg_stmt->execute();
$messages = $msg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 h-[calc(100vh-64px)]">
    <div class="flex h-full premium-card overflow-hidden shadow-premium">
        <!-- Sidebar -->
        <div class="w-full md:w-80 border-r border-slate-100 flex flex-col bg-slate-50/50">
            <div class="p-6 border-b border-slate-100 bg-white">
                <h2 class="text-lg font-bold text-slate-900"><?php echo sanitize($channel['name']); ?></h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Team Channel</p>
            </div>
            <div class="flex-1 p-4">
                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $channel['idea_id']; ?>" class="btn-outline !w-full !py-3 !text-xs mb-4">View Project</a>
                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-4 mb-4">Channel Members</h4>
                <div id="members-list" class="space-y-1">
                    <!-- Members will be loaded via AJAX if needed, but for now it's static in DB -->
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-white">
            <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/30" id="message-container">
                <?php foreach($messages as $m): ?>
                    <div class="flex <?php echo ($m['sender_id'] == $user_id) ? 'justify-end' : 'justify-start'; ?>">
                        <div class="max-w-[70%] p-4 rounded-2xl text-sm font-medium shadow-subtle <?php echo ($m['sender_id'] == $user_id) ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-100'; ?>">
                            <p class="text-[9px] font-black uppercase mb-1 <?php echo ($m['sender_id'] == $user_id) ? 'text-white/60' : 'text-primary'; ?>"><?php echo sanitize($m['name']); ?></p>
                            <?php echo sanitize($m['content']); ?>
                            <p class="text-[9px] mt-2 opacity-60 font-bold uppercase"><?php echo date('H:i', strtotime($m['created_at'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Input -->
            <div class="p-6 bg-white border-t border-slate-50">
                <form action="<?php echo BASE_URL; ?>/src/controllers/channels.php?action=addMessage" method="POST" class="flex gap-4">
                    <input type="hidden" name="channel_id" value="<?php echo $channel_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                    <input type="text" name="content" required class="form-input !rounded-2xl" placeholder="Message the team...">
                    <button type="submit" class="btn-primary !rounded-2xl !px-8">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
