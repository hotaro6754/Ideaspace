<?php
ob_start();
$idea_id = (int)($_GET['id'] ?? 0);
$conn = getConnection();

$stmt = $conn->prepare("SELECT ideas.*, users.name as creator_name, users.branch as creator_branch, users.roll_number as creator_roll
                        FROM ideas
                        JOIN users ON ideas.user_id = users.id
                        WHERE ideas.id = ?");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) redirect(BASE_URL . '/?page=ideas');

$has_applied = false;
if (isLoggedIn()) {
    $check = $conn->prepare("SELECT id FROM applications WHERE idea_id = ? AND user_id = ?");
    $check->bind_param("ii", $idea_id, $_SESSION['user_id']);
    $check->execute();
    if ($check->get_result()->fetch_assoc()) $has_applied = true;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-primary transition-colors flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Back to Tracks
        </a>
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-6">
                    <span class="badge badge-primary"><?php echo sanitize($idea['domain']); ?></span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Track #<?php echo $idea['id']; ?> <form action="<?php echo BASE_URL; ?>/src/controllers/comments.php?action=upvote" method="POST" class="inline ml-4"><input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>"><button type="submit" class="hover:text-primary transition-colors cursor-pointer"><i class="fas fa-arrow-up text-[10px] mr-1"></i> Upvote</button></form></span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4"><?php echo sanitize($idea['title']); ?></h1>
                <div class="flex items-center gap-4 text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-primary">
                            <?php echo strtoupper(substr($idea['creator_name'], 0, 1)); ?>
                        </div>
                        <span class="text-sm">Initiated by <span class="text-slate-900 font-bold"><?php echo sanitize($idea['creator_name']); ?></span></span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <span class="text-sm"><?php echo sanitize($idea['creator_branch']); ?> Dept</span>
                </div>
            </div>

            <div class="flex flex-col gap-3 min-w-[200px]">
                <?php if (isLoggedIn()): ?>
                    <?php if ($has_applied): ?>
                        <div class="btn-outline !bg-green-50 !border-green-100 !text-green-700 !cursor-default">
                            <i class="fas fa-check-circle mr-2"></i> Application Sent
                        </div>
                    <?php else: ?>
                        <button onclick="document.getElementById('applyModal').classList.remove('hidden')" class="btn-primary !py-4">
                            Join Collaboration
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/?page=login" class="btn-primary !py-4">Sign in to Join</a>
                <?php endif; ?>
                <div class="flex items-center justify-center gap-2 text-slate-400 font-bold text-[10px] uppercase tracking-widest">
                    <i class="fas fa-users"></i> <?php echo $idea['applicant_count']; ?> Applicants
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 animate-fade-up">
        <div class="lg:col-span-2 space-y-12">
            <section class="premium-card p-10 bg-white">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 border-b border-slate-50 pb-4">Vision & Requirements</h2>
                <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed">
                    <?php echo nl2br(sanitize($idea['description'])); ?>
                </div>

                <?php if ($idea['skills_needed']): ?>
                    <div class="mt-12">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Required Expertise</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach(json_decode($idea['skills_needed']) as $skill): ?>
                                <span class="px-3 py-1.5 bg-slate-50 text-slate-600 text-xs font-bold rounded-lg border border-slate-100"><?php echo sanitize($skill); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="space-y-8">
            <div class="premium-card p-8 bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Track Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Lifecycle</span>
                        <span class="text-primary font-bold uppercase text-[10px] bg-primary/10 px-2 py-0.5 rounded">Discovery</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Upvotes</span>
                        <span class="text-slate-900 font-bold"><?php echo $idea['upvotes']; ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Created</span>
                        <span class="text-slate-900 font-bold"><?php echo date('M d, Y', strtotime($idea['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="premium-card p-8">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Lead Builder</h3>
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-2xl bg-primary text-white flex items-center justify-center font-bold">
                        <?php echo strtoupper(substr($idea['creator_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo sanitize($idea['creator_name']); ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo sanitize($idea['creator_roll']); ?></p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=messages&to=<?php echo $idea['user_id']; ?>" class="btn-outline !w-full !text-xs !py-3">
                    Message Lead
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Application Modal -->
<div id="applyModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="premium-card !rounded-3xl w-full max-w-lg bg-white relative z-10 animate-fade-up">
        <form action="<?php echo BASE_URL; ?>/src/controllers/collaboration.php?action=apply" method="POST" class="p-10">
            <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Join Collaboration</h3>
            <p class="text-sm text-slate-500 font-medium mb-8">Briefly explain your interest and how you can contribute to this track.</p>

            <div class="mb-8">
                <label for="message" class="form-label">Contribution Pitch</label>
                <textarea id="message" name="message" rows="4" required class="form-textarea" placeholder="e.g. I have experience with PHP and would love to help with the database structure..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-4">
                <button type="button" onclick="document.getElementById('applyModal').classList.add('hidden')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary px-8">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
