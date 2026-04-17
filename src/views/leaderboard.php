<?php
ob_start();
?>

<div class="max-w-screen-xl mx-auto px-6 py-20">
    <div class="text-center mb-24 animate-fade-in">
        <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] mb-4">Rankings</p>
        <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight mb-8">The Builder Elite</h1>
        <p class="text-zinc-400 text-lg max-w-xl mx-auto">Celebrating the top contributors shaping the campus ecosystem.</p>
    </div>

    <!-- Top 3 Podium -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-24 items-end animate-fade-up animate-delay-100">
        <!-- #2 -->
        <div class="order-2 md:order-1 flex flex-col items-center p-8 premium-card">
            <div class="relative mb-6">
                <div class="h-20 w-20 rounded-full bg-zinc-800 border-2 border-zinc-500 flex items-center justify-center text-zinc-300 font-bold text-xl shadow-lg shadow-zinc-500/10">MK</div>
                <div class="absolute -bottom-2 -right-2 h-8 w-8 bg-zinc-600 rounded-full border-2 border-[#09090b] flex items-center justify-center text-white font-bold text-xs">2</div>
            </div>
            <h3 class="font-bold text-white mb-1">Meera Kapoor</h3>
            <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Silver Builder</p>
            <div class="text-xl font-bold text-white">2,840 <span class="text-[10px] text-zinc-600 uppercase">pts</span></div>
        </div>

        <!-- #1 -->
        <div class="order-1 md:order-2 flex flex-col items-center p-12 premium-card bg-white/[0.03] border-white/20 scale-105 relative z-10 shadow-premium">
            <div class="relative mb-8">
                 <div class="absolute -top-12 left-1/2 -translate-x-1/2 text-white text-3xl animate-bounce">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="h-28 w-28 rounded-full bg-white flex items-center justify-center text-black font-bold text-3xl shadow-xl">AS</div>
                <div class="absolute -bottom-2 -right-2 h-10 w-10 bg-white rounded-full border-4 border-[#09090b] flex items-center justify-center text-black font-bold text-sm">1</div>
            </div>
            <h3 class="font-bold text-white text-xl mb-1">Aryan Sharma</h3>
            <p class="text-[10px] text-zinc-400 uppercase font-bold tracking-widest mb-6">Elite Builder</p>
            <div class="text-3xl font-bold text-white tracking-tighter">4,120 <span class="text-xs text-zinc-600 uppercase">pts</span></div>
        </div>

        <!-- #3 -->
        <div class="order-3 md:order-3 flex flex-col items-center p-8 premium-card">
            <div class="relative mb-6">
                <div class="h-16 w-16 rounded-full bg-zinc-800 border-2 border-zinc-600 flex items-center justify-center text-zinc-300 font-bold text-lg">RV</div>
                <div class="absolute -bottom-1 -right-1 h-7 w-7 bg-zinc-700 rounded-full border-2 border-[#09090b] flex items-center justify-center text-white font-bold text-[10px]">3</div>
            </div>
            <h3 class="font-bold text-white mb-1">Rahul Verma</h3>
            <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest mb-4">Rising Star</p>
            <div class="text-lg font-bold text-white">2,410 <span class="text-[10px] text-zinc-600 uppercase">pts</span></div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="premium-card overflow-hidden animate-fade-up animate-delay-200">
        <div class="p-6 border-b border-white/5 bg-white/[0.01] flex items-center justify-between">
            <h3 class="text-sm font-bold text-white uppercase tracking-widest">Global Rankings</h3>
            <div class="flex gap-1 p-1 bg-white/5 rounded-lg border border-white/10">
                <button class="px-4 py-1.5 text-[10px] font-bold text-zinc-500 hover:text-white transition-colors">Monthly</button>
                <button class="px-4 py-1.5 bg-white text-black text-[10px] font-bold rounded">All Time</button>
            </div>
        </div>
        <div class="divide-y divide-white/5">
            <?php for($i=4; $i<=10; $i++): ?>
            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                <div class="flex items-center gap-8">
                    <span class="text-xs font-bold text-zinc-700 w-4 tracking-tighter">#<?php echo $i; ?></span>
                    <div class="h-10 w-10 rounded-lg bg-zinc-900 border border-white/10 flex items-center justify-center font-bold text-zinc-500 group-hover:border-white/20 transition-all">
                        <?php echo ['SK', 'IS', 'AM', 'PK', 'NS', 'DK', 'VJ'][$i-4]; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm"><?php echo ['Sneha Kapur', 'Ishaan Shah', 'Ananya Misra', 'Priya Kant', 'Nitin Singh', 'Deepa Kaur', 'Vivek Jain'][$i-4]; ?></h4>
                        <p class="text-[10px] font-semibold text-zinc-600 uppercase tracking-widest"><?php echo rand(5, 15); ?> Contributions</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-white text-sm tracking-tight"><?php echo 2400 - ($i * 100) + rand(10, 90); ?></div>
                    <div class="text-[8px] font-bold text-zinc-600 uppercase tracking-widest">Points</div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <div class="p-6 border-t border-white/5 bg-white/[0.01] text-center">
            <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-[0.2em]">Load more</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
