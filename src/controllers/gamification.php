<?php
/**
 * Gamification Controller
 * Handles leaderboard, user stats, and gamification features
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BuilderRank.php';

class GamificationController {
    private $builderRank;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->builderRank = new BuilderRank($db);
    }

    /**
     * Get global leaderboard
     */
    public function getLeaderboard($limit = 50, $timeframe = 'all') {
        $valid_timeframes = ['all', 'month', 'week'];
        if (!in_array($timeframe, $valid_timeframes)) {
            $timeframe = 'all';
        }

        $sql = "SELECT br.*, u.name, u.roll_number, u.profile_pic, u.branch,
                       COUNT(DISTINCT i.id) as ideas_posted,
                       COUNT(DISTINCT c.id) as collaborations
                FROM builder_rank br
                JOIN users u ON br.user_id = u.id
                LEFT JOIN ideas i ON u.id = i.user_id
                LEFT JOIN collaborations c ON u.id = c.collaborator_id
                WHERE br.rank > 0";

        if ($timeframe === 'month') {
            $sql .= " AND br.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($timeframe === 'week') {
            $sql .= " AND br.updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        }

        $sql .= " GROUP BY br.id
                 ORDER BY br.rank DESC
                 LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user's rank and stats
     */
    public function getUserStats($user_id) {
        if (!$user_id) {
            return null;
        }

        $sql = "SELECT br.*, u.name, u.email, u.profile_pic, u.branch, u.year,
                       COUNT(DISTINCT i.id) as ideas_posted,
                       COUNT(DISTINCT c.id) as collaborations,
                       COUNT(DISTINCT uv.id) as upvotes_received,
                       COUNT(DISTINCT n.id) as notifications_count
                FROM builder_rank br
                JOIN users u ON br.user_id = u.id
                LEFT JOIN ideas i ON u.id = i.user_id
                LEFT JOIN collaborations c ON u.id = c.collaborator_id AND c.status = 'active'
                LEFT JOIN upvotes uv ON i.id = uv.idea_id
                LEFT JOIN notifications n ON u.id = n.user_id AND n.is_read = 0
                WHERE br.user_id = ?
                GROUP BY br.id";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get user's rank position
     */
    public function getUserRankPosition($user_id) {
        if (!$user_id) {
            return null;
        }

        $sql = "SELECT
                    (SELECT COUNT(*) + 1 FROM builder_ranks WHERE rank >
                     (SELECT rank FROM builder_ranks WHERE user_id = ?)) as position,
                    (SELECT COUNT(*) FROM builder_ranks WHERE rank > 0) as total_users";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    /**
     * Get top contributors
     */
    public function getTopContributors($limit = 10) {
        $sql = "SELECT u.id, u.name, u.roll_number, u.profile_pic,
                       COUNT(DISTINCT i.id) as ideas_count,
                       COUNT(DISTINCT c.id) as collaborations_count,
                       SUM(br.points) as total_points
                FROM users u
                LEFT JOIN ideas i ON u.id = i.user_id
                LEFT JOIN collaborations c ON u.id = c.collaborator_id AND c.status = 'active'
                LEFT JOIN builder_rank br ON u.id = br.user_id
                GROUP BY u.id
                ORDER BY total_points DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top builders (most collaborations)
     */
    public function getTopBuilders($limit = 10) {
        $sql = "SELECT u.id, u.name, u.roll_number, u.profile_pic, u.branch,
                       COUNT(DISTINCT c.id) as collaborations,
                       COUNT(DISTINCT CASE WHEN c.status = 'completed' THEN c.id END) as completed_projects
                FROM users u
                LEFT JOIN collaborations c ON u.id = c.collaborator_id
                GROUP BY u.id
                HAVING collaborations > 0
                ORDER BY collaborations DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top visionaries (most successful ideas)
     */
    public function getTopVisionaries($limit = 10) {
        $sql = "SELECT u.id, u.name, u.roll_number, u.profile_pic, u.branch,
                       COUNT(DISTINCT i.id) as ideas_posted,
                       COUNT(DISTINCT c.id) as collaborators_gathered,
                       SUM(up.count) as total_upvotes
                FROM users u
                LEFT JOIN ideas i ON u.id = i.user_id
                LEFT JOIN collaborations c ON i.id = c.idea_id AND c.status = 'active'
                LEFT JOIN (
                    SELECT idea_id, COUNT(*) as count FROM upvotes GROUP BY idea_id
                ) up ON i.id = up.idea_id
                GROUP BY u.id
                HAVING ideas_posted > 0
                ORDER BY ideas_posted DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get badges/achievements for user
     */
    public function getUserAchievements($user_id) {
        if (!$user_id) {
            return [];
        }

        $achievements = [];

        // Get user stats
        $stats = $this->getUserStats($user_id);
        if (!$stats) {
            return $achievements;
        }

        // Determine achievements based on stats
        if ($stats['ideas_posted'] >= 1) {
            $achievements[] = ['name' => 'Visionary', 'description' => 'Posted your first idea', 'icon' => '💡'];
        }

        if ($stats['ideas_posted'] >= 5) {
            $achievements[] = ['name' => 'Prolific Visionary', 'description' => 'Posted 5 ideas', 'icon' => '💡💡'];
        }

        if ($stats['collaborations'] >= 1) {
            $achievements[] = ['name' => 'Team Builder', 'description' => 'Collaborated on an idea', 'icon' => '🤝'];
        }

        if ($stats['collaborations'] >= 5) {
            $achievements[] = ['name' => 'Collaboration Expert', 'description' => 'Collaborated on 5 ideas', 'icon' => '👥'];
        }

        if ($stats['upvotes_received'] >= 10) {
            $achievements[] = ['name' => 'Popular', 'description' => 'Received 10 upvotes on ideas', 'icon' => '⭐'];
        }

        if ($stats['upvotes_received'] >= 50) {
            $achievements[] = ['name' => 'Trending', 'description' => 'Received 50 upvotes on ideas', 'icon' => '🚀'];
        }

        if ($stats['rank'] >= 100) {
            $achievements[] = ['name' => 'Top 100', 'description' => 'In top 100 leaderboard', 'icon' => '🏆'];
        }

        if ($stats['rank'] >= 10) {
            $achievements[] = ['name' => 'Top 10', 'description' => 'In top 10 leaderboard', 'icon' => '👑'];
        }

        return $achievements;
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$gamifyCtrl = new GamificationController($conn);

// Route to appropriate method
if ($action === 'leaderboard') {
    $limit = (int)($_GET['limit'] ?? 50);
    $timeframe = $_GET['timeframe'] ?? 'all';
    $leaderboard = $gamifyCtrl->getLeaderboard($limit, $timeframe);
    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
    exit();
} elseif ($action === 'user-stats') {
    $user_id = (int)($_GET['user_id'] ?? ($_SESSION['user_id'] ?? 0));
    $stats = $gamifyCtrl->getUserStats($user_id);
    echo json_encode(['success' => true, 'stats' => $stats]);
    exit();
} elseif ($action === 'user-rank') {
    $user_id = (int)($_GET['user_id'] ?? ($_SESSION['user_id'] ?? 0));
    $rank = $gamifyCtrl->getUserRankPosition($user_id);
    echo json_encode(['success' => true, 'rank' => $rank]);
    exit();
} elseif ($action === 'top-contributors') {
    $limit = (int)($_GET['limit'] ?? 10);
    $contributors = $gamifyCtrl->getTopContributors($limit);
    echo json_encode(['success' => true, 'contributors' => $contributors]);
    exit();
} elseif ($action === 'top-builders') {
    $limit = (int)($_GET['limit'] ?? 10);
    $builders = $gamifyCtrl->getTopBuilders($limit);
    echo json_encode(['success' => true, 'builders' => $builders]);
    exit();
} elseif ($action === 'top-visionaries') {
    $limit = (int)($_GET['limit'] ?? 10);
    $visionaries = $gamifyCtrl->getTopVisionaries($limit);
    echo json_encode(['success' => true, 'visionaries' => $visionaries]);
    exit();
} elseif ($action === 'achievements') {
    $user_id = (int)($_GET['user_id'] ?? ($_SESSION['user_id'] ?? 0));
    $achievements = $gamifyCtrl->getUserAchievements($user_id);
    echo json_encode(['success' => true, 'achievements' => $achievements]);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
    exit();
}
?>
