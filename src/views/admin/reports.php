<?php
ob_start();
$user = getCurrentUser();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2 uppercase">Content <span class="text-red-500 italic">Moderation</span></h1>
            <p class="text-slate-500 font-medium tracking-wide italic">Maintaining the ecosystem standard of Zero Slop.</p>
        </div>
        <div class="flex gap-3">
             <button class="px-6 py-3 bg-surface-container-low text-slate-400 font-bold rounded-xl border border-white/5 hover:text-white transition-all text-xs uppercase tracking-widest">
                Resolve All
            </button>
        </div>
    </div>

    <div class="space-y-4">
        <?php for($i=1; $i<=5; $i++): ?>
        <div class="bg-surface-container-low p-8 rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
            <div class="flex flex-col md:flex-row gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="px-3 py-1 rounded-lg bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest border border-red-500/20 italic">High Severity</span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Reported by @builder_anonymous</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Spam content detected in "Free Money Idea"</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6 italic bg-background/50 p-4 rounded-2xl border border-white/5">"This user is posting referral links instead of a project idea. It's clearly not following the campus build protocols."</p>
                    <div class="flex items-center gap-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <span>Target: <a href="#" class="text-primary hover:underline">View Content</a></span>
                        <span class="h-1 w-1 bg-slate-700 rounded-full"></span>
                        <span>Author: @spammer_node</span>
                    </div>
                </div>
                <div class="md:w-56 flex flex-col gap-2 justify-center border-l border-white/5 md:pl-8">
                    <button class="w-full py-3 bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-600 transition-all">Remove Content</button>
                    <button class="w-full py-3 bg-surface-container-high text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:text-white transition-all border border-white/5">Dismiss Report</button>
                    <button class="w-full py-3 bg-surface-container-high text-amber-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-500/10 transition-all border border-amber-500/20">Suspend Author</button>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
