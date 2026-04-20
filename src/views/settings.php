<?php
if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');
$user_id = $_SESSION['user_id'];
$db = getConnection();
ob_start();
?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-12">
        <h1 class="text-3xl font-extrabold text-slate-900">Account Settings</h1>
        <p class="text-slate-500 font-medium">Manage your security and notification preferences.</p>
    </div>

    <div class="space-y-8">
        <!-- Password Change -->
        <div class="premium-card p-8 bg-white">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-lock text-primary"></i> Change Password
            </h3>
            <form action="<?php echo BASE_URL; ?>/src/controllers/settings.php?action=changePassword" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="password" name="current_password" required class="form-input" placeholder="Current Password">
                    <input type="password" name="new_password" required class="form-input" placeholder="New Password">
                    <input type="password" name="new_password_confirm" required class="form-input" placeholder="Confirm New Password">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary !py-2 !px-6 !text-xs">Update Password</button>
                </div>
            </form>
        </div>

        <!-- Notification Preferences -->
        <div class="premium-card p-8 bg-white">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-bell text-primary"></i> Notifications
            </h3>
            <form action="<?php echo BASE_URL; ?>/src/controllers/settings.php?action=updateNotifications" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Email Notifications</p>
                            <p class="text-xs text-slate-500">Receive major updates via your academic email.</p>
                        </div>
                        <input type="checkbox" name="email_notifications" checked class="rounded border-slate-300 text-primary focus:ring-primary">
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Collaboration Requests</p>
                            <p class="text-xs text-slate-500">Notify me when someone applies to my track.</p>
                        </div>
                        <input type="checkbox" name="email_on_application" checked class="rounded border-slate-300 text-primary focus:ring-primary">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary !py-2 !px-6 !text-xs">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
