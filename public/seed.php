<?php
/**
 * IdeaSync - Demo Data Seeding Script
 * Populates the database with sample ideas and data
 */

define('BASE_URL', 'http://localhost:8000');
define('ASSETS_URL', BASE_URL . '/assets');

require_once __DIR__ . '/../src/config/Database.php';

$db = new Database();
$conn = $db->connect();

$demo_ideas = [
    [
        'user_id' => 1,
        'title' => 'AI Chatbot for Campus Support',
        'description' => 'Create an intelligent chatbot that can answer student questions about courses, faculty, club activities, and campus facilities. The chatbot should use NLP to understand natural language queries and provide accurate, helpful responses. We\'re looking for developers with experience in Python, machine learning, and web development to build both the backend AI engine and a clean web interface.',
        'domain' => 'AI/ML',
        'skills_needed' => '["Python", "NLP", "React", "Node.js"]',
        'status' => 'open'
    ],
    [
        'user_id' => 2,
        'title' => 'Campus Marketplace Platform',
        'description' => 'Build a peer-to-peer marketplace platform where students can buy, sell, and exchange textbooks, course materials, and other campus essentials. The platform should include user profiles, product listings, messaging system, and secure payment integration. This project requires full-stack developers familiar with modern web technologies.',
        'domain' => 'Web Development',
        'skills_needed' => '["React", "Node.js", "MongoDB", "Stripe"]',
        'status' => 'open'
    ],
    [
        'user_id' => 1,
        'title' => 'Network Security Vulnerability Scanner',
        'description' => 'Develop a comprehensive network security scanning tool that can identify vulnerabilities in campus networks. The tool should perform port scanning, service detection, and provide detailed vulnerability reports. We need individuals with strong networking knowledge and cybersecurity expertise.',
        'domain' => 'Cybersecurity',
        'skills_needed' => '["Python", "Networking", "Security"]',
        'status' => 'in_progress'
    ],
    [
        'user_id' => 2,
        'title' => 'Mobile Attendance System',
        'description' => 'Create a mobile application for automated attendance tracking using face recognition and QR codes. The system should help professors take attendance easily and students can view their attendance records. Requires expertise in mobile app development, computer vision, and backend APIs.',
        'domain' => 'Mobile Development',
        'skills_needed' => '["React Native", "Python", "OpenCV", "Firebase"]',
        'status' => 'open'
    ],
    [
        'user_id' => 1,
        'title' => 'Data Analytics Dashboard for Campus Insights',
        'description' => 'Design and develop an analytics dashboard that visualizes campus data including student performance trends, course popularity, campus event attendance, and resource utilization. The dashboard should provide actionable insights to administration and students.',
        'domain' => 'Data Science',
        'skills_needed' => '["Python", "Tableau", "SQL", "JavaScript"]',
        'status' => 'open'
    ]
];

try {
    // Check if ideas already exist to avoid duplicates
    $result = $conn->query("SELECT COUNT(*) as count FROM ideas");
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $ideas_exist = true;
    } else {
        $ideas_exist = false;

        // Insert demo ideas
        foreach ($demo_ideas as $idea) {
            $query = "INSERT INTO ideas (user_id, title, description, domain, skills_needed, status)
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssss",
                $idea['user_id'],
                $idea['title'],
                $idea['description'],
                $idea['domain'],
                $idea['skills_needed'],
                $idea['status']
            );
            $stmt->execute();
        }
    }
} catch (Exception $e) {
    $ideas_exist = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IdeaSync Seed Data</title>
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

        .message.info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
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

        code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✨ Demo Data</h1>

        <div class="status">
            <?php if ($ideas_exist): ?>
                <div class="message info">
                    <span>ℹ️</span>
                    <span>Demo ideas are already in the database</span>
                </div>
            <?php else: ?>
                <div class="message success">
                    <span>✓</span>
                    <span>5 demo ideas have been added successfully!</span>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; color: #111827; margin-bottom: 0.75rem;">Demo Ideas Added:</h2>
            <ul style="margin-left: 1.25rem; color: #6b7280; line-height: 1.8;">
                <li>🤖 AI Chatbot for Campus Support</li>
                <li>🛍️ Campus Marketplace Platform</li>
                <li>🔒 Network Security Vulnerability Scanner</li>
                <li>📱 Mobile Attendance System</li>
                <li>📊 Data Analytics Dashboard for Campus Insights</li>
            </ul>
        </div>

        <p style="color: #6b7280; margin-bottom: 1rem;">
            These demo ideas showcase different domains and are ready for students to apply and collaborate on.
        </p>

        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="button">View Ideas →</a>
    </div>
</body>
</html>
