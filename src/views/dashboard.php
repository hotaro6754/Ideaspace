<?php
if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "/?page=login");
    exit();
}

$db = getConnection();
$user_id = $_SESSION['user_id'];

// Fetch user data with stats
$query = "SELECT u.*, br.rank, br.points, br.ideas_posted, br.collaborations
          FROM users u
          LEFT JOIN builder_rank br ON u.id = br.user_id
          WHERE u.id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Parse interests
$interests = array_filter(array_map('trim', explode(',', $user['interests'] ?? '')));
if (empty($interests)) $interests = ['Web Dev', 'AI/ML', 'Blockchain'];

// Personalized Feed Query (Personalized based on interests)
$personalized_query = "SELECT i.*, u.name as author_name, u.academic_role,
                        (SELECT COUNT(*) FROM idea_comments WHERE idea_id = i.id) as comment_count
                        FROM ideas i
                        JOIN users u ON i.user_id = u.id
                        WHERE i.status = 'open' ";

if (!empty($interests)) {
    $personalized_query .= " AND (";
    foreach ($interests as $idx => $interest) {
        $personalized_query .= ($idx === 0 ? "" : " OR ") . "i.domain LIKE ?";
    }
    $personalized_query .= ")";
}
$personalized_query .= " ORDER BY i.created_at DESC LIMIT 15";

$stmt_feed = $db->prepare($personalized_query);
if (!empty($interests)) {
    $params = array_map(fn($i) => "%$i%", $interests);
    $stmt_feed->bind_param(str_repeat("s", count($interests)), ...$params);
}
$stmt_feed->execute();
$ideas = $stmt_feed->get_result()->fetch_all(MYSQLI_ASSOC);

// Recent activity
$act_query = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt_act = $db->prepare($act_query);
$stmt_act->bind_param("i", $user_id);
$stmt_act->execute();
$recent_activities = $stmt_act->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Premium Dashboard Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-16 animate-fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest mb-4 border border-primary/20">
                <i class="fas fa-bolt"></i> Personal Control Center
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tighter leading-none mb-4">
                Welcome back, <br/><span class="text-primary"><?php echo sanitize(explode(' ', $user['name'])[0]); ?></span>
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <span class="px-3 py-1 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">
                    Rank: <?php echo $user['rank'] ?? 'INITIATE'; ?>
                </span>
                <span class="px-3 py-1 bg-white border border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-widest rounded-lg">
                    <?php echo count($interests); ?> Interests Mapped
                </span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn-primary !py-4 !px-8 !text-sm !rounded-2xl shadow-xl shadow-primary/20">
                <i class="fas fa-plus-circle mr-2"></i> Deploy Idea
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Activity Feed -->
        <div class="lg:col-span-2 space-y-8 animate-fade-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Personalized Tracks</h2>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-bold text-primary hover:underline">View All</a>
            </div>

            <?php if (empty($ideas)): ?>
                <div class="premium-card p-10 md:p-20 text-center bg-slate-50/50 border-dashed">
                    <div class="h-20 w-20 rounded-3xl bg-white shadow-premium flex items-center justify-center text-slate-300 mx-auto mb-8">
                        <i class="fas fa-satellite-dish text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Signal Lost</h3>
                    <p class="text-slate-500 max-w-sm mx-auto font-medium">Update your academic interests to receive targeted problem statement recommendations.</p>
                    <a href="<?php echo BASE_URL; ?>/?page=onboarding" class="btn-outline !py-3 !px-8 mt-8 !text-xs !font-black uppercase tracking-widest">Update Mapping</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6">
                    <?php foreach($ideas as $idea): ?>
                    <div class="premium-card p-1 bg-gradient-to-br from-white to-slate-50/50 hover:from-primary/5 hover:to-white transition-all group">
                        <div class="p-8 h-full flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                            <?php echo strtoupper(substr($idea['author_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-900"><?php echo sanitize($idea['author_name']); ?></p>
                                            <p class="text-[9px] font-black text-primary uppercase tracking-widest"><?php echo sanitize($idea['academic_role']); ?></p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-white border border-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-lg"><?php echo sanitize($idea['domain']); ?></span>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="block">
                                    <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h3>
                                    <p class="text-slate-500 text-sm font-medium leading-relaxed line-clamp-2 mb-8"><?php echo sanitize($idea['description']); ?></p>
                                </a>
                            </div>
                            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-2 text-slate-400">
                                        <i class="far fa-thumbs-up"></i>
                                        <span class="text-[10px] font-black"><?php echo $idea['upvotes']; ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-400">
                                        <i class="far fa-comment-alt"></i>
                                        <span class="text-[10px] font-black"><?php echo $idea['comment_count']; ?></span>
                                    </div>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 hover:text-primary transition-all flex items-center gap-2">
                                    Analyze <i class="fas fa-chevron-right text-[8px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Diagnostics -->
        <div class="space-y-10 animate-fade-up" style="animation-delay: 0.2s">
            <!-- User Pulse -->
            <div class="premium-card p-1 bg-slate-900">
                <div class="p-8 h-full bg-slate-900 rounded-[1.4rem] relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-white font-bold mb-8">Builder Pulse</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-[9px] font-black uppercase text-white/30 tracking-widest mb-1">Impact Points</p>
                                <p class="text-2xl font-black text-white"><?php echo $user['points'] ?? 0; ?></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                                <p class="text-[9px] font-black uppercase text-white/30 tracking-widest mb-1">Collaborations</p>
                                <p class="text-2xl font-black text-white"><?php echo $user['collaborations'] ?? 0; ?></p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="btn-primary !w-full !bg-white !text-slate-900 !py-4 !text-[10px] !font-black uppercase tracking-widest mt-8">
                            Talent Board
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Log -->
            <div class="premium-card p-8 bg-white">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-8">System Activity</h3>
                <div class="space-y-6">
                    <?php foreach($recent_activities as $idx => $act): ?>
                    <div class="flex gap-4 group cursor-default">
                        <div class="flex flex-col items-center">
                            <div class="h-2 w-2 rounded-full bg-primary ring-4 ring-primary/10"></div>
                            <?php if ($idx < count($recent_activities) - 1): ?>
                            <div class="w-px flex-1 bg-slate-100 my-1"></div>
                            <?php endif; ?>
                        </div>
                        <div class="pb-2">
                            <p class="text-[11px] font-bold text-slate-900 leading-none mb-1 group-hover:text-primary transition-colors"><?php echo ucfirst($act['action']); ?> <?php echo $act['entity_type']; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight"><?php echo date('H:i, M d', strtotime($act['created_at'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_activities)): ?>
                        <div class="text-center py-8 opacity-20">
                            <i class="fas fa-inbox text-2xl mb-4 block"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest">No logs found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Builders Recommendation -->
            <div class="premium-card p-8 bg-slate-50/50">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-8">Elite Builders</h3>
                <div class="space-y-4">
                    <?php
                    $suggested = [
                        ['name' => 'Sai Krishna', 'role' => 'Architect', 'points' => 1240],
                        ['name' => 'Priya Reddy', 'role' => 'Legend', 'points' => 3100]
                    ];
                    foreach($suggested as $person): ?>
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary font-bold shadow-sm group-hover:scale-105 transition-transform">
                                <?php echo substr($person['name'], 0, 1); ?>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900"><?php echo $person['name']; ?></p>
                                <p class="text-[9px] font-black text-primary uppercase tracking-widest"><?php echo $person['role']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                             <p class="text-[10px] font-black text-slate-900"><?php echo $person['points']; ?></p>
                             <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Points</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="w-full mt-8 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-colors border-t border-slate-100">
                    Find More Mentors
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
