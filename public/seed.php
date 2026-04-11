<?php
/**
 * IdeaSync - Master Data Seeding Script
 */
require_once __DIR__ . '/../src/config/Database.php';

$conn = getConnection();

// 1. Create Users
$users = [
    ['roll' => '21B21A0501', 'name' => 'Sai Krishna', 'email' => 'sai@lendi.org', 'branch' => 'CSE', 'year' => 3, 'points' => 1250, 'tier' => 5, 'admin' => 0],
    ['roll' => '21B21A0402', 'name' => 'Priya Sharma', 'email' => 'priya@lendi.org', 'branch' => 'ECE', 'year' => 3, 'points' => 450, 'tier' => 3, 'admin' => 0],
    ['roll' => '22B21A0512', 'name' => 'Harshith V.', 'email' => 'harshith@lendi.org', 'branch' => 'CSE', 'year' => 2, 'points' => 820, 'tier' => 4, 'admin' => 0],
    ['roll' => 'ADMIN001', 'name' => 'IIC Admin', 'email' => 'admin@lendi.org', 'branch' => 'IIC', 'year' => 4, 'points' => 0, 'tier' => 1, 'admin' => 1]
];

foreach ($users as $u) {
    $pass = password_hash('test123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT IGNORE INTO users (roll_number, name, email, branch, year, password_hash, total_points, tier, is_iic_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisiii", $u['roll'], $u['name'], $u['email'], $u['branch'], $u['year'], $pass, $u['points'], $u['tier'], $u['admin']);
    $stmt->execute();
}

// 2. Create Ideas
$ideas = [
    [
        'title' => 'Lendi Campus Navigation AI',
        'desc' => 'AR-based navigation for new students to find labs and blocks.',
        'domain' => 'AI/ML',
        'skills' => '["Unity", "ARCore", "Python"]',
        'featured' => 1
    ],
    [
        'title' => 'Smart Library Management',
        'desc' => 'Real-time book tracking using RFID and a mobile app.',
        'domain' => 'IoT',
        'skills' => '["Arduino", "React Native", "Node.js"]',
        'featured' => 0
    ]
];

$user_id = 1; // Sai Krishna
foreach ($ideas as $i) {
    $stmt = $conn->prepare("INSERT INTO ideas (user_id, title, description, domain, skills_needed, is_iic_featured) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $i['title'], $i['desc'], $i['domain'], $i['skills'], $i['featured']);
    $stmt->execute();
}

// 3. Create Events
$events = [
    [
        'title' => 'Next.js 14 Masterclass',
        'desc' => 'Deep dive into App Router and Server Components.',
        'domain' => 'Web Development',
        'format' => 'WORKSHOP',
        'date' => date('Y-m-d H:i:s', strtotime('+7 days'))
    ],
    [
        'title' => 'Building Scalable APIs',
        'desc' => 'Best practices for REST and GraphQL.',
        'domain' => 'Backend',
        'format' => 'SEMINAR',
        'date' => date('Y-m-d H:i:s', strtotime('+3 days'))
    ]
];

foreach ($events as $e) {
    $limit = 50;
    $stmt = $conn->prepare("INSERT INTO forge_events (conductor_id, title, description, domain, format, event_date, seat_limit) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $user_id, $e['title'], $e['desc'], $e['domain'], $e['format'], $e['date'], $limit);
    $stmt->execute();
}

echo "Database seeded successfully!";
?>
