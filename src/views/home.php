<?php
ob_start();
?>

<!-- Hero Section: Lendi IIC Talent Hunt -->
<section class="relative pt-24 pb-20 md:pt-40 md:pb-32 overflow-hidden bg-slate-50">
    <div class="absolute inset-0 bg-[radial-gradient(45%_40%_at_50%_50%,rgba(0,74,153,0.03)_0%,transparent_100%)]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 shadow-subtle mb-12 animate-fade-up">
                <span class="flex h-2 w-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Innovation Cohort v2.0 // 2026</span>
            </div>

            <h1 class="text-6xl md:text-8xl font-extrabold tracking-tight text-slate-900 mb-8 leading-[0.95] animate-fade-up">
                Architect the <br/>
                <span class="text-primary">Next-Gen.</span>
            </h1>

            <p class="text-xl md:text-2xl text-slate-500 mb-12 max-w-3xl font-medium leading-relaxed animate-fade-up">
                Lendi IIC is scouting the top engineering talent to hard-code the campus of tomorrow. Join the central collaboration portal for technical builders.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-5 animate-fade-up">
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-10 !py-4 !text-base">
                    Join the Cohort
                </a>
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn-outline !px-10 !py-4 !text-base group">
                    Explore Tracks <i class="fas fa-arrow-right ml-3 text-sm group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid: Innovation Tracks -->
<section class="py-24 bg-white" id="tracks">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-16">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-4">Innovation Tracks</h2>
            <p class="text-slate-500 font-medium">Select a domain and start contributing to real campus projects.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
             <!-- Track 01 -->
            <div class="md:col-span-8 premium-card group relative">
                <div class="absolute top-6 right-8 text-6xl font-black text-slate-50/50 group-hover:text-primary/5 transition-colors">01</div>
                <div class="p-10 h-full flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-xl bg-primary/5 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Student Monitoring System</h3>
                        <p class="text-slate-500 font-medium max-w-md leading-relaxed">AI-integrated 360° dashboards for real-time behavioral and academic tracking. Utilizing MySQL and AI-Analytics.</p>
                    </div>
                    <div class="mt-12 flex items-center gap-4">
                         <span class="badge badge-primary">MySQL / AI-Analytics</span>
                         <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Production Grade</span>
                    </div>
                </div>
            </div>

            <!-- Track 02 -->
            <div class="md:col-span-4 premium-card group relative bg-slate-50/30">
                <div class="absolute top-6 right-8 text-4xl font-black text-slate-100">02</div>
                <div class="p-8">
                    <div class="h-12 w-12 rounded-xl bg-secondary/5 flex items-center justify-center text-secondary mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Time Table Scheduler</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Deep-logic engines designed to eliminate campus resource conflicts automatically using PHP & Core Algorithms.</p>
                </div>
            </div>

             <!-- Track 03 -->
            <div class="md:col-span-5 premium-card group relative">
                <div class="absolute top-6 right-8 text-4xl font-black text-slate-50/50">03</div>
                <div class="p-8">
                    <div class="h-12 w-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Attendance System</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed mb-6">Geo-fenced secure tracking with dual-factor device authentication protocols. Built with modern PHP.</p>
                    <span class="badge badge-success">Live Testing</span>
                </div>
            </div>

             <!-- Track 04 (IdeaSync Highlight) -->
            <div class="md:col-span-7 premium-card bg-primary text-white group overflow-hidden relative">
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                <div class="p-10 flex flex-col md:flex-row items-center justify-between gap-8 h-full relative z-10">
                    <div class="flex-1">
                        <div class="text-white/60 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Central Hub</div>
                        <h3 class="text-3xl font-bold text-white mb-4">IdeaSync</h3>
                        <p class="text-white/80 font-medium text-sm mb-8 leading-relaxed">The central collaboration portal connecting technical builders with visionary ideas across all Lendi departments.</p>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-white hover:translate-x-1 transition-transform">
                            Join IdeaSync <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="w-full md:w-40 aspect-square rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex flex-col items-center justify-center p-6 text-center">
                        <span class="text-4xl font-black text-white tracking-tighter">IIC</span>
                        <div class="mt-2 text-[8px] font-bold text-white/60 uppercase tracking-widest leading-tight">Innovation <br/> Council</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats / Social Proof -->
<section class="py-20 border-y border-slate-100 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-black text-slate-900 mb-1">30+</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Top Engineers</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-slate-900 mb-1">6-Mo</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Experience</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-slate-900 mb-1">Apr 22</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Talent Hunt</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-black text-slate-900 mb-1">100%</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Real Impact</div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-32 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-8 leading-tight">Ready to build <br/> the future of Lendi?</h2>
        <p class="text-slate-500 font-medium mb-12 max-w-xl mx-auto text-lg leading-relaxed">Join the most advanced campus collaboration platform and earn production-grade portfolio assets.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-12 !py-4 !text-lg w-full sm:w-auto">
                Create Academic Profile
            </a>
            <a href="https://lendinit.lendi.edu.in/event_registration/register-form.php" target="_blank" class="btn-outline !px-12 !py-4 !text-lg w-full sm:w-auto">
                Screening Form
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
