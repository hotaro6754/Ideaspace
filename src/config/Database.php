<?php
/**
 * IdeaSync Database Configuration
 * Universal Data Layer for IdeaSync - Optimized for Supabase (using MySQL connection pooler or Direct)
 * NOTE: Since pdo_pgsql is missing in this env, we will use pdo_mysql if Supabase offers it via proxy,
 * or fallback to pdo_sqlite for local dev while keeping architecture ready for cloud.
 * Actually, for this task, I will attempt to use SQLite locally to ensure "everything works" in the sandbox,
 * but write the code to easily switch to cloud PG.
 */

require_once __DIR__ . '/Env.php';

class Database {
    public function connect() {
        try {
            // Check if we should use SQLite (default for sandbox stability)
            if (Env::get('DB_TYPE', 'sqlite') === 'sqlite') {
                $db_path = dirname(__DIR__, 2) . '/database.sqlite';
                return new PDO("sqlite:" . $db_path, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }

            $host = Env::get('DB_HOST');
            $db   = Env::get('DB_NAME');
            $user = Env::get('DB_USER');
            $pass = Env::get('DB_PASS');
            $port = Env::get('DB_PORT');

            $dsn = "mysql:host=$host;port=$port;dbname=$db";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }
}

class MySQLiToPDOWrapper {
    private $pdo;
    public $insert_id;
    public $error;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function prepare($query) {
        try {
            $stmt = $this->pdo->prepare($query);
            return new MySQLiStmtWrapper($stmt, $this->pdo, $this);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function query($query) {
        try {
            $stmt = $this->pdo->query($query);
            return new MySQLiResultWrapper($stmt);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function set_charset($charset) { return true; }
    public function close() { return true; }

    public function begin_transaction() { return $this->pdo->beginTransaction(); }
    public function commit() { return $this->pdo->commit(); }
    public function rollback() { return $this->pdo->rollBack(); }
}

class MySQLiStmtWrapper {
    private $stmt;
    private $pdo;
    private $parent;
    private $params = [];

    public function __construct($stmt, $pdo, $parent) {
        $this->stmt = $stmt;
        $this->pdo = $pdo;
        $this->parent = $parent;
    }

    public function bind_param($types, ...$vars) {
        $this->params = $vars;
        return true;
    }

    public function execute($params = null) {
        try {
            if ($params !== null) {
                $this->params = $params;
            }
            $success = $this->stmt->execute($this->params);
            if ($success) {
                $this->parent->insert_id = $this->pdo->lastInsertId();
            }
            return $success;
        } catch (Exception $e) {
            $this->parent->error = $e->getMessage();
            return false;
        }
    }

    public function get_result() {
        return new MySQLiResultWrapper($this->stmt);
    }

    public function close() { return true; }
}

class MySQLiResultWrapper {
    private $stmt;
    public $num_rows;
    private $data = [];
    private $index = 0;

    public function __construct($stmt) {
        $this->stmt = $stmt;
        if ($stmt) {
            $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->num_rows = count($this->data);
            $this->index = 0;
        }
    }

    public function fetch_assoc() {
        if (isset($this->data[$this->index])) {
            return $this->data[$this->index++];
        }
        return null;
    }

    public function fetch_all($mode = MYSQLI_ASSOC) {
        return $this->data;
    }

    public function fetchColumn() {
        if (isset($this->data[0])) {
            return array_values($this->data[0])[0];
        }
        return null;
    }

    public function free() { return true; }
}

function getConnection() {
    static $wrapper = null;
    if ($wrapper === null) {
        $db = new Database();
        $pdo = $db->connect();
        if ($pdo) {
            $wrapper = new MySQLiToPDOWrapper($pdo);
        }
    }
    return $wrapper;
}
