<?php
require_once __DIR__ . '/src/config/Database.php';

try {
    $conn = getConnection();
    $pdo = (fn() => $this->pdo)->call($conn);

    echo "Seeding LIET Real Data...\n";

    // 1. Initial Admin User
    $admin_pw = password_hash('LendiIIC2026!', PASSWORD_BCRYPT);
    $pdo->exec("INSERT OR IGNORE INTO users (roll_number, name, email, password, branch, year, user_type, email_verified)
               VALUES ('LIET000', 'IIC Coordinator', 'iic@lendi.edu.in', '$admin_pw', 'ADMIN', 4, 'visionary', 1)");

    // 2. Real Innovation Tracks (Ideas)
    $tracks = [
        [
            'title' => 'AI Student Monitoring System',
            'domain' => 'AI/ML',
            'desc' => 'AI-integrated 360° dashboards for real-time behavioral and academic tracking. Using MySQL and AI-Analytics to improve campus student success rates.',
            'skills' => '["Python", "MySQL", "TensorFlow", "React"]'
        ],
        [
            'title' => 'Campus Time Table Scheduler',
            'domain' => 'Core Algorithms',
            'desc' => 'Deep-logic engines designed to eliminate campus resource conflicts automatically. Optimizing room utilization for CSE and ECE blocks.',
            'skills' => '["PHP", "Algorithms", "Optimization", "MySQL"]'
        ],
        [
            'title' => 'Geo-fenced Attendance System',
            'domain' => 'Cybersecurity',
            'desc' => 'Secure tracking with dual-factor device authentication protocols. Ensuring integrity of attendance records using GPS and MAC filtering.',
            'skills' => '["Android", "PHP", "Security", "Networking"]'
        ],
        [
            'title' => 'IdeaSync Collaboration Portal',
            'domain' => 'Web Ecosystem',
            'desc' => 'The central hub for Lendi IIC to manage all student innovations and industry collaborations.',
            'skills' => '["Tailwind CSS", "PHP", "MVC", "SQLite"]'
        ]
    ];

    foreach ($tracks as $t) {
        $stmt = $pdo->prepare("INSERT INTO ideas (user_id, title, domain, description, skills_needed, status) VALUES (1, ?, ?, ?, ?, 'open')");
        $stmt->execute([$t['title'], $t['domain'], $t['desc'], $t['skills']]);
    }

    // 3. Real Events
    $pdo->exec("INSERT INTO events (creator_id, title, description, start_time, end_time, location, event_type)
               VALUES (1, 'Talent Hunt Day', 'The annual Lendi innovation scouting event for top engineers.', '2026-04-22 09:00:00', '2026-04-22 17:00:00', 'Main Auditorium', 'workshop')");

    $pdo->exec("INSERT INTO events (creator_id, title, description, start_time, end_time, location, event_type)
               VALUES (1, 'IIC Innovation Workshop', 'A deep dive into product design and prototyping for current tracks.', '2026-05-15 14:00:00', '2026-05-15 16:30:00', 'AI Lab 4', 'workshop')");

    echo "✓ Seeding Complete. 1 Admin, 4 Tracks, 2 Events added.\n";

} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
