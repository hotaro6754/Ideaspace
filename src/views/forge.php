<?php
/**
 * Forge - Events Feed
 */
require_once __DIR__ . '/../config/Database.php';

$conn = getConnection();
$query = "SELECT e.*, u.name as conductor_name
          FROM forge_events e
          JOIN users u ON e.conductor_id = u.id
          WHERE e.status != 'CANCELLED'
          ORDER BY e.event_date ASC";
$result = $conn->query($query);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forge | IdeaSync</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-primary text-primary">
    <div class="container py-12">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h1 class="text-4xl font-bold tight-tracking mb-2">The Forge</h1>
                <p class="text-secondary">Level up your skills with workshops run by top builders.</p>
            </div>
            <a href="/?page=forge&action=host" class="btn btn-primary">Host an Event</a>
        </div>

        <div class="grid grid-3">
            <?php foreach ($events as $event): ?>
                <div class="card p-5">
                    <div class="flex justify-between items-start mb-4">
                        <span class="badge badge-web"><?php echo $event['format']; ?></span>
                        <span class="text-xs text-muted"><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                    </div>
                    <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p class="text-secondary text-sm mb-6 line-clamp-3"><?php echo htmlspecialchars($event['description']); ?></p>

                    <div class="flex items-center gap-2 mb-6">
                        <div class="user-avatar"><?php echo strtoupper(substr($event['conductor_name'], 0, 1)); ?></div>
                        <span class="text-xs text-secondary">By <?php echo htmlspecialchars($event['conductor_name']); ?></span>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-border">
                        <span class="text-xs text-muted"><?php echo $event['seats_taken']; ?> / <?php echo $event['seat_limit']; ?> filled</span>
                        <a href="/?page=forge-detail&id=<?php echo $event['id']; ?>" class="btn btn-secondary btn-sm">Register →</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($events)): ?>
                <div class="col-span-3 card p-20 text-center">
                    <i data-lucide="calendar-off" class="mx-auto mb-4 text-muted" size="48"></i>
                    <h3 class="text-xl mb-2">No events scheduled</h3>
                    <p class="text-secondary">Check back later or host your own workshop.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
