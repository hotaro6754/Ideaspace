<div class="premium-card p-8 bg-white border border-slate-100 animate-fade-up">
    <div class="flex items-center gap-3 mb-8">
        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">
            <i class="fas fa-fire"></i>
        </div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Campus Trends</h3>
    </div>

    <div class="space-y-6">
        <?php
        $domains = $db->query("SELECT domain, COUNT(*) as count FROM ideas GROUP BY domain ORDER BY count DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
        foreach($domains as $d):
        ?>
        <div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo sanitize($d['domain']); ?></span>
                <span class="text-[10px] font-black text-slate-900"><?php echo $d['count']; ?> Tracks</span>
            </div>
            <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width: <?php echo min(100, ($d['count'] / 10) * 100); ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
