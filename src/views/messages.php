<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 h-[calc(100vh-140px)]">
    <div class="bg-surface-container-low rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden flex h-full">
        <!-- Sidebar: Conversations -->
        <div class="w-full md:w-96 border-r border-white/5 flex flex-col">
            <div class="p-8 border-b border-white/5">
                <h1 class="text-2xl font-black text-white tracking-tight mb-6">Channels</h1>
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors"></i>
                    <input type="text" placeholder="Search conversations..." class="w-full pl-11 pr-4 py-3 bg-surface-container-high border border-white/5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm text-white">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <!-- Channel Item -->
                <div class="p-4 bg-surface-container-highest rounded-2xl border border-primary/20 cursor-pointer">
                    <div class="flex gap-4">
                        <div class="h-12 w-12 rounded-xl bg-primary text-background flex items-center justify-center font-bold">#</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-bold text-white text-sm truncate">Campus AI Assistant</h3>
                                <span class="text-[10px] text-primary font-bold">12:45 PM</span>
                            </div>
                            <p class="text-xs text-slate-400 truncate">Rohan: I've updated the RAG pipeline...</p>
                        </div>
                    </div>
                </div>

                <!-- Direct Message Item -->
                <div class="p-4 hover:bg-surface-container-high rounded-2xl transition-all cursor-pointer group">
                    <div class="flex gap-4">
                        <div class="h-12 w-12 rounded-xl bg-slate-800 flex items-center justify-center font-bold text-slate-400 group-hover:bg-primary group-hover:text-background transition-colors">SK</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-bold text-white text-sm truncate">Sneha Kapur</h3>
                                <span class="text-[10px] text-slate-500 font-bold tracking-widest">Yesterday</span>
                            </div>
                            <p class="text-xs text-slate-500 truncate">Looking forward to the meeting tomorrow!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main: Chat Area -->
        <div class="hidden md:flex flex-1 flex-col bg-surface-container/30">
            <!-- Chat Header -->
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-surface-container-low">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-primary text-background flex items-center justify-center font-black text-xl shadow-lg shadow-primary/20">#</div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Campus AI Assistant</h2>
                        <p class="text-xs text-primary font-bold tracking-widest uppercase">General Channel • 12 Members</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="h-10 w-10 rounded-xl bg-surface-container-high text-slate-400 flex items-center justify-center hover:text-white transition-colors">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="h-10 w-10 rounded-xl bg-surface-container-high text-slate-400 flex items-center justify-center hover:text-white transition-colors">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8">
                <!-- Message Left -->
                <div class="flex gap-4 max-w-2xl">
                    <div class="h-10 w-10 rounded-xl bg-slate-800 flex-shrink-0 flex items-center justify-center font-bold text-slate-400">RK</div>
                    <div>
                        <div class="flex items-baseline gap-3 mb-2">
                            <span class="font-bold text-white text-sm">Rohan Kumar</span>
                            <span class="text-[10px] text-slate-500 font-bold tracking-widest uppercase">12:30 PM</span>
                        </div>
                        <div class="bg-surface-container-high p-4 rounded-2xl rounded-tl-none border border-white/5">
                            <p class="text-sm text-slate-300 leading-relaxed">Hey team, I've successfully integrated the OpenAI GPT-4o model into the backend. The response latency is significantly lower now.</p>
                        </div>
                        <div class="flex gap-2 mt-2">
                            <span class="px-2 py-1 rounded-lg bg-surface-container-highest text-[10px] text-slate-400 font-bold border border-white/5">🔥 4</span>
                            <span class="px-2 py-1 rounded-lg bg-surface-container-highest text-[10px] text-slate-400 font-bold border border-white/5">🚀 2</span>
                        </div>
                    </div>
                </div>

                <!-- Message Right (You) -->
                <div class="flex gap-4 max-w-2xl ml-auto flex-row-reverse">
                    <div class="h-10 w-10 rounded-xl bg-primary flex-shrink-0 flex items-center justify-center font-bold text-background shadow-lg shadow-primary/20">AS</div>
                    <div class="text-right">
                        <div class="flex items-baseline justify-end gap-3 mb-2">
                            <span class="text-[10px] text-slate-500 font-bold tracking-widest uppercase">12:45 PM</span>
                            <span class="font-bold text-white text-sm">You</span>
                        </div>
                        <div class="bg-primary p-4 rounded-2xl rounded-tr-none text-background font-medium text-sm shadow-xl shadow-primary/10">
                            Great work Rohan! Let's test the RAG pipeline with the campus library dataset next. I'll prepare the embeddings this afternoon.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="p-8 border-t border-white/5 bg-surface-container-low">
                <div class="bg-surface-container-high border border-white/5 rounded-[1.5rem] p-2 flex items-center gap-2 group focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                    <button class="h-12 w-12 rounded-xl text-slate-500 hover:text-primary transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                    <input type="text" placeholder="Send a message to #campus-ai-assistant..." class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-white px-2">
                    <div class="flex items-center gap-2 pr-2">
                        <button class="h-10 w-10 text-slate-500 hover:text-primary transition-colors">
                            <i class="fas fa-smile"></i>
                        </button>
                        <button class="h-12 w-12 bg-primary text-background rounded-xl flex items-center justify-center hover:scale-105 transition-all shadow-lg shadow-primary/20">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
