<?php
ob_start();
$conn = getConnection();
$res = $conn->query("SELECT users.name, users.branch, users.roll_number,
                            (SELECT COUNT(*) FROM ideas WHERE user_id = users.id) as ideas_count,
                            (SELECT COUNT(*) FROM collaborations WHERE collaborator_id = users.id) as collab_count
                     FROM users
                     ORDER BY ideas_count DESC, collab_count DESC
                     LIMIT 10");
$leaders = [];
while ($row = $res->fetch_assoc()) $leaders[] = $row;
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-16 text-center animate-fade-up">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">Talent Board</h1>
        <p class="mt-4 text-slate-500 font-medium text-lg">Recognizing the top engineering minds at Lendi Institute.</p>
    </div>

    <div class="premium-card bg-white overflow-hidden animate-fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Rank</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Student</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Department</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Tracks</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Collabs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($leaders as $index => $leader): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-lg font-black <?php echo ($index < 3) ? 'text-primary' : 'text-slate-300'; ?>">
                                #<?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center font-bold text-sm">
                                    <?php echo strtoupper(substr($leader['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?php echo sanitize($leader['name']); ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase"><?php echo sanitize($leader['roll_number']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="badge badge-primary"><?php echo sanitize($leader['branch']); ?></span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-sm font-black text-slate-700"><?php echo $leader['ideas_count']; ?></span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="text-sm font-black text-slate-700"><?php echo $leader['collab_count']; ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
