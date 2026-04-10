<?php
/**
 * BuilderRank Model
 * Handles gamification and ranking system
 */

class BuilderRank {
    private $conn;

    // Rank tiers
    const RANKS = [
        'INITIATE' => ['min' => 0, 'max' => 50, 'icon' => '🌱'],
        'CONTRIBUTOR' => ['min' => 50, 'max' => 150, 'icon' => '⭐'],
        'BUILDER' => ['min' => 150, 'max' => 300, 'icon' => '🏗️'],
        'ARCHITECT' => ['min' => 300, 'max' => 500, 'icon' => '🏛️'],
        'LEGEND' => ['min' => 500, 'max' => PHP_INT_MAX, 'icon' => '👑']
    ];

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Initialize builder rank for new user
     */
    public function initialize($user_id) {
        $query = "INSERT IGNORE INTO builder_rank (user_id, rank, points)
                  VALUES (?, 'INITIATE', 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Add points to user
     */
    public function addPoints($user_id, $points, $reason = '') {
        // Get current points
        $query = "SELECT points FROM builder_rank WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $this->initialize($user_id);
        }

        $row = $result->fetch_assoc();
        $current_points = $row['points'] ?? 0;
        $new_points = $current_points + $points;

        // Update points
        $update_query = "UPDATE builder_rank SET points = ? WHERE user_id = ?";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bind_param("ii", $new_points, $user_id);
        $update_stmt->execute();

        // Update rank based on points
        $this->updateRank($user_id, $new_points);

        return true;
    }

    /**
     * Update rank based on points
     */
    private function updateRank($user_id, $points) {
        $new_rank = 'INITIATE';

        foreach (self::RANKS as $rank => $range) {
            if ($points >= $range['min'] && $points < $range['max']) {
                $new_rank = $rank;
                break;
            }
        }

        $query = "UPDATE builder_rank SET rank = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $new_rank, $user_id);
        $stmt->execute();
    }

    /**
     * Get user rank info
     */
    public function getUserRank($user_id) {
        $query = "SELECT * FROM builder_rank WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Increment ideas posted
     */
    public function incrementIdeasPosted($user_id) {
        $query = "UPDATE builder_rank SET ideas_posted = ideas_posted + 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $this->addPoints($user_id, 10); // 10 points for posting an idea
    }

    /**
     * Increment completed projects
     */
    public function incrementCompleted($user_id) {
        $query = "UPDATE builder_rank SET ideas_completed = ideas_completed + 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $this->addPoints($user_id, 50); // 50 points for completing a project
    }

    /**
     * Increment collaborations
     */
    public function incrementCollaborations($user_id) {
        $query = "UPDATE builder_rank SET collaborations = collaborations + 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $this->addPoints($user_id, 25); // 25 points per collaboration
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard($limit = 10) {
        $query = "SELECT br.*, u.name, u.roll_number, u.branch
                  FROM builder_rank br
                  JOIN users u ON br.user_id = u.id
                  ORDER BY br.points DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get rank info
     */
    public static function getRankInfo($rank) {
        return self::RANKS[$rank] ?? self::RANKS['INITIATE'];
    }

    /**
     * Get next rank info
     */
    public function getNextRankInfo($current_rank) {
        $ranks = ['INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND'];
        $current_index = array_search($current_rank, $ranks);

        if ($current_index === false || $current_index === count($ranks) - 1) {
            return null; // Already at max rank
        }

        $next_rank = $ranks[$current_index + 1];
        return [
            'rank' => $next_rank,
            'points_needed' => self::RANKS[$next_rank]['min']
        ];
    }
}
?>
