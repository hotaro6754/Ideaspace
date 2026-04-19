<?php
ob_start();
$user = getCurrentUser();
$db = getConnection();

// Filter logic
$domain = $_GET['domain'] ?? null;
$role_filter = $_GET['role'] ?? null;

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
$senior_alumni_ideas = array_filter($ideas, fn($i) => in_array($i['academic_role'], ['senior', 'alumni']));
$student_ideas = array_filter($ideas, fn($i) => $i['academic_role'] === 'student' || !$i['academic_role']);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 animate-fade-up">
        <div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">Innovation Forge</h1>
            <p class="mt-2 text-slate-500 font-medium">Explore problem statements and find your next collaboration.</p>
        </div>
        <div class="flex items-center gap-4">
             <div class="flex bg-white p-1 rounded-xl border border-slate-100 shadow-subtle">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo !$role_filter ? 'bg-primary text-white' : 'text-slate-500 hover:text-slate-900'; ?>">All</a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas&role=senior" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $role_filter === 'senior' ? 'bg-primary text-white' : 'text-slate-500 hover:text-slate-900'; ?>">Senior-Led</a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas&role=alumni" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $role_filter === 'alumni' ? 'bg-primary text-white' : 'text-slate-500 hover:text-slate-900'; ?>">Alumni-Guided</a>
             </div>
        </div>
    </div>

    <!-- Senior & Alumni Spotlight -->
    <?php if (!empty($senior_alumni_ideas) && !$role_filter): ?>
    <section class="mb-20 animate-fade-up">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest flex items-center gap-3">
                <i class="fas fa-star text-amber-500"></i> Expert Problem Statements
            </h2>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Guidance from Seniors & Alumni</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($senior_alumni_ideas as $idea): ?>
                <div class="premium-card p-8 bg-slate-900 text-white border-none group relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 h-32 w-32 bg-primary/20 rounded-full blur-2xl"></div>
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-white/10 text-primary-light"><?php echo sanitize($idea['domain']); ?></span>
                        <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-amber-500/20 text-amber-500"><?php echo sanitize($idea['academic_role']); ?></span>
                    </div>
                    <h3 class="text-xl font-bold mb-4 relative z-10 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h3>
                    <p class="text-white/60 text-sm leading-relaxed line-clamp-3 mb-8 relative z-10"><?php echo sanitize($idea['description']); ?></p>
                    <div class="flex items-center justify-between relative z-10 pt-6 border-t border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-bold">
                                <?php echo substr($idea['author_name'], 0, 1); ?>
                            </div>
                            <span class="text-[10px] font-bold text-white/40"><?php echo sanitize($idea['author_name']); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-primary-light group-hover:translate-x-1 transition-transform">
                            View Mentorship <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <div class="h-px bg-slate-100 mb-20"></div>
    <?php endif; ?>

    <!-- Community Ideas -->
    <section class="animate-fade-up">
        <h2 class="text-xl font-black text-slate-900 uppercase tracking-widest mb-8">Community Feed</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $display_ideas = ($role_filter) ? $ideas : $student_ideas;
            if (empty($display_ideas)): ?>
                <div class="col-span-full py-20 text-center">
                    <p class="text-slate-400 font-medium">No problem statements found in this category.</p>
                </div>
            <?php else: ?>
                <?php foreach($display_ideas as $idea): ?>
                <div class="premium-card p-8 group hover:border-primary/20 transition-all">
                    <div class="flex items-start justify-between mb-6">
                        <span class="badge badge-primary"><?php echo sanitize($idea['domain']); ?></span>
                        <div class="h-8 w-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-300">
                             <i class="fas fa-lightbulb"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h3>
                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-6"><?php echo sanitize($idea['description']); ?></p>
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center gap-3">
                             <span class="text-[10px] font-black uppercase tracking-widest text-primary"><?php echo sanitize($idea['academic_role'] ?? 'Student'); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-slate-900 hover:text-primary transition-colors group-hover:translate-x-1 transition-transform">
                            Details <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
