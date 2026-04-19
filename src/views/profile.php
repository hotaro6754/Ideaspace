<?php
ob_start();
if (!isLoggedIn()) redirect(BASE_URL . '/?page=login');
$user = getCurrentUser();
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
                <?php echo sanitize($user['roll_number']); ?> • <?php echo sanitize($user['branch']); ?> DEPARTMENT • YEAR <?php echo sanitize($user['year']); ?>
            </p>
        </div>
        <div class="flex flex-col gap-3">
            <button class="btn-primary !px-8">Edit Academic Profile</button>
            <button class="btn-outline !px-8">Public Portfolio</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-10 animate-fade-up">
            <section class="premium-card p-8 bg-slate-50/50">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-8">Engineering Stack</h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $skills = ['Python', 'PHP', 'MySQL', 'IoT', 'SolidWorks', 'MATLAB'];
                    foreach($skills as $skill):
                    ?>
                    <span class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-xl shadow-subtle hover:border-primary transition-colors cursor-default"><?php echo $skill; ?></span>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="premium-card p-8">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-8">Digital Presence</h3>
                <div class="space-y-4">
                    <a href="#" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-github text-xl text-slate-900"></i>
                            <span class="text-xs font-bold text-slate-600">GitHub Repository</span>
                        </div>
                        <i class="fas fa-external-link-alt text-[10px] text-slate-300 group-hover:text-primary transition-colors"></i>
                    </a>
                    <a href="#" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-all group">
                        <div class="flex items-center gap-3">
                            <i class="fab fa-linkedin text-xl text-blue-600"></i>
                            <span class="text-xs font-bold text-slate-600">Professional Link</span>
                        </div>
                        <i class="fas fa-external-link-alt text-[10px] text-slate-300 group-hover:text-primary transition-colors"></i>
                    </a>
                </div>
            </section>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-10 animate-fade-up">
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Contribution Timeline</h2>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total 12 Units</span>
                </div>

                <div class="space-y-6">
                    <div class="premium-card p-8 flex items-center justify-between group bg-white">
                        <div class="flex items-center gap-6">
                            <div class="h-14 w-14 rounded-2xl bg-primary/5 flex items-center justify-center text-primary text-xl shadow-inner">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 group-hover:text-primary transition-colors">AI Student Monitoring System</h4>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Lead Builder • Phase: Discovery</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="badge badge-primary">ACTIVE</span>
                            <i class="fas fa-chevron-right text-slate-200 group-hover:text-primary transition-all"></i>
                        </div>
                    </div>

                    <div class="premium-card p-8 flex items-center justify-between group bg-white opacity-60 grayscale hover:opacity-100 hover:grayscale-0 transition-all">
                        <div class="flex items-center gap-6">
                            <div class="h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl shadow-inner">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Smart Attendance Prototype</h4>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Collaborator • Completed March 2024</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="badge badge-success">COMPLETED</span>
                            <i class="fas fa-chevron-right text-slate-200 transition-all"></i>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
