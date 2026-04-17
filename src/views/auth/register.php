<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6">
    <div class="max-w-md w-full space-y-10">
        <div class="text-center animate-fade-in">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-white text-black mb-6">
                <i class="fas fa-user-plus text-sm"></i>
            </div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Create your account</h2>
            <p class="mt-3 text-sm text-zinc-400">
                Already part of the network?
                <a href="<?php echo BASE_URL; ?>/?page=login" class="font-medium text-white hover:underline underline-offset-4">Sign in</a>
            </p>
        </div>

        <div class="premium-card p-8 animate-fade-up">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-xs font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=register" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input id="name" name="name" type="text" required
                           class="form-input"
                           placeholder="Aryan Sharma">
                </div>

                <div>
                    <label for="email" class="form-label">Campus Email</label>
                    <input id="email" name="email" type="email" required
                           class="form-input"
                           placeholder="name@university.edu">
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" required
                           class="form-input"
                           placeholder="Min. 8 characters">
                </div>

                <div class="pt-2">
                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox" required class="mt-1 h-4 w-4 bg-white/5 border-white/10 rounded text-black focus:ring-zinc-500">
                        <span class="ml-3 block text-xs text-zinc-500 leading-normal group-hover:text-zinc-300 transition-colors">
                            I accept the <a href="#" class="text-zinc-400 underline underline-offset-2">Build Protocols</a> and <a href="#" class="text-zinc-400 underline underline-offset-2">Privacy Rules</a>.
                        </span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-2.5">
                        Create Account
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
