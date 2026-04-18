<?php
ob_start();
$user = getCurrentUser();
?>

<div class="max-w-screen-xl mx-auto px-6 py-16">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 mb-20 animate-fade-in">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 tracking-tight">Discover Ideas</h1>
            <p class="text-zinc-400 text-lg">Browse vetted projects from the campus builder network.</p>
        </div>
        <div class="flex items-center gap-4">
             <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 group-focus-within:text-white transition-colors text-xs"></i>
                <input type="text" placeholder="Search..." class="form-input !pl-10 !py-2.5 !w-64">
             </div>
             <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="btn-primary">
                Post Project
            </a>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="flex items-center gap-3 mb-12 overflow-x-auto pb-4 animate-fade-in animate-delay-100 no-scrollbar">
        <button class="px-4 py-1.5 rounded-full bg-white text-black text-xs font-semibold whitespace-nowrap">All Projects</button>
        <button class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-zinc-400 text-xs font-semibold hover:bg-white/10 transition-colors whitespace-nowrap">Engineering</button>
        <button class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-zinc-400 text-xs font-semibold hover:bg-white/10 transition-colors whitespace-nowrap">Design</button>
        <button class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-zinc-400 text-xs font-semibold hover:bg-white/10 transition-colors whitespace-nowrap">Business</button>
        <button class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-zinc-400 text-xs font-semibold hover:bg-white/10 transition-colors whitespace-nowrap">Social Impact</button>
    </div>

    <!-- Ideas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-up animate-delay-200">
        <?php for($i=1; $i<=6; $i++): ?>
        <div class="premium-card group">
            <div class="p-8 h-full flex flex-col">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white font-bold text-[10px]">
                             <?php echo ['AI', 'FT', 'ED', 'ST', 'IO', 'WB'][$i-1]; ?>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-zinc-500 uppercase tracking-widest mb-0.5">Vetted by</p>
                            <p class="text-xs font-medium text-white">Aryan Sharma</p>
                        </div>
                    </div>
                    <button class="text-zinc-500 hover:text-white transition-colors">
                        <i class="far fa-bookmark text-xs"></i>
                    </button>
                </div>

                <h3 class="text-xl font-bold text-white mb-4 group-hover:text-zinc-300 transition-colors leading-tight">
                    <?php echo [
                        'Campus AI Study Buddy',
                        'Decentralized Peer-to-Peer Loans',
                        'Smart Attendance System',
                        'Vertical Farming Initiative',
                        'Smart Water Management',
                        'AI Resume Optimizer'
                    ][$i-1]; ?>
                </h3>

                <p class="text-zinc-400 text-sm leading-relaxed line-clamp-2 mb-8">
                    An innovative approach to solving complex campus challenges using high-end engineering and collaborative design.
                </p>

                <div class="mt-auto pt-6 border-t border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-1.5">
                            <div class="h-6 w-6 rounded-full border border-[#09090b] bg-zinc-800 flex items-center justify-center text-[8px] font-bold">AS</div>
                            <div class="h-6 w-6 rounded-full border border-[#09090b] bg-zinc-700 flex items-center justify-center text-[8px] font-bold">RK</div>
                        </div>
                        <span class="text-[10px] font-semibold text-zinc-500 uppercase tracking-widest">3 Open slots</span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $i; ?>" class="text-[10px] font-bold text-white uppercase tracking-widest hover:translate-x-1 transition-transform inline-flex items-center gap-2">
                        View <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-20 flex justify-center animate-fade-in animate-delay-300">
        <div class="flex items-center gap-1 p-1 bg-white/5 border border-white/10 rounded-lg">
            <button class="h-8 w-8 flex items-center justify-center text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </button>
            <button class="h-8 w-8 flex items-center justify-center bg-white text-black text-xs font-bold rounded-md">1</button>
            <button class="h-8 w-8 flex items-center justify-center text-zinc-400 text-xs font-bold hover:bg-white/5 rounded-md transition-colors">2</button>
            <button class="h-8 w-8 flex items-center justify-center text-zinc-400 text-xs font-bold hover:bg-white/5 rounded-md transition-colors">3</button>
            <button class="h-8 w-8 flex items-center justify-center text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
