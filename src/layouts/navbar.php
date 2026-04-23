<?php
$current_user = getCurrentUser();
$conn = getConnection();
?>
<script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const LOGGED_IN = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
</script>
<nav class="sticky top-0 z-50 bg-white/70 backdrop-blur-2xl border-b border-slate-100/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-12">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3 group">
                <div class="h-11 w-11 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-2xl group-hover:scale-105 transition-all duration-500">
                    <i class="fas fa-bolt text-lg text-primary"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black tracking-tighter text-slate-900 leading-none">IdeaSync</span>
                    <span class="text-[8px] font-black text-primary uppercase tracking-[0.3em] leading-none mt-1">LIET Hub // 2026</span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="<?php echo BASE_URL; ?>/?page=ideas" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-all">Forge</a>
                <?php if ($current_user): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=dashboard" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-all">My Stream</a>
                    <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-all">Talent Pool</a>
                    <a href="<?php echo BASE_URL; ?>/?page=proof-wall" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-all">Proof</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <?php if ($current_user): ?>
                <div class="flex items-center gap-2">
                    <a href="<?php echo BASE_URL; ?>/?page=notifications" class="h-10 w-10 flex items-center justify-center text-slate-400 hover:text-primary relative group transition-colors">
                        <i class="far fa-bell text-lg"></i>
                        <?php
                        $unread_notif = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE recipient_user_id = " . ($current_user['id'] ?? 0) . " AND is_read = 0")->fetch_assoc()['count'] ?? 0;
                        ?>
                        <span id="nav-notif-dot" class="notif-dot absolute top-2.5 right-2.5 h-2 w-2 bg-primary rounded-full ring-2 ring-white <?php echo $unread_notif > 0 ? '' : 'hidden'; ?>"></span>
                    </a>

                    <div class="h-8 w-px bg-slate-100 mx-2"></div>

                    <div class="relative">
                        <button id="userAvatarBtn" class="flex items-center gap-3 focus:outline-none p-1.5 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                            <div class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs overflow-hidden shadow-premium">
                                <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-[10px] font-black text-slate-900 leading-none"><?php echo sanitize(explode(' ', $current_user['name'])[0]); ?></p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"><?php echo sanitize($current_user['academic_role'] ?? 'Builder'); ?></p>
                            </div>
                        </button>

                        <div id="userDropdown" class="hidden absolute right-0 mt-4 w-64 bg-white rounded-3xl shadow-premium border border-slate-100 p-2 z-50 animate-fade-up">
                            <div class="px-4 py-4 border-b border-slate-50 mb-1">
                                <p class="text-xs font-black text-slate-900 truncate"><?php echo sanitize($current_user['email']); ?></p>
                            </div>
                            <div class="space-y-1">
                                <a href="<?php echo BASE_URL; ?>/?page=profile" class="flex items-center gap-3 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 hover:text-primary rounded-2xl transition-all">
                                    <i class="far fa-user-circle text-sm"></i> Profile
                                </a>
                                <?php if ($current_user['is_admin']): ?>
                                    <a href="<?php echo BASE_URL; ?>/?page=admin" class="flex items-center gap-3 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                        <i class="fas fa-user-shield"></i> Admin Panel
                                    </a>
                                <?php endif; ?>
                                <div class="h-px bg-slate-50 my-1"></div>
                                <a href="<?php echo BASE_URL; ?>/src/controllers/auth.php?action=logout" class="flex items-center gap-3 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-secondary hover:bg-red-50 rounded-2xl transition-all">
                                    <i class="fas fa-power-off"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/?page=login" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-primary transition-colors">Login</a>
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-8 !py-3 !text-[10px] !font-black uppercase tracking-widest !rounded-xl shadow-xl shadow-primary/10">Join Forge</a>
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
