<?php
/**
 * Admin Controller
 * Handles admin functions including user management, moderation, and reports
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../models/ContentReport.php';
require_once __DIR__ . '/../helpers/Security.php';

class AdminController {
    private $userModel;
    private $authLog;
    private $activityLog;
    private $contentReport;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
        $this->authLog = new AuthLog($db);
        $this->activityLog = new ActivityLog($db);
        $this->contentReport = new ContentReport($db);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $user_id = $_SESSION['user_id'];
        $query = "SELECT user_type FROM users WHERE id = ? AND user_type = 'admin'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Get all users with filters
     */
    public function getUsers() {
        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        $search = $_GET['search'] ?? $_POST['search'] ?? '';
        $role = $_GET['role'] ?? $_POST['role'] ?? '';
        $status = $_GET['status'] ?? $_POST['status'] ?? '';
        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? $_POST['offset'] ?? 0);

        $limit = min($limit, 100);
        $offset = max($offset, 0);

        $query = "SELECT id, roll_number, name, email, user_type, is_active, is_suspended, created_at
                  FROM users WHERE 1=1";
        $where_params = [];
        $where_types = "";

        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR email LIKE ? OR roll_number LIKE ?)";
            $search_term = "%{$search}%";
            $where_params = [$search_term, $search_term, $search_term];
            $where_types = "sss";
        }

        if (!empty($role)) {
            if ($where_types) {
                $query .= " AND user_type = ?";
                $where_params[] = $role;
                $where_types .= "s";
            } else {
                $query .= " AND user_type = ?";
                $where_params = [$role];
                $where_types = "s";
            }
        }

        if ($status === 'active') {
            $query .= " AND is_active = TRUE AND is_suspended = FALSE";
        } elseif ($status === 'suspended') {
            $query .= " AND is_suspended = TRUE";
        } elseif ($status === 'inactive') {
            $query .= " AND is_active = FALSE";
        }

        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $where_params[] = $limit;
        $where_params[] = $offset;
        $where_types .= "ii";

        $stmt = $this->conn->prepare($query);
        if ($where_params) {
            $stmt->bind_param($where_types, ...$where_params);
        }
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return ['success' => true, 'users' => $users, 'count' => count($users)];
    }

    /**
     * Get user details
     */
    public function getUser() {
        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        $user_id = (int)($_GET['user_id'] ?? $_POST['user_id'] ?? 0);

        if ($user_id === 0) {
            return ['success' => false, 'error' => 'User ID is required'];
        }

        $user = $this->userModel->getById($user_id);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        // Get auth logs
        $auth_logs = $this->authLog->getUserHistory($user_id, 10);

        // Get activity logs
        $activity_logs = $this->activityLog->getUserHistory($user_id, 10);

        return [
            'success' => true,
            'user' => $user,
            'auth_logs' => $auth_logs,
            'activity_logs' => $activity_logs
        ];
    }

    /**
     * Suspend user
     */
    public function suspendUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $admin_id = $_SESSION['user_id'];
        $target_user_id = (int)($_POST['user_id'] ?? 0);
        $duration_days = (int)($_POST['duration_days'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if ($target_user_id === 0) {
            return ['success' => false, 'error' => 'User ID is required'];
        }

        if ($target_user_id === $admin_id) {
            return ['success' => false, 'error' => 'Cannot suspend yourself'];
        }

        $suspended_until = null;
        if ($duration_days > 0) {
            $suspended_until = date('Y-m-d H:i:s', time() + ($duration_days * 24 * 60 * 60));
        }

        $query = "UPDATE users
                  SET is_suspended = TRUE, suspended_until = ?, suspension_reason = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("ssi", $suspended_until, $reason, $target_user_id);

        if ($stmt->execute()) {
            // Log action
            $this->authLog->log($admin_id, 'user_suspended', true, ['target_user_id' => $target_user_id, 'reason' => $reason]);
            return ['success' => true, 'message' => 'User suspended successfully'];
        }

        return ['success' => false, 'error' => 'Failed to suspend user'];
    }

    /**
     * Unsuspend user
     */
    public function unsuspendUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $admin_id = $_SESSION['user_id'];
        $target_user_id = (int)($_POST['user_id'] ?? 0);

        if ($target_user_id === 0) {
            return ['success' => false, 'error' => 'User ID is required'];
        }

        $query = "UPDATE users
                  SET is_suspended = FALSE, suspended_until = NULL, suspension_reason = NULL
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("i", $target_user_id);

        if ($stmt->execute()) {
            // Log action
            $this->authLog->log($admin_id, 'user_unsuspended', true, ['target_user_id' => $target_user_id]);
            return ['success' => true, 'message' => 'User unsuspended successfully'];
        }

        return ['success' => false, 'error' => 'Failed to unsuspend user'];
    }

    /**
     * Deactivate user
     */
    public function deactivateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $admin_id = $_SESSION['user_id'];
        $target_user_id = (int)($_POST['user_id'] ?? 0);

        if ($target_user_id === 0) {
            return ['success' => false, 'error' => 'User ID is required'];
        }

        if ($target_user_id === $admin_id) {
            return ['success' => false, 'error' => 'Cannot deactivate yourself'];
        }

        $query = "UPDATE users SET is_active = FALSE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $target_user_id);

        if ($stmt->execute()) {
            // Log action
            $this->authLog->log($admin_id, 'user_deactivated', true, ['target_user_id' => $target_user_id]);
            return ['success' => true, 'message' => 'User deactivated successfully'];
        }

        return ['success' => false, 'error' => 'Failed to deactivate user'];
    }

    /**
     * Reactivate user
     */
    public function reactivateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $admin_id = $_SESSION['user_id'];
        $target_user_id = (int)($_POST['user_id'] ?? 0);

        if ($target_user_id === 0) {
            return ['success' => false, 'error' => 'User ID is required'];
        }

        $query = "UPDATE users SET is_active = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $target_user_id);

        if ($stmt->execute()) {
            // Log action
            $this->authLog->log($admin_id, 'user_reactivated', true, ['target_user_id' => $target_user_id]);
            return ['success' => true, 'message' => 'User reactivated successfully'];
        }

        return ['success' => false, 'error' => 'Failed to reactivate user'];
    }

    /**
     * Get pending content reports
     */
    public function getReports() {
        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? $_POST['offset'] ?? 0);
        $status = $_GET['status'] ?? $_POST['status'] ?? 'pending';

        $limit = min($limit, 100);
        $offset = max($offset, 0);

        $query = "SELECT cr.*, u.name as reporter_name
                  FROM content_reports cr
                  LEFT JOIN users u ON cr.reporter_id = u.id
                  WHERE cr.status = ?
                  ORDER BY cr.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error'];
        }

        $stmt->bind_param("sii", $status, $limit, $offset);
        $stmt->execute();
        $reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return ['success' => true, 'reports' => $reports, 'count' => count($reports)];
    }

    /**
     * Update report status
     */
    public function updateReportStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'error' => 'Invalid request method'];
        }

        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // Verify CSRF token
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $admin_id = $_SESSION['user_id'];
        $report_id = (int)($_POST['report_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $admin_notes = trim($_POST['admin_notes'] ?? '');

        if ($report_id === 0) {
            return ['success' => false, 'error' => 'Report ID is required'];
        }

        return $this->contentReport->updateStatus($report_id, $status, $admin_id, $admin_notes);
    }

    /**
     * Get audit trail
     */
    public function getAuditTrail() {
        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 100);
        $offset = (int)($_GET['offset'] ?? $_POST['offset'] ?? 0);

        $limit = min($limit, 100);
        $offset = max($offset, 0);

        return ['success' => true, 'logs' => $this->authLog->getAuditTrail($limit, $offset)];
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats() {
        if (!$this->isAdmin()) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }

        // User stats
        $user_query = "SELECT
                            COUNT(*) as total_users,
                            SUM(CASE WHEN is_active = TRUE THEN 1 ELSE 0 END) as active_users,
                            SUM(CASE WHEN is_suspended = TRUE THEN 1 ELSE 0 END) as suspended_users,
                            SUM(CASE WHEN email_verified = TRUE THEN 1 ELSE 0 END) as verified_users
                       FROM users";
        $user_stmt = $this->conn->prepare($user_query);
        $user_stmt->execute();
        $user_stats = $user_stmt->get_result()->fetch_assoc();

        // Report stats
        $report_stats = $this->contentReport->getStats();

        // Recent activity
        $recent_activity = $this->activityLog->getRecentActivity(10);

        // Failed logins in last 24 hours
        $failed_login_query = "SELECT COUNT(*) as count FROM auth_logs
                               WHERE success = FALSE AND action = 'login_failure'
                               AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $failed_stmt = $this->conn->prepare($failed_login_query);
        $failed_stmt->execute();
        $failed_result = $failed_stmt->get_result()->fetch_assoc();

        return [
            'success' => true,
            'user_stats' => $user_stats,
            'report_stats' => $report_stats,
            'failed_logins_24h' => $failed_result['count'] ?? 0,
            'recent_activity' => $recent_activity
        ];
    }
}

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Initialize database and controller
$db = new Database();
$conn = $db->connect();
$admin = new AdminController($conn);

// Return JSON for AJAX requests
header('Content-Type: application/json');

// Route to appropriate method
if ($action === 'getUsers') {
    echo json_encode($admin->getUsers());
} elseif ($action === 'getUser') {
    echo json_encode($admin->getUser());
} elseif ($action === 'suspendUser') {
    echo json_encode($admin->suspendUser());
} elseif ($action === 'unsuspendUser') {
    echo json_encode($admin->unsuspendUser());
} elseif ($action === 'deactivateUser') {
    echo json_encode($admin->deactivateUser());
} elseif ($action === 'reactivateUser') {
    echo json_encode($admin->reactivateUser());
} elseif ($action === 'getReports') {
    echo json_encode($admin->getReports());
} elseif ($action === 'updateReportStatus') {
    echo json_encode($admin->updateReportStatus());
} elseif ($action === 'getAuditTrail') {
    echo json_encode($admin->getAuditTrail());
} elseif ($action === 'getDashboardStats') {
    echo json_encode($admin->getDashboardStats());
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
exit();
?>
