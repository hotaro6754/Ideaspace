<?php
ob_start();
$user = getCurrentUser();
// Simplified admin check
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2 uppercase">Command <span class="text-primary italic">Center</span></h1>
            <p class="text-slate-500 font-medium tracking-wide">High-level ecosystem oversight and moderation.</p>
        </div>
        <div class="flex gap-3">
             <button class="px-6 py-3 bg-red-500/10 text-red-400 font-bold rounded-xl border border-red-500/20 hover:bg-red-500/20 transition-all flex items-center gap-2 text-sm">
                <i class="fas fa-triangle-exclamation"></i> Critical Alerts
            </button>
        </div>
    </div>

    <!-- Admin Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bento-card !p-6">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">Total Builders</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-white">1,284</div>
                <div class="text-green-500 text-[10px] font-bold flex items-center gap-1"><i class="fas fa-arrow-up"></i> 12%</div>
            </div>
        </div>
        <div class="bento-card !p-6">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">Live Projects</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-white">452</div>
                <div class="text-primary text-[10px] font-bold flex items-center gap-1"><i class="fas fa-rocket"></i> 34 New</div>
            </div>
        </div>
        <div class="bento-card !p-6">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">Vetting Queue</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-white">18</div>
                <div class="text-amber-500 text-[10px] font-bold">Pending Review</div>
            </div>
        </div>
        <div class="bento-card !p-6">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">Security Scans</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-black text-white">100%</div>
                <div class="text-green-500 text-[10px] font-bold">All Secure</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- System Health -->
        <div class="lg:col-span-2 bento-card">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-white tracking-tight">System Node Performance</h3>
                <div class="h-2 w-2 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)] animate-pulse"></div>
            </div>
            <div class="space-y-6">
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span>Database Cluster</span>
                        <span class="text-white">1.2ms Latency</span>
                    </div>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: 85%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span>Compute Engine</span>
                        <span class="text-white">42% Load</span>
                    </div>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: 42%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span>Storage (CDN)</span>
                        <span class="text-white">12.5 TB Used</span>
                    </div>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: 68%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="bento-card">
            <h3 class="text-xl font-bold text-white tracking-tight mb-8">Sentinel Audit Trail</h3>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="h-8 w-8 rounded-lg bg-green-500/10 text-green-500 flex-shrink-0 flex items-center justify-center text-xs">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-white leading-tight">New Project Vetted: "AI Study Buddy"</p>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">2 mins ago</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="h-8 w-8 rounded-lg bg-red-500/10 text-red-500 flex-shrink-0 flex items-center justify-center text-xs">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-white leading-tight">IP Rate-Limited: 192.168.1.1</p>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">15 mins ago</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 text-blue-500 flex-shrink-0 flex items-center justify-center text-xs">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-white leading-tight">Admin Role Assigned: @maya_singh</p>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest mt-1">1 hour ago</p>
                    </div>
                </div>
            </div>
            <button class="mt-10 w-full py-4 bg-surface-container-high hover:bg-surface-container-highest text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] rounded-2xl border border-white/5 transition-all">View Full Logs</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
