<?php
ob_start();
$db = getConnection();
$stmt = $db->query("SELECT i.*, u.name as author_name, u.academic_role
                    FROM ideas i
                    JOIN users u ON i.user_id = u.id
                    WHERE i.status = 'abandoned'
                    ORDER BY i.updated_at DESC");
$archives = [];
if ($stmt) {
    while($row = $stmt->fetch_assoc()) $archives[] = $row;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-16 text-center animate-fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest mb-6 border border-amber-100">
            <i class="fas fa-box-archive"></i> Lessons Reservoir
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">The Archive</h1>
        <p class="mt-4 text-slate-500 font-medium text-lg">Ideas that didn't make it to production, but left valuable lessons for the next builder.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-up">
        <?php if (empty($archives)): ?>
            <div class="col-span-full py-20 text-center opacity-30">
                <i class="fas fa-ghost text-4xl mb-4"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest">Archive is empty</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">No abandoned projects yet. Let's keep it that way!</p>
            </div>
        <?php else: ?>
            <?php foreach($archives as $arc): ?>
            <div class="premium-card p-10 group bg-white grayscale hover:grayscale-0 transition-all border-amber-50">
                <div class="flex items-center justify-between mb-8">
                     <span class="badge !bg-amber-50 !text-amber-700 !border-amber-100">ARCHIVED</span>
                     <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo sanitize($arc['domain']); ?></span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4"><?php echo sanitize($arc['title']); ?></h3>

                <div class="p-6 rounded-2xl bg-amber-50/30 border border-amber-100/50 mb-8">
                    <p class="text-[10px] font-black uppercase text-amber-600 tracking-widest mb-3">Postmortem Analysis</p>
                    <p class="text-xs font-medium text-slate-600 leading-relaxed italic">"<?php echo sanitize($arc['lessons_learned']); ?>"</p>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-3">
                         <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-primary"><?php echo substr($arc['author_name'], 0, 1); ?></div>
                         <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase"><?php echo sanitize($arc['author_name']); ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase"><?php echo sanitize($arc['academic_role']); ?></p>
                         </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create&fork=<?php echo $arc['id']; ?>" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">Inherit Idea →</a>
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
