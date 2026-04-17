<?php
function renderZeroSlopScorecard($scores = []) {
    $default_scores = [
        'backend' => 25,
        'frontend' => 20,
        'data' => 15,
        'connect' => 15,
        'quality' => 15,
        'evidence' => 10
    ];
    $actual = array_merge($default_scores, $scores);
    $total = array_sum($actual);
    $status_color = $total >= 70 ? 'text-green-400' : 'text-red-400';
    $status_bg = $total >= 70 ? 'bg-green-500/10' : 'bg-red-500/10';
    $status_text = $total >= 70 ? 'SHIP READY' : 'REBUILD REQUIRED';
?>
<div class="bg-surface-container-high rounded-[2rem] border border-white/5 overflow-hidden">
    <div class="p-8 border-b border-white/5 flex items-center justify-between">
        <div>
            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">ZeroSlop Internal Audit</h4>
            <div class="text-2xl font-black text-white italic">Self-Score: <?php echo $total; ?>/100</div>
        </div>
        <div class="px-4 py-2 <?php echo $status_bg; ?> <?php echo $status_color; ?> rounded-xl text-[10px] font-black uppercase tracking-widest border border-white/5">
            <?php echo $status_text; ?>
        </div>
    </div>
    <div class="p-8 grid grid-cols-2 gap-6">
        <?php foreach($actual as $key => $score): ?>
        <div class="space-y-2">
            <div class="flex justify-between text-[9px] font-black text-slate-500 uppercase tracking-widest">
                <span><?php echo ucfirst($key); ?></span>
                <span class="text-white"><?php echo $score; ?>/<?php echo $default_scores[$key]; ?></span>
            </div>
            <div class="h-1 w-full bg-background rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width: <?php echo ($score / $default_scores[$key]) * 100; ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if($total < 70): ?>
    <div class="px-8 pb-8">
        <div class="p-4 bg-red-500/10 rounded-2xl border border-red-500/20">
            <p class="text-[10px] text-red-400 font-bold leading-relaxed italic">"Honest notes: Backend stubs detected in collaboration handler. Frontend mobile layout collapses at 375px. Rebuild essential."</p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
}
?>
