<?php
$current_user = getCurrentUser();
$conn = getConnection();
?>
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3 group">
                <div class="h-10 w-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-subtle group-hover:scale-105 transition-transform">
                    <i class="fas fa-sync text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold tracking-tight text-slate-900 leading-tight">IdeaSync</span>
                    <span class="text-[10px] font-semibold text-primary uppercase tracking-widest leading-none">Lendi Institute</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-primary transition-colors">Forge</a>
                <?php if ($current_user): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-primary transition-colors">My Feed</a>
                    <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-primary transition-colors">Talent Board</a>
                    <a href="<?php echo BASE_URL; ?>/?page=proof-wall" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-primary transition-colors">Wall of Proof</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <?php if ($current_user): ?>
                <div class="relative">
<a href="<?php echo BASE_URL; ?>/?page=notifications" class="p-2 text-slate-400 hover:text-primary relative group">
                    <i class="far fa-bell text-lg"></i>
                    <?php
                    $unread_notif = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE recipient_user_id = " . ($current_user['id'] ?? 0) . " AND is_read = 0")->fetch_assoc()['count'] ?? 0;
                    if ($unread_notif > 0): ?>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-secondary rounded-full ring-2 ring-white"></span>
                    <?php endif; ?>
                </a>
                    <button id="userAvatarBtn" class="flex items-center gap-3 focus:outline-none p-1 rounded-full hover:bg-slate-50 transition-colors">
                        <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-primary font-bold text-xs overflow-hidden shadow-inner">
                            <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
                        </div>
                        <span class="hidden sm:block text-xs font-semibold text-slate-700"><?php echo sanitize($current_user['name']); ?></span>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-premium border border-slate-100 py-2 z-50 animate-fade-up">
                        <div class="px-4 py-3 border-b border-slate-50">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-0.5"><?php echo sanitize($current_user['academic_role'] ?? 'Builder'); ?></p>
                            <p class="text-xs font-bold text-slate-900 truncate"><?php echo sanitize($current_user['email']); ?></p>
                        </div>
                        <div class="p-2 space-y-0.5">
                            <a href="<?php echo BASE_URL; ?>/?page=profile" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-primary rounded-xl transition-all">
                                <i class="far fa-user-circle text-base"></i> Academic Profile
                            </a>
                            <a href="<?php echo BASE_URL; ?>/?page=profile-edit" class="flex items-center gap-3 px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-primary rounded-xl transition-all">
                                <i class="fas fa-user-edit text-base"></i> Edit Profile
                            </a>
                            <?php if ($current_user['is_admin']): ?>
                                <div class="h-px bg-slate-50 my-1 mx-2"></div>
                                <a href="<?php echo BASE_URL; ?>/?page=admin" class="flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-primary hover:bg-primary/5 rounded-xl transition-all">
                                    <i class="fas fa-user-shield"></i> Admin Dashboard
                                </a>
                            <?php endif; ?>
                            <div class="h-px bg-slate-50 my-1 mx-2"></div>
                            <a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout" class="flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-secondary hover:bg-red-50 rounded-xl transition-all">
                                <i class="fas fa-power-off"></i> Secure Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/?page=login" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Login</a>
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-5 !py-2 text-xs">Join IdeaSync</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.getElementById('userAvatarBtn')?.addEventListener('click', function() {
    document.getElementById('userDropdown')?.classList.toggle('hidden');
});

window.addEventListener('click', function(e) {
    if (!document.getElementById('userAvatarBtn')?.contains(e.target)) {
        document.getElementById('userDropdown')?.classList.add('hidden');
    }
});
</script>
