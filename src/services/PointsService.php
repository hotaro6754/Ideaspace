<?php
/**
 * IdeaSync Points Engine
 * Handles awarding points and calculating builder tiers
 */

require_once __DIR__ . '/../config/Database.php';

class PointsService {
    public const POINTS_MAP = [
        'post_idea' => 10,
        'apply_to_idea' => 5,
        'get_accepted' => 25,
        'complete_project' => 100,
        'sos_rescue_accepted' => 30,
        'write_postmortem' => 20,
        'commit_linked' => 5,
        'host_event' => 50,
        'attend_event' => 10,
        'quality_post' => 15,
    ];

    public const TIER_THRESHOLDS = [
        1 => 0,    // INITIATE
        2 => 50,   // CONTRIBUTOR
        3 => 200,  // BUILDER
        4 => 500,  // ARCHITECT
        5 => 1000, // LEGEND
    ];

    public const TIER_NAMES = [
        1 => 'INITIATE',
        2 => 'CONTRIBUTOR',
        3 => 'BUILDER',
        4 => 'ARCHITECT',
        5 => 'LEGEND',
    ];

    /**
     * Award points to a user
     */
    public static function awardPoints($userId, $action, $referenceId = null, $referenceType = null) {
        $conn = getConnection();

        if (!isset(self::POINTS_MAP[$action])) {
            return false;
        }

        $points = self::POINTS_MAP[$action];

        try {
            $conn->begin_transaction();

            // 1. Log transaction
            $stmt = $conn->prepare("INSERT INTO point_transactions (user_id, action, points, reference_id, reference_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isiis", $userId, $action, $points, $referenceId, $referenceType);
            $stmt->execute();

            // 2. Update user total
            $stmt = $conn->prepare("UPDATE users SET total_points = total_points + ? WHERE id = ?");
            $stmt->bind_param("ii", $points, $userId);
            $stmt->execute();

            // 3. Recalculate tier
            $stmt = $conn->prepare("SELECT total_points, tier FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $newTier = self::calculateTier($user['total_points']);

            if ($newTier !== (int)$user['tier']) {
                $stmt = $conn->prepare("UPDATE users SET tier = ? WHERE id = ?");
                $stmt->bind_param("ii", $newTier, $userId);
                $stmt->execute();

                // Create tier upgrade notification
                $tierName = self::TIER_NAMES[$newTier];
                $message = "🎉 You've reached {$tierName}! Keep building.";
                $type = 'message';
                $stmt = $conn->prepare("INSERT INTO notifications (recipient_user_id, notification_type, message) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $userId, $type, $message);
                $stmt->execute();
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Points Award Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate tier based on points
     */
    private static function calculateTier($points) {
        $highestTier = 1;
        foreach (self::TIER_THRESHOLDS as $tier => $threshold) {
            if ($points >= $threshold) {
                $highestTier = $tier;
            } else {
                break;
            }
        }
        return $highestTier;
    }
}
