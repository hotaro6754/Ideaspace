<?php
ob_start();
$db = getConnection();
$query = "SELECT ideas.*, users.name as creator_name, users.branch
          FROM ideas
          JOIN users ON ideas.user_id = users.id
          WHERE ideas.status = 'completed'
          ORDER BY ideas.updated_at DESC";
$res = $db->query($query);
$completed_ideas = [];
while($row = $res->fetch_assoc()) $completed_ideas[] = $row;
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-16 animate-fade-up">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">Wall of Proof</h1>
        <p class="mt-4 text-slate-500 font-medium max-w-2xl mx-auto">Celebrating the tracks that made it from vision to reality at Lendi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach($completed_ideas as $idea): ?>
            <div class="premium-card p-8 bg-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4">
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[9px] font-black uppercase rounded tracking-widest">IIC Verified ⭐</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4"><?php echo sanitize($idea['title']); ?></h3>
                <p class="text-sm text-slate-500 line-clamp-3 mb-6"><?php echo sanitize($idea['description']); ?></p>
                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[8px]">
                            <?php echo strtoupper(substr($idea['creator_name'], 0, 1)); ?>
                        </div>
                        <span class="text-xs font-bold text-slate-700"><?php echo sanitize($idea['creator_name']); ?></span>
                    </div>
                    <span class="text-[9px] font-black text-slate-400 uppercase"><?php echo date('M Y', strtotime($idea['updated_at'])); ?></span>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($completed_ideas)): ?>
            <div class="col-span-full py-20 text-center opacity-40">
                <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-300 text-3xl mx-auto mb-6">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">The wall is waiting for you</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">Complete your first project to be featured here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
