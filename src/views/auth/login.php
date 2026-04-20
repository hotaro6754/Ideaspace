<?php
require_once __DIR__ . '/../../helpers/Security.php';
require_once __DIR__ . '/../../services/GitHubAPI.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-white shadow-premium mb-6">
                <i class="fas fa-fingerprint text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Access Your Portal</h2>
            <p class="mt-4 text-slate-500 font-medium">
                New to the IdeaSync platform?
                <a href="<?php echo BASE_URL; ?>/?page=register" class="text-primary font-bold hover:underline underline-offset-4 ml-1">Create an account</a>
            </p>
        </div>

        <div class="premium-card p-10 bg-white">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-xl text-secondary text-sm font-bold flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="mb-8 p-4 bg-green-50 border border-green-100 rounded-xl text-green-700 text-sm font-bold flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                <input type="hidden" name="action" value="login">

                <div>
                    <label for="identifier" class="form-label text-xs">Roll Number or Email</label>
                    <input id="identifier" name="identifier" type="text" required class="form-input" placeholder="LID001 / name@lendi.edu.in">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="form-label text-xs mb-0">Security Password</label>
                        <a href="<?php echo BASE_URL; ?>/?page=forgot-password" class="text-xs font-bold text-primary hover:text-primary-dark transition-colors">Forgot?</a>
                    </div>
                    <input id="password" name="password" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <a href="<?php echo (new GitHubAPI())->getAuthUrl(); ?>" class="btn-outline w-full py-4 text-sm font-bold uppercase tracking-widest flex items-center justify-center gap-3 mb-4">
                        <i class="fab fa-github text-xl"></i> Continue with GitHub
                    </a>
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest">
                        Verify & Enter
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
