<?php
ob_start();
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
$user = getCurrentUser();
$interests = json_decode($user['interests'] ?? '[]', true);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Profile Header -->
    <div class="premium-card p-10 mb-10 flex flex-col md:flex-row items-center gap-10 animate-fade-up bg-white">
        <div class="h-32 w-32 rounded-3xl bg-primary flex items-center justify-center text-white text-4xl font-black shadow-premium transform -rotate-3 hover:rotate-0 transition-transform">
            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
        </div>
        <div class="flex-1 text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-[10px] font-black uppercase tracking-widest mb-4">
                <i class="fas fa-award"></i> Verified Lendi Innovator
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-2"><?php echo sanitize($user['name']); ?></h1>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">
                <?php echo sanitize($user['roll_number']); ?> • <?php echo sanitize($user['branch']); ?> DEPARTMENT • <?php echo sanitize($user['academic_role'] ?? 'BUILDER'); ?>
            </p>
        </div>
        <div class="flex flex-col gap-3">
            <a href="<?php echo BASE_URL; ?>/?page=onboarding" class="btn-primary !px-8 text-center">Update Interests</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-10 animate-fade-up">
            <section class="premium-card p-8 bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-8">Technical Interests</h3>
                <div class="flex flex-wrap gap-2">
                    <?php if (empty($interests)): ?>
                        <span class="text-xs text-slate-400 font-medium">No interests selected.</span>
                    <?php else: ?>
                        <?php foreach($interests as $skill): ?>
                        <span class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl shadow-subtle hover:border-primary transition-colors cursor-default"><?php echo sanitize($skill); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-10 animate-fade-up">
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Your Problem Statements</h2>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas&action=create" class="text-xs font-bold text-primary uppercase tracking-widest">Post New +</a>
                </div>

                <div class="space-y-6">
                    <?php
                    $db = getConnection();
                    $stmt = $db->prepare("SELECT * FROM ideas WHERE user_id = ? ORDER BY created_at DESC");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $my_ideas = $stmt->get_result();
                    if ($my_ideas->num_rows === 0):
                    ?>
                        <div class="p-12 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <p class="text-slate-400 font-medium">You haven't posted any problem statements yet.</p>
                        </div>
                    <?php else: ?>
                        <?php while($idea = $my_ideas->fetch_assoc()): ?>
                        <div class="premium-card p-8 flex items-center justify-between group bg-white">
                            <div class="flex items-center gap-6">
                                <div class="h-14 w-14 rounded-2xl bg-primary/5 flex items-center justify-center text-primary text-xl shadow-inner">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900 group-hover:text-primary transition-colors"><?php echo sanitize($idea['title']); ?></h4>
                                    <div class="flex items-center gap-3 mt-1">
                                         <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?php echo sanitize($idea['domain']); ?></span>
                                         <span class="badge <?php echo ($idea['status'] === 'completed') ? 'badge-success' : 'badge-primary'; ?> !text-[8px] !py-0.5"><?php echo strtoupper(sanitize($idea['status'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="<?php echo BASE_URL; ?>/?page=idea-manage&id=<?php echo $idea['id']; ?>" class="btn-outline !py-2 !px-4 !text-[10px] font-black uppercase tracking-widest">
                                    Manage
                                </a>
                                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-primary group-hover:text-white transition-all">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
