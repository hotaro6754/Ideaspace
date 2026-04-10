<?php
/**
 * Events Controller
 * Handles event management and RSVPs
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventRsvp.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../helpers/Security.php';

class EventsController {
    private $eventModel;
    private $rsvpModel;
    private $activityLog;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->eventModel = new Event($db);
        $this->rsvpModel = new EventRsvp($db);
        $this->activityLog = new ActivityLog($db);
    }

    /**
     * Create a new event
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        $event_type = trim($_POST['event_type'] ?? 'meeting');
        $collaboration_id = (int)($_POST['collaboration_id'] ?? 0);
        $location = trim($_POST['location'] ?? '');
        $is_virtual = isset($_POST['is_virtual']) ? (bool)$_POST['is_virtual'] : true;
        $max_attendees = (int)($_POST['max_attendees'] ?? 0);
        $max_attendees = $max_attendees > 0 ? $max_attendees : null;

        $collaboration_id = $collaboration_id > 0 ? $collaboration_id : null;

        $result = $this->eventModel->create(
            $user_id, $title, $description, $start_time, $end_time,
            $event_type, $collaboration_id, $location, $is_virtual, $max_attendees
        );

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'event', 'create', $collaboration_id);
        }

        return $result;
    }

    /**
     * Get upcoming events
     */
    public function getUpcoming() {
        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 10);
        $limit = min($limit, 100);

        $events = $this->eventModel->getUpcoming($limit);

        return [
            'success' => true,
            'events' => $events,
            'count' => count($events)
        ];
    }

    /**
     * Get events for collaboration
     */
    public function getForCollaboration() {
        $collaboration_id = (int)($_GET['collaboration_id'] ?? $_POST['collaboration_id'] ?? 0);
        $include_past = isset($_GET['include_past']) || isset($_POST['include_past']);

        if ($collaboration_id === 0) {
            return ['success' => false, 'error' => 'Collaboration ID is required'];
        }

        $events = $this->eventModel->getForCollaboration($collaboration_id, $include_past);

        return [
            'success' => true,
            'events' => $events,
            'count' => count($events)
        ];
    }

    /**
     * Get event details
     */
    public function getEvent() {
        $event_id = (int)($_GET['event_id'] ?? $_POST['event_id'] ?? 0);

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        $event = $this->eventModel->getById($event_id);

        if (!$event) {
            return ['success' => false, 'error' => 'Event not found'];
        }

        return ['success' => true, 'event' => $event];
    }

    /**
     * Update event
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $event_id = (int)($_POST['event_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        $event_type = trim($_POST['event_type'] ?? 'meeting');
        $location = trim($_POST['location'] ?? '');

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        $result = $this->eventModel->update($event_id, $user_id, $title, $description, $start_time, $end_time, $event_type, $location);

        if ($result['success']) {
            // Log activity
            $this->activityLog->log($user_id, 'event', 'update', $event_id);
        }

        return $result;
    }

    /**
     * Cancel event
     */
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $event_id = (int)($_POST['event_id'] ?? 0);

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        return $this->eventModel->cancel($event_id, $user_id);
    }

    /**
     * Delete event
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $event_id = (int)($_POST['event_id'] ?? 0);

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        return $this->eventModel->delete($event_id, $user_id);
    }

    /**
     * RSVP to event
     */
    public function rsvp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $user_id = $_SESSION['user_id'];
        $event_id = (int)($_POST['event_id'] ?? 0);
        $status = $_POST['status'] ?? 'attending';

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        return $this->rsvpModel->rsvp($event_id, $user_id, $status);
    }

    /**
     * Get event attendees
     */
    public function getAttendees() {
        $event_id = (int)($_GET['event_id'] ?? $_POST['event_id'] ?? 0);
        $status = $_GET['status'] ?? $_POST['status'] ?? null;

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        $attendees = $this->rsvpModel->getAttendees($event_id, $status);

        return [
            'success' => true,
            'attendees' => $attendees,
            'count' => count($attendees)
        ];
    }

    /**
     * Get event statistics
     */
    public function getStats() {
        $event_id = (int)($_GET['event_id'] ?? $_POST['event_id'] ?? 0);

        if ($event_id === 0) {
            return ['success' => false, 'error' => 'Event ID is required'];
        }

        $stats = $this->rsvpModel->getEventStats($event_id);

        return ['success' => true, 'stats' => $stats];
    }

    /**
     * Get user's RSVPs
     */
    public function getUserRsvps() {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        $user_id = $_SESSION['user_id'];
        $include_past = isset($_GET['include_past']) || isset($_POST['include_past']);

        $rsvps = $this->rsvpModel->getUserRsvps($user_id, $include_past);

        return [
            'success' => true,
            'rsvps' => $rsvps,
            'count' => count($rsvps)
        ];
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$events = new EventsController($conn);

// Return JSON for AJAX requests
header('Content-Type: application/json');

// Route to appropriate method
if ($action === 'create') {
    echo json_encode($events->create());
} elseif ($action === 'getUpcoming') {
    echo json_encode($events->getUpcoming());
} elseif ($action === 'getForCollaboration') {
    echo json_encode($events->getForCollaboration());
} elseif ($action === 'get') {
    echo json_encode($events->getEvent());
} elseif ($action === 'update') {
    echo json_encode($events->update());
} elseif ($action === 'cancel') {
    echo json_encode($events->cancel());
} elseif ($action === 'delete') {
    echo json_encode($events->delete());
} elseif ($action === 'rsvp') {
    echo json_encode($events->rsvp());
} elseif ($action === 'getAttendees') {
    echo json_encode($events->getAttendees());
} elseif ($action === 'getStats') {
    echo json_encode($events->getStats());
} elseif ($action === 'getUserRsvps') {
    echo json_encode($events->getUserRsvps());
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
exit();
?>
