<?php
/**
 * IdeaSync - Database Setup Script
 * Run this file in a browser: http://localhost:8000/setup.php
 */

define('BASE_URL', 'http://localhost:8000');
define('ASSETS_URL', BASE_URL . '/assets');

require_once __DIR__ . '/../src/config/Database.php';

$setup_complete = false;
$errors = [];
$success_messages = [];

try {
    $db = new Database();
    $conn = $db->connect();

    // Create demo users
    $demo_users = [
        [
            'roll_number' => 'LID001',
            'name' => 'Visionary User',
            'email' => 'visionary@example.com',
            'password_hash' => password_hash('demo123456', PASSWORD_BCRYPT),
            'branch' => 'CSE',
            'year' => 3,
            'user_type' => 'visionary'
        ],
        [
            'roll_number' => 'LID002',
            'name' => 'Builder User',
            'email' => 'builder@example.com',
            'password_hash' => password_hash('demo123456', PASSWORD_BCRYPT),
            'branch' => 'CSE',
            'year' => 2,
            'user_type' => 'builder'
        ]
    ];

    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        $errors[] = "Users table doesn't exist. Please run DATABASE_SCHEMA.sql first.";
    } else {
        $success_messages[] = "✓ Users table exists";

        // Insert demo users
        foreach ($demo_users as $user) {
            $query = "INSERT IGNORE INTO users (roll_number, name, email, password_hash, branch, year, user_type)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ssssisi",
                    $user['roll_number'],
                    $user['name'],
                    $user['email'],
                    $user['password_hash'],
                    $user['branch'],
                    $user['year'],
                    $user['user_type']
                );
                if ($stmt->execute()) {
                    $success_messages[] = "✓ Demo user created: {$user['name']} ({$user['roll_number']})";
                }
            }
        }

        $setup_complete = true;
    }

} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IdeaSync Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #111827;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .status {
            margin-bottom: 2rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .message.error {
            background: #fecaca;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .next-steps {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .next-steps h2 {
            color: #1e40af;
            font-size: 1.125rem;
            margin-bottom: 1rem;
        }

        .next-steps ol {
            color: #1e40af;
            margin-left: 1.25rem;
        }

        .next-steps li {
            margin-bottom: 0.5rem;
        }

        code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            color: #111827;
        }

        .button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-align: center;
            margin-top: 2rem;
        }

        .button:hover {
            opacity: 0.9;
        }

        .credentials {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #92400e;
        }

        .credentials p {
            margin-bottom: 0.5rem;
        }

        strong {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 IdeaSync Setup</h1>

        <div class="status">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="message error">
                        <span>❌</span>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($success_messages)): ?>
                <?php foreach ($success_messages as $msg): ?>
                    <div class="message success">
                        <span>✓</span>
                        <span><?php echo htmlspecialchars($msg); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (empty($errors)): ?>
            <div class="next-steps">
                <h2>✅ Setup Complete!</h2>
                <p style="margin-bottom: 1rem;">Your IdeaSync platform is ready to use. Here's what to do next:</p>

                <ol>
                    <li>Open your browser and go to <code><?php echo BASE_URL; ?></code></li>
                    <li>Sign in with these demo credentials:</li>
                </ol>

                <div class="credentials">
                    <p><strong>Visionary Account:</strong><br />
                       Roll: <code>LID001</code><br />
                       Password: <code>demo123456</code></p>
                    <p><strong>Builder Account:</strong><br />
                       Roll: <code>LID002</code><br />
                       Password: <code>demo123456</code></p>
                </div>

                <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                    <strong>Note:</strong> Change these credentials on your profile after first login.
                </p>

                <a href="<?php echo BASE_URL; ?>/?page=home" class="button">Go to IdeaSync →</a>
            </div>
        <?php else: ?>
            <div class="next-steps" style="background: #fee2e2; border-color: #fca5a5;">
                <h2>⚠️ Database Setup Required</h2>
                <p>Please follow these steps to set up your database:</p>
                <ol>
                    <li>Open your database management tool (phpMyAdmin, MySQL Workbench, etc.)</li>
                    <li>Create a new database named <code>ideaSync_db</code></li>
                    <li>Run the SQL from <code>DATABASE_SCHEMA.sql</code> to create all tables</li>
                    <li>Refresh this page to verify the setup</li>
                </ol>
                <p style="margin-top: 1rem; font-size: 0.9rem;">
                    <strong>Database Info:</strong><br />
                    Host: <code>localhost</code><br />
                    User: <code>root</code><br />
                    Database: <code>ideaSync_db</code>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
