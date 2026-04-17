<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight mb-2 uppercase">Agent <span class="text-primary italic">Intelligence</span></h1>
            <p class="text-slate-500 font-medium">Your personal suite of campus collaboration AI agents.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/?page=agents-onboarding" class="btn-primary flex items-center gap-2">
            <i class="fas fa-plus"></i> Configure New Agent
        </a>
    </div>

    <!-- Active Agents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        <!-- Agent Card: Matchmaker -->
        <div class="bento-card border border-primary/20 bg-surface-container overflow-hidden group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-16 w-16 rounded-2xl bg-primary text-background flex items-center justify-center text-3xl shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-2 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-bold tracking-widest uppercase mb-2">Active</span>
                    <span class="text-[10px] text-slate-500 font-medium tracking-widest uppercase">98% Accuracy</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Project Matchmaker</h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">Analyzes your skill profile and the campus idea pool to find the 100% match for your next build.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                 <div class="flex -space-x-2">
                    <div class="h-8 w-8 rounded-full bg-slate-800 border-2 border-surface-container flex items-center justify-center text-[10px] font-bold text-slate-400">RK</div>
                    <div class="h-8 w-8 rounded-full bg-slate-800 border-2 border-surface-container flex items-center justify-center text-[10px] font-bold text-slate-400">SM</div>
                </div>
                <button class="text-xs font-bold text-primary hover:text-white transition-colors uppercase tracking-[0.2em]">Open Agent <i class="fas fa-chevron-right ml-1"></i></button>
            </div>
        </div>

        <!-- Agent Card: Quality Sentinel -->
        <div class="bento-card bg-surface-container-high group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-16 w-16 rounded-2xl bg-surface-container-highest text-primary flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="flex flex-col items-end text-slate-500">
                    <span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-400 text-[10px] font-bold tracking-widest uppercase mb-2">Dormant</span>
                    <span class="text-[10px] font-medium tracking-widest uppercase">Zero Slop Gate</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Quality Sentinel</h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">Monitors your project's technical debt and security vulnerabilities. Enforces the ZeroSlop pipeline rules.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                <button class="text-xs font-bold text-slate-500 hover:text-primary transition-colors uppercase tracking-[0.2em]">Deploy Agent <i class="fas fa-arrow-up ml-1"></i></button>
            </div>
        </div>

        <!-- Agent Card: Research Scout -->
        <div class="bento-card bg-surface-container-high group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-16 w-16 rounded-2xl bg-surface-container-highest text-primary flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-magnifying-glass-chart"></i>
                </div>
                <div class="flex flex-col items-end text-slate-500">
                    <span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-400 text-[10px] font-bold tracking-widest uppercase mb-2">Idle</span>
                    <span class="text-[10px] font-medium tracking-widest uppercase">Market Intelligence</span>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Research Scout</h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">Scans GitHub, ProductHunt, and local research papers to provide competitive intelligence for your ideas.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                <button class="text-xs font-bold text-slate-500 hover:text-primary transition-colors uppercase tracking-[0.2em]">Configure <i class="fas fa-gear ml-1"></i></button>
            </div>
        </div>
    </div>

    <!-- Agent Recommendation Feed -->
    <div class="bg-surface-container-low rounded-[2.5rem] p-8 md:p-12 border border-white/5">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-2xl font-bold text-white tracking-tight">Recent Insights</h2>
            <button class="text-xs font-bold text-primary uppercase tracking-widest">Refresh Feed</button>
        </div>

        <div class="space-y-6">
            <div class="flex gap-6 p-6 bg-surface-container rounded-3xl border border-white/5 hover:border-primary/20 transition-all cursor-pointer">
                <div class="h-12 w-12 rounded-xl bg-primary/10 text-primary flex-shrink-0 flex items-center justify-center text-xl">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-1">Potential Team Synergy Detected</h4>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">Based on your recent upvote of "Campus AI Assistant", the Matchmaker suggests connecting with <span class="text-primary">Rohan Kumar</span>. He has the backend expertise you currently lack in your projects.</p>
                    <div class="flex gap-3">
                         <button class="text-[10px] font-bold text-background bg-primary px-3 py-1 rounded-lg uppercase tracking-widest hover:bg-white transition-colors">Connect Now</button>
                         <button class="text-[10px] font-bold text-slate-400 hover:text-white transition-colors uppercase tracking-widest">Ignore</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
