<?php
header("Content-Type: text/html; charset=utf-8", true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Test</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .success { color: green; font-size: 24px; }
        .test { margin: 10px 0; padding: 10px; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1 class="success">✅ PHP IS EXECUTING CORRECTLY!</h1>

    <div class="test">
        <strong>PHP Version:</strong> <?php echo phpversion(); ?>
    </div>

    <div class="test">
        <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
    </div>

    <div class="test">
        <strong>Current URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?>
    </div>

    <div class="test">
        <strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <hr>

    <p><a href="/index.php?page=home">← Go to Home</a></p>

</body>
</html>
