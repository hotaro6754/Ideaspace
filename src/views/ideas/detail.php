<?php
ob_start();
$user = getCurrentUser();
// Simplified detail view
?>

<div class="bg-slate-50 min-h-screen">
    <!-- Project Hero -->
    <div class="relative pt-16 pb-24 bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://neuform.ai/wp-content/uploads/2024/02/Pattern.png')] bg-repeat"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="px-3 py-1 rounded-lg bg-accent-600/20 text-accent-400 text-[10px] font-extrabold uppercase tracking-widest border border-accent-600/30">Artificial Intelligence</span>
                        <span class="px-3 py-1 rounded-lg bg-slate-800 text-slate-400 text-[10px] font-extrabold uppercase tracking-widest border border-slate-700">Production Ready</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-6">Campus AI Assistant</h1>
                    <div class="flex items-center gap-6">
                         <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-accent-500 to-accent-700 flex items-center justify-center text-white font-bold ring-2 ring-slate-800">AS</div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none mb-1">Lead Builder</p>
                                <p class="text-sm font-bold text-white">Aryan Sharma</p>
                            </div>
                         </div>
                         <div class="h-8 w-px bg-slate-800"></div>
                         <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none mb-1">Posted</p>
                            <p class="text-sm font-bold text-white">2 days ago</p>
                         </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="flex-1 md:flex-none px-6 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-2xl border border-white/20 hover:bg-white/20 transition-all shadow-xl">
                        <i class="fas fa-arrow-up mr-2 text-accent-400"></i> Upvote (142)
                    </button>
                    <button class="flex-1 md:flex-none px-8 py-4 bg-accent-600 text-white font-extrabold rounded-2xl hover:bg-accent-700 hover:scale-105 transition-all shadow-xl shadow-accent-600/25">
                        Join Team
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Body -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 pb-20">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <!-- Main Content -->
            <div class="md:col-span-8 space-y-8">
                <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6 tracking-tight">The Mission</h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed space-y-4">
                        <p>We're building a centralized AI chatbot designed to help students with academic queries, faculty contact information, and real-time campus event scheduling. </p>
                        <p>The goal is to reduce the friction of finding campus resources by providing an intelligent, 24/7 interface accessible via web and mobile.</p>
                        <h4 class="text-slate-900 font-bold text-lg mt-8">Technical Stack</h4>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Backend: Node.js with Express</li>
                            <li>AI: OpenAI API (GPT-4) with RAG</li>
                            <li>Database: PostgreSQL for campus knowledge base</li>
                            <li>Frontend: React + Tailwind CSS</li>
                        </ul>
                    </div>
                </div>

                <!-- Comments / Discussion -->
                <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-100">
                     <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Discussion <span class="text-slate-300 ml-2">12</span></h2>
                        <button class="text-sm font-bold text-accent-600 hover:underline">Newest First</button>
                    </div>

                    <div class="space-y-8">
                        <!-- Single Comment -->
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-slate-100 flex-shrink-0 flex items-center justify-center font-bold text-slate-400">RK</div>
                            <div class="flex-1">
                                <div class="bg-slate-50 p-5 rounded-2xl rounded-tl-none border border-slate-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-slate-900">Rohan Kumar</h4>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">5h ago</span>
                                    </div>
                                    <p class="text-sm text-slate-600 leading-relaxed">This is exactly what our campus needs! How are you handling the real-time event updates? Are you scraping the site or using an API?</p>
                                </div>
                                <div class="flex items-center gap-4 mt-3 ml-2">
                                    <button class="text-[10px] font-bold text-slate-400 hover:text-accent-600 transition-colors uppercase tracking-widest">Reply</button>
                                    <button class="text-[10px] font-bold text-slate-400 hover:text-accent-600 transition-colors uppercase tracking-widest"><i class="fas fa-heart mr-1"></i> Like</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="md:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Open Roles</h3>
                    <div class="space-y-4">
                         <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer hover:bg-accent-600 hover:border-accent-600 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-white flex items-center justify-center text-accent-600 text-xs shadow-sm">
                                    <i class="fas fa-code"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">Backend Dev</span>
                            </div>
                            <i class="fas fa-plus text-[10px] text-slate-300 group-hover:text-white"></i>
                         </div>
                         <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 group cursor-pointer hover:bg-accent-600 hover:border-accent-600 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-white flex items-center justify-center text-accent-600 text-xs shadow-sm">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">UI Designer</span>
                            </div>
                            <i class="fas fa-plus text-[10px] text-slate-300 group-hover:text-white"></i>
                         </div>
                    </div>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute -bottom-4 -right-4 text-accent-500/10 text-8xl -rotate-12 group-hover:rotate-0 transition-transform">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="font-bold mb-4 uppercase text-[10px] tracking-widest text-accent-400">Project Quality</h3>
                    <div class="text-3xl font-extrabold mb-2 italic">A+ Grade</div>
                    <p class="text-xs text-slate-400 leading-relaxed relative z-10">This project has passed all Zero Slop quality gates and is considered production-ready.</p>
                    <button class="mt-6 w-full py-3 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-xs font-bold transition-all">View Audit Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
