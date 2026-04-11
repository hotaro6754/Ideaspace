<?php
/**
 * Event Model - Event management for collaborations
 * File: /src/models/Event.php
 */

class Event {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create event
     */
    public function create($creator_id, $title, $description, $start_time, $end_time, $event_type, $collaboration_id = null, $location = null, $is_virtual = true, $max_attendees = null) {
        if (empty($title) || strlen($title) < 3) {
            return ['success' => false, 'error' => 'Event title required'];
        }

        if (strlen($title) > 200) {
            return ['success' => false, 'error' => 'Title too long'];
        }

        $valid_types = ['presentation', 'standup', 'meeting', 'workshop', 'brainstorm', 'other'];
        if (!in_array($event_type, $valid_types)) {
            return ['success' => false, 'error' => 'Invalid event type'];
        }

        // Validate times
        if (strtotime($start_time) >= strtotime($end_time)) {
            return ['success' => false, 'error' => 'Start time must be before end time'];
        }

        if (strtotime($start_time) < time()) {
            return ['success' => false, 'error' => 'Start time must be in the future'];
        }

        // Check collaboration exists if provided
        if ($collaboration_id) {
            $collab_query = "SELECT id FROM collaborations WHERE id = ? AND (leader_id = ? OR collaborator_id = ?)";
            $collab_stmt = $this->conn->prepare($collab_query);
            $collab_stmt->bind_param("iii", $collaboration_id, $creator_id, $creator_id);
            $collab_stmt->execute();

            if ($collab_stmt->get_result()->num_rows === 0) {
                return ['success' => false, 'error' => 'Not authorized for this collaboration'];
            }
        }

        $query = "INSERT INTO events (creator_id, collaboration_id, title, description, start_time, end_time, event_type, location, is_virtual, max_attendees)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("iissssssii", $creator_id, $collaboration_id, $title, $description, $start_time, $end_time, $event_type, $location, $is_virtual, $max_attendees);

        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;

            // Creator automatically attends
            $rsvp_query = "INSERT INTO event_rsvps (event_id, user_id, status) VALUES (?, ?, 'attending')";
            $rsvp_stmt = $this->conn->prepare($rsvp_query);
            $rsvp_stmt->bind_param("ii", $event_id, $creator_id);
            $rsvp_stmt->execute();

            // Notify collaboration members if applicable
            if ($collaboration_id) {
                $notif_query = "INSERT INTO notifications (recipient_user_id, actor_user_id, notification_type, message)
                               SELECT DISTINCT user_id, ?, 'event', ?
                               FROM (
                                   SELECT leader_id as user_id FROM collaborations WHERE id = ?
                                   UNION
                                   SELECT collaborator_id FROM collaborations WHERE id = ?
                               ) members";
                $notif_message = "New event: {$title}";
                $notif_stmt = $this->conn->prepare($notif_query);
                $notif_stmt->bind_param("isii", $creator_id, $notif_message, $collaboration_id, $collaboration_id);
                $notif_stmt->execute();
            }

            return ['success' => true, 'event_id' => $event_id];
        }

        return ['success' => false, 'error' => 'Failed to create event'];
    }

    /**
     * Get events for collaboration
     */
    public function getForCollaboration($collaboration_id, $include_past = false) {
        $time_filter = $include_past ? "" : "AND start_time >= NOW()";

        $query = "SELECT e.*,
                         (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status = 'attending') as attending_count,
                         (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id) as total_rsvps
                  FROM events e
                  WHERE e.collaboration_id = ? AND e.is_cancelled = FALSE {$time_filter}
                  ORDER BY e.start_time ASC";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $collaboration_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get upcoming events
     */
    public function getUpcoming($limit = 10) {
        $query = "SELECT e.*,
                         (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status = 'attending') as attending_count
                  FROM events e
                  WHERE e.is_cancelled = FALSE AND e.start_time >= NOW()
                  ORDER BY e.start_time ASC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get event by ID
     */
    public function getById($event_id) {
        $query = "SELECT e.*,
                         (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id AND status = 'attending') as attending_count,
                         (SELECT COUNT(*) FROM event_rsvps WHERE event_id = e.id) as total_rsvps
                  FROM events e
                  WHERE e.id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update event
     */
    public function update($event_id, $creator_id, $title, $description, $start_time, $end_time, $event_type, $location = null) {
        // Check ownership
        $check_query = "SELECT creator_id FROM events WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $event_id);
        $check_stmt->execute();
        $event = $check_stmt->get_result()->fetch_assoc();

        if (!$event || $event['creator_id'] != $creator_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        if (strtotime($start_time) >= strtotime($end_time)) {
            return ['success' => false, 'error' => 'Start time must be before end time'];
        }

        $query = "UPDATE events SET title = ?, description = ?, start_time = ?, end_time = ?, event_type = ?, location = ?, updated_at = NOW()
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssi", $title, $description, $start_time, $end_time, $event_type, $location, $event_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to update event'];
    }

    /**
     * Cancel event
     */
    public function cancel($event_id, $creator_id) {
        // Check ownership
        $check_query = "SELECT creator_id FROM events WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $event_id);
        $check_stmt->execute();
        $event = $check_stmt->get_result()->fetch_assoc();

        if (!$event || $event['creator_id'] != $creator_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        $query = "UPDATE events SET is_cancelled = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $event_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to cancel event'];
    }

    /**
     * Delete event (hard delete - only for uncancelled events)
     */
    public function delete($event_id, $creator_id) {
        // Check authorization
        $check_query = "SELECT creator_id FROM events WHERE id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param("i", $event_id);
        $check_stmt->execute();
        $event = $check_stmt->get_result()->fetch_assoc();

        if (!$event || $event['creator_id'] != $creator_id) {
            return ['success' => false, 'error' => 'Not authorized'];
        }

        $query = "DELETE FROM events WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $event_id);

        return $stmt->execute()
            ? ['success' => true]
            : ['success' => false, 'error' => 'Failed to delete event'];
    }
}
?>
