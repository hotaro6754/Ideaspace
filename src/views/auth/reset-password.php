<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
$token = $_GET['token'] ?? '';
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-white shadow-premium mb-6">
                <i class="fas fa-lock-open text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Set New Password</h2>
        </div>

        <div class="premium-card p-10 bg-white">
            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=reset-password" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                <input type="hidden" name="token" value="<?php echo Security::escape($token); ?>">

                <div>
                    <label for="password" class="form-label text-xs">New Password</label>
                    <input id="password" name="password" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirm" class="form-label text-xs">Confirm New Password</label>
                    <input id="password_confirm" name="password_confirm" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest">
                        Update Password
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
