<?php
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    redirect(BASE_URL . '/?page=login');
}
ob_start();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Admin Control Panel</h1>
            <p class="text-slate-500 font-medium">Monitor and manage the IdeaSync ecosystem.</p>
        </div>
        <div class="flex gap-4">
            <a href="<?php echo BASE_URL; ?>/?page=admin-users" class="btn-outline !text-xs !py-2">Manage Users</a>
            <a href="<?php echo BASE_URL; ?>/?page=admin-reports" class="btn-outline !text-xs !py-2">Moderation Queue</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="premium-card p-6 bg-white">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Users</p>
            <h3 class="text-3xl font-black text-primary mt-2" id="stat-total-users">--</h3>
        </div>
        <div class="premium-card p-6 bg-white">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Ideas</p>
            <h3 class="text-3xl font-black text-green-500 mt-2" id="stat-total-ideas">--</h3>
        </div>
        <div class="premium-card p-6 bg-white">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pending Reports</p>
            <h3 class="text-3xl font-black text-secondary mt-2" id="stat-pending-reports">--</h3>
        </div>
        <div class="premium-card p-6 bg-white">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Security Alerts</p>
            <h3 class="text-3xl font-black text-orange-500 mt-2" id="stat-security-alerts">--</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="premium-card bg-white overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Recent Activity</h3>
                <a href="#" class="text-xs font-bold text-primary hover:underline">View All</a>
            </div>
            <div class="p-0" id="recent-activity-list">
                <div class="p-12 text-center opacity-40">Loading activity...</div>
            </div>
        </div>

        <div class="premium-card bg-white overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Content Reports</h3>
                <a href="<?php echo BASE_URL; ?>/?page=admin-reports" class="text-xs font-bold text-primary hover:underline">Manage Queue</a>
            </div>
            <div class="p-0" id="pending-reports-list">
                <div class="p-12 text-center opacity-40">Loading reports...</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/admin.php?action=getDashboardStats');
        const data = await res.json();
        if (data.success) {
            document.getElementById('stat-total-users').textContent = data.user_stats.total_users;
            document.getElementById('stat-pending-reports').textContent = data.report_stats.pending || 0;
            document.getElementById('stat-security-alerts').textContent = data.failed_logins_24h;

            // Populate activity
            const activityList = document.getElementById('recent-activity-list');
            if (data.recent_activity && data.recent_activity.length > 0) {
                activityList.innerHTML = data.recent_activity.map(log => `
                    <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-slate-900">${log.action}</p>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${log.created_at}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-medium">${log.details || ''}</p>
                    </div>
                `).join('');
            } else {
                activityList.innerHTML = '<div class="p-12 text-center opacity-40 text-sm italic">No recent activity</div>';
            }
        }
    } catch (e) {
        console.error('Failed to load admin stats', e);
    }
});
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
