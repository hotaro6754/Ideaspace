<?php
ob_start();
$user = getCurrentUser();
$role = $_GET['role'] ?? 'Builder';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest border border-primary/20 mb-4">
                <i class="fas fa-id-badge"></i> Access Level: <?php echo sanitize($role); ?>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase italic">Sector <span class="text-white not-italic">Briefing</span></h1>
            <p class="text-slate-500 mt-1 font-medium">Custom intelligence for your specific ecosystem role.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Role Performance -->
        <div class="md:col-span-2 bento-card relative overflow-hidden">
             <div class="absolute top-0 right-0 p-8 text-primary/5 text-9xl -mr-10 -mt-10">
                <i class="fas fa-chart-line-up"></i>
            </div>
            <h3 class="text-xl font-bold text-white uppercase tracking-tight mb-8">Performance Metrics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Build Quality</p>
                    <p class="text-3xl font-black text-white italic">A-</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Collab Rate</p>
                    <p class="text-3xl font-black text-white">92%</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Vibe Score</p>
                    <p class="text-3xl font-black text-white">4.8</p>
                </div>
            </div>

            <div class="mt-12 h-32 w-full bg-surface-container-high rounded-2xl relative overflow-hidden border border-white/5 flex items-end px-4 gap-1 pb-4">
                <?php for($i=0; $i<20; $i++): ?>
                    <div class="flex-1 bg-primary/20 rounded-t-sm hover:bg-primary transition-colors cursor-pointer" style="height: <?php echo rand(20, 100); ?>%"></div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Role Actions -->
        <div class="bento-card bg-surface-container overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-tight mb-8">Role Protocol</h3>
                <div class="space-y-4">
                    <button class="w-full flex items-center justify-between p-4 bg-surface-container-high rounded-xl border border-white/5 hover:border-primary/50 transition-all group">
                        <span class="text-xs font-bold text-slate-400 group-hover:text-white uppercase tracking-widest">Sync Repositories</span>
                        <i class="fas fa-sync text-primary"></i>
                    </button>
                    <button class="w-full flex items-center justify-between p-4 bg-surface-container-high rounded-xl border border-white/5 hover:border-primary/50 transition-all group">
                        <span class="text-xs font-bold text-slate-400 group-hover:text-white uppercase tracking-widest">Audit Projects</span>
                        <i class="fas fa-magnifying-glass text-primary"></i>
                    </button>
                    <button class="w-full flex items-center justify-between p-4 bg-surface-container-high rounded-xl border border-white/5 hover:border-primary/50 transition-all group">
                        <span class="text-xs font-bold text-slate-400 group-hover:text-white uppercase tracking-widest">Update Bio</span>
                        <i class="fas fa-pen text-primary"></i>
                    </button>
                </div>
            </div>
            <button class="mt-8 py-4 bg-primary text-background text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:scale-[1.02] transition-all">Execute All Syncs</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/main.php';
?>
