<?php
if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');
$user_id = $_SESSION['user_id'];
$db = getConnection();

$query = "SELECT e.*, u.name as creator_name,
          (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status = 'attending') as attendees
          FROM events e
          JOIN users u ON e.creator_id = u.id
          WHERE e.is_cancelled = 0 AND e.end_time >= CURRENT_TIMESTAMP
          ORDER BY e.start_time ASC";
$res = $db->query($query);
$events = [];
while($row = $res->fetch_assoc()) $events[] = $row;

ob_start();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-end mb-12 animate-fade-up">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Campus Events</h1>
            <p class="mt-2 text-slate-500 font-medium">Workshops, Hackathons, and Team Syncs.</p>
        </div>
        <button onclick="document.getElementById('createEventModal').classList.remove('hidden')" class="btn-primary !py-3 !text-xs">
            <i class="fas fa-plus mr-2"></i> Host Event
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach($events as $e): ?>
            <div class="premium-card p-8 bg-white flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <span class="px-2 py-1 bg-primary/10 text-primary text-[9px] font-black uppercase rounded"><?php echo sanitize($e['event_type']); ?></span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><i class="fas fa-users mr-1"></i> <?php echo $e['attendees']; ?> attending</span>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo sanitize($e['title']); ?></h3>
                <p class="text-sm text-slate-500 line-clamp-2 mb-6"><?php echo sanitize($e['description']); ?></p>

                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3 text-xs font-medium text-slate-600">
                        <i class="far fa-calendar text-primary w-4"></i>
                        <?php echo date('D, M d • h:i A', strtotime($e['start_time'])); ?>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-medium text-slate-600">
                        <i class="fas fa-map-marker-alt text-primary w-4"></i>
                        <?php echo sanitize($e['location'] ?: ($e['is_virtual'] ? 'Virtual Meeting' : 'Lendi Campus')); ?>
                    </div>
                </div>

                <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded-lg bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-400">
                            <?php echo strtoupper(substr($e['creator_name'], 0, 1)); ?>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase"><?php echo sanitize($e['creator_name']); ?></span>
                    </div>
                    <form action="<?php echo BASE_URL; ?>/src/controllers/events.php?action=rsvp" method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $e['id']; ?>">
                        <input type="hidden" name="status" value="attending">
                        <button type="submit" class="btn-primary !py-2 !px-4 !text-[10px]">RSVP NOW</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Event Modal -->
<div id="createEventModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="premium-card !rounded-3xl w-full max-w-lg bg-white relative z-10 animate-fade-up">
        <form action="<?php echo BASE_URL; ?>/src/controllers/events.php?action=create" method="POST" class="p-10">
            <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
            <h3 class="text-2xl font-bold text-slate-900 mb-6">Host New Event</h3>

            <div class="space-y-4">
                <div>
                    <label class="form-label">Event Title</label>
                    <input type="text" name="title" required class="form-input" placeholder="e.g. AI Ethics Workshop">
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select name="event_type" class="form-select">
                        <option value="workshop">Workshop</option>
                        <option value="hackathon">Hackathon</option>
                        <option value="meeting">Team Sync</option>
                        <option value="bootcamp">Bootcamp</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Starts</label>
                        <input type="datetime-local" name="start_time" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Ends</label>
                        <input type="datetime-local" name="end_time" required class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Location / Link</label>
                    <input type="text" name="location" class="form-input" placeholder="Room 201 or Meeting Link">
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-textarea" placeholder="What should attendees expect?"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8">
                <button type="button" onclick="document.getElementById('createEventModal').classList.add('hidden')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary px-8">Launch Event</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
