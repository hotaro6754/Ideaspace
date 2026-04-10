<?php
/**
 * IdeaSync Database Configuration
 * Handles all database connections and queries
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'ideaSync_db';
    private $user = 'root';
    private $password = '';
    private $port = 3306;
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->conn->connect_error) {
                throw new Exception("Connection Error: " . $this->conn->connect_error);
            }

            // Set charset to utf8mb4 for better emoji/unicode support
            $this->conn->set_charset("utf8mb4");

            return $this->conn;
        } catch (Exception $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            die("Database connection failed. Please contact administrator.");
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
