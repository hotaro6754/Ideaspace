<?php
ob_start();
$user = getCurrentUser();
?>

<div class="min-h-screen">
    <!-- Project Hero -->
    <div class="relative pt-20 pb-16 border-b border-white/5 bg-[#070708]">
        <div class="max-w-screen-xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-10">
                <div class="flex-1 animate-fade-in">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="badge badge-brand">Artificial Intelligence</span>
                        <span class="badge badge-outline">Production Ready</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight mb-8">Campus AI Assistant</h1>
                    <div class="flex items-center gap-8">
                         <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-zinc-800 border border-white/10 flex items-center justify-center text-zinc-300 font-bold text-xs">AS</div>
                            <div>
                                <p class="text-[10px] font-semibold text-zinc-500 uppercase tracking-widest mb-0.5">Lead Builder</p>
                                <p class="text-sm font-medium text-white">Aryan Sharma</p>
                            </div>
                         </div>
                         <div class="h-8 w-px bg-white/5"></div>
                         <div>
                            <p class="text-[10px] font-semibold text-zinc-500 uppercase tracking-widest mb-0.5">Posted</p>
                            <p class="text-sm font-medium text-white">2 days ago</p>
                         </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 animate-fade-up">
                    <button class="btn-outline !px-6 !py-3">
                        <i class="far fa-heart mr-2 text-zinc-500"></i> 142
                    </button>
                    <button class="btn-primary !px-10 !py-3 !text-base">
                        Join Team
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-screen-xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
            <!-- Main -->
            <div class="md:col-span-8 space-y-12 animate-fade-up animate-delay-100">
                <section class="premium-card p-10">
                    <h2 class="text-xl font-bold text-white mb-8 tracking-tight">The Mission</h2>
                    <div class="text-zinc-400 leading-relaxed space-y-6">
                        <p>We're building a centralized AI chatbot designed to help students with academic queries, faculty contact information, and real-time campus event scheduling. </p>
                        <p>The goal is to reduce the friction of finding campus resources by providing an intelligent, 24/7 interface accessible via web and mobile.</p>

                        <div class="pt-6">
                            <h4 class="text-white font-semibold mb-4">Technical Stack</h4>
                            <div class="flex flex-wrap gap-2">
                                <span class="badge badge-outline">Node.js</span>
                                <span class="badge badge-outline">GPT-4</span>
                                <span class="badge badge-outline">PostgreSQL</span>
                                <span class="badge badge-outline">React</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Discussion -->
                <section class="premium-card p-10">
                     <div class="flex items-center justify-between mb-10">
                        <h2 class="text-xl font-bold text-white">Discussion <span class="text-zinc-600 ml-2">12</span></h2>
                        <button class="text-xs font-semibold text-zinc-500 hover:text-white transition-colors">Sort by Newest</button>
                    </div>

                    <div class="space-y-10">
                        <div class="flex gap-4">
                            <div class="h-9 w-9 rounded-full bg-zinc-800 flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-zinc-500">RK</div>
                            <div class="flex-1">
                                <div class="bg-white/[0.02] p-6 rounded-xl border border-white/5">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-xs font-bold text-white">Rohan Kumar</h4>
                                        <span class="text-[10px] text-zinc-600 font-semibold uppercase tracking-widest">5h ago</span>
                                    </div>
                                    <p class="text-sm text-zinc-400 leading-relaxed">This is exactly what our campus needs! How are you handling the real-time event updates?</p>
                                </div>
                                <div class="flex items-center gap-6 mt-4 ml-2">
                                    <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-widest">Reply</button>
                                    <button class="text-[10px] font-bold text-zinc-500 hover:text-white transition-colors uppercase tracking-widest inline-flex items-center gap-1.5">
                                        <i class="far fa-heart"></i> Like
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="md:col-span-4 space-y-8 animate-fade-up animate-delay-200">
                <div class="premium-card p-8">
                    <h3 class="text-xs font-bold text-white mb-8 uppercase tracking-[0.2em]">Open Roles</h3>
                    <div class="space-y-3">
                         <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/5 group cursor-pointer hover:border-white/20 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:text-white transition-colors">
                                    <i class="fas fa-code text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-zinc-400 group-hover:text-white transition-colors">Backend Dev</span>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-zinc-700 group-hover:text-white transition-all"></i>
                         </div>
                         <div class="flex items-center justify-between p-4 rounded-lg bg-white/5 border border-white/5 group cursor-pointer hover:border-white/20 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:text-white transition-colors">
                                    <i class="fas fa-palette text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-zinc-400 group-hover:text-white transition-colors">UI Designer</span>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-zinc-700 group-hover:text-white transition-all"></i>
                         </div>
                    </div>
                </div>

                <div class="premium-card bg-white/[0.02] p-8">
                    <h3 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-4">Quality Grade</h3>
                    <div class="flex items-end gap-3 mb-6">
                        <span class="text-5xl font-bold text-white italic tracking-tighter">A+</span>
                        <span class="text-xs font-bold text-brand uppercase mb-1.5">Verified</span>
                    </div>
                    <p class="text-xs text-zinc-500 leading-relaxed mb-8">This project has passed all Zero Slop quality gates and is considered production-ready.</p>
                    <button class="btn-outline w-full !text-xs !py-2.5">Audit Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
