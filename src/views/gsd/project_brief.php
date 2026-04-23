<?php
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
$idea_id = (int)($_GET['id'] ?? 0);
$db = getConnection();

$stmt = $db->prepare("SELECT i.*, pb.detailed_requirements, pb.technical_stack, pb.risk_assessment
                      FROM ideas i
                      LEFT JOIN project_briefs pb ON i.id = pb.idea_id
                      WHERE i.id = ?");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$idea = $stmt->get_result()->fetch_assoc();

if (!$idea) redirect(BASE_URL . '/?page=ideas');

ob_start();
?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea_id; ?>" class="text-[10px] font-black uppercase text-slate-400 hover:text-primary">Track Details</a></li>
                    <li><i class="fas fa-chevron-right text-[8px] text-slate-300"></i></li>
                    <li><span class="text-[10px] font-black uppercase text-slate-900">Project Brief</span></li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Project Brief: <span class="text-primary"><?php echo sanitize($idea['title']); ?></span></h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-10">
            <div class="premium-card p-10 bg-white shadow-premium">
                <form id="brief-form" class="space-y-10">
                    <input type="hidden" name="idea_id" value="<?php echo $idea_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <i class="fas fa-list-check text-primary"></i> Detailed Requirements
                        </h3>
                        <textarea name="requirements" class="form-input h-64 text-sm leading-relaxed" placeholder="List all functional and non-functional requirements..."><?php echo sanitize($idea['detailed_requirements'] ?? ''); ?></textarea>
                    </section>

                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <i class="fas fa-layer-group text-primary"></i> Technical Stack
                        </h3>
                        <textarea name="stack" class="form-input h-32 text-sm leading-relaxed" placeholder="Frontend, Backend, Database, Cloud Providers..."><?php echo sanitize($idea['technical_stack'] ?? ''); ?></textarea>
                    </section>

                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <i class="fas fa-triangle-exclamation text-secondary"></i> Risk Assessment
                        </h3>
                        <textarea name="risks" class="form-input h-32 text-sm leading-relaxed" placeholder="Potential blockers, security risks, or resource limitations..."><?php echo sanitize($idea['risk_assessment'] ?? ''); ?></textarea>
                    </section>

                    <?php if ($_SESSION['user_id'] == $idea['user_id']): ?>
                    <button type="submit" class="btn-primary !w-full !py-4 !rounded-2xl !text-sm !font-black uppercase tracking-widest shadow-xl shadow-primary/20">
                        Update Project Brief
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="space-y-8">
            <div class="premium-card p-8 bg-slate-50/50 border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">GSD Progress</h3>
                <p class="text-xs text-slate-500 leading-relaxed">The Project Brief is the "Spec" for your innovation. It converts the vision into actionable requirements.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('brief-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/gsd.php?action=save_brief', {
        method: 'POST',
        body: new FormData(e.target)
    });
    const data = await res.json();
    if(data.success) alert('Brief updated successfully.');
    else alert(data.error);
});
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
