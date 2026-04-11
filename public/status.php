<?php
/**
 * Status Page for Debugging
 * Shows deployment status and configuration
 * Remove in production
 */

?>
<!DOCTYPE html>
<html>
<head>
    <title>IdeaSpace - Deployment Status</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #2c3e50; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .ok { background: #d4edda; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        code { background: #eee; padding: 2px 4px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 IdeaSpace Deployment Status</h1>

        <div class="status ok">
            <strong>✅ Application Running</strong>
            <p>PHP version: <?php echo phpversion(); ?></p>
        </div>

        <h2>Environment Variables</h2>
        <table>
            <thead>
                <tr>
                    <th>Variable</th>
                    <th>Value</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PORT', 'APP_ENV', 'APP_DEBUG'];
                foreach ($env_vars as $var) {
                    $value = getenv($var);
                    $source = $value ? 'System' : 'Not set';
                    $display = $value ? (strlen($value) > 30 ? substr($value, 0, 27) . '...' : $value) : '❌ NOT SET';
                    echo "<tr>";
                    echo "<td><code>$var</code></td>";
                    echo "<td>$display</td>";
                    echo "<td>$source</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>PHP Extensions</h2>
        <div class="status <?php echo extension_loaded('mysqli') ? 'ok' : 'error'; ?>">
            <strong><?php echo extension_loaded('mysqli') ? '✅' : '❌'; ?> MySQLi</strong>
            <p><?php echo extension_loaded('mysqli') ? 'Installed and ready' : 'NOT installed - deployment will fail'; ?></p>
        </div>

        <div class="status <?php echo extension_loaded('pdo_mysql') ? 'ok' : 'error'; ?>">
            <strong><?php echo extension_loaded('pdo_mysql') ? '✅' : '❌'; ?> PDO MySQL</strong>
            <p><?php echo extension_loaded('pdo_mysql') ? 'Installed' : 'Not installed'; ?></p>
        </div>

        <h2>Database Connection Test</h2>
        <?php
        require_once __DIR__ . '/../src/config/Env.php';

        $db_host = Env::get('DB_HOST', 'localhost');
        $db_user = Env::get('DB_USER', 'root');
        $db_pass = Env::get('DB_PASSWORD', '');
        $db_name = Env::get('DB_NAME', 'ideaspace_db');
        $db_port = (int)Env::get('DB_PORT', 3306);

        try {
            $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
            if ($conn->connect_error) {
                echo '<div class="status error">';
                echo '❌ <strong>Connection Failed</strong>';
                echo '<p>Error: ' . htmlspecialchars($conn->connect_error) . '</p>';
                echo '<p>Check database credentials in Railway settings</p>';
                echo '</div>';
            } else {
                echo '<div class="status ok">';
                echo '✅ <strong>Connected Successfully!</strong>';
                echo '<p>Host: ' . htmlspecialchars($db_host) . ':' . $db_port . '</p>';
                echo '<p>Database: ' . htmlspecialchars($db_name) . '</p>';

                // Check tables
                $result = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '" . $conn->escape_string($db_name) . "'");
                $row = $result->fetch_assoc();
                echo '<p>Tables: ' . $row['count'] . '</p>';

                $conn->close();
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="status error">';
            echo '❌ <strong>Exception</strong>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>

        <h2>Configuration Files</h2>
        <div class="status info">
            <p>
                <strong>.env.production:</strong>
                <?php echo file_exists(__DIR__ . '/../.env.production') ? '✅ Found' : '❌ Not found (OK in production - use Railway variables)'; ?>
            </p>
            <p>
                <strong>Dockerfile:</strong>
                <?php echo file_exists(__DIR__ . '/../Dockerfile') ? '✅ Found' : '❌ Not found'; ?>
            </p>
            <p>
                <strong>railway.toml:</strong>
                <?php echo file_exists(__DIR__ . '/../railway.toml') ? '✅ Found' : '❌ Not found'; ?>
            </p>
        </div>

        <h2>Next Steps</h2>
        <?php if (extension_loaded('mysqli') && getenv('DB_HOST')): ?>
            <div class="status ok">
                <strong>✅ Ready for Production</strong>
                <p>All systems operational. You can remove this status page.</p>
            </div>
        <?php else: ?>
            <div class="status error">
                <strong>⚠️ Action Required</strong>
                <ol>
                    <li>Ensure all environment variables are set in Railway dashboard</li>
                    <li>Verify DB_HOST, DB_USER, DB_PASSWORD, DB_PORT, DB_NAME are configured</li>
                    <li>Redeploy from Railway dashboard</li>
                    <li>Check the logs for any errors</li>
                </ol>
            </div>
        <?php endif; ?>

        <hr style="margin-top: 40px; opacity: 0.5;">
        <p style="color: #666; font-size: 12px;">
            Generated: <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>⚠️ Remove this file from production for security</strong>
        </p>
    </div>
</body>
</html>
