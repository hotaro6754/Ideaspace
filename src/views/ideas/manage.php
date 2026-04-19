<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

$idea_id = (int)($_GET['id'] ?? 0);
$db = getConnection();

$stmt = $db->prepare("SELECT * FROM ideas WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $idea_id, $user['id']);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) redirect(BASE_URL . '/?page=dashboard');

// Get Pending Applications
$app_stmt = $db->prepare("SELECT a.*, u.name, u.branch, u.year, u.roll_number
                          FROM applications a
                          JOIN users u ON a.user_id = u.id
                          WHERE a.idea_id = ? AND a.status = 'pending'");
$app_stmt->bind_param("i", $idea_id);
$app_stmt->execute();
$pending_apps = $app_stmt->get_result();

// Get Active Collaborators
$col_stmt = $db->prepare("SELECT c.*, u.name, u.branch
                          FROM collaborations c
                          JOIN users u ON c.collaborator_id = u.id
                          WHERE c.idea_id = ? AND c.status = 'active'");
$col_stmt->bind_param("i", $idea_id);
$col_stmt->execute();
$active_collabs = $col_stmt->get_result();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <a href="<?php echo BASE_URL; ?>/?page=profile" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-primary transition-colors flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Manage: <?php echo sanitize($idea['title']); ?></h1>
        <p class="mt-2 text-slate-500 font-medium">Review applications and update project milestones.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 animate-fade-up">
        <!-- Sidebar: Status Control -->
        <div class="space-y-8">
            <section class="premium-card p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 h-32 w-32 bg-primary/20 rounded-full blur-2xl"></div>
                <h3 class="text-sm font-black uppercase tracking-widest mb-6 relative z-10">Lifecycle Status</h3>
                <form action="<?php echo BASE_URL; ?>/src/controllers/ideas.php?action=update_status" method="POST" class="space-y-6 relative z-10">
                    <input type="hidden" name="idea_id" value="<?php echo $idea_id; ?>">
                    <select name="status" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all">
                        <option value="open" <?php echo $idea['status'] === 'open' ? 'selected' : ''; ?> class="text-slate-900">Open for Collabs</option>
                        <option value="in_progress" <?php echo $idea['status'] === 'in_progress' ? 'selected' : ''; ?> class="text-slate-900">In Progress</option>
                        <option value="completed" <?php echo $idea['status'] === 'completed' ? 'selected' : ''; ?> class="text-slate-900">Completed / Solved</option>
                        <option value="abandoned" <?php echo $idea['status'] === 'abandoned' ? 'selected' : ''; ?> class="text-slate-900">Abandoned / Archived</option>
                    </select>

                    <div id="completion_fields" class="<?php echo in_array($idea['status'], ['completed', 'abandoned']) ? '' : 'hidden'; ?> space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-white/40 tracking-widest mb-2 block">Solution URL (optional)</label>
                            <input type="url" name="solution_url" value="<?php echo sanitize($idea['solution_url']); ?>" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-xs text-white" placeholder="https://github.com/...">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-white/40 tracking-widest mb-2 block">Lessons Learned</label>
                            <textarea name="lessons_learned" rows="4" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-xs text-white" placeholder="What worked? What failed?"><?php echo sanitize($idea['lessons_learned']); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary !w-full !py-3 !text-xs !bg-white !text-slate-900">
                        Update Tracking
                    </button>
                </form>
            </section>
        </div>

        <!-- Main Content: Applications & Team -->
        <div class="lg:col-span-2 space-y-12">
            <!-- Pending Applications -->
            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Pending Applications</h2>
                <div class="space-y-4">
                    <?php if ($pending_apps->num_rows === 0): ?>
                        <div class="p-8 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <p class="text-slate-400 font-medium italic">No pending requests at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php while($app = $pending_apps->fetch_assoc()): ?>
                        <div class="premium-card p-8 group bg-white">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-primary font-bold text-sm">
                                        <?php echo substr($app['name'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900"><?php echo sanitize($app['name']); ?></h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo sanitize($app['roll_number']); ?> • <?php echo sanitize($app['branch']); ?> Dept</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <form action="<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=respond" method="POST" class="inline">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn-primary !py-2 !px-5 !text-[10px]">Accept</button>
                                    </form>
                                    <form action="<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=respond" method="POST" class="inline">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-outline !py-2 !px-5 !text-[10px] !text-red-500 hover:!bg-red-50">Decline</button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100 italic text-sm text-slate-600">
                                "<?php echo sanitize($app['message']); ?>"
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Current Team -->
            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Builder Team</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if ($active_collabs->num_rows === 0): ?>
                        <div class="col-span-full p-8 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <p class="text-slate-400 font-medium italic">Team building in progress...</p>
                        </div>
                    <?php else: ?>
                        <?php while($col = $active_collabs->fetch_assoc()): ?>
                        <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-subtle flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center font-bold text-xs">
                                    <?php echo substr($col['name'], 0, 1); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?php echo sanitize($col['name']); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?php echo sanitize($col['branch']); ?></p>
                                </div>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/?page=messages&to=<?php echo $col['collaborator_id']; ?>" class="text-primary hover:scale-110 transition-transform">
                                <i class="fas fa-comment-dots"></i>
                            </a>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const fields = document.getElementById('completion_fields');
    if (this.value === 'completed' || this.value === 'abandoned') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
