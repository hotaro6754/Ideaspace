<?php
// Test file to verify PHP execution
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Test</title>
</head>
<body>
    <h1>✅ PHP is Working!</h1>
    <p>If you see this page, PHP is executing correctly.</p>
    <hr>
    <h2>System Information:</h2>
    <ul>
        <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
        <li><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
        <li><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></li>
        <li><strong>Current File:</strong> <?php echo __FILE__; ?></li>
    </ul>
    <hr>
    <p><a href="/index.php?page=home">Go to Home Page</a></p>
</body>
</html>
