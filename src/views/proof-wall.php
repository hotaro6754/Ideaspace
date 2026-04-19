<?php
ob_start();
$db = getConnection();
$stmt = $db->query("SELECT i.*, u.name as author_name, u.academic_role
                    FROM ideas i
                    JOIN users u ON i.user_id = u.id
                    WHERE i.status = 'completed'
                    ORDER BY i.updated_at DESC");
$solutions = [];
if ($stmt) {
    while($row = $stmt->fetch_assoc()) $solutions[] = $row;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-16 text-center animate-fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 text-green-600 text-[10px] font-black uppercase tracking-widest mb-6 border border-green-100">
            <i class="fas fa-check-double"></i> Verified Solutions
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">Proof Wall</h1>
        <p class="mt-4 text-slate-500 font-medium text-lg">Every problem statement that was successfully solved by the community.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-up">
        <?php if (empty($solutions)): ?>
            <div class="col-span-full py-20 text-center opacity-30">
                <i class="fas fa-hammer text-4xl mb-4"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest">No solutions yet</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">The first completed project will be showcased here.</p>
            </div>
        <?php else: ?>
            <?php foreach($solutions as $sol): ?>
            <div class="premium-card p-10 group bg-white border-green-50">
                <div class="flex items-center justify-between mb-8">
                     <span class="badge badge-success">COMPLETED</span>
                     <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo sanitize($sol['domain']); ?></span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-primary transition-colors"><?php echo sanitize($sol['title']); ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-8 line-clamp-3"><?php echo sanitize($sol['description']); ?></p>

                <div class="space-y-4 mb-10">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Lessons Learned</p>
                        <p class="text-xs font-medium text-slate-700 italic">"<?php echo sanitize($sol['lessons_learned']); ?>"</p>
                    </div>
                    <?php if ($sol['solution_url']): ?>
                    <a href="<?php echo sanitize($sol['solution_url']); ?>" target="_blank" class="flex items-center gap-2 text-xs font-bold text-primary hover:underline">
                        <i class="fab fa-github"></i> View Solution Repo
                    </a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-3">
                         <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-primary"><?php echo substr($sol['author_name'], 0, 1); ?></div>
                         <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase"><?php echo sanitize($sol['author_name']); ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase"><?php echo sanitize($sol['academic_role']); ?></p>
                         </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
