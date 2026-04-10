<?php
/**
 * EventRsvp Model - Event RSVP management
 * File: /src/models/EventRsvp.php
 */

class EventRsvp {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create or update RSVP
     */
    public function rsvp($event_id, $user_id, $status = 'attending') {
        $valid_statuses = ['attending', 'maybe', 'not_attending'];

        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'error' => 'Invalid RSVP status'];
        }

        // Check if event exists
        $event_query = "SELECT id, max_attendees FROM events WHERE id = ?";
        $event_stmt = $this->conn->prepare($event_query);
        $event_stmt->bind_param("i", $event_id);
        $event_stmt->execute();
        $event = $event_stmt->get_result()->fetch_assoc();

        if (!$event) {
            return ['success' => false, 'error' => 'Event not found'];
        }

        // Check capacity if attending
        if ($status === 'attending' && $event['max_attendees']) {
            $count_query = "SELECT COUNT(*) as count FROM event_rsvps WHERE event_id = ? AND status = 'attending'";
            $count_stmt = $this->conn->prepare($count_query);
            $count_stmt->bind_param("i", $event_id);
            $count_stmt->execute();
            $result = $count_stmt->get_result()->fetch_assoc();

            if ($result['count'] >= $event['max_attendees']) {
                return ['success' => false, 'error' => 'Event is at maximum capacity'];
            }
        }

        // Check if already RSVP'd
        $check_query = "SELECT id FROM event_rsvps WHERE event_id = ? AND user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("ii", $event_id, $user_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            // Update existing RSVP
            $update_query = "UPDATE event_rsvps SET status = ?, responded_at = NOW() WHERE event_id = ? AND user_id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("sii", $status, $event_id, $user_id);

            return $update_stmt->execute()
                ? ['success' => true, 'action' => 'updated']
                : ['success' => false, 'error' => 'Failed to update RSVP'];
        }

        // Create new RSVP
        $insert_query = "INSERT INTO event_rsvps (event_id, user_id, status) VALUES (?, ?, ?)";
        $insert_stmt = $this->conn->prepare($insert_query);
        $insert_stmt->bind_param("iis", $event_id, $user_id, $status);

        return $insert_stmt->execute()
            ? ['success' => true, 'action' => 'created']
            : ['success' => false, 'error' => 'Failed to create RSVP'];
    }

    /**
     * Get user's RSVPs
     */
    public function getUserRsvps($user_id, $include_past = false) {
        $time_filter = $include_past ? "" : "AND e.start_time >= NOW()";

        $query = "SELECT er.*, e.title, e.start_time, e.end_time, e.location, e.is_virtual
                  FROM event_rsvps er
                  JOIN events e ON er.event_id = e.id
                  WHERE er.user_id = ? {$time_filter}
                  ORDER BY e.start_time ASC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get attendees for event
     */
    public function getAttendees($event_id, $status = null) {
        if ($status) {
            $query = "SELECT er.*, u.name, u.profile_pic, u.roll_number
                      FROM event_rsvps er
                      JOIN users u ON er.user_id = u.id
                      WHERE er.event_id = ? AND er.status = ?
                      ORDER BY er.responded_at DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return [];
            }

            $stmt->bind_param("is", $event_id, $status);
        } else {
            $query = "SELECT er.*, u.name, u.profile_pic, u.roll_number
                      FROM event_rsvps er
                      JOIN users u ON er.user_id = u.id
                      WHERE er.event_id = ?
                      ORDER BY er.status DESC, er.responded_at DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return [];
            }

            $stmt->bind_param("i", $event_id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get event statistics
     */
    public function getEventStats($event_id) {
        $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'attending' THEN 1 ELSE 0 END) as attending,
                    SUM(CASE WHEN status = 'maybe' THEN 1 ELSE 0 END) as maybe,
                    SUM(CASE WHEN status = 'not_attending' THEN 1 ELSE 0 END) as not_attending
                  FROM event_rsvps
                  WHERE event_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Remove RSVP
     */
    public function removeRsvp($event_id, $user_id) {
        $query = "DELETE FROM event_rsvps WHERE event_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $event_id, $user_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to remove RSVP'];
    }
}
?>
