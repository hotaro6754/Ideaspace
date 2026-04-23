<div class="premium-card bg-white p-6 shadow-sm border border-slate-100 rounded-2xl mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-slate-900">Health Signals</h3>
        <span id="health-badge" class="px-2 py-0.5 bg-green-100 text-green-600 text-[9px] font-black uppercase rounded">Healthy</span>
    </div>

    <div class="space-y-4" id="pattern-alerts">
        <div class="flex items-start gap-3">
            <div class="h-2 w-2 rounded-full bg-green-500 mt-1.5"></div>
            <p class="text-xs text-slate-500 font-medium">No anti-patterns detected in the last 7 days.</p>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t border-slate-50">
        <button onclick="scanPatterns()" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Run Diagnostic</button>
    </div>
</div>

<script>
async function scanPatterns() {
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/antipatterns.php?action=scan&idea_id=<?php echo $idea['id']; ?>');
    const data = await res.json();
    const container = document.getElementById('pattern-alerts');
    const badge = document.getElementById('health-badge');

    if (data.patterns && data.patterns.length > 0) {
        badge.textContent = 'At Risk';
        badge.className = 'px-2 py-0.5 bg-red-100 text-red-600 text-[9px] font-black uppercase rounded';
        container.innerHTML = data.patterns.map(p => `
            <div class="flex items-start gap-3 mb-3 animate-fade-in">
                <div class="h-2 w-2 rounded-full bg-red-500 mt-1.5"></div>
                <div>
                    <p class="text-xs font-bold text-slate-900">${p.pattern_name}</p>
                    <p class="text-[10px] text-slate-500 font-medium">${p.message}</p>
                </div>
            </div>
        `).join('');
    } else {
        badge.textContent = 'Healthy';
        badge.className = 'px-2 py-0.5 bg-green-100 text-green-600 text-[9px] font-black uppercase rounded';
        container.innerHTML = '<div class="flex items-start gap-3"><div class="h-2 w-2 rounded-full bg-green-500 mt-1.5"></div><p class="text-xs text-slate-500 font-medium">No anti-patterns detected.</p></div>';
    }
}
</script>
