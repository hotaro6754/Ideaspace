<?php
/**
 * SearchQuery Class
 * Handles advanced search functionality across ideas, users, and collaborations
 */

class SearchQuery {
    private $conn;
    private $search_term;
    private $filters;

    public function __construct($db) {
        $this->conn = $db;
        $this->filters = [];
    }

    /**
     * Set search term and filters
     */
    public function setQuery($search_term, $filters = []) {
        $this->search_term = trim($search_term);
        $this->filters = $filters;
        return $this;
    }

    /**
     * Search ideas
     */
    public function searchIdeas($limit = 20, $offset = 0) {
        if (empty($this->search_term)) {
            return [];
        }

        $search_pattern = "%" . $this->search_term . "%";

        $query = "SELECT i.*, u.name, u.roll_number, u.profile_pic,
                         COUNT(DISTINCT a.id) as applicant_count,
                         COUNT(DISTINCT uv.id) as upvote_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN applications a ON i.id = a.idea_id AND a.status = 'accepted'
                  LEFT JOIN upvotes uv ON i.id = uv.idea_id
                  WHERE (i.title LIKE ? OR i.description LIKE ? OR i.domain LIKE ?)
                        AND i.status != 'abandoned'";

        $params = [$search_pattern, $search_pattern, $search_pattern];
        $types = "sss";

        // Apply filters
        if (!empty($this->filters['domain'])) {
            $query .= " AND i.domain = ?";
            $params[] = $this->filters['domain'];
            $types .= "s";
        }

        if (!empty($this->filters['status'])) {
            $query .= " AND i.status = ?";
            $params[] = $this->filters['status'];
            $types .= "s";
        }

        if (!empty($this->filters['min_upvotes'])) {
            $query .= " AND upvotes >= ?";
            $params[] = $this->filters['min_upvotes'];
            $types .= "i";
        }

        if (!empty($this->filters['sort_by'])) {
            switch ($this->filters['sort_by']) {
                case 'recent':
                    $query .= " GROUP BY i.id ORDER BY i.created_at DESC";
                    break;
                case 'trending':
                    $query .= " GROUP BY i.id ORDER BY upvote_count DESC, i.created_at DESC";
                    break;
                case 'most_applicants':
                    $query .= " GROUP BY i.id ORDER BY applicant_count DESC";
                    break;
                default:
                    $query .= " GROUP BY i.id ORDER BY i.created_at DESC";
            }
        } else {
            $query .= " GROUP BY i.id ORDER BY i.created_at DESC";
        }

        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search users
     */
    public function searchUsers($limit = 10, $offset = 0) {
        if (empty($this->search_term)) {
            return [];
        }

        $search_pattern = "%" . $this->search_term . "%";

        $query = "SELECT u.*, br.rank, br.points, br.collaborations
                  FROM users u
                  LEFT JOIN builder_rank br ON u.id = br.user_id
                  WHERE (u.name LIKE ? OR u.roll_number LIKE ? OR u.branch LIKE ?)";

        $params = [$search_pattern, $search_pattern, $search_pattern];
        $types = "sss";

        // Apply filters
        if (!empty($this->filters['branch'])) {
            $query .= " AND u.branch = ?";
            $params[] = $this->filters['branch'];
            $types .= "s";
        }

        if (!empty($this->filters['year'])) {
            $query .= " AND u.year = ?";
            $params[] = $this->filters['year'];
            $types .= "i";
        }

        if (!empty($this->filters['min_rank'])) {
            $query .= " AND br.rank = ?";
            $params[] = $this->filters['min_rank'];
            $types .= "s";
        }

        $query .= " ORDER BY br.points DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Combined search across ideas and users
     */
    public function searchAll($limit = 20, $offset = 0) {
        if (empty($this->search_term)) {
            return ['ideas' => [], 'users' => []];
        }

        return [
            'ideas' => $this->searchIdeas($limit, $offset),
            'users' => $this->searchUsers($limit / 2, $offset)
        ];
    }

    /**
     * Search ideas by skills
     */
    public function searchBySkills($skills, $limit = 20, $offset = 0) {
        if (empty($skills) || !is_array($skills)) {
            return [];
        }

        $query = "SELECT i.*, u.name, u.roll_number, u.profile_pic,
                         COUNT(DISTINCT a.id) as applicant_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN applications a ON i.id = a.idea_id
                  WHERE i.status = 'open'";

        // Search for skill matches in JSON array - use only parameterized queries
        $placeholders = array_fill(0, count($skills), "i.skills_needed LIKE CONCAT('%', ?, '%')");
        $query .= " AND (" . implode(" OR ", $placeholders) . ")";

        $query .= " GROUP BY i.id ORDER BY i.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        // Bind parameters - all skills as strings, then limit and offset as integers
        $types = str_repeat("s", count($skills)) . "ii";
        $params = array_merge($skills, [$limit, $offset]);

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions($term, $limit = 5) {
        $search_pattern = $term . "%";

        // Get recent idea titles matching the term
        $query = "SELECT DISTINCT title as suggestion, 'idea' as type
                  FROM ideas
                  WHERE title LIKE ?
                  ORDER BY created_at DESC
                  LIMIT ?
                  UNION ALL
                  SELECT DISTINCT domain as suggestion, 'domain' as type
                  FROM ideas
                  WHERE domain LIKE ?
                  ORDER BY RAND()
                  LIMIT 3
                  UNION ALL
                  SELECT DISTINCT name as suggestion, 'user' as type
                  FROM users
                  WHERE name LIKE ?
                  ORDER BY created_at DESC
                  LIMIT 3";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("sss", $search_pattern, $search_pattern, $search_pattern);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get advanced search filters
     */
    public function getAvailableFilters() {
        return [
            'domains' => $this->getUniqueDomains(),
            'statuses' => ['open', 'in_progress', 'completed'],
            'branches' => $this->getUniqueBranches(),
            'years' => [1, 2, 3, 4],
            'ranks' => ['INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND']
        ];
    }

    /**
     * Get unique domains from ideas
     */
    private function getUniqueDomains() {
        $query = "SELECT DISTINCT domain FROM ideas WHERE domain IS NOT NULL ORDER BY domain";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($results, 'domain');
    }

    /**
     * Get unique branches from users
     */
    private function getUniqueBranches() {
        $query = "SELECT DISTINCT branch FROM users ORDER BY branch";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($results, 'branch');
    }

    /**
     * Get search history for user
     */
    public function getSearchHistory($user_id, $limit = 10) {
        // Note: This would require a search_history table
        // For now, returning empty as table doesn't exist yet
        return [];
    }

    /**
     * Search ideas by multiple criteria (advanced)
     */
    public function advancedSearch($criteria = [], $limit = 20, $offset = 0) {
        $query = "SELECT i.*, u.name, u.roll_number, u.profile_pic,
                         COUNT(DISTINCT a.id) as applicant_count,
                         COUNT(DISTINCT uv.id) as upvote_count
                  FROM ideas i
                  JOIN users u ON i.user_id = u.id
                  LEFT JOIN applications a ON i.id = a.idea_id
                  LEFT JOIN upvotes uv ON i.id = uv.idea_id
                  WHERE 1=1";

        $params = [];
        $types = "";

        // Search term
        if (!empty($criteria['search_term'])) {
            $query .= " AND (i.title LIKE ? OR i.description LIKE ?)";
            $search = "%" . $criteria['search_term'] . "%";
            $params[] = &$search;
            $params[] = &$search;
            $types .= "ss";
        }

        // Domain filter
        if (!empty($criteria['domain'])) {
            $query .= " AND i.domain = ?";
            $params[] = &$criteria['domain'];
            $types .= "s";
        }

        // Status filter
        if (!empty($criteria['status'])) {
            $query .= " AND i.status = ?";
            $params[] = &$criteria['status'];
            $types .= "s";
        }

        // Year filter
        if (!empty($criteria['year'])) {
            $query .= " AND u.year = ?";
            $params[] = &$criteria['year'];
            $types .= "i";
        }

        // Branch filter
        if (!empty($criteria['branch'])) {
            $query .= " AND u.branch = ?";
            $params[] = &$criteria['branch'];
            $types .= "s";
        }

        $query .= " GROUP BY i.id ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
        $params[] = &$limit;
        $params[] = &$offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $params));
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Log a search query (for analytics)
     */
    public function logSearch($user_id, $query_term, $search_type = 'general') {
        // For now, storing in a simple format
        // In production, this could be stored in a search_history table
        if (!isset($_SESSION['search_history'])) {
            $_SESSION['search_history'] = [];
        }

        $search_entry = [
            'user_id' => $user_id,
            'query' => $query_term,
            'type' => $search_type,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        array_unshift($_SESSION['search_history'], $search_entry);

        // Keep only last 50 searches in session
        if (count($_SESSION['search_history']) > 50) {
            array_pop($_SESSION['search_history']);
        }

        return ['success' => true];
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10) {
        // Return commonly searched domains and skills
        $query = "SELECT domain as search_term, COUNT(*) as frequency
                  FROM ideas
                  WHERE domain IS NOT NULL
                  GROUP BY domain
                  ORDER BY frequency DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user search history
     */
    public function getUserHistory($user_id, $limit = 20) {
        // If search_history table exists, query it
        // Otherwise, return from session if user matches
        if (!isset($_SESSION['search_history'])) {
            return [];
        }

        $user_searches = array_filter($_SESSION['search_history'], function($item) use ($user_id) {
            return $item['user_id'] == $user_id;
        });

        return array_slice($user_searches, 0, $limit);
    }

    /**
     * Delete user search history
     */
    public function deleteUserHistory($user_id) {
        if (isset($_SESSION['search_history'])) {
            $_SESSION['search_history'] = array_filter($_SESSION['search_history'], function($item) use ($user_id) {
                return $item['user_id'] != $user_id;
            });
            return ['success' => true];
        }
        return ['success' => true];
    }
}
?>
