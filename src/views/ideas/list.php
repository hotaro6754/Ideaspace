<?php
ob_start();
$conn = getConnection();
$res = $conn->query("SELECT ideas.*, users.name as creator_name, users.branch as creator_branch
                     FROM ideas
                     JOIN users ON ideas.user_id = users.id
                     ORDER BY ideas.created_at DESC");
$ideas = [];
while ($row = $res->fetch_assoc()) {
    $ideas[] = $row;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-16 animate-fade-up">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Active Innovation Tracks</h1>
            <p class="mt-2 text-slate-500 font-medium">Explore and collaborate on cross-departmental campus projects.</p>
        </div>
        <?php if (isLoggedIn()): ?>
            <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i> Post New Idea
            </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-up">
        <?php foreach($ideas as $idea): ?>
        <div class="premium-card p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <span class="badge badge-primary"><?php echo sanitize($idea['domain']); ?></span>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <i class="fas fa-arrow-up text-[10px]"></i>
                        <span class="text-xs font-bold"><?php echo $idea['upvotes']; ?></span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4"><?php echo sanitize($idea['title']); ?></h3>
                <p class="text-sm text-slate-500 font-medium line-clamp-3 mb-6 leading-relaxed">
                    <?php echo sanitize($idea['description']); ?>
                </p>

                <?php if ($idea['skills_needed']): ?>
                    <div class="flex flex-wrap gap-2 mb-8">
                        <?php foreach(json_decode($idea['skills_needed']) as $skill): ?>
                            <span class="text-[10px] font-bold px-2 py-1 bg-slate-50 text-slate-400 rounded uppercase border border-slate-100"><?php echo sanitize($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-primary/5 flex items-center justify-center text-primary text-[10px] font-bold">
                        <?php echo strtoupper(substr($idea['creator_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900"><?php echo sanitize($idea['creator_name']); ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight"><?php echo sanitize($idea['creator_branch']); ?> Dept</p>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-primary hover:translate-x-1 transition-all">
                    View Details <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
