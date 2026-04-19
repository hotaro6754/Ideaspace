<?php
ob_start();
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-primary transition-colors flex items-center gap-2 mb-6">
            <i class="fas fa-arrow-left"></i> Back to Tracks
        </a>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Post Innovation Track</h1>
        <p class="mt-2 text-slate-500 font-medium">Define a new challenge for the Lendi engineering community.</p>
    </div>

    <div class="premium-card p-10 bg-white animate-fade-up">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-xl text-secondary text-sm font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form class="space-y-8" action="<?php echo BASE_URL; ?>/src/controllers/ideas.php?action=create" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

            <div>
                <label for="title" class="form-label">Track Title</label>
                <input id="title" name="title" type="text" required class="form-input" placeholder="e.g. Smart Energy Management System">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="domain" class="form-label">Primary Domain</label>
                    <select id="domain" name="domain" required class="form-select">
                        <option value="AI / ML">AI / ML</option>
                        <option value="IoT">IoT</option>
                        <option value="Cybersecurity">Cybersecurity</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Core Engineering">Core Engineering</option>
                        <option value="Data Analytics">Data Analytics</option>
                    </select>
                </div>
                <div>
                    <label for="skills" class="form-label">Key Skills (Comma separated)</label>
                    <input id="skills_input" name="skills_raw" type="text" class="form-input" placeholder="Python, Arduino, PHP">
                    <!-- Simple JS to split raw to array for controller if needed, or handle in controller -->
                </div>
            </div>

            <div>
                <label for="description" class="form-label">Vision & Requirements</label>
                <textarea id="description" name="description" rows="6" required class="form-textarea" placeholder="Describe the problem, the proposed solution, and how it impacts Lendi campus..."></textarea>
            </div>

            <div class="pt-6 border-t border-slate-50 flex items-center justify-end gap-4">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn-outline">Cancel</a>
                <button type="submit" class="btn-primary px-8">Launch Track</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
