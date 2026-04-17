<?php
ob_start();
?>

<!-- Hero Section -->
<section class="relative overflow-hidden pt-20 pb-28 md:pt-32 md:pb-48 bg-background">
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full bg-[radial-gradient(circle_at_50%_0%,rgba(76,215,246,0.1)_0%,transparent_50%)]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-surface-container-high text-primary font-bold text-[10px] tracking-[0.1em] uppercase mb-8 ring-1 ring-white/5 shadow-2xl animate-fade-in">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
            </span>
            <span>The Digital Curator Experience</span>
        </div>

        <h1 class="text-5xl md:text-8xl font-black tracking-[-0.04em] text-white mb-8 max-w-5xl mx-auto leading-[0.95] font-heading">
            Design. Collaborate. <br/> <span class="text-transparent bg-clip-text bg-gradient-to-br from-primary to-primary-container">Ship Reality.</span>
        </h1>

        <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed font-medium">
            The high-end campus platform where ideas are treated as assets. Curated collaborations, vetted builders, and zero slop.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="<?php echo BASE_URL; ?>/?page=register" class="w-full sm:w-auto btn-primary">
                Start Building Free
            </a>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="w-full sm:w-auto px-8 py-4 bg-surface-container-low text-white font-bold rounded-xl border border-white/5 hover:bg-surface-container transition-all flex items-center justify-center gap-2 group">
                Browse Projects <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>

<!-- Bento Grid Section -->
<section class="py-24 bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
             <!-- Card 1: Large Feature -->
            <div class="md:col-span-8 bento-card relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="relative z-10">
                    <div class="h-12 w-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-8">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-4 leading-tight max-w-md">Precision Ingestion with ZeroSlop Pipeline</h3>
                    <p class="text-slate-400 max-w-sm mb-8">We don't just build; we curate. Every line of code passes through automated gates to ensure shippable quality.</p>
                    <div class="flex gap-3">
                         <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-slate-300 text-[10px] font-bold tracking-widest uppercase">Completeness</span>
                         <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-slate-300 text-[10px] font-bold tracking-widest uppercase">Security</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Small Feature -->
            <div class="md:col-span-4 bento-card bg-primary-container/20 group">
                <div class="h-12 w-12 bg-primary rounded-xl flex items-center justify-center text-background mb-8">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Enterprise Grade Security</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Built with the sentinel aesthetic. Hardened against all vulnerabilities.</p>
            </div>

             <!-- Card 3: Medium Feature -->
            <div class="md:col-span-5 bento-card">
                 <div class="h-12 w-12 bg-surface-container-highest rounded-xl flex items-center justify-center text-primary mb-8">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Vetted Builder Network</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Connect with the top 1% of builders on campus. No fluff, just production skills.</p>
            </div>

             <!-- Card 4: CTA Feature -->
            <div class="md:col-span-7 bento-card bg-gradient-to-br from-surface-container-low to-background border border-white/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 h-full">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2">The Builder Elite Rank</h3>
                        <p class="text-slate-400 text-sm mb-6">Climb the leaderboard by shipping high-quality projects.</p>
                        <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="text-primary font-bold text-xs uppercase tracking-[0.2em] hover:text-white transition-colors">View Rankings <i class="fas fa-chevron-right ml-2 text-[10px]"></i></a>
                    </div>
                    <div class="bg-surface-container-high p-6 rounded-2xl ring-1 ring-white/5">
                        <div class="text-4xl font-black text-white italic">#12</div>
                        <div class="text-[10px] font-bold text-primary uppercase tracking-widest mt-1">Silver Tier</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
