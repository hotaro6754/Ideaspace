<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/navbar.php';
?>
<main class="min-h-screen">
    <?php echo $content ?? ''; ?>
</main>
<?php
require_once __DIR__ . '/footer.php';
?>
