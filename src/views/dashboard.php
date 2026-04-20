<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

$db = getConnection();
$user_id = $user['id'];
$interests = json_decode($user['interests'] ?? '[]', true);
$activity_stmt = $db->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$activity_stmt->bind_param("i", $user_id);
$activity_stmt->execute();
$recent_activities = $activity_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch personalized ideas based on interests
$interest_placeholders = empty($interests) ? "''" : implode(',', array_fill(0, count($interests), '?'));
$query = "SELECT i.*, u.name as author_name, u.academic_role
          FROM ideas i
          JOIN users u ON i.user_id = u.id
          WHERE i.status = 'open'";

if (!empty($interests)) {
    $query .= " AND (";
    foreach ($interests as $index => $interest) {
        $query .= ($index === 0 ? "" : " OR ") . "i.domain LIKE ?";
    }
    $query .= ")";
}
$query .= " ORDER BY i.created_at DESC LIMIT 10";

$stmt = $db->prepare($query);
if (!empty($interests)) {
    $params = array_map(fn($i) => "%$i%", $interests);
    $stmt->bind_param(str_repeat("s", count($interests)), ...$params);
}
$stmt->execute();
$personalized_ideas = $stmt->get_result();
$ideas = [];
while ($row = $personalized_ideas->fetch_assoc()) {
    $ideas[] = $row;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 animate-fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-[10px] font-bold uppercase tracking-widest mb-4">
                <i class="fas fa-shield-alt"></i> Personalized Feed
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                For You, <?php echo sanitize(explode(' ', $user['name'])[0]); ?>
            </h1>
            <p class="mt-2 text-slate-500 font-medium">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                    <?php echo sanitize($user['academic_role'] ?? 'Builder'); ?>
                </span>
                • Based on your interests: <?php echo implode(', ', array_map('sanitize', $interests)); ?>
            </p>
        </div>
        <div class="flex items-center gap-4">
             <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn-primary !py-3 !px-6 text-sm">
                <i class="fas fa-plus mr-2"></i> Post Problem Statement
             </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Feed -->
        <div class="lg:col-span-2 space-y-8 animate-fade-up">
            <?php if (empty($ideas)): ?>
                <div class="premium-card p-12 text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mx-auto mb-6">
                        <i class="fas fa-rss text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No matching ideas yet</h3>
                    <p class="text-slate-500 max-w-sm mx-auto">Try updating your interests or be the first to post a problem statement in your domain!</p>
                    <a href="<?php echo BASE_URL; ?>/?page=onboarding" class="text-primary font-bold text-sm mt-6 inline-block hover:underline">Update Interests</a>
                </div>
            <?php else: ?>
                <?php foreach($ideas as $idea): ?>
                <div class="premium-card p-8 group hover:border-primary/20 transition-all">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-sm shadow-inner">
                                <?php echo strtoupper(substr($idea['author_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900"><?php echo sanitize($idea['author_name']); ?></h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-primary"><?php echo sanitize($idea['academic_role']); ?></span>
                                    <span class="text-[10px] text-slate-400 font-medium">• <?php echo date('M d', strtotime($idea['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        <span class="badge badge-primary"><?php echo sanitize($idea['domain']); ?></span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="block group-hover:translate-x-1 transition-transform">
                        <h3 class="text-2xl font-bold text-slate-900 mb-4"><?php echo sanitize($idea['title']); ?></h3>
                        <p class="text-slate-500 leading-relaxed line-clamp-3 mb-6"><?php echo sanitize($idea['description']); ?></p>
                    </a>
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center gap-6">
                            <button class="flex items-center gap-2 text-slate-400 hover:text-primary transition-colors">
                                <i class="far fa-thumbs-up text-lg"></i>
                                <span class="text-xs font-bold"><?php echo $idea['upvotes']; ?></span>
                            </button>
                            <button class="flex items-center gap-2 text-slate-400 hover:text-primary transition-colors">
                                <i class="far fa-comment text-lg"></i>
                                <span class="text-xs font-bold"><?php echo $idea['comment_count'] ?? 0; ?></span>
                            </button>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-slate-900 hover:text-primary transition-colors">
                            Collaborate <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-12 animate-fade-up">
            <!-- Stats -->
            <div class="premium-card p-8 bg-slate-900 text-white relative overflow-hidden">
                 <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
                 <h3 class="text-lg font-bold mb-6 relative z-10">Your Stats</h3>
                 <div class="grid grid-cols-2 gap-4 relative z-10">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <p class="text-[10px] font-black uppercase text-white/40 tracking-widest mb-1">Impact</p>
                        <p class="text-xl font-bold"><?php echo $user['points'] ?? 0; ?></p>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <p class="text-[10px] font-black uppercase text-white/40 tracking-widest mb-1">Streak</p>
                        <p class="text-xl font-bold">5d</p>
                    </div>
                 </div>
                 <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="btn-primary !w-full !py-3 !text-xs !bg-white !text-slate-900 mt-6 relative z-10">
                    View Talent Board
                </a>
            </div>

<section class="mb-12">
                <h3 class="text-sm font-black text-slate-900 mb-6 uppercase tracking-[0.2em]">Your Activity</h3>
                <div class="space-y-3">
                    <?php foreach($recent_activities as $act): ?>
                    <div class="p-3 rounded-xl bg-white border border-slate-50 shadow-subtle">
                        <p class="text-[11px] font-bold text-slate-700"><?php echo ucfirst($act['action']); ?> <?php echo $act['entity_type']; ?></p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase"><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_activities)): ?>
                        <p class="text-xs text-slate-400 italic">No activity recorded yet.</p>
                    <?php endif; ?>
                </div>
            </section>
            <!-- Suggested Builders -->
            <section>
                <h3 class="text-sm font-black text-slate-900 mb-6 uppercase tracking-[0.2em]">Top Collaborators</h3>
                <div class="space-y-4">
                    <?php
                    $suggested = [
                        ['name' => 'Sai Krishna', 'role' => 'Senior', 'branch' => 'CSE'],
                        ['name' => 'Priya Reddy', 'role' => 'Alumni', 'branch' => 'ECE'],
                        ['name' => 'Manoj Kumar', 'role' => 'Senior', 'branch' => 'CSSE']
                    ];
                    foreach($suggested as $person): ?>
                    <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-subtle flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-slate-50 flex items-center justify-center text-primary font-bold text-xs">
                                <?php echo substr($person['name'], 0, 1); ?>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900"><?php echo $person['name']; ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?php echo $person['role']; ?> • <?php echo $person['branch']; ?></p>
                            </div>
                        </div>
                        <button class="text-primary"><i class="fas fa-plus-circle"></i></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
