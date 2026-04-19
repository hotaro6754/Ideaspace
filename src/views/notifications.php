<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM notifications WHERE recipient_user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$res = $stmt->get_result();

$notifications = [];
while ($row = $res->fetch_assoc()) $notifications[] = $row;

// Mark as read when viewing
$conn->query("UPDATE notifications SET is_read = 1 WHERE recipient_user_id = " . $user['id']);
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-12 animate-fade-up">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight italic">Alerts</h1>
            <p class="text-slate-500 font-medium mt-1">Updates from your Lendi innovation network.</p>
        </div>
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">Verified Stream</span>
    </div>

    <div class="space-y-4 animate-fade-up">
        <?php foreach($notifications as $notif): ?>
        <div class="premium-card p-6 bg-white <?php echo $notif['is_read'] ? 'opacity-80' : 'border-primary/20 shadow-premium'; ?> group hover:opacity-100 transition-all cursor-default">
            <div class="flex gap-5">
                <div class="h-12 w-12 rounded-2xl bg-primary/5 text-primary flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas <?php
                        echo [
                            'upvote' => 'fa-arrow-up',
                            'message' => 'fa-comments',
                            'application' => 'fa-user-plus',
                            'acceptance' => 'fa-check-circle',
                            'rejection' => 'fa-times-circle'
                        ][$notif['notification_type']] ?? 'fa-bell';
                    ?>"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900"><?php echo sanitize($notif['message']); ?></p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2"><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($notifications)): ?>
            <div class="text-center py-20 opacity-30">
                <i class="fas fa-satellite-dish text-4xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest">No new updates</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">Active innovations will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
