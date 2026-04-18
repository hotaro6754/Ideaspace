<?php
ob_start();
if (!isLoggedIn()) {
    redirect(BASE_URL . '/?page=login');
}
$current_user = getCurrentUser();
?>

<div class="max-w-screen-xl mx-auto px-6 py-12">
    <!-- Profile Header -->
    <div class="premium-card p-10 mb-10 flex flex-col md:flex-row items-center gap-10 animate-fade-in">
        <div class="h-32 w-32 rounded-full bg-white flex items-center justify-center text-black text-4xl font-bold shadow-premium">
            <?php echo ($current_user ? strtoupper(substr($current_user['name'], 0, 1)) : 'U'); ?>
        </div>
        <div class="flex-1 text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-4">
                Verified Builder
            </div>
            <h1 class="text-4xl font-bold text-white mb-2"><?php echo htmlspecialchars($current_user['name']); ?></h1>
            <p class="text-zinc-400 font-medium">
                <?php echo htmlspecialchars($current_user['roll_number']); ?> • <?php echo htmlspecialchars($current_user['branch']); ?> • Year <?php echo htmlspecialchars($current_user['year']); ?>
            </p>
        </div>
        <a href="#" class="btn-primary !px-6">Edit Profile</a>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
        <!-- Main Info -->
        <div class="md:col-span-8 space-y-10 animate-fade-up animate-delay-100">
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-6">
                <div class="premium-card p-8 text-center">
                    <p class="text-3xl font-bold text-white mb-1">12</p>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Ideas</p>
                </div>
                <div class="premium-card p-8 text-center">
                    <p class="text-3xl font-bold text-white mb-1">5</p>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Projects</p>
                </div>
                <div class="premium-card p-8 text-center">
                    <p class="text-3xl font-bold text-white mb-1">156</p>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Upvotes</p>
                </div>
            </div>

            <!-- Projects Section -->
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-semibold text-white">Recent Projects</h2>
                    <a href="#" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">View all</a>
                </div>
                <div class="space-y-4">
                    <div class="premium-card p-6 flex items-center justify-between group">
                        <div class="flex items-center gap-6">
                            <div class="h-10 w-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white">
                                <i class="fas fa-microchip text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white mb-1">Campus AI Study Buddy</h4>
                                <p class="text-xs text-zinc-500">March 2024 • Completed</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-zinc-700 group-hover:text-white transition-colors text-xs"></i>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="md:col-span-4 space-y-10 animate-fade-up animate-delay-200">
            <!-- Skills -->
            <div class="premium-card p-8">
                <h3 class="text-sm font-bold text-white mb-8 uppercase tracking-widest">Skills</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 rounded bg-white/5 border border-white/10 text-xs text-zinc-300">Python</span>
                    <span class="px-3 py-1 rounded bg-white/5 border border-white/10 text-xs text-zinc-300">React</span>
                    <span class="px-3 py-1 rounded bg-white/5 border border-white/10 text-xs text-zinc-300">Tailwind</span>
                    <span class="px-3 py-1 rounded bg-white/5 border border-white/10 text-xs text-zinc-300">PHP</span>
                </div>
            </div>

            <!-- Social/Links -->
            <div class="premium-card p-8">
                <h3 class="text-sm font-bold text-white mb-8 uppercase tracking-widest">Network</h3>
                <div class="space-y-6">
                    <a href="#" class="flex items-center gap-4 text-zinc-400 hover:text-white transition-colors">
                        <i class="fab fa-github"></i>
                        <span class="text-xs font-medium">github.com/<?php echo strtolower(explode(' ', $current_user['name'])[0]); ?></span>
                    </a>
                    <a href="#" class="flex items-center gap-4 text-zinc-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter"></i>
                        <span class="text-xs font-medium">twitter.com/<?php echo strtolower(explode(' ', $current_user['name'])[0]); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
