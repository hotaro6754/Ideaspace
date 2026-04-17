<?php
ob_start();
$user = getCurrentUser();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div class="max-w-2xl">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-4 tracking-tight leading-tight">Explore <span class="text-accent-600 italic font-serif">Brilliant</span> Ideas</h1>
            <p class="text-slate-500 text-lg">Discover projects from campus builders and find your next big collaboration.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
             <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-accent-600 transition-colors"></i>
                <input type="text" placeholder="Search ideas..." class="pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-accent-600/20 focus:border-accent-600 transition-all text-sm">
             </div>
             <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="inline-flex items-center justify-center px-6 py-3 bg-accent-600 text-white font-bold rounded-2xl hover:bg-accent-700 transition-all shadow-xl shadow-accent-500/20">
                <i class="fas fa-plus mr-2 text-xs"></i> Post Idea
            </a>
        </div>
    </div>

    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 mb-10 overflow-x-auto pb-2 scrollbar-hide">
        <button class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-lg shadow-slate-900/20 transition-all">All Projects</button>
        <button class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">AI / ML</button>
        <button class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">Fintech</button>
        <button class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">EdTech</button>
        <button class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">Sustainability</button>
        <button class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">IoT</button>
    </div>

    <!-- Ideas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php for($i=1; $i<=6; $i++): ?>
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden flex flex-col">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center font-bold text-xs ring-1 ring-accent-100">
                             <?php echo ['AI', 'FT', 'ED', 'ST', 'IO', 'WEB'][$i-1]; ?>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Posted by</p>
                            <p class="text-xs font-bold text-slate-900">Aryan Sharma</p>
                        </div>
                    </div>
                    <button class="h-8 w-8 rounded-full hover:bg-slate-50 text-slate-300 hover:text-red-500 transition-colors">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>

                <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-accent-600 transition-colors line-clamp-1">
                    <?php echo [
                        'Campus AI Study Buddy',
                        'Decentralized Peer-to-Peer Loans',
                        'Smart Attendance with QR',
                        'Vertical Farming for Hostels',
                        'Smart Water Management',
                        'AI Resume Optimizer'
                    ][$i-1]; ?>
                </h3>

                <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-8">
                    An innovative solution to tackle daily campus problems using modern technology and collaborative building. Join us in shaping the future!
                </p>

                <div class="flex items-center gap-4 mt-auto">
                    <div class="flex -space-x-2">
                        <div class="h-6 w-6 rounded-full bg-slate-200 border-2 border-white ring-1 ring-slate-100 flex items-center justify-center text-[8px] font-bold">AS</div>
                        <div class="h-6 w-6 rounded-full bg-accent-500 border-2 border-white ring-1 ring-accent-100 flex items-center justify-center text-[8px] text-white font-bold">RK</div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">3 Open Roles</span>
                </div>
            </div>

            <div class="mt-auto px-8 py-5 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                        <i class="fas fa-arrow-up text-accent-500"></i> <?php echo rand(20, 200); ?>
                    </span>
                    <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                        <i class="fas fa-comment text-slate-400"></i> <?php echo rand(5, 30); ?>
                    </span>
                </div>
                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $i; ?>" class="text-xs font-extrabold text-accent-600 group-hover:translate-x-1 transition-transform uppercase tracking-wider">
                    Join Team <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex justify-center">
        <nav class="flex items-center gap-2 p-1 bg-white border border-slate-100 rounded-2xl shadow-sm">
            <button class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-50 text-slate-400 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="h-10 w-10 flex items-center justify-center rounded-xl bg-accent-600 text-white font-bold shadow-lg shadow-accent-600/20">1</button>
            <button class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-50 text-slate-600 font-bold">2</button>
            <button class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-50 text-slate-600 font-bold">3</button>
            <button class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-50 text-slate-400 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </nav>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
