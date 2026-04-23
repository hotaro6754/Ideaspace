<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) redirect(BASE_URL . '/?page=login');
?>

<div class="min-h-screen py-20 px-6 bg-slate-50 flex items-center justify-center">
    <div class="max-w-3xl w-full">
        <div class="text-center mb-12 animate-fade-up">
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter mb-4">Welcome to IdeaSync</h1>
            <p class="text-slate-500 font-medium">Let's personalize your experience to match your expertise and interests.</p>
        </div>

        <form action="<?php echo BASE_URL; ?>/src/controllers/onboarding.php?action=complete" method="POST" class="space-y-10 animate-fade-up" style="animation-delay: 0.1s">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

            <!-- Role Selection -->
            <div class="space-y-6">
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-3">
                    <span class="h-8 w-8 rounded-lg bg-primary text-white flex items-center justify-center text-sm font-black">1</span>
                    Choose Your Path
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative group cursor-pointer">
                        <input type="radio" name="academic_role" value="Student" class="peer sr-only" required>
                        <div class="p-6 md:p-8 rounded-[2rem] bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full shadow-subtle group-hover:border-slate-200">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-6 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Student</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2 leading-relaxed">Looking for ideas and projects to build.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="academic_role" value="Senior" class="peer sr-only">
                        <div class="p-6 md:p-8 rounded-[2rem] bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full shadow-subtle group-hover:border-slate-200">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-6 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-medal"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Senior</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2 leading-relaxed">Mentoring and sharing complex problem statements.</p>
                        </div>
                    </label>

                    <label class="relative group cursor-pointer">
                        <input type="radio" name="academic_role" value="Alumni" class="peer sr-only">
                        <div class="p-6 md:p-8 rounded-[2rem] bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all h-full shadow-subtle group-hover:border-slate-200">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform mb-6 peer-checked:bg-primary peer-checked:text-white">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Alumni</h3>
                            <p class="text-xs text-slate-400 font-medium mt-2 leading-relaxed">Providing industry-level ideas and guidance.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Interest Selection -->
            <div class="space-y-6">
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-3">
                    <span class="h-8 w-8 rounded-lg bg-primary text-white flex items-center justify-center text-sm font-black">2</span>
                    Select Technical Expertise
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
                        <div class="flex items-center gap-3 p-5 rounded-2xl bg-white border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all shadow-subtle">
                            <i class="fas <?php echo $icon; ?> text-slate-400 peer-checked:text-primary"></i>
                            <span class="text-sm font-bold text-slate-600 peer-checked:text-primary"><?php echo $name; ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-10">
                <button type="submit" class="btn-primary w-full py-5 text-sm font-black uppercase tracking-[0.2em] shadow-premium !rounded-2xl">
                    Finalize Deployment
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
