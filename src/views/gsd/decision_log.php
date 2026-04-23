<?php
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
$idea_id = (int)($_GET['id'] ?? 0);
$db = getConnection();

$stmt = $db->prepare("SELECT * FROM ideas WHERE id = ?");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();
if (!$idea) redirect(BASE_URL . '/?page=ideas');

$log_stmt = $db->prepare("SELECT dl.*, u.name as user_name FROM decision_logs dl JOIN users u ON dl.user_id = u.id WHERE dl.idea_id = ? ORDER BY dl.created_at DESC");
$log_stmt->bind_param("i", $idea_id);
$log_stmt->execute();
$logs = $log_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea_id; ?>" class="text-[10px] font-black uppercase text-slate-400 hover:text-primary">Track Details</a></li>
                    <li><i class="fas fa-chevron-right text-[8px] text-slate-300"></i></li>
                    <li><span class="text-[10px] font-black uppercase text-slate-900">Decision Log</span></li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Architectural <span class="text-primary">Decision Log</span></h1>
            <p class="text-slate-500 font-medium mt-2"><?php echo sanitize($idea['title']); ?></p>
        </div>
        <button onclick="document.getElementById('decisionModal').classList.remove('hidden')" class="btn-primary !py-3 !px-6 !text-xs !rounded-xl">
            Log Decision
        </button>
    </div>

    <div class="space-y-6">
        <?php foreach($logs as $l): ?>
        <div class="premium-card p-8 bg-white border border-slate-50 shadow-subtle group">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-black text-slate-900 group-hover:text-primary transition-colors"><?php echo sanitize($l['decision_title']); ?></h3>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"><?php echo date('M d, Y', strtotime($l['created_at'])); ?></span>
            </div>
            <p class="text-sm text-slate-600 font-medium leading-relaxed mb-6"><?php echo nl2br(sanitize($l['decision_context'])); ?></p>
            <div class="pt-4 border-t border-slate-50 flex items-center gap-2">
                <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-500"><?php echo strtoupper(substr($l['user_name'], 0, 1)); ?></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Logged by <?php echo sanitize($l['user_name']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($logs)): ?>
            <div class="py-20 text-center opacity-30">
                <i class="fas fa-history text-4xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase">Empty Log</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">No critical decisions have been recorded for this track yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Log Modal -->
<div id="decisionModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="premium-card !rounded-3xl w-full max-w-lg bg-white relative z-10 animate-fade-up">
        <form action="<?php echo BASE_URL; ?>/src/controllers/gsd.php?action=log_decision" method="POST" class="p-10">
            <input type="hidden" name="idea_id" value="<?php echo $idea_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

            <h3 class="text-2xl font-black text-slate-900 mb-2">Log Decision</h3>
            <p class="text-sm text-slate-500 font-medium mb-8">Record why a specific path or tool was chosen.</p>

            <div class="space-y-6">
                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" required class="form-input" placeholder="e.g. Migrating to Supabase PG">
                </div>
                <div>
                    <label class="form-label">Context & Rationale</label>
                    <textarea name="context" rows="4" required class="form-textarea" placeholder="Explain the reasoning..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-10">
                <button type="button" onclick="document.getElementById('decisionModal').classList.add('hidden')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary px-8">Persist Decision</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
