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
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Initiate Protocol</h2>
            <p class="mt-2 text-sm text-slate-500 font-medium">
                Already registered?
                <a href="<?php echo BASE_URL; ?>/?page=login" class="font-bold text-primary hover:text-white transition-colors">Sign in here</a>
            </p>
        </div>

        <div class="bg-surface-container-low p-10 rounded-[2.5rem] shadow-2xl border border-white/5">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-bold flex items-center gap-3">
                    <i class="fas fa-circle-exclamation"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-5" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=register" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label for="name" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Full Designation</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors text-sm"></i>
                        <input id="name" name="name" type="text" required
                               class="block w-full pl-12 pr-4 py-4 bg-surface-container-high border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm"
                               placeholder="Aryan Sharma">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Campus Identity</label>
                    <div class="relative group">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors text-sm"></i>
                        <input id="email" name="email" type="email" required
                               class="block w-full pl-12 pr-4 py-4 bg-surface-container-high border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm"
                               placeholder="you@university.edu">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Secure Key</label>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors text-sm"></i>
                        <input id="password" name="password" type="password" required
                               class="block w-full pl-12 pr-4 py-4 bg-surface-container-high border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm"
                               placeholder="Min. 8 characters">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox" required class="mt-1 h-4 w-4 bg-surface-container-high border-white/10 rounded text-primary focus:ring-primary/20 transition-all">
                        <span class="ml-3 block text-[10px] text-slate-500 font-bold uppercase tracking-widest leading-normal group-hover:text-slate-300 transition-colors">
                            I accept the <a href="#" class="text-primary hover:underline">Build Protocols</a> & <a href="#" class="text-primary hover:underline">Privacy Rules</a>.
                        </span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full btn-primary py-4 uppercase tracking-[0.2em] text-xs font-black">
                        Initialize Profile
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
