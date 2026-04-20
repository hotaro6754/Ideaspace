<?php
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    redirect(BASE_URL . '/?page=login');
}
ob_start();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Moderation Queue</h1>
        <p class="text-slate-500 font-medium">Review and resolve reported content.</p>
    </div>

    <div class="premium-card bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Reported Content</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Reason</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Reporter</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="reports-table-body">
                    <tr><td colspan="5" class="px-6 py-12 text-center opacity-40 italic">Loading reports...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/admin.php?action=getReports');
    const data = await res.json();
    if (data.success) {
        const body = document.getElementById('reports-table-body');
        if (data.reports.length > 0) {
            body.innerHTML = data.reports.map(report => `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-[9px] font-black uppercase text-slate-500">${report.reported_type}</span>
                            <span class="text-sm font-bold text-slate-900">ID: ${report.reported_id}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-700">${report.reason}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-slate-900">${report.reporter_name || 'System'}</p>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-slate-400">${report.created_at}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="resolveReport(${report.id})" class="text-xs font-bold text-primary hover:underline">Resolve</button>
                    </td>
                </tr>
            `).join('');
        } else {
            body.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center opacity-40 italic">No pending reports</td></tr>';
        }
    }
});

async function resolveReport(reportId) {
    const notes = prompt("Enter resolution notes:");
    if (notes === null) return;

    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/admin.php?action=updateReportStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: \`report_id=\${reportId}&status=resolved&admin_notes=\${encodeURIComponent(notes)}&csrf_token=<?php echo Security::getCsrfToken(); ?>\`
    });

    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.error || 'Failed to resolve report');
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
