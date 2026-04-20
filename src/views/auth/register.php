<?php
require_once __DIR__ . '/../../helpers/Security.php';
require_once __DIR__ . '/../../services/GitHubAPI.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-white shadow-premium mb-6">
                <i class="fas fa-user-plus text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create Academic Profile</h2>
            <p class="mt-4 text-slate-500 font-medium">
                Already have a profile?
                <a href="<?php echo BASE_URL; ?>/?page=login" class="text-primary font-bold hover:underline underline-offset-4 ml-1">Sign in here</a>
            </p>
        </div>

        <div class="premium-card p-10 bg-white">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-xl text-secondary text-sm font-bold flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=register" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label for="name" class="form-label text-xs">Full Name</label>
                        <input id="name" name="name" type="text" required class="form-input" placeholder="Enter your full name">
                    </div>

                    <div class="col-span-2">
                        <label for="roll_number" class="form-label text-xs">Roll Number</label>
                        <input id="roll_number" name="roll_number" type="text" required class="form-input" placeholder="LID001">
                    </div>
                </div>

                <div>
                    <label for="email" class="form-label text-xs">Academic Email</label>
                    <input id="email" name="email" type="email" required class="form-input" placeholder="name@lendi.edu.in">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="branch" class="form-label text-xs">Department</label>
                        <select id="branch" name="branch" required class="form-select">
                            <option value="CSE">CSE</option>
                            <option value="ECE">ECE</option>
                            <option value="EEE">EEE</option>
                            <option value="MECH">MECH</option>
                            <option value="CSSE">CSSE</option>
                            <option value="CSIT">CSIT</option>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="form-label text-xs">Current Year</label>
                        <select id="year" name="year" required class="form-select">
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="form-label text-xs">Create Password</label>
                    <input id="password" name="password" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <a href="<?php echo (new GitHubAPI())->getAuthUrl(); ?>" class="btn-outline w-full py-4 text-sm font-bold uppercase tracking-widest flex items-center justify-center gap-3 mb-4">
                        <i class="fab fa-github text-xl"></i> Signup with GitHub
                    </a>
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest">
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
