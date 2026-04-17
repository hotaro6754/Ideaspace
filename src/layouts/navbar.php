<?php
$current_user = getCurrentUser();
?>
<nav class="sticky top-0 z-50 bg-background/80 backdrop-blur-xl border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3 group">
                    <div class="bg-primary p-2 rounded-xl group-hover:scale-110 transition-all shadow-lg shadow-primary/20">
                        <i class="fas fa-lightbulb text-background text-lg"></i>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tighter">IdeaSync</span>
                </a>

                <div class="hidden md:ml-12 md:flex md:space-x-8">
                    <?php if ($current_user): ?>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Explore</a>
                        <a href="<?php echo BASE_URL; ?>/?page=agents" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Agents</a>
                        <a href="<?php echo BASE_URL; ?>/?page=workflow" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Workflow</a>
                        <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Leaderboard</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Projects</a>
                        <a href="#features" class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.2em]">Features</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <?php if ($current_user): ?>
                    <div class="hidden md:flex items-center gap-6">
                         <a href="<?php echo BASE_URL; ?>/?page=notifications" class="text-slate-500 hover:text-primary transition-colors">
                            <i class="fas fa-bell"></i>
                         </a>
                         <a href="<?php echo BASE_URL; ?>/?page=messages" class="text-slate-500 hover:text-primary transition-colors">
                            <i class="fas fa-envelope"></i>
                         </a>
                    </div>

                    <div class="relative ml-3">
                        <button id="userAvatarBtn" class="flex items-center gap-2 focus:outline-none group">
                            <div class="h-10 w-10 rounded-xl bg-surface-container-high border border-white/10 flex items-center justify-center text-white font-bold text-sm group-hover:border-primary/50 transition-all">
                                <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
                            </div>
                        </button>

                        <div id="userDropdown" class="hidden absolute right-0 mt-4 w-56 bg-surface-container-low rounded-2xl shadow-2xl border border-white/5 py-2 ring-1 ring-black ring-opacity-5 focus:outline-none z-[100]">
                            <div class="px-6 py-4 border-b border-white/5">
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Authenticated as</p>
                                <p class="text-sm font-bold text-white truncate"><?php echo sanitize($current_user['name']); ?></p>
                            </div>
                            <div class="p-2 space-y-1">
                                <a href="<?php echo BASE_URL; ?>/?page=profile" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-400 hover:bg-surface-container-high hover:text-white rounded-xl transition-all">
                                    <i class="fas fa-user-circle w-4"></i> Profile
                                </a>
                                <a href="<?php echo BASE_URL; ?>/?page=profile-collaborations" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-400 hover:bg-surface-container-high hover:text-white rounded-xl transition-all">
                                    <i class="fas fa-handshake w-4"></i> Collaborations
                                </a>
                                <div class="h-px bg-white/5 mx-4 my-2"></div>
                                <a href="<?php echo BASE_URL; ?>/?page=logout" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 rounded-xl transition-all font-bold">
                                    <i class="fas fa-sign-out-alt w-4"></i> Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/?page=login" class="text-xs font-bold text-slate-400 hover:text-white transition-colors uppercase tracking-widest">Login</a>
                    <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-5 !py-2.5 text-xs">Join Network</a>
                <?php endif; ?>

                <button type="button" class="md:hidden p-2 rounded-xl text-slate-500 hover:text-primary transition-colors" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-surface-container-low border-t border-white/5 px-4 pt-4 pb-10 space-y-1 shadow-2xl">
         <?php if ($current_user): ?>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="block px-4 py-4 rounded-xl text-base font-bold text-slate-300 hover:bg-surface-container-high">Explore Ideas</a>
            <a href="<?php echo BASE_URL; ?>/?page=agents" class="block px-4 py-4 rounded-xl text-base font-bold text-slate-300 hover:bg-surface-container-high">Agent Intel</a>
            <a href="<?php echo BASE_URL; ?>/?page=workflow" class="block px-4 py-4 rounded-xl text-base font-bold text-slate-300 hover:bg-surface-container-high">Build Workflow</a>
            <a href="<?php echo BASE_URL; ?>/?page=logout" class="block px-4 py-4 rounded-xl text-base font-bold text-red-400 hover:bg-red-500/10">Sign Out</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="block px-4 py-4 rounded-xl text-base font-bold text-slate-300 hover:bg-surface-container-high">Browse Projects</a>
            <a href="<?php echo BASE_URL; ?>/?page=login" class="block px-4 py-4 rounded-xl text-base font-bold text-slate-300 hover:bg-surface-container-high">Login</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    }

    // User dropdown logic
    const avatarBtn = document.getElementById('userAvatarBtn');
    const dropdown = document.getElementById('userDropdown');

    if (avatarBtn && dropdown) {
        avatarBtn.onclick = (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        };

        window.onclick = (e) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        };
    }
</script>
