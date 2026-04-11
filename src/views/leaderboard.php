<?php
/**
 * IdeaSync Leaderboard
 */
require_once __DIR__ . '/../config/Database.php';

$conn = getConnection();
$query = "SELECT u.name, u.branch, u.year, u.total_points, u.tier, u.roll_number
          FROM users u
          ORDER BY u.total_points DESC
          LIMIT 20";
$result = $conn->query($query);
$leaders = $result->fetch_all(MYSQLI_ASSOC);

function getTierName($tier) {
    return ['INITIATE', 'CONTRIBUTOR', 'BUILDER', 'ARCHITECT', 'LEGEND'][$tier-1] ?? 'INITIATE';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
</head>
<body class="bg-primary text-primary">
    <div class="container py-12">
        <h1 class="text-4xl font-bold mb-8 tight-tracking text-center">Campus Leaderboard</h1>

        <div class="card overflow-hidden">
            <table class="table w-full">
                <thead>
                    <tr class="text-left border-b border-border">
                        <th class="p-4 text-muted text-xs uppercase tracking-widest">Rank</th>
                        <th class="p-4 text-muted text-xs uppercase tracking-widest">Builder</th>
                        <th class="p-4 text-muted text-xs uppercase tracking-widest">Branch</th>
                        <th class="p-4 text-muted text-xs uppercase tracking-widest">Tier</th>
                        <th class="p-4 text-muted text-xs uppercase tracking-widest text-right">Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaders as $index => $leader): ?>
                        <tr class="border-b border-border hover:bg-secondary/50 transition-colors">
                            <td class="p-4 font-mono text-accent">#<?php echo $index + 1; ?></td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="user-avatar"><?php echo strtoupper(substr($leader['name'], 0, 1)); ?></div>
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($leader['name']); ?></div>
                                        <div class="text-xs text-muted"><?php echo $leader['roll_number']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-secondary"><?php echo $leader['branch']; ?> (Year <?php echo $leader['year']; ?>)</td>
                            <td class="p-4">
                                <span class="badge badge-ai"><?php echo getTierName($leader['tier']); ?></span>
                            </td>
                            <td class="p-4 text-right font-bold text-accent"><?php echo number_format($leader['total_points']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
