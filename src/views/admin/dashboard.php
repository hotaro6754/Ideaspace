<?php
ob_start();
$user = getCurrentUser();
if (!$user || $user['user_type'] !== 'visionary') redirect(BASE_URL);

$conn = getConnection();
$user_count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$idea_count = $conn->query("SELECT COUNT(*) FROM ideas")->fetchColumn();
$collab_count = $conn->query("SELECT COUNT(*) FROM collaborations")->fetchColumn();
$rsvp_count = $conn->query("SELECT COUNT(*) FROM event_rsvps")->fetchColumn();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight italic">IIC Command Center</h1>
        <p class="mt-2 text-slate-500 font-medium">Administrative overview of Lendi's Innovation Cohort.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16 animate-fade-up">
        <div class="premium-card p-10 bg-primary text-white">
            <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2"> <a href="<?php echo BASE_URL; ?>/?page=admin-users" class="hover:underline">Total Students</a></p>
            <h3 class="text-4xl font-black"><?php echo $user_count; ?></h3>
        </div>
        <div class="premium-card p-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Active Tracks</p>
            <h3 class="text-4xl font-black text-slate-900"><?php echo $idea_count; ?></h3>
        </div>
        <div class="premium-card p-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Collaborations</p>
            <h3 class="text-4xl font-black text-slate-900"><?php echo $collab_count; ?></h3>
        </div>
        <div class="premium-card p-10 bg-secondary text-white">
            <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Event RSVPs</p>
            <h3 class="text-4xl font-black"><?php echo $rsvp_count; ?></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-fade-up">
        <section class="premium-card p-8 bg-white">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8 border-b border-slate-50 pb-4">Recent User Registrations</h3>
            <div class="space-y-6">
                <?php
                $latest_users = $conn->query("SELECT name, roll_number, branch FROM users ORDER BY created_at DESC LIMIT 5");
                while ($lu = $latest_users->fetch_assoc()):
                ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-primary font-bold">
                            <?php echo strtoupper(substr($lu['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?php echo sanitize($lu['name']); ?></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo sanitize($lu['roll_number']); ?></p>
                        </div>
                    </div>
                    <span class="badge badge-primary"><?php echo sanitize($lu['branch']); ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="premium-card p-8 bg-white">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8 border-b border-slate-50 pb-4"> <a href="<?php echo BASE_URL; ?>/?page=admin-reports" class="hover:underline">Moderation Queue</a></h3>
            <div class="flex flex-col items-center justify-center py-10 text-center opacity-40">
                <i class="fas fa-check-double text-4xl text-green-500 mb-4"></i>
                <p class="text-sm font-bold text-slate-900">Queue is Clear</p>
                <p class="text-xs font-medium text-slate-500 mt-1">No reported content at this time.</p>
            </div>
        </section>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
