-- IdeaSync Database Schema
-- Core tables for campus collaboration platform

-- 1. USERS TABLE (All students)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    branch VARCHAR(50) NOT NULL,  -- CSE, ECE, MBA, etc.
    year INT NOT NULL,  -- 1, 2, 3, 4
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    github_username VARCHAR(100),
    user_type ENUM('visionary', 'builder') DEFAULT 'builder',
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. IDEAS TABLE (Projects/Ideas posted)
CREATE TABLE ideas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    domain VARCHAR(100),  -- AI/ML, Web Dev, Cybersecurity, etc.
    skills_needed JSON,  -- Store as JSON array: ["Python", "React"]
    status ENUM('open', 'in_progress', 'completed', 'abandoned') DEFAULT 'open',
    github_repo_url VARCHAR(255),
    github_commits INT DEFAULT 0,
    upvotes INT DEFAULT 0,
    applicant_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. APPLICATIONS TABLE (Who want to collaborate on ideas)
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    user_id INT NOT NULL,  -- Builder applying
    status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
    message TEXT,  -- Why they want to join
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (idea_id, user_id)
);

-- 4. COLLABORATIONS TABLE (Accepted teams)
CREATE TABLE collaborations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    leader_id INT NOT NULL,  -- Original idea poster
    collaborator_id INT NOT NULL,
    role VARCHAR(100),  -- Developer, Designer, etc.
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (leader_id) REFERENCES users(id),
    FOREIGN KEY (collaborator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. GITHUB_PROFILES TABLE (Cache GitHub data)
CREATE TABLE github_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    github_id VARCHAR(100),
    public_repos INT,
    followers INT,
    following INT,
    primary_language VARCHAR(50),
    bio TEXT,
    last_fetched TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. GITHUB_REPOS TABLE (Top 3 repos per user)
CREATE TABLE github_repos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    repo_name VARCHAR(255) NOT NULL,
    repo_url VARCHAR(500) NOT NULL,
    language VARCHAR(50),
    stars INT DEFAULT 0,
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. BUILDER_RANK TABLE (Gamification)
CREATE TABLE builder_rank (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    rank ENUM('INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND') DEFAULT 'INITIATE',
    points INT DEFAULT 0,
    ideas_posted INT DEFAULT 0,
    ideas_completed INT DEFAULT 0,
    collaborations INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. UPVOTES TABLE (Community signal)
CREATE TABLE upvotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idea_id INT NOT NULL,
    user_id INT NOT NULL,
    upvoted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_upvote (idea_id, user_id)
);

-- 9. NOTIFICATIONS TABLE
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_user_id INT NOT NULL,
    actor_user_id INT,  -- Who triggered the notification
    notification_type ENUM('application', 'acceptance', 'rejection', 'upvote', 'message') NOT NULL,
    related_idea_id INT,
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (related_idea_id) REFERENCES ideas(id) ON DELETE CASCADE
);

-- 10. ADMIN_ACTIONS TABLE (For IIC admin dashboard)
CREATE TABLE admin_actions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_user_id INT NOT NULL,
    action_type ENUM('feature_idea', 'remove_idea', 'flag_user', 'verify_skills') NOT NULL,
    target_idea_id INT,
    target_user_id INT,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES users(id),
    FOREIGN KEY (target_idea_id) REFERENCES ideas(id) ON DELETE SET NULL,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 11. MESSAGES TABLE (Direct messaging between users)
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_user_id INT NOT NULL,
    recipient_user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation (sender_user_id, recipient_user_id),
    INDEX idx_recipient (recipient_user_id),
    INDEX idx_unread (recipient_user_id, is_read)
);

-- 12. FILE_UPLOADS TABLE (Track uploaded files for ideas/collaborations)
CREATE TABLE file_uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uploader_user_id INT NOT NULL,
    idea_id INT,
    collaboration_id INT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,  -- in bytes
    file_type VARCHAR(50),  -- 'image', 'document', 'code', etc.
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (uploader_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
    FOREIGN KEY (collaboration_id) REFERENCES collaborations(id) ON DELETE CASCADE,
    INDEX idx_idea_uploads (idea_id),
    INDEX idx_uploader (uploader_user_id),
    INDEX idx_uploaded_date (uploaded_at)
);

-- INDEXES for performance
CREATE INDEX idx_user_branch ON users(branch);
CREATE INDEX idx_idea_domain ON ideas(domain);
CREATE INDEX idx_idea_status ON ideas(status);
CREATE INDEX idx_idea_creator ON ideas(user_id);
CREATE INDEX idx_application_idea ON applications(idea_id);
CREATE INDEX idx_application_status ON applications(status);
CREATE INDEX idx_collaboration_idea ON collaborations(idea_id);
CREATE INDEX idx_notification_user ON notifications(recipient_user_id);
CREATE INDEX idx_notification_read ON notifications(is_read);
CREATE INDEX idx_message_sender ON messages(sender_user_id);
CREATE INDEX idx_message_recipient ON messages(recipient_user_id);
CREATE INDEX idx_file_uploads_idea ON file_uploads(idea_id);
CREATE INDEX idx_file_uploads_user ON file_uploads(uploader_user_id);
