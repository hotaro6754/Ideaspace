<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-white shadow-premium mb-6">
                <i class="fas fa-key text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Recover Password</h2>
            <p class="mt-4 text-slate-500 font-medium">
                Remembered?
                <a href="<?php echo BASE_URL; ?>/?page=login" class="text-primary font-bold hover:underline underline-offset-4 ml-1">Sign in instead</a>
            </p>
        </div>

        <div class="premium-card p-10 bg-white">
            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=forgot-password" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label for="email" class="form-label text-xs">Academic Email</label>
                    <input id="email" name="email" type="email" required class="form-input" placeholder="name@lendi.edu.in">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest">
                        Send Recovery Link
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
