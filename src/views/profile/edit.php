<?php
if (!isset($_SESSION['user_id'])) redirect(BASE_URL . '/?page=login');
$current_user_id = $_SESSION['user_id'];
$db = getConnection();
$userModel = new User($db);
$user = $userModel->getById($current_user_id);
ob_start();
?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Edit Profile</h1>
        <p class="text-slate-500 font-medium">Update your academic and professional presence.</p>
    </div>

    <form action="<?php echo BASE_URL; ?>/src/controllers/settings.php?action=update_profile" method="POST" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

        <div class="premium-card p-8 bg-white">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" value="<?php echo Security::escape($user['name']); ?>" required>
                </div>
                <div>
                    <label class="form-label">GitHub Username</label>
                    <input type="text" name="github_username" class="form-input" value="<?php echo Security::escape($user['github_username'] ?? ''); ?>" placeholder="octocat">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-input h-32" placeholder="Tell us about your expertise and interests..."><?php echo Security::escape($user['bio'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="premium-card p-8 bg-white">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Academic Role</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Role</label>
                    <select name="academic_role" class="form-select">
                        <option value="builder" <?php echo ($user['academic_role'] == 'builder') ? 'selected' : ''; ?>>Builder (Developer/Designer)</option>
                        <option value="visionary" <?php echo ($user['academic_role'] == 'visionary') ? 'selected' : ''; ?>>Visionary (Idea Generator)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Interests (Comma separated)</label>
                    <input type="text" name="interests" class="form-input" value="<?php echo Security::escape($user['interests'] ?? ''); ?>" placeholder="AI, Web Dev, Blockchain">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="<?php echo BASE_URL; ?>/?page=profile" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-primary px-12">Save Changes</button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
