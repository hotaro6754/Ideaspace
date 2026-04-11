<?php
/**
 * IdeaHealth score calculator
 */

class HealthService {
    public static function calculateScore($idea) {
        $score = 0;

        // 1. Description length (max 20)
        $descLen = strlen($idea['description'] ?? '');
        if ($descLen > 500) $score += 20;
        else if ($descLen > 200) $score += 15;
        else if ($descLen > 100) $score += 10;

        // 2. Skills needed (max 20)
        $skills = json_decode($idea['skills_needed'] ?? '[]', true);
        if (count($skills) >= 4) $score += 20;
        else if (count($skills) >= 2) $score += 15;
        else if (count($skills) >= 1) $score += 10;

        // 3. Activity/Applicants (max 20)
        $applicants = (int)($idea['applicant_count'] ?? 0);
        if ($applicants > 5) $score += 20;
        else if ($applicants > 2) $score += 15;
        else if ($applicants > 0) $score += 10;

        // 4. Recency (max 20)
        $updatedAt = strtotime($idea['updated_at'] ?? 'now');
        $daysSinceUpdate = (time() - $updatedAt) / (60 * 60 * 24);
        if ($daysSinceUpdate <= 3) $score += 20;
        else if ($daysSinceUpdate <= 7) $score += 15;
        else if ($daysSinceUpdate <= 14) $score += 10;

        // 5. Featured/Verification (max 20)
        if ($idea['is_iic_featured'] ?? false) $score += 20;
        if ($idea['github_repo_url'] ?? false) $score += 10;

        return min(100, $score);
    }

    public static function getHealthColor($score) {
        if ($score >= 70) return '#22C55E'; // Green
        if ($score >= 40) return '#EAB308'; // Yellow
        return '#EF4444'; // Red
    }
}
