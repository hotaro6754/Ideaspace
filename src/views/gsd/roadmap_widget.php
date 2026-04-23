<div class="premium-card bg-white p-6 shadow-sm border border-slate-100 rounded-2xl mb-8">
    <h3 class="font-bold text-slate-900 mb-6">Execution Roadmap</h3>
    <div class="space-y-6 relative">
        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-slate-100"></div>

        <?php
        $phases = ['Discuss', 'Plan', 'Execute', 'Verify', 'Ship'];
        $gsd = new GSDWorkflow(getConnection());
        $progress = $gsd->getProgress($idea['id']);
        foreach($phases as $phase):
            $is_passed = in_array($phase, $progress['passed']);
            $is_current = ($phase === $progress['current']);
        ?>
        <div class="flex items-center gap-6 relative z-10">
            <div class="h-8 w-8 rounded-full flex items-center justify-center border-2 <?php echo $is_passed ? 'bg-green-500 border-green-500 text-white' : ($is_current ? 'bg-white border-primary text-primary' : 'bg-white border-slate-200 text-slate-300'); ?>">
                <?php if($is_passed): ?>
                    <i class="fas fa-check text-xs"></i>
                <?php else: ?>
                    <span class="text-xs font-black"><?php echo array_search($phase, $phases) + 1; ?></span>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold <?php echo $is_passed ? 'text-slate-900' : 'text-slate-500'; ?>"><?php echo $phase; ?></p>
                    <?php if($is_current && isLoggedIn() && $_SESSION['user_id'] == $idea['user_id']): ?>
                        <button onclick="passGate('<?php echo $phase; ?>')" class="text-[10px] font-black text-primary uppercase hover:underline">Complete</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
async function passGate(phase) {
    if(!confirm('Mark ' + phase + ' as complete?')) return;
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/gsd.php?action=pass_gate', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'idea_id=<?php echo $idea['id']; ?>&phase=' + phase
    });
    const data = await res.json();
    if(data.success) location.reload();
}
</script>
