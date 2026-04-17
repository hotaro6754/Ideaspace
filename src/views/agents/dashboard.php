<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-screen-xl mx-auto px-6 py-16">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 mb-20 animate-fade-in">
        <div class="max-w-2xl">
            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] mb-4">Intelligence Suite</p>
            <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight">Agent Dashboard</h1>
            <p class="text-zinc-400 text-lg mt-6">Your personal autonomous workforce for campus collaboration.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/?page=agents-onboarding" class="btn-primary">
            Deploy New Agent
        </a>
    </div>

    <!-- Active Agents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-20 animate-fade-up animate-delay-100">
        <!-- Agent Card: Matchmaker -->
        <div class="premium-card p-8 flex flex-col group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-12 w-12 rounded-xl bg-white text-black flex items-center justify-center text-xl shadow-lg transition-transform group-hover:scale-110">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-2 py-0.5 rounded bg-brand/10 text-brand text-[10px] font-bold uppercase tracking-widest mb-2">Active</span>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Project Matchmaker</h3>
            <p class="text-zinc-400 text-sm leading-relaxed mb-10">Analyzes your skill profile and the campus idea pool to find the 100% match for your next build.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                 <div class="flex -space-x-1.5">
                    <div class="h-6 w-6 rounded-full bg-zinc-800 border border-[#09090b] flex items-center justify-center text-[8px] font-bold text-zinc-400">RK</div>
                    <div class="h-6 w-6 rounded-full bg-zinc-700 border border-[#09090b] flex items-center justify-center text-[8px] font-bold text-zinc-400">SM</div>
                </div>
                <button class="text-[10px] font-bold text-white hover:text-zinc-400 transition-colors uppercase tracking-[0.2em]">Open Agent <i class="fas fa-arrow-right ml-1 text-[8px]"></i></button>
            </div>
        </div>

        <!-- Agent Card: Quality Sentinel -->
        <div class="premium-card p-8 flex flex-col group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-12 w-12 rounded-xl bg-zinc-800 border border-white/10 text-white flex items-center justify-center text-xl transition-transform group-hover:scale-110">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-2">Dormant</span>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Quality Sentinel</h3>
            <p class="text-zinc-400 text-sm leading-relaxed mb-10">Monitors technical debt and security vulnerabilities. Enforces the ZeroSlop pipeline rules across all projects.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-[0.2em]">Deploy <i class="fas fa-arrow-up ml-1 text-[8px]"></i></button>
            </div>
        </div>

        <!-- Agent Card: Research Scout -->
        <div class="premium-card p-8 flex flex-col group">
            <div class="flex items-start justify-between mb-8">
                <div class="h-12 w-12 rounded-xl bg-zinc-800 border border-white/10 text-white flex items-center justify-center text-xl transition-transform group-hover:scale-110">
                    <i class="fas fa-magnifying-glass-chart"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-2 py-0.5 rounded bg-zinc-800 text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-2">Idle</span>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Research Scout</h3>
            <p class="text-zinc-400 text-sm leading-relaxed mb-10">Scans GitHub and local research to provide competitive intelligence for your project briefings.</p>
            <div class="pt-6 border-t border-white/5 flex items-center justify-between mt-auto">
                <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-[0.2em]">Configure <i class="fas fa-gear ml-1 text-[8px]"></i></button>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="premium-card p-10 animate-fade-up animate-delay-200">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-xl font-bold text-white tracking-tight">Recent Insights</h2>
            <button class="text-[10px] font-bold text-zinc-500 hover:text-white uppercase tracking-widest">Refresh Feed</button>
        </div>

        <div class="space-y-4">
            <div class="flex gap-8 p-8 bg-white/[0.01] border border-white/5 rounded-2xl hover:border-white/20 transition-all cursor-pointer group">
                <div class="h-10 w-10 rounded bg-white/5 text-brand flex-shrink-0 flex items-center justify-center text-lg">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-white mb-2">Project Synergy Alert</h4>
                    <p class="text-sm text-zinc-400 leading-relaxed mb-6">Based on your recent activity, the Matchmaker suggests connecting with <span class="text-white font-medium">Rohan Kumar</span> for your AI project. He has the high-end expertise you require.</p>
                    <div class="flex gap-4">
                         <button class="btn-primary !px-4 !py-1.5 !text-[10px] !uppercase">Connect</button>
                         <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-widest">Ignore</button>
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
