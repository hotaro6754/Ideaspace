<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-xl w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-primary text-white shadow-premium mb-6">
                <i class="fas fa-user-plus text-xl"></i>
            </div>
            <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Join the Innovation Hub</h2>
            <p class="mt-4 text-slate-500 font-medium">
                Already part of Lendi IIC?
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

            <form class="grid grid-cols-1 md:grid-cols-2 gap-6" action="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=register" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                <input type="hidden" name="action" value="register">

                <div class="md:col-span-2">
                    <label for="name" class="form-label text-xs">Full Name (As per College Records)</label>
                    <input id="name" name="name" type="text" required class="form-input" placeholder="Aryan Sharma">
                </div>

                <div class="md:col-span-1">
                    <label for="roll_number" class="form-label text-xs">College Roll Number</label>
                    <input id="roll_number" name="roll_number" type="text" required class="form-input" placeholder="LID001">
                </div>

                <div class="md:col-span-1">
                    <label for="email" class="form-label text-xs">Academic Email</label>
                    <input id="email" name="email" type="email" required class="form-input" placeholder="aryan@lendi.edu.in">
                </div>

                <div class="md:col-span-1">
                    <label for="branch" class="form-label text-xs">Department / Branch</label>
                    <select id="branch" name="branch" required class="form-select">
                        <option value="">Select Dept</option>
                        <option value="CSE">CSE</option>
                        <option value="ECE">ECE</option>
                        <option value="EEE">EEE</option>
                        <option value="ME">Mechanical</option>
                        <option value="CIVIL">Civil</option>
                        <option value="CSIT">CSIT</option>
                        <option value="MBA">MBA</option>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label for="year" class="form-label text-xs">Academic Year</label>
                    <select id="year" name="year" required class="form-select">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label for="password" class="form-label text-xs">Secure Password</label>
                    <input id="password" name="password" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div class="md:col-span-1">
                    <label for="password_confirm" class="form-label text-xs">Confirm Password</label>
                    <input id="password_confirm" name="password_confirm" type="password" required class="form-input" placeholder="••••••••">
                </div>

                <div class="md:col-span-2 pt-2">
                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox" required class="mt-1 h-5 w-5 bg-slate-50 border-slate-200 rounded-lg text-primary focus:ring-primary/20">
                        <span class="ml-3 block text-xs text-slate-500 font-medium leading-relaxed group-hover:text-slate-700 transition-colors">
                            I agree to the Lendi IIC <a href="#" class="text-primary font-bold hover:underline">Innovation Protocol</a> and certify that I am a registered student.
                        </span>
                    </label>
                </div>

                <div class="md:col-span-2 pt-4">
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
