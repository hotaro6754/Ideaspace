<?php
ob_start();
$user = getCurrentUser();
if (!$user || $user['user_type'] !== 'visionary') redirect(BASE_URL);

$conn = getConnection();
$res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$all_users = [];
while ($row = $res->fetch_assoc()) $all_users[] = $row;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-12 animate-fade-up">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight italic">Registry</h1>
            <p class="text-slate-500 font-medium mt-1">Full student database for the Lendi IIC network.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/?page=admin" class="btn-outline !py-3 !text-xs font-black uppercase tracking-widest">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
    </div>

    <div class="premium-card bg-white animate-fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Student</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Department</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Type</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-medium">
                    <?php foreach($all_users as $u): ?>
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-primary font-bold">
                                    <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-bold"><?php echo sanitize($u['name']); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?php echo sanitize($u['roll_number']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-slate-600 font-bold"><?php echo sanitize($u['branch']); ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-black uppercase tracking-widest <?php echo ($u['user_type'] == 'visionary') ? 'text-secondary' : 'text-slate-400'; ?>">
                                <?php echo $u['user_type']; ?>
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <?php if ($u['is_suspended']): ?>
                                <span class="badge !bg-red-50 !text-red-600 !border-red-100">SUSPENDED</span>
                            <?php else: ?>
                                <span class="badge badge-success">ACTIVE</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6 text-right">
                             <button class="text-slate-300 hover:text-primary transition-colors"><i class="fas fa-ellipsis-v"></i></button>
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
include __DIR__ . '/../../layouts/main.php';
?>
