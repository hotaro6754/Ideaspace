<?php
ob_start();
$db = getConnection();
$query = "SELECT ideas.*, users.name as creator_name FROM ideas JOIN users ON ideas.user_id = users.id WHERE ideas.status = 'abandoned' ORDER BY ideas.created_at DESC";
$res = $db->query($query);
$abandoned_ideas = [];
while($row = $res->fetch_assoc()) $abandoned_ideas[] = $row;
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">The Archive</h1>
        <p class="mt-2 text-slate-500 font-medium">Lessons learned from tracks that didn't cross the finish line.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach($abandoned_ideas as $idea): ?>
            <div class="premium-card p-8 bg-white border-l-4 border-l-slate-400 opacity-80">
                <div class="flex items-center justify-between mb-4">
                    <span class="badge bg-slate-100 text-slate-500 border-slate-200"><?php echo sanitize($idea['domain']); ?></span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Postmortem Available</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo sanitize($idea['title']); ?></h3>
                <p class="text-sm text-slate-500 mb-6 italic">"<?php echo sanitize(substr($idea['description'], 0, 150)); ?>..."</p>
                <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">By <?php echo sanitize($idea['creator_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="text-[10px] font-black text-primary uppercase hover:underline">Read Lessons →</a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($abandoned_ideas)): ?>
            <div class="col-span-full py-20 text-center opacity-30">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p class="text-sm font-bold uppercase tracking-widest">No archived tracks yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
