<?php
ob_start();
$db = getConnection();
$query = "SELECT u.id, u.name, u.roll_number, u.branch,
          u.points as points,
          COALESCE((SELECT rank FROM builder_rank WHERE user_id = u.id), 'INITIATE') as rank_name
          FROM users u
          ORDER BY points DESC
          LIMIT 20";
$res = $db->query($query);
$leaders = [];
if ($res) {
    while($row = $res->fetch_assoc()) $leaders[] = $row;
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 text-center animate-fade-up">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-[10px] font-bold uppercase tracking-widest mb-6">
            <i class="fas fa-trophy"></i> Elite Talent Pool
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">Talent Board</h1>
        <p class="text-slate-500 font-medium max-w-2xl mx-auto">The top technical builders and visionary minds across Lendi Institute.</p>
    </div>

    <div class="premium-card bg-white overflow-hidden animate-fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Rank</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Builder</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Expertise</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Impact Points</th>
                        <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Profile</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($leaders as $index => $l): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <span class="text-xl font-black <?php echo ($index < 3) ? 'text-primary' : 'text-slate-300'; ?>">
                                    #<?php echo $index + 1; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-primary font-bold shadow-inner">
                                        <?php echo strtoupper(substr($l['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900"><?php echo sanitize($l['name']); ?></p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase"><?php echo sanitize($l['roll_number']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-2 py-1 rounded bg-primary/5 text-primary text-[10px] font-black uppercase"><?php echo sanitize($l['rank_name']); ?></span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-24 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary" style="width: <?php echo min(100, ($l['points'] / 1000) * 100); ?>%"></div>
                                    </div>
                                    <span class="text-sm font-black text-slate-900"><?php echo number_format($l['points'] ?: 0); ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="<?php echo BASE_URL; ?>/?page=profile&id=<?php echo $l['id']; ?>" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View Proof <i class="fas fa-arrow-right ml-1"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
