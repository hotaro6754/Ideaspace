<?php
require_once __DIR__ . '/src/config/Database.php';

try {
    $conn = getConnection();
    if (!$conn) die("DB Connection failed\n");
    $pdo = (fn() => $this->pdo)->call($conn); // Access private pdo for schema creation

    echo "Syncing SQLite Schema...\n";

    $sql = [
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            roll_number TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            branch TEXT NOT NULL,
            year INTEGER NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            github_username TEXT,
            user_type TEXT CHECK(user_type IN ('visionary', 'builder')) DEFAULT 'builder',
            profile_pic TEXT,
            email_verified INTEGER DEFAULT 0,
            email_verified_at TEXT,
            bio TEXT,
            last_login TEXT,
            last_activity TEXT,
            is_active INTEGER DEFAULT 1,
            is_suspended INTEGER DEFAULT 0,
            suspended_until TEXT,
            suspension_reason TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS ideas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            domain TEXT,
            skills_needed TEXT,
            status TEXT CHECK(status IN ('open', 'in_progress', 'completed', 'abandoned')) DEFAULT 'open',
            github_repo_url TEXT,
            github_commits INTEGER DEFAULT 0,
            upvotes INTEGER DEFAULT 0,
            applicant_count INTEGER DEFAULT 0,
            comment_count INTEGER DEFAULT 0,
            total_collaborators INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            idea_id INTEGER NOT NULL REFERENCES ideas(id) ON DELETE CASCADE,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            status TEXT CHECK(status IN ('pending', 'accepted', 'rejected', 'withdrawn')) DEFAULT 'pending',
            message TEXT,
            applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            responded_at DATETIME,
            UNIQUE(idea_id, user_id)
        )",
        "CREATE TABLE IF NOT EXISTS collaborations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            idea_id INTEGER NOT NULL REFERENCES ideas(id) ON DELETE CASCADE,
            leader_id INTEGER NOT NULL REFERENCES users(id),
            collaborator_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            role TEXT,
            joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            left_at DATETIME,
            status TEXT CHECK(status IN ('active', 'inactive')) DEFAULT 'active'
        )",
        "CREATE TABLE IF NOT EXISTS builder_rank (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
            rank TEXT CHECK(rank IN ('INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND')) DEFAULT 'INITIATE',
            points INTEGER DEFAULT 0,
            ideas_posted INTEGER DEFAULT 0,
            ideas_completed INTEGER DEFAULT 0,
            collaborations INTEGER DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sender_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            recipient_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            read_at TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS channels (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            idea_id INTEGER NOT NULL REFERENCES ideas(id) ON DELETE CASCADE,
            name TEXT NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS channel_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            channel_id INTEGER NOT NULL REFERENCES channels(id) ON DELETE CASCADE,
            sender_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            recipient_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            actor_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
            notification_type TEXT NOT NULL,
            related_idea_id INTEGER REFERENCES ideas(id) ON DELETE CASCADE,
            message TEXT,
            is_read INTEGER DEFAULT 0,
            read_at TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            creator_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            collaboration_id INTEGER REFERENCES collaborations(id) ON DELETE SET NULL,
            title TEXT NOT NULL,
            description TEXT,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            location TEXT,
            event_type TEXT DEFAULT 'meeting',
            is_virtual INTEGER DEFAULT 1,
            max_attendees INTEGER,
            is_cancelled INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS event_rsvps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_id INTEGER NOT NULL REFERENCES events(id) ON DELETE CASCADE,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            status TEXT DEFAULT 'attending',
            responded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(event_id, user_id)
        )"
    ];

    foreach ($sql as $stmt) {
        $pdo->exec($stmt);
    }
    echo "✓ SQLite Schema Ready\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
