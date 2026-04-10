<?php
/**
 * Database Migration Script
 * Run this script once to set up all required database tables and schema changes
 * Usage: php migrate.php
 *
 * This script is safe to run multiple times - all migrations use IF NOT EXISTS clauses
 */

require_once __DIR__ . '/src/config/Database.php';

class DatabaseMigration {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();

        if (!$this->conn) {
            die("Database connection failed\n");
        }
    }

    /**
     * Run all migrations
     */
    public function run() {
        echo "Starting database migrations...\n";
        echo str_repeat("=", 50) . "\n\n";

        try {
            $this->migrateAuthenticationTables();
            echo "✓ Authentication tables migrated\n\n";

            $this->migrateCollaborationTables();
            echo "✓ Collaboration tables migrated\n\n";

            $this->migrateDiscussionTables();
            echo "✓ Discussion tables migrated\n\n";

            $this->migrateEventsTables();
            echo "✓ Events tables migrated\n\n";

            $this->migrateUserEnhancements();
            echo "✓ User table enhancements applied\n\n";

            $this->migrateIdeasTableEnhancements();
            echo "✓ Ideas table enhancements applied\n\n";

            $this->migrateIndexes();
            echo "✓ Performance indexes created\n\n";

            echo str_repeat("=", 50) . "\n";
            echo "Database migration completed successfully!\n";
            echo "All tables and columns are now ready for production.\n";

        } catch (Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Migrate authentication tables
     */
    private function migrateAuthenticationTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS email_verifications (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL UNIQUE,
                token VARCHAR(255) UNIQUE NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_token (token),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS password_resets (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                token VARCHAR(255) UNIQUE NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_token (token),
                INDEX idx_user (user_id),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS user_preferences (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL UNIQUE,
                email_notifications BOOLEAN DEFAULT TRUE,
                email_on_application BOOLEAN DEFAULT TRUE,
                email_on_acceptance BOOLEAN DEFAULT TRUE,
                email_on_message BOOLEAN DEFAULT TRUE,
                email_on_upvote BOOLEAN DEFAULT FALSE,
                email_on_comment BOOLEAN DEFAULT TRUE,
                profile_visibility ENUM('public', 'private') DEFAULT 'public',
                ideas_visibility ENUM('public', 'private') DEFAULT 'public',
                theme ENUM('light', 'dark') DEFAULT 'light',
                language VARCHAR(10) DEFAULT 'en',
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS auth_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT,
                action ENUM('login_attempt', 'login_success', 'login_failure', 'logout', 'password_reset', 'email_verified', 'failed_verification', 'register', 'user_suspended', 'user_unsuspended', 'user_deactivated', 'user_reactivated', 'password_reset_requested') NOT NULL,
                ip_address VARCHAR(45),
                user_agent VARCHAR(500),
                details JSON,
                success BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user (user_id),
                INDEX idx_action (action),
                INDEX idx_created (created_at),
                INDEX idx_ip (ip_address)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                entity_type ENUM('idea', 'application', 'collaboration', 'message', 'comment', 'upvote', 'profile', 'event') NOT NULL,
                entity_id INT,
                action ENUM('create', 'update', 'delete', 'view') NOT NULL,
                details JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user (user_id),
                INDEX idx_entity (entity_type, entity_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS rate_limits (
                id INT PRIMARY KEY AUTO_INCREMENT,
                identifier VARCHAR(255) NOT NULL,
                action VARCHAR(50) NOT NULL,
                attempt_count INT DEFAULT 1,
                first_attempt_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at TIMESTAMP,
                UNIQUE KEY unique_identifier_action (identifier, action),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->executeMultipleStatements($sql);
    }

    /**
     * Migrate collaboration tables (channels)
     */
    private function migrateCollaborationTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS channels (
                id INT PRIMARY KEY AUTO_INCREMENT,
                collaboration_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                type ENUM('general', 'announcements', 'random', 'resources', 'custom') DEFAULT 'custom',
                is_archived BOOLEAN DEFAULT FALSE,
                created_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (collaboration_id) REFERENCES collaborations(id) ON DELETE CASCADE,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_collaboration (collaboration_id),
                UNIQUE KEY unique_channel (collaboration_id, name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS channel_messages (
                id INT PRIMARY KEY AUTO_INCREMENT,
                channel_id INT NOT NULL,
                sender_id INT NOT NULL,
                content TEXT NOT NULL,
                attachments JSON,
                is_pinned BOOLEAN DEFAULT FALSE,
                is_edited BOOLEAN DEFAULT FALSE,
                edited_at TIMESTAMP NULL,
                is_deleted BOOLEAN DEFAULT FALSE,
                deleted_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE,
                FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_channel (channel_id),
                INDEX idx_sender (sender_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS channel_members (
                id INT PRIMARY KEY AUTO_INCREMENT,
                channel_id INT NOT NULL,
                user_id INT NOT NULL,
                last_read_message_id INT,
                joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                muted BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_member (channel_id, user_id),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS channel_message_reactions (
                id INT PRIMARY KEY AUTO_INCREMENT,
                message_id INT NOT NULL,
                user_id INT NOT NULL,
                emoji VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (message_id) REFERENCES channel_messages(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_reaction (message_id, user_id, emoji),
                INDEX idx_message (message_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->executeMultipleStatements($sql);
    }

    /**
     * Migrate discussion tables (comments)
     */
    private function migrateDiscussionTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS idea_comments (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idea_id INT NOT NULL,
                user_id INT NOT NULL,
                parent_comment_id INT,
                content TEXT NOT NULL,
                attachments JSON,
                likes_count INT DEFAULT 0,
                is_edited BOOLEAN DEFAULT FALSE,
                edited_at TIMESTAMP NULL,
                is_deleted BOOLEAN DEFAULT FALSE,
                deleted_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (parent_comment_id) REFERENCES idea_comments(id) ON DELETE CASCADE,
                INDEX idx_idea (idea_id),
                INDEX idx_user (user_id),
                INDEX idx_parent (parent_comment_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS comment_likes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                comment_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (comment_id) REFERENCES idea_comments(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_like (comment_id, user_id),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS content_reports (
                id INT PRIMARY KEY AUTO_INCREMENT,
                reporter_id INT NOT NULL,
                reported_type ENUM('idea', 'comment', 'message', 'user') NOT NULL,
                reported_id INT NOT NULL,
                reason ENUM('spam', 'inappropriate', 'offensive', 'plagiarism', 'other') NOT NULL,
                description TEXT,
                status ENUM('pending', 'under_review', 'resolved', 'dismissed') DEFAULT 'pending',
                admin_notes TEXT,
                resolved_by INT,
                resolved_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_status (status),
                INDEX idx_created (created_at),
                INDEX idx_type (reported_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->executeMultipleStatements($sql);
    }

    /**
     * Migrate events tables
     */
    private function migrateEventsTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS events (
                id INT PRIMARY KEY AUTO_INCREMENT,
                creator_id INT NOT NULL,
                collaboration_id INT,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                start_time TIMESTAMP NOT NULL,
                end_time TIMESTAMP NOT NULL,
                location VARCHAR(255),
                event_type ENUM('presentation', 'standup', 'meeting', 'workshop', 'brainstorm', 'other') DEFAULT 'meeting',
                is_virtual BOOLEAN DEFAULT TRUE,
                max_attendees INT,
                is_cancelled BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (collaboration_id) REFERENCES collaborations(id) ON DELETE SET NULL,
                INDEX idx_creator (creator_id),
                INDEX idx_collaboration (collaboration_id),
                INDEX idx_start_time (start_time)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS event_rsvps (
                id INT PRIMARY KEY AUTO_INCREMENT,
                event_id INT NOT NULL,
                user_id INT NOT NULL,
                status ENUM('attending', 'maybe', 'not_attending') DEFAULT 'attending',
                responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_rsvp (event_id, user_id),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->executeMultipleStatements($sql);
    }

    /**
     * Enhance users table with new columns
     */
    private function migrateUserEnhancements() {
        // Add columns if they don't exist
        $columns_to_add = [
            ['email_verified', 'BOOLEAN DEFAULT FALSE'],
            ['email_verified_at', 'TIMESTAMP NULL'],
            ['bio', 'TEXT NULL'],
            ['last_login', 'TIMESTAMP NULL'],
            ['last_activity', 'TIMESTAMP NULL'],
            ['is_active', 'BOOLEAN DEFAULT TRUE'],
            ['is_suspended', 'BOOLEAN DEFAULT FALSE'],
            ['suspended_until', 'TIMESTAMP NULL'],
            ['suspension_reason', 'TEXT NULL']
        ];

        foreach ($columns_to_add as $column) {
            $column_name = $column[0];
            $column_def = $column[1];

            $check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                           WHERE TABLE_NAME='users' AND COLUMN_NAME='$column_name'";
            $result = $this->conn->query($check_query);

            if ($result === false || $result->num_rows === 0) {
                $alter_query = "ALTER TABLE users ADD COLUMN $column_name $column_def";
                if (!$this->conn->query($alter_query)) {
                    throw new Exception("Failed to add column $column_name: " . $this->conn->error);
                }
            }
        }

        // Also rename password_hash to password if it exists
        $check_password = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                          WHERE TABLE_NAME='users' AND COLUMN_NAME='password_hash'";
        $result = $this->conn->query($check_password);

        if ($result && $result->num_rows > 0) {
            // Check if 'password' already exists
            $check_password_new = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                                  WHERE TABLE_NAME='users' AND COLUMN_NAME='password'";
            $result2 = $this->conn->query($check_password_new);

            if (!$result2 || $result2->num_rows === 0) {
                // Rename password_hash to password
                $rename_query = "ALTER TABLE users CHANGE COLUMN password_hash password VARCHAR(255) NOT NULL";
                if (!$this->conn->query($rename_query)) {
                    throw new Exception("Failed to rename password_hash to password: " . $this->conn->error);
                }
            }
        }

        // Create indexes
        $indexes = [
            'idx_email_verified' => 'email_verified',
            'idx_is_active' => 'is_active',
            'idx_last_login' => 'last_login'
        ];

        foreach ($indexes as $index_name => $column_name) {
            $check_index = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
                           WHERE TABLE_NAME='users' AND INDEX_NAME='$index_name'";
            $result = $this->conn->query($check_index);

            if (!$result || $result->num_rows === 0) {
                $create_index = "CREATE INDEX $index_name ON users($column_name)";
                if (!$this->conn->query($create_index)) {
                    throw new Exception("Failed to create index $index_name: " . $this->conn->error);
                }
            }
        }
    }

    /**
     * Enhance ideas table with new columns
     */
    private function migrateIdeasTableEnhancements() {
        $columns_to_add = [
            ['comment_count', 'INT DEFAULT 0'],
            ['total_collaborators', 'INT DEFAULT 0']
        ];

        foreach ($columns_to_add as $column) {
            $column_name = $column[0];
            $column_def = $column[1];

            $check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                           WHERE TABLE_NAME='ideas' AND COLUMN_NAME='$column_name'";
            $result = $this->conn->query($check_query);

            if ($result === false || $result->num_rows === 0) {
                $alter_query = "ALTER TABLE ideas ADD COLUMN $column_name $column_def";
                if (!$this->conn->query($alter_query)) {
                    throw new Exception("Failed to add column $column_name: " . $this->conn->error);
                }
            }
        }

        // Add column to notifications table
        $check_read_at = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                         WHERE TABLE_NAME='notifications' AND COLUMN_NAME='read_at'";
        $result = $this->conn->query($check_read_at);

        if ($result === false || $result->num_rows === 0) {
            $alter_query = "ALTER TABLE notifications ADD COLUMN read_at TIMESTAMP NULL";
            if (!$this->conn->query($alter_query)) {
                throw new Exception("Failed to add read_at to notifications: " . $this->conn->error);
            }
        }
    }

    /**
     * Create performance indexes
     */
    private function migrateIndexes() {
        $indexes = [
            ['channel_messages', 'idx_channel_messages_channel', 'channel_id'],
            ['channel_messages', 'idx_channel_messages_sender', 'sender_id'],
            ['channel_messages', 'idx_channel_messages_created', 'created_at'],
            ['idea_comments', 'idx_idea_comments_idea', 'idea_id'],
            ['idea_comments', 'idx_idea_comments_user', 'user_id'],
            ['idea_comments', 'idx_idea_comments_created', 'created_at'],
            ['auth_logs', 'idx_auth_logs_user', 'user_id'],
            ['auth_logs', 'idx_auth_logs_created', 'created_at'],
            ['activity_logs', 'idx_activity_logs_user', 'user_id'],
            ['activity_logs', 'idx_activity_logs_created', 'created_at']
        ];

        foreach ($indexes as $index_spec) {
            $table = $index_spec[0];
            $index_name = $index_spec[1];
            $column = $index_spec[2];

            $check_index = "SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
                           WHERE TABLE_NAME='$table' AND INDEX_NAME='$index_name'";
            $result = $this->conn->query($check_index);

            if (!$result || $result->num_rows === 0) {
                $create_index = "CREATE INDEX $index_name ON $table ($column)";
                if (!$this->conn->query($create_index)) {
                    throw new Exception("Failed to create index $index_name on $table: " . $this->conn->error);
                }
            }
        }
    }

    /**
     * Execute multiple SQL statements
     */
    private function executeMultipleStatements($sql) {
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (empty($statement)) {
                continue;
            }

            if (!$this->conn->query($statement)) {
                throw new Exception("SQL Error: " . $this->conn->error . "\nStatement: " . substr($statement, 0, 100));
            }
        }
    }
}

// Run migrations
$migration = new DatabaseMigration();
$migration->run();
?>
