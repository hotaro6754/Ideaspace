<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

$conn = getConnection();
// Projects I am collaborating on (Joined)
$res = $conn->prepare("SELECT collaborations.*, ideas.title, ideas.domain, users.name as leader_name,
                        (SELECT id FROM channels WHERE collaboration_id = collaborations.id LIMIT 1) as channel_id
                        FROM collaborations
                        JOIN ideas ON collaborations.idea_id = ideas.id
                        JOIN users ON collaborations.leader_id = users.id
                        WHERE (collaborations.collaborator_id = ? OR collaborations.leader_id = ?) AND collaborations.status = 'active'");
$res->bind_param("ii", $user['id'], $user['id']);
$res->execute();
$collabs = [];
$collab_rows = $res->get_result();
while ($row = $collab_rows->fetch_assoc()) $collabs[] = $row;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Active Collaborations</h1>
        <p class="mt-2 text-slate-500 font-medium">Innovation tracks where you are an active contributor.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-up">
        <?php foreach($collabs as $c): ?>
        <div class="premium-card p-8 bg-white border-l-4 border-l-green-500">
            <div class="flex items-center justify-between mb-6">
                <span class="badge badge-primary"><?php echo sanitize($c['domain']); ?></span>
                <span class="text-[10px] font-black text-green-600 uppercase tracking-widest"><?php echo ($c['leader_id'] == $user['id']) ? 'Lead' : 'Active Partner'; ?></span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo sanitize($c['title']); ?></h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 italic">Lead: <?php echo sanitize($c['leader_name']); ?></p>

            <div class="pt-6 border-t border-slate-50 flex gap-3">
                <?php if ($c['channel_id']): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=channel&id=<?php echo $c['channel_id']; ?>" class="btn-primary !w-full !py-3 !text-[10px] uppercase tracking-widest">Channel</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/?page=messages&to=<?php echo ($c['leader_id'] == $user['id'] ? $c['collaborator_id'] : $c['leader_id']); ?>" class="btn-primary !w-full !py-3 !text-[10px] uppercase tracking-widest">Team Chat</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $c['idea_id']; ?>" class="btn-outline !py-3 !text-[10px] uppercase tracking-widest">Project File</a>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($collabs)): ?>
            <div class="md:col-span-3 text-center py-20 opacity-30">
                <i class="fas fa-handshake-slash text-4xl mb-4 text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-900 uppercase tracking-widest">No active collabs</h3>
                <p class="text-sm font-medium text-slate-500 mt-2">Join a track to see it here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
