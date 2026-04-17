<?php
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-16">
        <div class="inline-flex items-center justify-center h-20 w-20 rounded-3xl bg-primary text-background text-4xl mb-8 shadow-2xl shadow-primary/30">
            <i class="fas fa-brain-circuit"></i>
        </div>
        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight uppercase mb-4">Initialize <span class="text-primary italic">Neural</span> Link</h1>
        <p class="text-slate-500 text-lg max-w-2xl mx-auto">Configure your personal suite of campus collaboration agents to enhance your building potential.</p>
    </div>

    <div class="bg-surface-container-low rounded-[3rem] shadow-2xl border border-white/5 overflow-hidden">
        <div class="p-10 md:p-16 space-y-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tight mb-4">Agent Selection</h3>
                        <div class="space-y-4">
                            <label class="block cursor-pointer group">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="p-6 bg-surface-container-high border border-white/5 rounded-2xl peer-checked:border-primary/50 peer-checked:bg-primary/5 transition-all group-hover:bg-surface-container-highest">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20"><i class="fas fa-users-viewfinder"></i></div>
                                        <div>
                                            <p class="text-sm font-black text-white uppercase tracking-tight">Project Matchmaker</p>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 italic">Vetting Teammates</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="block cursor-pointer group">
                                <input type="checkbox" class="sr-only peer">
                                <div class="p-6 bg-surface-container-high border border-white/5 rounded-2xl peer-checked:border-primary/50 peer-checked:bg-primary/5 transition-all group-hover:bg-surface-container-highest">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-slate-800 text-slate-500 flex items-center justify-center border border-white/5 group-hover:text-primary transition-colors"><i class="fas fa-shield-halved"></i></div>
                                        <div>
                                            <p class="text-sm font-black text-white uppercase tracking-tight">Quality Sentinel</p>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 italic">Zero Slop Compliance</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tight mb-4">Protocol Tuning</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Matching Threshold</label>
                                <input type="range" class="w-full h-1 bg-surface-container-highest rounded-lg appearance-none cursor-pointer accent-primary">
                                <div class="flex justify-between mt-2 text-[10px] font-black text-slate-600 uppercase tracking-widest">
                                    <span>Broad</span>
                                    <span class="text-primary font-black">95% Fidelity</span>
                                    <span>Surgical</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Intelligence Sources</label>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-4 py-2 bg-primary/20 text-primary border border-primary/30 rounded-xl text-[10px] font-black uppercase tracking-widest">GitHub API</span>
                                    <span class="px-4 py-2 bg-primary/20 text-primary border border-primary/30 rounded-xl text-[10px] font-black uppercase tracking-widest">LinkedIn</span>
                                    <span class="px-4 py-2 bg-surface-container-highest text-slate-500 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:text-white transition-colors cursor-pointer">+ Add source</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-3 text-primary bg-primary/10 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] border border-primary/20">
                    <i class="fas fa-lock"></i>
                    End-to-End Encrypted Link
                </div>
                <button class="w-full md:w-auto btn-primary !px-12 !py-5 uppercase tracking-[0.2em] text-xs font-black shadow-2xl shadow-primary/20">
                    Finalize Connection
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
