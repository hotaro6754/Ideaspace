<?php
/**
 * IdeaSync Database Configuration
 * Handles all database connections and queries
 * Uses environment variables for credentials (see .env.example)
 */

require_once __DIR__ . '/Env.php';

class Database {
    private $host;
    private $db_name;
    private $user;
    private $password;
    private $port;
    private $conn;

    public function __construct() {
        // Load configuration from environment variables with fallbacks
        $this->host = Env::get('DB_HOST', 'localhost');
        $this->db_name = Env::get('DB_NAME', 'ideaspace_db');
        $this->user = Env::get('DB_USER', 'root');
        $this->password = Env::get('DB_PASSWORD', '');
        $this->port = (int)Env::get('DB_PORT', 3306);
    }

    public function connect() {
        $this->conn = null;

        try {
            // Suppress warnings to handle errors gracefully
            $this->conn = @new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->conn->connect_error) {
                // Log error without exposing details to user
                error_log("Database Connection Failed: " . $this->conn->connect_error);
                throw new Exception("Database connection failed");
            }

            // Set charset to utf8mb4 for better emoji/unicode support
            $this->conn->set_charset("utf8mb4");

            // Enable strict mode for better error handling
            $this->conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");

            return $this->conn;
        } catch (Exception $e) {
            // Log error but don't expose technical details
            error_log("Database Error: " . $e->getMessage());

            if (Env::get('APP_DEBUG') === 'true' || Env::get('APP_ENV') === 'development') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please contact administrator.");
            }
        }
    }

    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
        }
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Initialize database connection
$db = new Database();
$conn = $db->connect();
?>
