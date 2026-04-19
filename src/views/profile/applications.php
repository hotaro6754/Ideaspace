<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

$conn = getConnection();
// Applications for tracks I OWN
$res = $conn->prepare("SELECT applications.*, users.name, users.roll_number, users.branch, ideas.title
                        FROM applications
                        JOIN users ON applications.user_id = users.id
                        JOIN ideas ON applications.idea_id = ideas.id
                        WHERE ideas.user_id = ?
                        ORDER BY applications.applied_at DESC");
$res->bind_param("i", $user['id']);
$res->execute();
$apps = $res->get_result();

$received_apps = [];
while ($row = $apps->fetch_assoc()) $received_apps[] = $row;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Collaboration Requests</h1>
        <p class="mt-2 text-slate-500 font-medium">Review and accept students into your innovation tracks.</p>
    </div>

    <div class="space-y-6 animate-fade-up">
        <?php foreach($received_apps as $app): ?>
        <div class="premium-card p-10 bg-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-primary text-white flex items-center justify-center font-bold">
                            <?php echo strtoupper(substr($app['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900"><?php echo sanitize($app['name']); ?></h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo sanitize($app['roll_number']); ?> • <?php echo sanitize($app['branch']); ?> Dept</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <p class="text-xs font-black text-primary uppercase tracking-widest mb-2">Track: <?php echo sanitize($app['title']); ?></p>
                        <p class="text-sm text-slate-600 font-medium italic">"<?php echo sanitize($app['message']); ?>"</p>
                    </div>
                </div>

                <div class="flex md:flex-col gap-3 min-w-[160px]">
                    <?php if ($app['status'] === 'pending'): ?>
                        <form action="<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=respond" method="POST">
                            <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn-primary !w-full !py-3 !text-xs">Accept Student</button>
                        </form>
                        <form action="<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=respond" method="POST">
                            <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn-outline !w-full !py-3 !text-xs">Decline</button>
                        </form>
                    <?php else: ?>
                        <span class="badge <?php echo ($app['status'] === 'accepted') ? 'badge-success' : 'badge-primary'; ?> text-center !py-3 !w-full">
                            <?php echo strtoupper($app['status']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($received_apps)): ?>
            <div class="text-center py-20 opacity-30">
                <i class="fas fa-user-clock text-4xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest">No requests yet</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">Requests for your posted tracks will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
