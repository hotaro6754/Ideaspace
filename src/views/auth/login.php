<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6">
    <div class="max-w-md w-full space-y-10">
        <div class="text-center animate-fade-in">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-white text-black mb-6">
                <i class="fas fa-terminal"></i>
            </div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Welcome back</h2>
            <p class="mt-3 text-sm text-zinc-400">
                Don't have an account?
                <a href="<?php echo BASE_URL; ?>/?page=register" class="font-medium text-white hover:underline underline-offset-4">Join the network</a>
            </p>
        </div>

        <div class="premium-card p-8 animate-fade-up">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" required
                           class="form-input"
                           placeholder="name@university.edu">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="<?php echo BASE_URL; ?>/?page=forgot-password" class="text-xs text-zinc-400 hover:text-white transition-colors">Forgot?</a>
                    </div>
                    <input id="password" name="password" type="password" required
                           class="form-input"
                           placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-2.5">
                        Sign in
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
