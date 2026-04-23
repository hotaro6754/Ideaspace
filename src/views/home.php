<?php
ob_start();
?>

<!-- Hero Section: Lendi IdeaSync -->
<section class="relative pt-24 pb-20 md:pt-40 md:pb-32 overflow-hidden bg-slate-900 selection:bg-primary selection:text-white">
    <!-- Premium Mesh Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_20%_20%,rgba(0,74,153,0.15)_0%,transparent_50%)]"></div>
        <div class="absolute bottom-0 right-0 w-full h-full bg-[radial-gradient(circle_at_80%_80%,rgba(0,255,255,0.05)_0%,transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-12 animate-fade-up">
                <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-white/60">Lendi Institute Innovation Hub</span>
            </div>

<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-20 pointer-events-none"><lottie-player src="https://assets3.lottiefiles.com/packages/lf20_sk5h17nd.json" background="transparent" speed="1" style="width: 600px; height: 600px;" loop autoplay></lottie-player></div>
            <h1 class="text-6xl md:text-8xl font-black tracking-tight text-white mb-8 leading-[0.9] animate-fade-up">
                Forge the <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-cyan-400">Future.</span>
            </h1>

            <p class="text-xl md:text-2xl text-white/60 mb-12 max-w-3xl font-medium leading-relaxed animate-fade-up" style="animation-delay: 0.1s">
                IdeaSync is the elite engine for technical excellence at Lendi. Connect with builders, solve real-world problems, and scale your impact.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-5 animate-fade-up" style="animation-delay: 0.2s">
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-12 !py-5 !text-base !rounded-2xl shadow-2xl shadow-primary/20">
                    Join the Forge
                </a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="group flex items-center gap-3 px-8 py-5 text-base font-bold text-white/80 hover:text-white transition-all">
                    Explore Problems <i class="fas fa-arrow-right ml-2 text-sm group-hover:translate-x-2 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid: The Infrastructure -->
<section class="py-32 bg-[#0a0f18] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-20 text-center">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Built for Builders</h2>
            <p class="text-white/40 font-medium max-w-2xl mx-auto">Our platform integrates deep academic rigor with modern execution frameworks to ensure student projects actually ship.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Feature 1: GSD System -->
            <div class="md:col-span-8 p-1 rounded-[2rem] bg-gradient-to-br from-white/10 to-transparent">
                <div class="h-full w-full bg-slate-900 rounded-[1.9rem] p-10 relative overflow-hidden group">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all"></div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div>
                            <div class="h-14 w-14 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary mb-8">
                                <i class="fas fa-bolt text-2xl"></i>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-4">GSD Framework</h3>
                            <p class="text-white/60 font-medium max-w-md leading-relaxed">From Idea Charter to Project Brief, our workflow enforces real progress through wave-based execution and quality gates.</p>
                        </div>
                        <div class="mt-12 flex items-center gap-6">
                            <div class="flex -space-x-3">
                                <div class="h-10 w-10 rounded-full border-2 border-slate-900 bg-primary flex items-center justify-center text-[10px] font-bold text-white">D</div>
                                <div class="h-10 w-10 rounded-full border-2 border-slate-900 bg-secondary flex items-center justify-center text-[10px] font-bold text-white">P</div>
                                <div class="h-10 w-10 rounded-full border-2 border-slate-900 bg-green-500 flex items-center justify-center text-[10px] font-bold text-white">E</div>
                                <div class="h-10 w-10 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white">+2</div>
                            </div>
                            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">Execution Roadmap</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 2: Agents -->
            <div class="md:col-span-4 p-1 rounded-[2rem] bg-gradient-to-b from-white/5 to-transparent">
                <div class="h-full w-full bg-slate-900/50 rounded-[1.9rem] p-8 border border-white/5 group">
                    <div class="h-12 w-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-robot text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">AI Agents</h3>
                    <p class="text-white/40 text-sm font-medium leading-relaxed mb-8">Persona-driven assistants that provide tailored research, mentoring, and project lead suggestions.</p>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-[10px] font-mono text-cyan-400/80">
                        "Dr. Insight: Analyzing IEEE benchmarks for your AI track..."
                    </div>
                </div>
            </div>

            <!-- Feature 3: Analytics -->
            <div class="md:col-span-5 p-1 rounded-[2rem] bg-gradient-to-t from-white/5 to-transparent">
                <div class="h-full w-full bg-slate-900/50 rounded-[1.9rem] p-8 border border-white/5 group">
                    <div class="h-12 w-12 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400 mb-8">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">Live Insights</h3>
                    <p class="text-white/40 text-sm font-medium leading-relaxed">Real-time tracking of problem statement trends and builder impact across the campus.</p>
                </div>
            </div>

            <!-- Feature 4: Integration -->
            <div class="md:col-span-7 p-1 rounded-[2rem] bg-gradient-to-bl from-white/10 to-transparent">
                <div class="h-full w-full bg-primary rounded-[1.9rem] p-10 relative overflow-hidden">
                    <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10 h-full">
                        <div class="flex-1">
                            <div class="text-white/50 text-[10px] font-black uppercase tracking-[0.4em] mb-4">Ecosystem</div>
                            <h3 class="text-3xl font-black text-white mb-4">Unified Stack</h3>
                            <p class="text-white/80 font-medium text-sm leading-relaxed mb-8">Direct integration with GitHub, Supabase, and IIC moderation tools for a seamless builder experience.</p>
                            <div class="flex gap-4">
                                <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white"><i class="fab fa-github"></i></div>
                                <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white"><i class="fas fa-database"></i></div>
                                <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white"><i class="fas fa-shield-alt"></i></div>
                            </div>
                        </div>
                        <div class="hidden md:block w-32 aspect-square rounded-full border-4 border-white/20 border-t-white animate-spin-slow"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-24 bg-slate-900 border-y border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
            <?php
            $stats = [
                ['label' => 'Technical Tracks', 'val' => '50+'],
                ['label' => 'Active Builders', 'val' => '240+'],
                ['label' => 'Commits Made', 'val' => '1.2k'],
                ['label' => 'IIC Verified', 'val' => '100%']
            ];
            foreach($stats as $s): ?>
            <div class="text-center group">
                <div class="text-5xl font-black text-white mb-2 group-hover:text-primary transition-colors"><?php echo $s['val']; ?></div>
                <div class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em]"><?php echo $s['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-40 bg-[#0a0f18] overflow-hidden relative">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(0,74,153,0.1)_0%,transparent_70%)]"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-5xl md:text-7xl font-black text-white mb-10 leading-tight">Ready to build the <br/> future of LIET?</h2>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-12 !py-5 !text-lg !rounded-2xl w-full sm:w-auto shadow-2xl shadow-primary/20">
                Initialize Profile
            </a>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="px-10 py-5 text-lg font-bold text-white/60 hover:text-white transition-all w-full sm:w-auto">
                Explore Tracks
            </a>
        </div>
    </div>
</section>

<style>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
