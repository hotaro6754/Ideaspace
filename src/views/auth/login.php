<?php
/**
 * IdeaSync - Professional Login Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body style="background: linear-gradient(135deg, var(--color-primary-800) 0%, var(--color-accent-600) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">

    <div style="width: 100%; max-width: 420px;">
        <!-- Card -->
        <div class="card" style="box-shadow: var(--shadow-xl);">
            <div class="card-body">
                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <a href="<?php echo BASE_URL; ?>/" class="navbar-brand" style="font-size: 1.875rem; margin-bottom: 1rem; display: inline-block;">IdeaSync</a>
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--color-text-primary);">Welcome Back</h1>
                    <p style="color: var(--color-text-secondary); font-size: 0.875rem;">Sign in to continue building</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                        <span style="flex: 1;"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                        <span style="flex: 1;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="<?php echo BASE_URL; ?>/src/controllers/auth.php" style="margin-bottom: 1.5rem;">
                    <input type="hidden" name="action" value="login">

                    <!-- Email/Roll -->
                    <div class="form-group">
                        <label class="form-label">Email or Roll Number</label>
                        <input type="text" name="identifier" class="form-input" placeholder="you@college.edu or LID001" required autofocus style="font-size: 16px;">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label class="form-label" style="margin-bottom: 0;">Password</label>
                            <a href="<?php echo BASE_URL; ?>/?page=forgot-password" style="font-size: 0.875rem; color: var(--color-accent-600);">Forgot?</a>
                        </div>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required style="font-size: 16px;">
                    </div>

                    <!-- Remember Me -->
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: center;">
                        <input type="checkbox" id="remember" name="remember" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--color-accent-600);">
                        <label for="remember" style="margin-left: 0.5rem; cursor: pointer; font-size: 0.875rem; color: var(--color-text-secondary);">Remember me</label>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 1rem;">Sign In</button>
                </form>

                <!-- Divider -->
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
                    <span style="color: var(--color-text-secondary); font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
                </div>

                <!-- GitHub Button -->
                <button type="button" class="btn btn-ghost btn-block" style="cursor: pointer; margin-bottom: 2rem;">
                    <span style="margin-right: 0.5rem;">🐙</span> Continue with GitHub
                </button>

                <!-- Sign Up Link -->
                <div style="text-align: center; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                    <p style="color: var(--color-text-secondary); font-size: 0.875rem;">
                        Don't have an account?
                        <a href="<?php echo BASE_URL; ?>/?page=register" style="color: var(--color-accent-600); font-weight: 600;">Create one free</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Link -->
        <div style="text-align: center; margin-top: 2rem; color: white; opacity: 0.8; font-size: 0.875rem;">
            <a href="<?php echo BASE_URL; ?>/" style="color: white; text-decoration: underline;">← Back to Home</a>
        </div>
    </div>

</body>
</html>
