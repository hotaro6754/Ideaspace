<?php
/**
 * IdeaRecommendation Model
 * Handles personalized recommendations, trending ideas, and skill matching
 *
 * Features:
 * - Trending ideas ranking (upvotes, applications, recency)
 * - Personalized feed based on user skills
 * - Perfect team suggestions for idea creators
 * - Skill match percentage calculation
 */

class IdeaRecommendation {
    private $conn;

    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }

    /**
     * Get trending ideas for all users
     * Ranking: (upvotes × 0.3) + (recent apps × 0.3) + (recency × 0.4)
     *
     * @param int $limit Number of ideas to return
     * @param int $days Look back this many days for trending
     * @return array List of trending ideas with scores
     */
    public function getTrendingIdeas($limit = 10, $days = 7) {
        $query = "
            SELECT
                i.id,
                i.user_id,
                i.title,
                i.description,
                i.domain,
                i.status,
                i.upvotes as total_upvotes,
                i.applicant_count,
                u.name as creator_name,
                u.profile_pic as creator_pic,
                br.rank as creator_rank,
                br.points as creator_points,
                COUNT(DISTINCT uv.id) as recent_upvotes,
                COUNT(DISTINCT CASE WHEN a.status IN ('pending', 'accepted') THEN a.id END) as recent_applications,
                DATEDIFF(NOW(), i.created_at) as days_old,
                (COUNT(DISTINCT uv.id) * 0.3 +
                 COUNT(DISTINCT CASE WHEN a.status IN ('pending', 'accepted') THEN a.id END) * 0.3 +
                 (? - LEAST(DATEDIFF(NOW(), i.created_at), ?)) * 0.4) as trending_score
            FROM ideas i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN builder_rank br ON u.id = br.user_id
            LEFT JOIN upvotes uv ON i.id = uv.idea_id AND uv.upvoted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            LEFT JOIN applications a ON i.id = a.idea_id AND a.applied_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            WHERE i.status = 'open'
            GROUP BY i.id
            ORDER BY trending_score DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiii", $days, $days, $days, $days, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get recommended ideas for logged-in user
     * Based on skill match with idea's requirements
     *
     * @param int $userId User ID to get recommendations for
     * @param int $limit Number of recommendations
     * @return array List of recommended ideas with match percentages
     */
    public function getRecommendedIdeas($userId, $limit = 10) {
        // First query: Get user's skills
        $user_skills = $this->getUserSkills($userId);

        // Second query: Get open ideas
        $query = "
            SELECT
                i.id,
                i.user_id,
                i.title,
                i.description,
                i.domain,
                i.skills_needed,
                i.upvotes,
                i.applicant_count,
                u.name as creator_name,
                u.profile_pic as creator_pic,
                br.rank as creator_rank,
                br.points as creator_points,
                (COUNT(DISTINCT uv.id) * 0.3 +
                 COUNT(DISTINCT a.id) * 0.3 +
                 (7 - LEAST(DATEDIFF(NOW(), i.created_at), 7)) * 0.4) as trending_score
            FROM ideas i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN builder_rank br ON u.id = br.user_id
            LEFT JOIN upvotes uv ON i.id = uv.idea_id AND uv.upvoted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            LEFT JOIN applications a ON i.id = a.idea_id AND a.applied_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            WHERE i.status = 'open'
            AND i.user_id != ?
            AND NOT EXISTS (
                SELECT 1 FROM applications WHERE idea_id = i.id AND user_id = ? AND status != 'withdrawn'
            )
            GROUP BY i.id
            ORDER BY trending_score DESC
            LIMIT 100
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $all_ideas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate skill match for each idea and rank
        $recommended = [];
        foreach ($all_ideas as $idea) {
            $match_percentage = $this->calculateSkillMatch(
                $user_skills,
                json_decode($idea['skills_needed'], true) ?? []
            );

            // Combined score: skill_match (60%) + trending (40%)
            $combined_score = ($match_percentage * 0.6) + ($idea['trending_score'] * 0.4);

            $idea['match_percentage'] = $match_percentage;
            $idea['combined_score'] = $combined_score;
            $recommended[] = $idea;
        }

        // Sort by combined score
        usort($recommended, function($a, $b) {
            return $b['combined_score'] <=> $a['combined_score'];
        });

        return array_slice($recommended, 0, $limit);
    }

    /**
     * Find perfect team members for an idea
     * Matches builders who have required skills
     *
     * @param int $ideaId Idea ID
     * @param int $limit Number of suggestions
     * @param int $minMatchPercentage Minimum match % (0-100)
     * @return array List of matching builders
     */
    public function findPerfectTeam($ideaId, $limit = 5, $minMatchPercentage = 50) {
        // Get idea's required skills
        $idea_query = "SELECT skills_needed, user_id FROM ideas WHERE id = ?";
        $stmt = $this->conn->prepare($idea_query);
        $stmt->bind_param("i", $ideaId);
        $stmt->execute();
        $idea = $stmt->get_result()->fetch_assoc();

        if (!$idea) return [];

        $required_skills = json_decode($idea['skills_needed'], true) ?? [];
        if (empty($required_skills)) return [];

        // Find builders with matching skills
        $query = "
            SELECT DISTINCT
                u.id,
                u.name,
                u.email,
                u.profile_pic,
                u.branch,
                u.year,
                u.team_rating,
                u.projects_completed,
                br.rank,
                br.points,
                br.success_rate,
                GROUP_CONCAT(DISTINCT us.skill_name SEPARATOR ',') as builder_skills,
                GROUP_CONCAT(DISTINCT us.proficiency SEPARATOR ',') as skill_levels
            FROM users u
            LEFT JOIN builder_rank br ON u.id = br.user_id
            LEFT JOIN user_skills us ON u.id = us.user_id
            WHERE u.id != ?
            AND br.rank IN ('BUILDER', 'ARCHITECT', 'LEGEND')
            AND u.id NOT IN (
                SELECT collaborator_id FROM collaborations WHERE idea_id = ? AND status = 'active'
            )
            AND u.id NOT IN (
                SELECT user_id FROM applications WHERE idea_id = ? AND status IN ('accepted', 'pending')
            )
            GROUP BY u.id
            HAVING builder_skills IS NOT NULL
            LIMIT 50
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $idea['user_id'], $ideaId, $ideaId);
        $stmt->execute();
        $potential_team = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate skill match for each candidate
        $matched_team = [];
        foreach ($potential_team as $builder) {
            $builder_skills = array_map('trim', explode(',', $builder['builder_skills']));
            $match = $this->calculateSkillMatch($builder_skills, $required_skills);

            if ($match >= $minMatchPercentage) {
                $builder['match_percentage'] = $match;
                $matched_team[] = $builder;
            }
        }

        // Sort by match percentage desc
        usort($matched_team, function($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        return array_slice($matched_team, 0, $limit);
    }

    /**
     * Calculate skill match between user skills and required skills
     * Returns percentage (0-100)
     *
     * @param array $userSkills Array of user's skills
     * @param array $requiredSkills Array of required skills
     * @return int Match percentage (0-100)
     */
    public function calculateSkillMatch($userSkills, $requiredSkills) {
        if (empty($requiredSkills)) return 100; // If no skills required, perfect match
        if (empty($userSkills)) return 0; // If user has no skills, no match

        // Normalize to lowercase for comparison
        $user_skills_lower = array_map('strtolower', $userSkills);
        $required_lower = array_map('strtolower', $requiredSkills);

        // Find matching skills
        $matches = array_intersect($user_skills_lower, $required_lower);

        // Calculate percentage
        return (int)(count($matches) / count($required_lower) * 100);
    }

    /**
     * Get user's skills from database (public wrapper for helper)
     *
     * @param int $userId User ID
     * @return array Array of skill names
     */
    public function getUserSkills_Helper($userId) {
        return $this->getUserSkills($userId);
    }

    /**
     * Get user's skills from database
        $query = "SELECT skill_name FROM user_skills WHERE user_id = ? ORDER BY proficiency DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return array_column($results, 'skill_name');
    }

    /**
     * Find similar ideas (same domain)
     *
     * @param int $ideaId Current idea ID
     * @param int $limit Number of suggestions
     * @return array List of similar ideas
     */
    public function getSimilarIdeas($ideaId, $limit = 5) {
        // Get current idea's domain
        $domain_query = "SELECT domain FROM ideas WHERE id = ?";
        $stmt = $this->conn->prepare($domain_query);
        $stmt->bind_param("i", $ideaId);
        $stmt->execute();
        $idea = $stmt->get_result()->fetch_assoc();

        if (!$idea) return [];

        // Find similar ideas
        $query = "
            SELECT
                i.id,
                i.user_id,
                i.title,
                i.description,
                i.domain,
                i.upvotes,
                i.applicant_count,
                u.name as creator_name,
                br.rank as creator_rank
            FROM ideas i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN builder_rank br ON u.id = br.user_id
            WHERE i.domain = ?
            AND i.id != ?
            AND i.status = 'open'
            ORDER BY i.upvotes DESC, i.created_at DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $idea['domain'], $ideaId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Check if an idea is trending (top 20 by trending score)
     *
     * @param int $ideaId Idea ID
     * @return bool True if idea is trending
     */
    public function isTrending($ideaId) {
        $query = "
            SELECT COUNT(*) as trending_count FROM (
                SELECT i.id,
                    (COUNT(DISTINCT uv.id) * 0.3 +
                     COUNT(DISTINCT a.id) * 0.3 +
                     (7 - LEAST(DATEDIFF(NOW(), i.created_at), 7)) * 0.4) as trending_score
                FROM ideas i
                LEFT JOIN upvotes uv ON i.id = uv.idea_id AND uv.upvoted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                LEFT JOIN applications a ON i.id = a.idea_id AND a.applied_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                WHERE i.status = 'open'
                GROUP BY i.id
                ORDER BY trending_score DESC
                LIMIT 20
            ) trending_ideas
            WHERE id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $ideaId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['trending_count'] > 0;
    }

    /**
     * Get ideas sorted by various criteria
     *
     * @param string $sortBy 'newest' | 'trending' | 'most-applied' | 'upvotes'
     * @param int $offset Pagination offset
     * @param int $limit Items per page
     * @param string $domain Filter by domain (optional)
     * @return array List of ideas
     */
    public function getIdeasSorted($sortBy = 'newest', $offset = 0, $limit = 12, $domain = null) {
        $where = "WHERE i.status = 'open'";
        if ($domain) {
            $domain = '%' . $domain . '%';
            $where .= " AND i.domain LIKE ?";
        }

        $order = "i.created_at DESC"; // default: newest
        switch ($sortBy) {
            case 'trending':
                $order = "(COUNT(DISTINCT uv.id) * 0.3 + COUNT(DISTINCT a.id) * 0.3 + (7 - LEAST(DATEDIFF(NOW(), i.created_at), 7)) * 0.4) DESC";
                break;
            case 'most-applied':
                $order = "i.applicant_count DESC, i.created_at DESC";
                break;
            case 'upvotes':
                $order = "i.upvotes DESC, i.created_at DESC";
                break;
        }

        $query = "
            SELECT
                i.id,
                i.user_id,
                i.title,
                i.description,
                i.domain,
                i.upvotes,
                i.applicant_count,
                u.name as creator_name,
                u.profile_pic as creator_pic,
                br.rank as creator_rank
            FROM ideas i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN builder_rank br ON u.id = br.user_id
            LEFT JOIN upvotes uv ON i.id = uv.idea_id AND uv.upvoted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            LEFT JOIN applications a ON i.id = a.idea_id AND a.applied_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            {$where}
            GROUP BY i.id
            ORDER BY {$order}
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->conn->prepare($query);
        if ($domain) {
            $stmt->bind_param("sii", $domain, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
