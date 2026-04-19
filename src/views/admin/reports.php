<?php
ob_start();
$user = getCurrentUser();
if (!$user || $user['user_type'] !== 'visionary') redirect(BASE_URL);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-12 animate-fade-up">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight italic">Content Shield</h1>
            <p class="text-slate-500 font-medium mt-1">Review flagged innovation tracks and comments.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/?page=admin" class="btn-outline !py-3 !text-xs font-black uppercase tracking-widest">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
    </div>

    <div class="premium-card bg-white p-20 flex flex-col items-center justify-center text-center opacity-40 animate-fade-up">
        <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center text-green-500 text-3xl mb-6 shadow-inner">
            <i class="fas fa-shield-check"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-widest">All Clear</h3>
        <p class="text-sm font-medium text-slate-500 mt-2 max-w-sm">There are currently no reports in the moderation queue. Lendi students are adhering to the Innovation Protocol.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
