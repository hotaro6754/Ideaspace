<?php
ob_start();
$user = getCurrentUser();
$db = getConnection();

// Filter logic
$domain = $_GET['domain'] ?? null;
$role_filter = $_GET['role'] ?? null;
$search = $_GET['search'] ?? null;

$query = "SELECT i.*, u.name as author_name, u.academic_role
          FROM ideas i
          JOIN users u ON i.user_id = u.id
          WHERE i.status = 'open'";

$params = [];
$types = "";

if ($domain) {
    $query .= " AND i.domain = ?";
    $params[] = $domain;
    $types .= "s";
}

if ($role_filter) {
    $query .= " AND u.academic_role = ?";
    $params[] = $role_filter;
    $types .= "s";
}

if ($search) {
    $query .= " AND (i.title LIKE ? OR i.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$query .= " ORDER BY i.created_at DESC";

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$all_ideas = $stmt->get_result();

$ideas = [];
while ($row = $all_ideas->fetch_assoc()) {
    $ideas[] = $row;
}

// Group ideas
$expert_ideas = array_filter($ideas, fn($i) => in_array($i['academic_role'], ['Senior', 'Alumni', 'Faculty', 'Advisor']));
$community_ideas = array_filter($ideas, fn($i) => !in_array($i['academic_role'], ['Senior', 'Alumni', 'Faculty', 'Advisor']));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Premium Header -->
    <div class="relative mb-20">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-10">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-[10px] font-black uppercase tracking-[0.3em] mb-6 border border-primary/10">
                    <i class="fas fa-hammer"></i> Innovation Forge
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter leading-none mb-6">Explore the <br/><span class="text-primary">Problem Stack.</span></h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">Filter through genuine technical challenges posted by the LIET community. From embedded systems to decentralized finance.</p>
            </div>

            <div class="flex-shrink-0">
                <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn-primary !px-10 !py-5 !text-base !rounded-2xl shadow-xl shadow-primary/20">
                    Deploy Problem <i class="fas fa-plus-circle ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="mt-16 p-2 bg-white rounded-3xl border border-slate-100 shadow-premium flex flex-col md:flex-row gap-2">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <form action="<?php echo BASE_URL; ?>" method="GET">
                    <input type="hidden" name="page" value="ideas">
                    <input type="text" name="search" value="<?php echo sanitize($search); ?>" class="w-full bg-transparent border-none py-5 pl-14 pr-6 text-sm font-bold text-slate-900 focus:ring-0" placeholder="Search by keyword, technology, or domain...">
                </form>
            </div>
            <div class="flex bg-slate-50 p-1 rounded-2xl">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="px-6 py-4 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all <?php echo !$role_filter ? 'bg-white shadow-premium text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">All Signal</a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas&role=senior" class="px-6 py-4 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all <?php echo $role_filter === 'senior' ? 'bg-white shadow-premium text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">Senior-Led</a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas&role=alumni" class="px-6 py-4 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all <?php echo $role_filter === 'alumni' ? 'bg-white shadow-premium text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">Alumni-Guided</a>
            </div>
        </div>
    </div>

    <!-- Expert Tracks -->
    <?php if (!empty($expert_ideas) && !$search): ?>
    <div class="mb-24 animate-fade-up">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase flex items-center gap-3">
                <span class="h-2 w-2 bg-amber-500 rounded-full animate-pulse"></span> Expert Problem Statements
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($expert_ideas as $idea): ?>
            <div class="premium-card p-1 bg-slate-900 border-none group transition-all">
                <div class="p-8 h-full bg-slate-900 rounded-[1.4rem] relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute -right-12 -top-12 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-[9px] font-black uppercase tracking-widest text-primary-light bg-primary/10 px-2 py-1 rounded"><?php echo sanitize($idea['domain']); ?></span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-amber-500 bg-amber-500/10 px-2 py-1 rounded"><?php echo sanitize($idea['academic_role']); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>">
                            <h3 class="text-xl font-black text-white mb-4 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h3>
                            <p class="text-white/50 text-sm font-medium leading-relaxed line-clamp-3 mb-10"><?php echo sanitize($idea['description']); ?></p>
                        </a>
                    </div>
                    <div class="relative z-10 flex items-center justify-between pt-6 border-t border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="h-6 w-6 rounded-lg bg-white/10 flex items-center justify-center text-[10px] font-bold text-white"><?php echo substr($idea['author_name'], 0, 1); ?></div>
                            <span class="text-[10px] font-bold text-white/30 tracking-widest uppercase"><?php echo sanitize($idea['author_name']); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black text-primary-light uppercase tracking-widest group-hover:translate-x-1 transition-transform flex items-center gap-1">
                            Analyze <i class="fas fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Community Grid -->
    <div class="animate-fade-up" style="animation-delay: 0.1s">
        <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase mb-10 flex items-center gap-3">
             Community Feed
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php
            $display_list = $search ? $ideas : $community_ideas;
            if (empty($display_list)): ?>
                <div class="col-span-full py-32 text-center bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
                    <div class="h-16 w-16 bg-white shadow-premium rounded-2xl flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">No problems detected</h3>
                    <p class="text-sm text-slate-500 font-medium">Try broadening your search or domain filters.</p>
                </div>
            <?php else: ?>
                <?php foreach($display_list as $idea): ?>
                <div class="premium-card p-10 bg-white hover:border-primary/20 group transition-all">
                    <div class="flex items-center justify-between mb-8">
                        <span class="badge badge-primary !text-[9px]"><?php echo sanitize($idea['domain']); ?></span>
                        <div class="flex items-center gap-2">
                            <i class="far fa-thumbs-up text-slate-300 text-xs"></i>
                            <span class="text-[10px] font-black text-slate-400"><?php echo $idea['upvotes']; ?></span>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h3>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed line-clamp-3 mb-10"><?php echo sanitize($idea['description']); ?></p>
                    </a>
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center gap-3">
                             <div class="h-8 w-8 rounded-xl bg-slate-50 flex items-center justify-center text-primary font-bold text-[10px] border border-slate-100">
                                <?php echo substr($idea['author_name'], 0, 1); ?>
                             </div>
                             <div>
                                 <p class="text-[10px] font-bold text-slate-900 leading-none mb-1"><?php echo sanitize($idea['author_name']); ?></p>
                                 <p class="text-[8px] font-black text-primary uppercase tracking-widest"><?php echo sanitize($idea['academic_role'] ?? 'Student'); ?></p>
                             </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-slate-900 hover:text-primary transition-all flex items-center gap-2">
                            Forge <i class="fas fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
