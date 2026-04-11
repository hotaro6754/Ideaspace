<?php
// Quick database connection test
$host = '127.0.0.1';  // Use IP instead of localhost
$user = 'ideaspace_user';
$password = 'IdeaSpace@Local2024';
$database = 'ideaspace_dev';
$port = 3306;

echo "Testing Database Connection...\n";
echo "Host: $host:$port\n";
echo "User: $user\n";
echo "Database: $database\n\n";

// Try to connect
$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    echo "❌ Connection Failed: " . $conn->connect_error . "\n";
    exit(1);
}

echo "✓ Connection Successful!\n\n";

// Check tables
$query = "SHOW TABLES;";
$result = $conn->query($query);

echo "Tables in database:\n";
while ($row = $result->fetch_array()) {
    echo "  - " . $row[0] . "\n";
}

// Check data
echo "\nUsers in database:\n";
$query = "SELECT id, name, email, user_type FROM users;";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo "  [ID: {$row['id']}] {$row['name']} ({$row['email']}) - {$row['user_type']}\n";
}

echo "\nIdeas in database:\n";
$query = "SELECT id, title, domain, status FROM ideas;";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo "  [ID: {$row['id']}] {$row['title']} - {$row['domain']} ({$row['status']})\n";
}

$conn->close();
echo "\n✓ All tests passed!\n";
?>
