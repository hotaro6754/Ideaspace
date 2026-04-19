<?php
ob_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) redirect(BASE_URL . '/?page=login');
?>

<div class="min-h-screen py-20 px-6 bg-slate-50 flex items-center justify-center">
    <div class="max-w-3xl w-full">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-4">Welcome to IdeaSync</h1>
            <p class="text-slate-500 font-medium">Let's personalize your experience to match your expertise and interests.</p>
        </div>

        <form action="<?php echo BASE_URL; ?>/src/controllers/onboarding.php" method="POST" class="space-y-10">
            <!-- Role Selection -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                    <span class="h-8 w-8 rounded-lg bg-primary text-white flex items-center justify-center text-sm font-black">1</span>
                    Choose Your Role
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role" value="student" class="peer sr-only" required>
                        <div class="p-6 rounded-2xl bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full">
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-4 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Student</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2">Looking for ideas and projects to build.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role" value="senior" class="peer sr-only">
                        <div class="p-6 rounded-2xl bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full">
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-4 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-medal"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Senior</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2">Mentoring and sharing complex problem statements.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="role" value="alumni" class="peer sr-only">
                        <div class="p-6 rounded-2xl bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full">
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-4 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Alumni</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2">Providing industry-level ideas and guidance.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Interest Selection -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                    <span class="h-8 w-8 rounded-lg bg-primary text-white flex items-center justify-center text-sm font-black">2</span>
                    Select Interests
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    $interests = [
                        'Web Dev' => 'fa-code',
                        'AI/ML' => 'fa-brain',
                        'Cloud' => 'fa-cloud',
                        'Mobile' => 'fa-mobile-alt',
                        'Cyber' => 'fa-shield-alt',
                        'IoT' => 'fa-microchip',
                        'Blockchain' => 'fa-link',
                        'Design' => 'fa-paint-brush'
                    ];
                    foreach($interests as $name => $icon): ?>
                    <label class="relative cursor-pointer">
                        <input type="checkbox" name="interests[]" value="<?php echo $name; ?>" class="peer sr-only">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                            <i class="fas <?php echo $icon; ?> text-slate-400 peer-checked:text-primary"></i>
                            <span class="text-sm font-bold text-slate-600 peer-checked:text-primary"><?php echo $name; ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest shadow-premium">
                    Complete My Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
