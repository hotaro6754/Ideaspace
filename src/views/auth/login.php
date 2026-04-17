<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-background relative overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-accent/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-md w-full space-y-8 animate-fade-in-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-background text-2xl mb-6 shadow-lg shadow-primary/30">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Access Ecosystem</h2>
            <p class="mt-2 text-sm text-slate-500 font-medium">
                New builder?
                <a href="<?php echo BASE_URL; ?>/?page=register" class="font-bold text-primary hover:text-white transition-colors">Join the network</a>
            </p>
        </div>

        <div class="bg-surface-container-low p-10 rounded-[2.5rem] shadow-2xl border border-white/5">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-bold flex items-center gap-3">
                    <i class="fas fa-circle-exclamation"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Identity (Email)</label>
                    <div class="relative group">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors text-sm"></i>
                        <input id="email" name="email" type="email" required
                               class="block w-full pl-12 pr-4 py-4 bg-surface-container-high border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm"
                               placeholder="builder@university.edu">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3 ml-1">
                        <label for="password" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Key (Password)</label>
                        <a href="<?php echo BASE_URL; ?>/?page=forgot-password" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:text-white transition-colors">Lost Key?</a>
                    </div>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors text-sm"></i>
                        <input id="password" name="password" type="password" required
                               class="block w-full pl-12 pr-4 py-4 bg-surface-container-high border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm"
                               placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full btn-primary py-4 uppercase tracking-[0.2em] text-xs font-black">
                        Authorize Access
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
