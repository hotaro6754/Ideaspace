<?php
require_once __DIR__ . '/src/config/Database.php';

try {
    $db = getConnection();
    echo "Seeding LIET Real Data...\n";

    // 1. Initial Admin User
    $admin_pw = password_hash('LendiIIC2026!', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT OR IGNORE INTO users (roll_number, name, email, password, branch, year, user_type, email_verified, is_admin, academic_role, interests)
                         VALUES ('LID000', 'IIC Coordinator', 'iic@lendi.edu.in', ?, 'CSE', 4, 'visionary', 1, 1, 'Faculty', 'Web, AI, Cloud')");
    $stmt->bind_param("s", $admin_pw);
    $stmt->execute();

    // 2. Sample Builders
    $builders = [
        ['LID001', 'Sai Krishna', 'sai@lendi.edu.in', 'Senior', 'AI/ML, Python'],
        ['LID002', 'Priya Reddy', 'priya@lendi.edu.in', 'Alumni', 'Web Dev, Cloud'],
        ['LID003', 'Manoj Kumar', 'manoj@lendi.edu.in', 'Senior', 'Blockchain, Security']
    ];

    foreach ($builders as $b) {
        $pw = password_hash('Builder123!', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT OR IGNORE INTO users (roll_number, name, email, password, branch, year, academic_role, interests, email_verified)
                             VALUES (?, ?, ?, ?, 'CSE', 4, ?, ?, 1)");
        $stmt->bind_param("ssssss", $b[0], $b[1], $b[2], $pw, $b[3], $b[4]);
        $stmt->execute();

        $check = $db->prepare("SELECT id FROM users WHERE roll_number = ?");
        $check->bind_param("s", $b[0]);
        $check->execute();
        $uid = $check->get_result()->fetch_assoc()['id'] ?? 0;

        if($uid > 0) {
            $rankStmt = $db->prepare("INSERT OR IGNORE INTO builder_rank (user_id, rank, points) VALUES (?, 'BUILDER', ?)");
            $pts = rand(100, 2000);
            $rankStmt->bind_param("ii", $uid, $pts);
            $rankStmt->execute();

            // Also update points in users table for leaderboard compatibility
            $upd = $db->prepare("UPDATE users SET points = ? WHERE id = ?");
            $upd->bind_param("ii", $pts, $uid);
            $upd->execute();
        }
    }

    // 3. Innovation Tracks (Ideas)
    $tracks = [
        ['Edge AI Attendance', 'AI/ML', 'Implementing local facial recognition on edge devices.', '["Python", "TensorFlow", "IoT"]'],
        ['Smart Campus Mesh', 'Networking', 'Decentralized communication network for campus-wide IoT.', '["C++", "LoRaWAN", "Go"]'],
        ['Zero Trust Auth', 'Cybersecurity', 'Securing student records with biometric protocols.', '["Cryptography", "React", "Node.js"]']
    ];

    foreach ($tracks as $t) {
        $stmt = $db->prepare("INSERT INTO ideas (user_id, title, domain, description, skills_needed, status, upvotes) VALUES (1, ?, ?, ?, ?, 'open', ?)");
        $uv = rand(10, 50);
        $stmt->bind_param("ssssi", $t[0], $t[1], $t[2], $t[3], $uv);
        $stmt->execute();
    }

    echo "✓ Seeding Complete.\n";

} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
