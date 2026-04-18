<?php
$current_user = getCurrentUser();
?>
<nav class="sticky top-0 z-50 bg-background/80 backdrop-blur-md border-b border-white/5">
    <div class="max-w-screen-xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-10">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-2 group">
                <div class="h-8 w-8 rounded-lg bg-white flex items-center justify-center text-black">
                    <i class="fas fa-terminal text-sm"></i>
                </div>
                <span class="text-sm font-bold tracking-tight text-white uppercase">IdeaSync</span>
            </a>

            <div class="hidden md:flex items-center gap-6">
                <?php if ($current_user): ?>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Projects</a>
                    <a href="<?php echo BASE_URL; ?>/?page=agents" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Agents</a>
                    <a href="<?php echo BASE_URL; ?>/?page=workflow" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Workflow</a>
                    <a href="<?php echo BASE_URL; ?>/?page=leaderboard" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Leaderboard</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Projects</a>
                    <a href="#features" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Features</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <?php if ($current_user): ?>
                <div class="hidden md:flex items-center gap-4 mr-2">
                     <a href="<?php echo BASE_URL; ?>/?page=notifications" class="text-zinc-500 hover:text-white transition-colors">
                        <i class="far fa-bell"></i>
                     </a>
                     <a href="<?php echo BASE_URL; ?>/?page=messages" class="text-zinc-500 hover:text-white transition-colors">
                        <i class="far fa-envelope"></i>
                     </a>
                </div>

                <div class="relative">
                    <button id="userAvatarBtn" class="flex items-center gap-2 focus:outline-none py-1">
                        <div class="h-8 w-8 rounded-full bg-zinc-800 border border-white/10 flex items-center justify-center text-zinc-300 font-medium text-xs hover:border-white/30 transition-all overflow-hidden">
                            <?php echo ($current_user ? strtoupper(substr($current_user['name'], 0, 1)) : 'U'); ?>
                        </div>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-zinc-900 rounded-xl shadow-2xl border border-white/10 py-2 focus:outline-none z-50">
                        <div class="px-4 py-3 border-b border-white/5">
                            <p class="text-[10px] text-zinc-500 font-semibold uppercase tracking-widest mb-0.5">Account</p>
                            <p class="text-xs font-medium text-white truncate"><?php echo sanitize($current_user['name']); ?></p>
                        </div>
                        <div class="p-1.5 space-y-0.5">
                            <a href="<?php echo BASE_URL; ?>/?page=profile" class="flex items-center gap-3 px-3 py-2 text-xs text-zinc-400 hover:bg-white/5 hover:text-white rounded-lg transition-all">
                                <i class="far fa-user w-4"></i> Profile
                            </a>
                            <a href="<?php echo BASE_URL; ?>/?page=profile-collaborations" class="flex items-center gap-3 px-3 py-2 text-xs text-zinc-400 hover:bg-white/5 hover:text-white rounded-lg transition-all">
                                <i class="far fa-handshake w-4"></i> Collaborations
                            </a>
                            <div class="h-px bg-white/5 my-1.5 mx-2"></div>
                            <a href="<?php echo BASE_URL; ?>/?page=logout" class="flex items-center gap-3 px-3 py-2 text-xs text-red-400 hover:bg-red-500/10 rounded-lg transition-all">
                                <i class="fas fa-sign-out-alt w-4"></i> Sign out
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/?page=login" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">Login</a>
                <a href="<?php echo BASE_URL; ?>/?page=register" class="btn-primary !px-4 !py-1.5 text-xs">Join</a>
            <?php endif; ?>

            <button type="button" class="md:hidden p-2 text-zinc-400" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-zinc-950 border-t border-white/5 p-4 space-y-1">
         <?php if ($current_user): ?>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="block px-4 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:bg-white/5">Explore Ideas</a>
            <a href="<?php echo BASE_URL; ?>/?page=agents" class="block px-4 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:bg-white/5">Agents</a>
            <a href="<?php echo BASE_URL; ?>/?page=workflow" class="block px-4 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:bg-white/5">Workflow</a>
            <a href="<?php echo BASE_URL; ?>/?page=logout" class="block px-4 py-3 rounded-lg text-sm font-medium text-red-400 hover:bg-red-500/10">Sign Out</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="block px-4 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:bg-white/5">Projects</a>
            <a href="<?php echo BASE_URL; ?>/?page=login" class="block px-4 py-3 rounded-lg text-sm font-medium text-zinc-300 hover:bg-white/5">Login</a>
            <a href="<?php echo BASE_URL; ?>/?page=register" class="block px-4 py-3 rounded-lg text-sm font-medium text-white bg-white/10 mt-2">Join Now</a>
        <?php endif; ?>
    </div>
</nav>
