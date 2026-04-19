<?php
ob_start();
?>

<div class="min-h-[calc(100vh-64px)] flex items-center justify-center px-6 py-24 bg-slate-50/50">
    <div class="max-w-xl w-full text-center animate-fade-up">
        <div class="inline-flex items-center justify-center h-24 w-24 rounded-3xl bg-white shadow-premium text-slate-300 text-4xl mb-8">
            <i class="fas fa-unlink"></i>
        </div>
        <h1 class="text-6xl md:text-8xl font-black text-slate-900 tracking-tighter mb-4 italic">404</h1>
        <h2 class="text-2xl font-bold text-slate-700 mb-6 uppercase tracking-widest">Protocol Interrupted</h2>
        <p class="text-slate-500 font-medium mb-12 max-w-sm mx-auto leading-relaxed">
            The innovation track you are looking for has been moved or archived. Please return to the central hub.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?php echo BASE_URL; ?>" class="btn-primary !px-10 !py-4 !w-full sm:w-auto">
                Return Home
            </a>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="btn-outline !px-10 !py-4 !w-full sm:w-auto">
                Explore Tracks
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
