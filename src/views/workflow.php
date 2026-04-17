<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Build Workflow</h1>
            <p class="text-slate-500 font-medium">Tracking the production cycle of <span class="text-primary italic">Campus AI Assistant</span></p>
        </div>
        <div class="flex gap-3">
             <button class="px-6 py-3 bg-surface-container-low text-white font-bold rounded-xl border border-white/5 hover:bg-surface-container transition-all flex items-center gap-2">
                <i class="fas fa-filter text-xs text-primary"></i> Filter
            </button>
            <button class="btn-primary">
                <i class="fas fa-plus mr-2 text-xs"></i> Add Task
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Backlog -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-slate-500"></div>
                    Backlog <span class="text-slate-500 text-xs font-medium ml-2">4</span>
                </h3>
            </div>

            <div class="space-y-4">
                <div class="bento-card !p-6 cursor-grab active:cursor-grabbing hover:scale-[1.02] transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-widest px-2 py-0.5 bg-primary/10 rounded-lg">Feature</span>
                        <div class="flex -space-x-2">
                            <div class="h-6 w-6 rounded-full bg-slate-800 border-2 border-surface-container-low flex items-center justify-center text-[8px] font-bold text-slate-400">RK</div>
                        </div>
                    </div>
                    <h4 class="font-bold text-white text-sm mb-2">Implement Vector Search</h4>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">Integrate Pinecone for efficient document retrieval in the AI pipeline.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-clock"></i> 3 days</span>
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-paperclip"></i> 2</span>
                    </div>
                </div>

                <div class="bento-card !p-6 cursor-grab hover:scale-[1.02] transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest px-2 py-0.5 bg-amber-500/10 rounded-lg">Bug</span>
                    </div>
                    <h4 class="font-bold text-white text-sm mb-2">Fix Auth Token Expiry</h4>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">Refresh tokens are not being issued correctly in the login controller.</p>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-primary animate-pulse"></div>
                    In Progress <span class="text-slate-500 text-xs font-medium ml-2">2</span>
                </h3>
            </div>

            <div class="space-y-4">
                <div class="bento-card !p-6 ring-1 ring-primary/30 bg-surface-container cursor-grab hover:scale-[1.02] transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-widest px-2 py-0.5 bg-primary/10 rounded-lg">UI / UX</span>
                        <div class="flex -space-x-2">
                            <div class="h-6 w-6 rounded-full bg-primary border-2 border-surface-container flex items-center justify-center text-[8px] font-bold text-background">AS</div>
                        </div>
                    </div>
                    <h4 class="font-bold text-white text-sm mb-2">Redesign Dashboard Bento</h4>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4">Overhaul the main user dashboard with the 'Digital Curator' design system.</p>
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-1">
                            <div class="h-1 w-12 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: 75%"></div>
                            </div>
                            <span class="text-[8px] text-primary font-bold">75%</span>
                         </div>
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-comment"></i> 4</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Done / Vetted -->
        <div class="space-y-6">
            <div class="flex items-center justify-between px-4">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-green-500"></div>
                    Vetted <span class="text-slate-500 text-xs font-medium ml-2">8</span>
                </h3>
            </div>

            <div class="space-y-4">
                <div class="bento-card !p-6 opacity-60 hover:opacity-100 transition-opacity">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest px-2 py-0.5 bg-green-500/10 rounded-lg">Core</span>
                    </div>
                    <h4 class="font-bold text-white text-sm mb-2 line-through decoration-slate-600">Secure Database Connection</h4>
                    <div class="flex items-center gap-2 text-green-500">
                        <i class="fas fa-shield-check text-xs"></i>
                        <span class="text-[8px] font-bold uppercase tracking-widest">Zero Slop Certified</span>
                        <div class="mt-4 pt-4 border-t border-white/5">
                            <div class="flex justify-between text-[8px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                <span>Build Confidence</span>
                                <span class="text-primary">96/100</span>
                            </div>
                            <div class="h-1 w-full bg-background rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: 96%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
