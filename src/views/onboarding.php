<?php
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
$user = getCurrentUser();
ob_start();
?>
<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-6 bg-slate-50/50">
    <div class="max-w-2xl w-full space-y-8 animate-fade-up">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Complete Your Profile</h2>
            <p class="mt-4 text-slate-500 font-medium">Help us personalize your IdeaSync experience.</p>
        </div>

        <div class="premium-card p-10 bg-white">
            <form class="space-y-8" action="<?php echo BASE_URL; ?>/src/controllers/onboarding.php?action=complete" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                <div>
                    <label class="form-label">Academic Role</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-100 cursor-pointer hover:bg-slate-50 transition-all peer-checked:border-primary">
                            <input type="radio" name="academic_role" value="builder" required class="absolute top-4 right-4 text-primary focus:ring-primary">
                            <i class="fas fa-code text-primary text-xl mb-3"></i>
                            <span class="text-sm font-bold text-slate-900">Builder</span>
                            <span class="text-[10px] text-slate-500 font-medium mt-1">I want to contribute technical skills to existing ideas.</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-100 cursor-pointer hover:bg-slate-50 transition-all">
                            <input type="radio" name="academic_role" value="visionary" required class="absolute top-4 right-4 text-primary focus:ring-primary">
                            <i class="fas fa-lightbulb text-primary text-xl mb-3"></i>
                            <span class="text-sm font-bold text-slate-900">Visionary</span>
                            <span class="text-[10px] text-slate-500 font-medium mt-1">I have ideas and need a team to help build them.</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Your Interests (Select Domains)</label>
                    <div class="flex flex-wrap gap-3">
                        <?php
                        $domains = ['AI/ML', 'Web Dev', 'Cybersecurity', 'IoT', 'Blockchain', 'Cloud', 'Data Science', 'Mobile App', 'Robotics', 'Embedded Systems'];
                        foreach($domains as $d): ?>
                            <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer hover:border-primary transition-colors">
                                <input type="checkbox" name="interests[]" value="<?php echo $d; ?>" class="rounded text-primary focus:ring-primary">
                                <span class="text-xs font-bold text-slate-700"><?php echo $d; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-primary w-full py-4 text-base font-bold uppercase tracking-widest">
                        Enter Workspace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
