<?php
/**
 * IdeaSync - Professional Login Page
 */
require_once __DIR__ . '/../../config/Database.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/?page=feed');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <div class="flex" style="min-height: 100vh;">
        <!-- Left Panel: Brand -->
        <div style="flex: 1; background-color: var(--bg-secondary); border-right: 1px solid var(--border);" class="flex flex-col justify-center p-20">
            <a href="/" class="logo mb-12">IDEASYNC</a>
            <h1 class="mb-6">Build the future with Lendi's best.</h1>
            <p class="text-secondary text-lg mb-8">Access the campus OS for innovation, collaboration, and growth.</p>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="text-accent" size="20"></i>
                    <span class="text-secondary">Verified builder profiles</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="text-accent" size="20"></i>
                    <span class="text-secondary">Real-time collaboration</span>
                </div>
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="text-accent" size="20"></i>
                    <span class="text-secondary">Gamified ranking system</span>
                </div>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div style="flex: 1.2;" class="flex items-center justify-center p-20">
            <div style="width: 100%; max-width: 400px;">
                <h2 class="mb-2">Welcome back</h2>
                <p class="text-secondary text-sm mb-8">Don't have an account? <a href="/?page=register">Join here</a></p>

                <form action="/src/controllers/auth.php?action=login" method="POST">
                    <div class="input-group">
                        <label>Roll Number / Email</label>
                        <input type="text" name="identifier" class="form-input" placeholder="e.g. 21B21A0501" required>
                    </div>

                    <div class="input-group">
                        <div class="flex justify-between items-center mb-2">
                            <label style="margin-bottom: 0;">Password</label>
                            <a href="#" class="text-xs text-accent">Forgot?</a>
                        </div>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3 mt-4">Sign In</button>

                    <div class="mt-8 text-center">
                        <span class="text-muted text-xs uppercase tracking-widest">— Or continue with —</span>
                    </div>

                    <button type="button" class="btn btn-secondary w-full py-3 mt-6">
                        <i data-lucide="github" size="18"></i>
                        GitHub
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
