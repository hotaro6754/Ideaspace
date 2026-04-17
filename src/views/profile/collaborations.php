<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-12">
        <a href="<?php echo BASE_URL; ?>/?page=profile" class="text-[10px] font-black text-slate-500 hover:text-primary transition-colors flex items-center gap-2 mb-6 uppercase tracking-[0.2em]">
            <i class="fas fa-arrow-left"></i> My Profile
        </a>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Active <span class="text-primary italic">Alliances</span></h1>
        <p class="text-slate-500 mt-2 text-lg font-medium">Tracking your strategic partnerships across the ecosystem.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Collaboration Card -->
        <div class="bento-card relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 text-primary/10 text-9xl -mr-10 -mt-10">
                <i class="fas fa-handshake-angle"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-3 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest border border-primary/20">Lead Partner</span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Since March 12</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tight group-hover:text-primary transition-colors">Campus AI Study Buddy</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-8 max-w-xl">Working with Aryan Sharma and Sneha Kapur on the core RAG architecture and frontend integration. Currently in MVP phase.</p>

                    <div class="flex items-center gap-6">
                         <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-xl bg-slate-800 border-2 border-surface-container-low flex items-center justify-center text-[10px] font-bold text-slate-400">AS</div>
                            <div class="h-8 w-8 rounded-xl bg-slate-800 border-2 border-surface-container-low flex items-center justify-center text-[10px] font-bold text-slate-400">SK</div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">3 Core Members</span>
                    </div>
                </div>

                <div class="md:w-64 flex flex-col justify-between border-l border-white/5 md:pl-8">
                    <div>
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Milestone Progress</div>
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-xl font-black text-white">82%</span>
                            <span class="text-[10px] font-bold text-primary">v0.8-alpha</span>
                        </div>
                        <div class="h-1.5 w-full bg-surface-container-high rounded-full overflow-hidden">
                            <div class="h-full bg-primary" style="width: 82%"></div>
                        </div>
                    </div>
                    <button class="mt-8 w-full py-4 bg-surface-container-high hover:bg-primary hover:text-background text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] rounded-2xl transition-all">Open Workspace</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
