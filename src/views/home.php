<?php
/**
 * IdeaSync - Master Landing Page
 */
require_once __DIR__ . '/../config/Database.php';
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IdeaSync | Lendi Innovation Platform</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">

    <header class="navbar">
        <div class="container flex justify-between items-center">
            <a href="/" class="navbar-brand">IDEASYNC</a>
            <div class="flex gap-4">
                <?php if ($current_user): ?>
                    <a href="/?page=feed" class="btn btn-primary">Go to Feed</a>
                <?php else: ?>
                    <a href="/?page=login" class="btn btn-secondary">Log in</a>
                    <a href="/?page=register" class="btn btn-primary">Join Lendi</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section py-20 text-center">
            <div class="container">
                <div class="mb-4">
                    <span class="text-muted text-xs uppercase tracking-widest border border-border px-3 py-1 rounded-full">
                        Lendi Campus Platform • Since 2025
                    </span>
                </div>
                <h1 class="font-bold text-5xl mb-6 tight-tracking">
                    Your idea deserves a builder.<br>Your skills deserve a vision.
                </h1>
                <p class="text-secondary text-lg mb-8 max-w-2xl mx-auto">
                    IdeaSync is Lendi's campus operating system — where ideas find builders, projects get completed, and nothing is lost to WhatsApp.
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="/?page=ideas&action=create" class="btn btn-primary px-8 py-3 rounded-lg">I have an idea</a>
                    <a href="/?page=ideas" class="btn btn-secondary px-8 py-3 rounded-lg">I have skills</a>
                </div>
            </div>
        </section>

        <section class="py-10">
            <div class="container grid grid-3">
                <div class="card p-5">
                    <i data-lucide="layers" class="text-accent mb-4"></i>
                    <h3 class="mb-2">IdeaBoard</h3>
                    <p class="text-secondary text-sm">Ideas meet verified builders. No more dead-end projects.</p>
                </div>
                <div class="card p-5">
                    <i data-lucide="calendar" class="text-accent mb-4"></i>
                    <h3 class="mb-2">Forge</h3>
                    <p class="text-secondary text-sm">Workshops run by top builders. Level up your stack.</p>
                </div>
                <div class="card p-5">
                    <i data-lucide="archive" class="text-accent mb-4"></i>
                    <h3 class="mb-2">The Archive</h3>
                    <p class="text-secondary text-sm">Every project, permanent record. Learn from postmortems.</p>
                </div>
            </div>
        </section>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
