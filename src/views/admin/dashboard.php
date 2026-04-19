<?php
ob_start();
// Basic Admin Auth check
$user = getCurrentUser();
if (!$user || $user['user_type'] !== 'admin' && !str_contains($user['email'], 'admin')) {
    // For demo, we might allow access or redirect
    // redirect(BASE_URL);
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12 animate-fade-up">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">System Overview</h1>
        <p class="mt-2 text-slate-500 font-medium">Administrative overview of Lendi's IdeaSync Platform.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12 animate-fade-up">
        <div class="premium-card p-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Total Builders</h3>
            <p class="text-4xl font-black text-slate-900">42</p>
        </div>
        <div class="premium-card p-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Problem Statements</h3>
            <p class="text-4xl font-black text-slate-900">12</p>
        </div>
        <div class="premium-card p-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Active Collabs</h3>
            <p class="text-4xl font-black text-slate-900">08</p>
        </div>
    </div>

    <!-- More admin modules would go here -->
    <div class="premium-card p-8 animate-fade-up">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Recent Activity</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-slate-50">
                <span class="text-sm text-slate-600">New user registration: <strong>LID124</strong></span>
                <span class="text-xs text-slate-400">2 mins ago</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-50">
                <span class="text-sm text-slate-600">Problem Statement posted: <strong>Smart Energy</strong></span>
                <span class="text-xs text-slate-400">1 hour ago</span>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
