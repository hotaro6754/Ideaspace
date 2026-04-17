<?php
ob_start();
$user = getCurrentUser();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-12">
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Student <span class="text-primary italic">Command</span></h1>
        <p class="text-slate-500 mt-2 text-lg font-medium italic">Operational overview for @<?php echo sanitize(strtolower(str_replace(' ', '', $user['name']))); ?></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Academic Node -->
        <div class="bento-card border border-primary/10">
            <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3 class="text-lg font-bold text-white uppercase tracking-tight mb-2">Academic Sync</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-6">Synchronized with university LMS. 3 project deadlines detected this week.</p>
            <div class="flex items-center justify-between text-[10px] font-black text-primary uppercase tracking-widest">
                <span>Vetted Status</span>
                <span class="px-2 py-0.5 bg-green-500/10 text-green-500 rounded">Active</span>
            </div>
        </div>

        <!-- Build Node -->
        <div class="bento-card border border-primary/10">
            <div class="h-10 w-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center mb-6">
                <i class="fas fa-code-branch"></i>
            </div>
            <h3 class="text-lg font-bold text-white uppercase tracking-tight mb-2">Build Velocity</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-6">Averaging 12 commits/day. Ranked top 5% in technical output.</p>
            <div class="flex items-center justify-between text-[10px] font-black text-blue-400 uppercase tracking-widest">
                <span>Tier Rank</span>
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded">Elite</span>
            </div>
        </div>

        <!-- Social Node -->
        <div class="bento-card border border-primary/10">
            <div class="h-10 w-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center mb-6">
                <i class="fas fa-network-wired"></i>
            </div>
            <h3 class="text-lg font-bold text-white uppercase tracking-tight mb-2">Network Influence</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-6">12 active collaborations. Your suggestions have 85% adoption rate.</p>
            <div class="flex items-center justify-between text-[10px] font-black text-purple-400 uppercase tracking-widest">
                <span>Karma Score</span>
                <span class="px-2 py-0.5 bg-purple-500/10 text-purple-400 rounded">9.2</span>
            </div>
        </div>

        <!-- Event Node -->
        <div class="bento-card border border-primary/10 bg-gradient-to-br from-primary/5 to-transparent">
            <div class="h-10 w-10 rounded-xl bg-accent-500/10 text-accent-400 flex items-center justify-center mb-6">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="text-lg font-bold text-white uppercase tracking-tight mb-2">Upcoming Briefs</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-6">Hackathon 2024 begins in 48 hours. Team assembly required.</p>
            <button class="w-full py-2 bg-primary text-background text-[9px] font-black uppercase tracking-widest rounded-lg">Review Invite</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
