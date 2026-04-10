<?php
/**
 * Search Controller
 * Handles advanced search across ideas and users
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/SearchQuery.php';
require_once __DIR__ . '/../models/Idea.php';
require_once __DIR__ . '/../models/User.php';

class SearchController {
    private $searchQuery;
    private $idea;
    private $user;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->searchQuery = new SearchQuery($db);
        $this->idea = new Idea($db);
        $this->user = new User($db);
    }

    /**
     * Search ideas
     */
    public function searchIdeas($query, $filters = [], $limit = 20, $offset = 0) {
        if (empty($query)) {
            return [];
        }

        // Log search query
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            $this->searchQuery->logSearch($user_id, $query, 'idea');
        }

        // Build search filters
        $searchFilters = [
            'search' => $query
        ];

        if (!empty($filters['domain'])) {
            $searchFilters['domain'] = $filters['domain'];
        }

        if (!empty($filters['status'])) {
            $searchFilters['status'] = $filters['status'];
        }

        if (!empty($filters['created_after'])) {
            $searchFilters['created_after'] = $filters['created_after'];
        }

        return $this->idea->getAll($limit, $offset, $searchFilters);
    }

    /**
     * Search users
     */
    public function searchUsers($query, $filters = [], $limit = 20, $offset = 0) {
        if (empty($query)) {
            return [];
        }

        // Log search query
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            $this->searchQuery->logSearch($user_id, $query, 'user');
        }

        // Search users by name, roll number, branch
        $query_param = '%' . $query . '%';
        $sql = "SELECT id, name, roll_number, email, branch, year, profile_pic, bio
                FROM users
                WHERE name LIKE ? OR roll_number LIKE ? OR email LIKE ?";

        if (!empty($filters['branch'])) {
            $branch = $filters['branch'];
            $sql .= " AND branch = ?";
        }

        if (!empty($filters['year'])) {
            $year = (int)$filters['year'];
            $sql .= " AND year = ?";
        }

        $sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        // Build parameter types and values
        $types = "sss";
        $params = [$query_param, $query_param, $query_param];

        if (!empty($filters['branch'])) {
            $types .= "s";
            $params[] = $filters['branch'];
        }

        if (!empty($filters['year'])) {
            $types .= "i";
            $params[] = $filters['year'];
        }

        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions($query, $type = 'all') {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $suggestions = [];
        $query_param = $query . '%';

        if ($type === 'all' || $type === 'ideas') {
            // Get idea titles
            $sql = "SELECT DISTINCT title FROM ideas WHERE title LIKE ? LIMIT 5";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $query_param);
            $stmt->execute();
            $result = $stmt->get_result();
            $titles = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($titles as $title) {
                $suggestions[] = ['text' => $title['title'], 'type' => 'idea'];
            }
        }

        if ($type === 'all' || $type === 'users') {
            // Get user names
            $sql = "SELECT DISTINCT name FROM users WHERE name LIKE ? LIMIT 5";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $query_param);
            $stmt->execute();
            $result = $stmt->get_result();
            $names = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($names as $name) {
                $suggestions[] = ['text' => $name['name'], 'type' => 'user'];
            }
        }

        return $suggestions;
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10) {
        return $this->searchQuery->getPopularSearches($limit);
    }

    /**
     * Get user's search history
     */
    public function getUserSearchHistory($user_id, $limit = 20) {
        if (!$user_id) {
            return [];
        }
        return $this->searchQuery->getUserHistory($user_id, $limit);
    }

    /**
     * Clear user's search history
     */
    public function clearSearchHistory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return ['success' => false, 'error' => 'User not authenticated'];
        }

        return $this->searchQuery->deleteUserHistory($user_id);
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$searchCtrl = new SearchController($conn);

// Route to appropriate method
if ($action === 'search') {
    $query = trim($_GET['q'] ?? $_POST['q'] ?? '');
    $type = $_GET['type'] ?? 'all'; // all, ideas, users
    $page = (int)($_GET['page'] ?? 1);
    $limit = 20;
    $offset = ($page - 1) * $limit;

    if (empty($query)) {
        echo json_encode(['success' => false, 'error' => 'Search query is required']);
        exit();
    }

    if (strlen($query) < 2) {
        echo json_encode(['success' => false, 'error' => 'Search query must be at least 2 characters']);
        exit();
    }

    $results = [];

    if ($type === 'all' || $type === 'ideas') {
        $results['ideas'] = $searchCtrl->searchIdeas($query, [], $limit, $offset);
    }

    if ($type === 'all' || $type === 'users') {
        $results['users'] = $searchCtrl->searchUsers($query, [], $limit, $offset);
    }

    echo json_encode(['success' => true, 'query' => $query, 'results' => $results]);
    exit();
} elseif ($action === 'suggestions') {
    $query = trim($_GET['q'] ?? '');
    $type = $_GET['type'] ?? 'all';
    $suggestions = $searchCtrl->getSuggestions($query, $type);
    echo json_encode(['success' => true, 'suggestions' => $suggestions]);
    exit();
} elseif ($action === 'popular') {
    $limit = (int)($_GET['limit'] ?? 10);
    $searches = $searchCtrl->getPopularSearches($limit);
    echo json_encode(['success' => true, 'popular' => $searches]);
    exit();
} elseif ($action === 'history') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $limit = (int)($_GET['limit'] ?? 20);
    $history = $searchCtrl->getUserSearchHistory($user_id, $limit);
    echo json_encode(['success' => true, 'history' => $history]);
    exit();
} elseif ($action === 'clear-history') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }
    $result = $searchCtrl->clearSearchHistory();
    echo json_encode($result);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
