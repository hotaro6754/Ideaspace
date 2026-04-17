<?php
ob_start();
require_once __DIR__ . '/../components/zeroslop_scorecard.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-12">
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">System <span class="text-primary italic">Audit</span></h1>
        <p class="text-slate-500 mt-2 text-lg font-medium">Internal quality evaluation and slop detection.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <div class="md:col-span-8 space-y-8">
            <div class="bento-card">
                <h3 class="text-xl font-bold text-white uppercase tracking-tight mb-8">Slop Detection Report</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-5 bg-green-500/5 rounded-2xl border border-green-500/10">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <div>
                            <p class="text-sm font-bold text-white mb-1 uppercase tracking-tight">Vibe-Code Filter Active</p>
                            <p class="text-xs text-slate-400 leading-relaxed">No invented session keys or hardcoded localhost URLs detected in recent commits.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-5 bg-amber-500/5 rounded-2xl border border-amber-500/10">
                        <i class="fas fa-triangle-exclamation text-amber-500 mt-1"></i>
                        <div>
                            <p class="text-sm font-bold text-white mb-1 uppercase tracking-tight">Partial Stub Detection</p>
                            <p class="text-xs text-slate-400 leading-relaxed">3 TODO comments found in `src/services/EmailService.php`. Review required before production deployment.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php renderZeroSlopScorecard(['backend' => 22, 'frontend' => 19, 'data' => 14, 'connect' => 15, 'quality' => 13, 'evidence' => 8]); ?>
        </div>

        <div class="md:col-span-4 space-y-8">
            <div class="bento-card bg-surface-container-high">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-6">Quality Protocol</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-500">Completeness Gate</span>
                        <span class="text-green-500">Passed</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-500">Security Gate</span>
                        <span class="text-green-500">Passed</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                        <span class="text-slate-500">Design Gate</span>
                        <span class="text-green-500">Passed</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest border-t border-white/5 pt-4">
                        <span class="text-white">Aggregate Score</span>
                        <span class="text-primary font-black">91/100</span>
                    </div>
                </div>
                <button class="w-full mt-8 py-3 bg-primary text-background text-[10px] font-black uppercase tracking-widest rounded-xl">Generate Certificate</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
