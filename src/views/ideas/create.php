<?php
require_once __DIR__ . '/../../helpers/Security.php';
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12">
        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-[10px] font-black text-slate-500 hover:text-primary transition-colors flex items-center gap-2 mb-6 uppercase tracking-[0.2em]">
            <i class="fas fa-arrow-left"></i> Project Database
        </a>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Initiate <span class="text-primary italic">Idea</span> Build</h1>
        <p class="text-slate-500 mt-2 text-lg font-medium">Define your vision and deploy it to the campus network.</p>
    </div>

    <div class="bg-surface-container-low rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
        <form id="createIdeaForm" action="<?php echo BASE_URL; ?>/src/controllers/ideas.php?action=create" method="POST" class="p-8 md:p-12 space-y-12">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
            <input type="hidden" name="skills_needed" id="skills_needed" value="[]">

            <!-- Basic Info -->
            <div class="space-y-8">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm border border-primary/20">
                        <i class="fas fa-terminal"></i>
                    </div>
                    <h3 class="text-xl font-black text-white tracking-tight uppercase">Core Specifications</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Project Identifier (Title)</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-6 py-4 bg-surface-container-high border border-white/5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-white font-medium transition-all"
                               placeholder="e.g. Neural Campus Hub">
                    </div>

                    <div>
                        <label for="domain" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Primary Domain (Category)</label>
                        <select id="domain" name="domain" required
                                class="w-full px-6 py-4 bg-surface-container-high border border-white/5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-white font-medium transition-all appearance-none">
                            <option value="">Select Domain</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                            <option value="Web3 / Blockchain">Web3 / Blockchain</option>
                            <option value="Internet of Things">Internet of Things</option>
                            <option value="Fintech">Fintech</option>
                            <option value="EdTech">EdTech</option>
                        </select>
                    </div>

                    <div>
                        <label for="difficulty" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Complexity Level</label>
                        <select id="difficulty" name="difficulty" required
                                class="w-full px-6 py-4 bg-surface-container-high border border-white/5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-white font-medium transition-all appearance-none">
                            <option value="beginner">V1 Prototype</option>
                            <option value="intermediate">Production MVP</option>
                            <option value="advanced">Scalable Asset</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-8">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm border border-primary/20">
                        <i class="fas fa-file-lines"></i>
                    </div>
                    <h3 class="text-xl font-black text-white tracking-tight uppercase">Vision Protocol</h3>
                </div>

                <div>
                    <label for="description" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Briefing (Description)</label>
                    <textarea id="description" name="description" rows="6" required
                              class="w-full px-6 py-4 bg-surface-container-high border border-white/5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-white font-medium transition-all leading-relaxed"
                              placeholder="Describe the problem, the solution, and the stack..."></textarea>
                </div>
            </div>

            <!-- Collaboration -->
            <div class="space-y-8">
                 <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm border border-primary/20">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3 class="text-xl font-black text-white tracking-tight uppercase">Resource Requirements</h3>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="skillSelector">
                    <?php
                    $skills = ['Frontend', 'Backend', 'UI/UX', 'Cloud', 'Data Science', 'Security', 'IoT', 'Marketing'];
                    foreach($skills as $skill): ?>
                    <button type="button" data-skill="<?php echo $skill; ?>" class="skill-btn px-4 py-3 bg-surface-container-high border border-white/5 rounded-xl text-[10px] font-black text-slate-400 uppercase tracking-widest hover:border-primary/50 transition-all">
                        <?php echo $skill; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8">
                 <div class="flex items-center gap-3 text-primary bg-primary/10 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] border border-primary/20">
                    <i class="fas fa-shield-check"></i>
                    Verified Build Sequence
                 </div>
                 <div class="flex items-center gap-4 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto btn-primary !px-12 !py-4 uppercase tracking-[0.2em] text-xs font-black shadow-2xl shadow-primary/20">
                        Authorize Build
                    </button>
                 </div>
            </div>
        </form>
    </div>
</div>

<script>
    const selectedSkills = new Set();
    const skillsInput = document.getElementById('skills_needed');
    const skillBtns = document.querySelectorAll('.skill-btn');

    skillBtns.forEach(btn => {
        btn.onclick = () => {
            const skill = btn.getAttribute('data-skill');
            if (selectedSkills.has(skill)) {
                selectedSkills.delete(skill);
                btn.classList.remove('bg-primary', 'text-background', 'border-primary');
                btn.classList.add('bg-surface-container-high', 'text-slate-400', 'border-white/5');
            } else {
                selectedSkills.add(skill);
                btn.classList.add('bg-primary', 'text-background', 'border-primary');
                btn.classList.remove('bg-surface-container-high', 'text-slate-400', 'border-white/5');
            }
            skillsInput.value = JSON.stringify(Array.from(selectedSkills));
        };
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
