<?php
/**
 * IdeaSync - Professional Register Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body style="background: linear-gradient(135deg, var(--color-primary-800) 0%, var(--color-accent-600) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">

    <div style="width: 100%; max-width: 480px;">
        <!-- Card -->
        <div class="card" style="box-shadow: var(--shadow-xl);">
            <div class="card-body">
                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <a href="<?php echo BASE_URL; ?>/" class="navbar-brand" style="font-size: 1.875rem; margin-bottom: 1rem; display: inline-block;">IdeaSync</a>
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--color-text-primary);">Create Your Account</h1>
                    <p style="color: var(--color-text-secondary); font-size: 0.875rem;">Join the campus collaboration platform</p>
                </div>

                <!-- Error Message -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                        <span style="flex: 1;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Register Form -->
                <form method="POST" action="<?php echo BASE_URL; ?>/src/controllers/auth.php">
                    <input type="hidden" name="action" value="register">

                    <!-- Full Name -->
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-input" placeholder="Your full name" required style="font-size: 16px;">
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="you@college.edu" required style="font-size: 16px;">
                    </div>

                    <!-- Roll Number -->
                    <div class="form-group">
                        <label class="form-label">Roll Number (Optional)</label>
                        <input type="text" name="roll_number" class="form-input" placeholder="LID001" style="font-size: 16px;">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters" required style="font-size: 16px;">
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-input" placeholder="Confirm password" required style="font-size: 16px;">
                    </div>

                    <!-- User Type -->
                    <div class="form-group">
                        <label class="form-label">What are you?</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer;">
                                <input type="radio" name="user_type" value="visionary" required style="width: 18px; height: 18px; margin-right: 0.5rem; accent-color: var(--color-accent-600);">
                                <span>💡 Visionary</span>
                            </label>
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer;">
                                <input type="radio" name="user_type" value="builder" required style="width: 18px; height: 18px; margin-right: 0.5rem; accent-color: var(--color-accent-600);">
                                <span>🔨 Builder</span>
                            </label>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: flex-start;">
                        <input type="checkbox" id="terms" name="terms" required style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--color-accent-600); margin-top: 2px;">
                        <label for="terms" style="margin-left: 0.5rem; cursor: pointer; font-size: 0.875rem; color: var(--color-text-secondary);">
                            I agree to the <a href="#" style="color: var(--color-accent-600);">Terms of Service</a> and <a href="#" style="color: var(--color-accent-600);">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Create Account Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 1rem;">Create Free Account</button>
                </form>

                <!-- Divider -->
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
                    <span style="color: var(--color-text-secondary); font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
                </div>

                <!-- GitHub Button -->
                <button type="button" class="btn btn-ghost btn-block" style="cursor: pointer; margin-bottom: 2rem;">
                    <span style="margin-right: 0.5rem;">🐙</span> Sign up with GitHub
                </button>

                <!-- Sign In Link -->
                <div style="text-align: center; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                    <p style="color: var(--color-text-secondary); font-size: 0.875rem;">
                        Already have an account?
                        <a href="<?php echo BASE_URL; ?>/?page=login" style="color: var(--color-accent-600); font-weight: 600;">Sign in</a>
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
