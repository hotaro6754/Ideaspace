<?php
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    redirect(BASE_URL . '/?page=login');
}
ob_start();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">User Management</h1>
            <p class="text-slate-500 font-medium">Suspend or verify users across the platform.</p>
        </div>
    </div>

    <div class="premium-card bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Department</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Joined</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <tr><td colspan="5" class="px-6 py-12 text-center opacity-40 italic">Loading users...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/admin.php?action=getUsers');
    const data = await res.json();
    if (data.success) {
        const body = document.getElementById('users-table-body');
        body.innerHTML = data.users.map(user => `
            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                            ${user.name.charAt(0)}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">${user.name}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">${user.roll_number}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-slate-600">${user.branch}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase ${user.is_suspended ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'}">
                        ${user.is_suspended ? 'Suspended' : 'Active'}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs font-medium text-slate-400">${user.created_at}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick="toggleSuspend(${user.id}, ${user.is_suspended})" class="text-xs font-bold ${user.is_suspended ? 'text-green-600' : 'text-red-600'} hover:underline">
                        ${user.is_suspended ? 'Unsuspend' : 'Suspend'}
                    </button>
                </td>
            </tr>
        `).join('');
    }
});

async function toggleSuspend(userId, currentlySuspended) {
    if (!confirm(\`Are you sure you want to \${currentlySuspended ? 'unsuspend' : 'suspend'} this user?\`)) return;

    const action = currentlySuspended ? 'unsuspendUser' : 'suspendUser';
    const res = await fetch(\`<?php echo BASE_URL; ?>/src/controllers/admin.php?action=\${action}\`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: \`user_id=\${userId}&csrf_token=<?php echo Security::getCsrfToken(); ?>&reason=Administrative action\`
    });

    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.error || 'Failed to update user status');
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
