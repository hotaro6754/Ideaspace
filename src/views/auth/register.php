<?php
/**
 * IdeaSync - Professional Register Page
 */
require_once __DIR__ . '/../../config/Database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join IdeaSync | Lendi Innovation Platform</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <div class="flex" style="min-height: 100vh;">
        <!-- Left Panel -->
        <div style="flex: 1; background-color: var(--bg-secondary); border-right: 1px solid var(--border);" class="flex flex-col justify-center p-20">
            <a href="/" class="logo mb-12">IDEASYNC</a>
            <h1 class="mb-6">Turn your ideas into production code.</h1>
            <p class="text-secondary text-lg mb-8">Join the community of builders at Lendi Institute of Engineering and Technology.</p>

            <div class="card p-5" style="background-color: var(--bg-tertiary);">
                <p class="text-sm italic mb-4">"IdeaSync is where I found my co-founders for our AI startup. It's the GitHub of our campus."</p>
                <div class="flex items-center gap-3">
                    <div class="user-avatar">H</div>
                    <div>
                        <div class="text-xs font-semibold">Harshith</div>
                        <div class="text-[10px] text-muted">CSE 3rd Year</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div style="flex: 1.2;" class="flex items-center justify-center p-20">
            <div style="width: 100%; max-width: 440px;">
                <h2 class="mb-2">Create your account</h2>
                <p class="text-secondary text-sm mb-8">Already building? <a href="/?page=login">Sign in here</a></p>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="card mb-6" style="border-color: var(--color-error); background: rgba(239, 68, 68, 0.05);">
                        <p class="text-xs" style="color: var(--color-error);"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="/src/controllers/auth.php" method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="grid grid-2">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-input" placeholder="Ravi Kumar" required>
                        </div>
                        <div class="input-group">
                            <label>Roll Number</label>
                            <input type="text" name="roll_number" class="form-input" placeholder="21B21A0501" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>College Email (@lendi.org)</label>
                        <input type="email" name="email" class="form-input" placeholder="ravi@lendi.org" required>
                    </div>

                    <div class="grid grid-2">
                        <div class="input-group">
                            <label>Branch</label>
                            <select name="branch" class="form-select">
                                <option value="CSE">CSE</option>
                                <option value="ECE">ECE</option>
                                <option value="EEE">EEE</option>
                                <option value="MECH">MECH</option>
                                <option value="CIVIL">CIVIL</option>
                                <option value="IT">IT</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Year</label>
                            <select name="year" class="form-select">
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3 mt-4">Start Building</button>

                    <p class="mt-6 text-[10px] text-muted text-center">
                        By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
