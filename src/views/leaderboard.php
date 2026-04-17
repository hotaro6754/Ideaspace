<?php
ob_start();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-16">
        <h2 class="text-base font-semibold text-accent-600 tracking-wide uppercase mb-3">Community Rankings</h2>
        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight">The <span class="text-accent-600">Builder</span> Elite</h1>
        <p class="text-slate-500 mt-4 text-lg max-w-2xl mx-auto">Celebrating the top contributors and innovators shaping the campus future.</p>
    </div>

    <!-- Top 3 Podium -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <!-- #2 -->
        <div class="md:mt-12 flex flex-col items-center">
            <div class="relative mb-6">
                <div class="h-24 w-24 rounded-[2rem] bg-gradient-to-tr from-slate-200 to-slate-400 p-1 shadow-xl">
                    <div class="h-full w-full rounded-[1.8rem] bg-white flex items-center justify-center text-slate-400 font-bold text-2xl">MK</div>
                </div>
                <div class="absolute -bottom-3 -right-3 h-10 w-10 bg-slate-300 rounded-full border-4 border-slate-50 flex items-center justify-center text-white font-black text-sm">2</div>
            </div>
            <h3 class="font-bold text-slate-900">Meera Kapoor</h3>
            <p class="text-xs text-accent-600 font-bold mb-4">Silver Builder</p>
            <div class="text-2xl font-black text-slate-900">2,840 <span class="text-[10px] text-slate-400 uppercase tracking-widest">pts</span></div>
        </div>

        <!-- #1 -->
        <div class="flex flex-col items-center scale-110">
            <div class="relative mb-6">
                 <div class="absolute -top-10 left-1/2 -translate-x-1/2 text-amber-400 text-4xl animate-bounce">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="h-32 w-32 rounded-[2.5rem] bg-gradient-to-tr from-amber-300 to-amber-500 p-1.5 shadow-2xl shadow-amber-500/20">
                    <div class="h-full w-full rounded-[2.3rem] bg-white flex items-center justify-center text-amber-500 font-bold text-3xl">AS</div>
                </div>
                <div class="absolute -bottom-3 -right-3 h-12 w-12 bg-amber-400 rounded-full border-4 border-slate-50 flex items-center justify-center text-white font-black text-sm">1</div>
            </div>
            <h3 class="font-bold text-slate-900 text-lg">Aryan Sharma</h3>
            <p class="text-xs text-amber-600 font-bold mb-4">Master Builder</p>
            <div class="text-3xl font-black text-slate-900">4,120 <span class="text-[10px] text-slate-400 uppercase tracking-widest">pts</span></div>
        </div>

        <!-- #3 -->
        <div class="md:mt-16 flex flex-col items-center">
            <div class="relative mb-6">
                <div class="h-20 w-20 rounded-[1.5rem] bg-gradient-to-tr from-orange-200 to-orange-400 p-1 shadow-lg">
                    <div class="h-full w-full rounded-[1.3rem] bg-white flex items-center justify-center text-orange-400 font-bold text-xl">RV</div>
                </div>
                <div class="absolute -bottom-2 -right-2 h-8 w-8 bg-orange-300 rounded-full border-4 border-slate-50 flex items-center justify-center text-white font-black text-xs">3</div>
            </div>
            <h3 class="font-bold text-slate-900">Rahul Verma</h3>
            <p class="text-xs text-orange-600 font-bold mb-4">Rising Star</p>
            <div class="text-xl font-black text-slate-900">2,410 <span class="text-[10px] text-slate-400 uppercase tracking-widest">pts</span></div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">All Rankings</h3>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-white rounded-xl border border-slate-200 text-xs font-bold text-slate-600 shadow-sm">Monthly</button>
                <button class="px-4 py-2 bg-slate-900 rounded-xl text-xs font-bold text-white shadow-lg shadow-slate-900/20">All Time</button>
            </div>
        </div>
        <div class="divide-y divide-slate-50">
            <?php for($i=4; $i<=10; $i++): ?>
            <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                <div class="flex items-center gap-6">
                    <span class="text-sm font-black text-slate-300 w-4"><?php echo $i; ?></span>
                    <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-400 group-hover:bg-white transition-colors">
                        <?php echo ['SK', 'IS', 'AM', 'PK', 'NS', 'DK', 'VJ'][$i-4]; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm"><?php echo ['Sneha Kapur', 'Ishaan Shah', 'Ananya Misra', 'Priya Kant', 'Nitin Singh', 'Deepa Kaur', 'Vivek Jain'][$i-4]; ?></h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo rand(5, 15); ?> Projects Contributed</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-black text-slate-900 text-sm"><?php echo 2400 - ($i * 100) + rand(10, 90); ?></div>
                    <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Points</div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        <div class="p-6 bg-slate-50/50 text-center">
            <button class="text-sm font-bold text-accent-600 hover:text-accent-500 transition-colors">Load more rankings</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
