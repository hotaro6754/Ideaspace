<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../helpers/Security.php';

$idea_id = (int)($_GET['id'] ?? 0);
if ($idea_id === 0) {
    header("Location: " . BASE_URL . "/?page=ideas");
    exit();
}

$db = getConnection();
if (!$db) exit('DB Error');

// Fetch idea with creator info
$query = "SELECT i.*, u.name as creator_name, u.roll_number as creator_roll, u.profile_pic
          FROM ideas i
          JOIN users u ON i.user_id = u.id
          WHERE i.id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) {
    header("Location: " . BASE_URL . "/?page=ideas");
    exit();
}

// Fetch comments
$c_query = "SELECT c.*, u.name, u.profile_pic
            FROM idea_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.idea_id = ? AND c.is_deleted = 0
            ORDER BY c.created_at DESC";
$c_stmt = $db->prepare($c_query);
$c_stmt->bind_param("i", $idea_id);
$c_stmt->execute();
$comments = $c_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="mb-12 animate-fade-up">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li><a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-primary transition-colors">Innovation Tracks</a></li>
                <li><i class="fas fa-chevron-right text-[10px] text-slate-300"></i></li>
                <li><span class="text-xs font-bold text-slate-900 uppercase tracking-widest">Detail</span></li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-full border border-primary/20">
                        <?php echo sanitize($idea['domain']); ?>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                        Track #<?php echo $idea['id']; ?>
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-2"><?php echo sanitize($idea['title']); ?></h1>
            </div>

            <?php if (isLoggedIn() && $_SESSION['user_id'] != $idea['user_id']): ?>
                <button onclick="document.getElementById('applyModal').classList.remove('hidden')" class="btn-primary !px-10 !py-4 shadow-premium">
                    Apply to Track
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 animate-fade-up" style="animation-delay: 0.1s">
        <div class="lg:col-span-2 space-y-12">
            <section class="premium-card p-10 bg-white">
                <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed mb-10">
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

                <?php
                // GSD Charter Widget Integration (for creator)
                if (isLoggedIn() && $_SESSION['user_id'] == $idea['user_id']) {
                    include __DIR__ . '/../gsd/charter_widget.php';
                }
                ?>
            </section>

            <!-- Comments Section -->
            <section class="space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Discussion</h2>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?php echo count($comments); ?> Comments</span>
                </div>

                <?php if (isLoggedIn()): ?>
                    <div class="premium-card p-6 bg-white">
                        <form action="<?php echo BASE_URL; ?>/src/controllers/comments.php?action=create" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                            <input type="hidden" name="idea_id" value="<?php echo $idea_id; ?>">
                            <textarea name="content" required class="form-input h-24 mb-4" placeholder="Add your thoughts or questions..."></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary !py-2 !text-xs">Post Comment</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <?php foreach($comments as $comment): ?>
                        <div class="premium-card p-6 bg-white border border-slate-50">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                    <?php echo strtoupper(substr($comment['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?php echo sanitize($comment['name']); ?></p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo date('M d, H:i', strtotime($comment['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-600 font-medium leading-relaxed">
                                <?php echo nl2br(sanitize($comment['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-12 opacity-40 italic text-sm">No comments yet. Start the conversation!</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="space-y-8">
            <?php
            // Agent Widget Integration
            include __DIR__ . '/../gsd/agent_widget.php';
            include __DIR__ . '/../gsd/roadmap_widget.php';
            include __DIR__ . '/../gsd/health_widget.php';
            include __DIR__ . '/../gsd/nav_widget.php';
            ?>

            <div class="premium-card p-8 bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Track Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Lifecycle</span>
                        <span class="text-primary font-bold uppercase text-[10px] bg-primary/10 px-2 py-0.5 rounded"><?php echo strtoupper($idea['status']); ?></span>
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
                        <?php echo strtoupper(substr($idea['creator_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo sanitize($idea['creator_name']); ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo sanitize($idea['creator_roll']); ?></p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=messages&to=<?php echo $idea['user_id']; ?>" class="btn-outline !w-full !text-xs !py-3">
                    Message Lead
                </a>
                <button onclick="document.getElementById('reportModal').classList.remove('hidden')" class="text-[10px] font-bold text-slate-400 hover:text-secondary transition-colors mt-4 block w-full text-center uppercase tracking-widest">
                    <i class="fas fa-flag mr-1"></i> Report Content
                </button>
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

<!-- Report Modal -->
<div id="reportModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="premium-card !rounded-3xl w-full max-w-lg bg-white relative z-10 animate-fade-up">
        <form action="<?php echo BASE_URL; ?>/src/controllers/admin.php?action=report" method="POST" class="p-10">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
            <input type="hidden" name="content_type" value="idea">
            <input type="hidden" name="content_id" value="<?php echo $idea_id; ?>">

            <h3 class="text-2xl font-bold text-slate-900 mb-2">Report Content</h3>
            <p class="text-sm text-slate-500 font-medium mb-8">Help us keep the community safe. Why are you reporting this?</p>

            <div class="mb-6">
                <label class="form-label">Reason</label>
                <select name="reason" class="form-select" required>
                    <option value="spam">Spam or Misleading</option>
                    <option value="inappropriate">Inappropriate Content</option>
                    <option value="offensive">Offensive/Hate Speech</option>
                    <option value="plagiarism">Plagiarism</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-8">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" rows="3" class="form-textarea" placeholder="Provide more details..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-4">
                <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary px-8">Submit Report</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
