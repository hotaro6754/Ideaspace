<?php
ob_start();
?>

<!-- Hero Section -->
<section class="relative pt-32 pb-24 md:pt-48 md:pb-32 overflow-hidden">
    <!-- Subtle Background Element -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[600px] bg-[radial-gradient(ellipse_at_50%_-20%,rgba(255,255,255,0.05)_0%,transparent_70%)] pointer-events-none"></div>

    <div class="max-w-screen-xl mx-auto px-6 relative">
        <div class="flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-zinc-400 text-[10px] font-semibold uppercase tracking-[0.2em] mb-10 animate-fade-in">
                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                The Precision Ecosystem
            </div>

            <h1 class="text-5xl md:text-8xl font-bold tracking-tight text-white mb-8 leading-[1.1] animate-fade-up">
                Design. Collaborate. <br/>
                <span class="text-zinc-500">Ship Reality.</span>
            </h1>

            <p class="text-lg md:text-xl text-zinc-400 mb-12 max-w-2xl leading-relaxed animate-fade-up animate-delay-100">
                The high-end platform for campus innovation. Vetted builders, production-ready workflows, and zero slop.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-4 animate-fade-up animate-delay-200">
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-8 !py-3 !text-base">
                    Start Building
                </a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn-outline !px-8 !py-3 !text-base group">
                    View Projects <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Section -->
<section class="py-24 border-t border-white/5 bg-[#070708]">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
             <!-- Card 1: Large Feature -->
            <div class="md:col-span-8 premium-card group">
                <div class="p-10 h-full flex flex-col justify-between">
                    <div>
                        <div class="h-10 w-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bolt-lightning text-sm"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-white mb-4">Precision Workflow</h3>
                        <p class="text-zinc-400 max-w-sm">Every project passes through automated quality gates. From idea to production, we ensure excellence at every step.</p>
                    </div>
                    <div class="mt-12 flex items-center gap-4">
                         <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-full border-2 border-[#09090b] bg-zinc-800 flex items-center justify-center text-[10px] font-bold">JD</div>
                            <div class="h-8 w-8 rounded-full border-2 border-[#09090b] bg-zinc-700 flex items-center justify-center text-[10px] font-bold">AS</div>
                            <div class="h-8 w-8 rounded-full border-2 border-[#09090b] bg-zinc-600 flex items-center justify-center text-[10px] font-bold">+12</div>
                         </div>
                         <span class="text-xs text-zinc-500 font-medium">Active Builders</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Medium Feature -->
            <div class="md:col-span-4 premium-card group">
                <div class="p-10">
                    <div class="h-10 w-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield text-sm"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">Secured Assets</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">Enterprise-grade security for your intellectual property. Your ideas are assets, protected by the latest standards.</p>
                </div>
            </div>

             <!-- Card 3: Small Feature -->
            <div class="md:col-span-5 premium-card group">
                <div class="p-10">
                    <div class="h-10 w-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-layer-group text-sm"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">Builder Network</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">Connect with the top talent on campus. Our network is strictly vetted to maintain high standards of collaboration.</p>
                </div>
            </div>

             <!-- Card 4: Action Card -->
            <div class="md:col-span-7 premium-card bg-white/[0.02] group">
                <div class="p-10 flex flex-col md:flex-row items-center justify-between gap-8 h-full">
                    <div class="flex-1">
                        <h3 class="text-2xl font-semibold text-white mb-4">Proof of Build</h3>
                        <p class="text-zinc-400 text-sm mb-8">Showcase your completed projects on the Wall of Proof and climb the global rankings.</p>
                        <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="text-xs font-bold uppercase tracking-widest text-white hover:text-zinc-400 transition-colors inline-flex items-center gap-2">
                            Explore Rankings <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="w-full md:w-48 aspect-square rounded-2xl bg-zinc-950 border border-white/5 flex flex-col items-center justify-center p-6 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2">Rank</span>
                        <span class="text-5xl font-bold text-white tracking-tighter">#01</span>
                        <div class="mt-4 px-2 py-0.5 rounded-full bg-white/10 border border-white/10 text-[10px] font-bold text-white uppercase">Elite Tier</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-32 border-t border-white/5">
    <div class="max-w-screen-xl mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-8">Ready to ship?</h2>
        <p class="text-zinc-400 mb-12 max-w-lg mx-auto">Join the most advanced campus collaboration platform and start building projects that matter.</p>
        <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-12 !py-4 !text-lg">
            Create Account
        </a>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
