<footer class="bg-background text-zinc-500 py-24 border-t border-white/5">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16">
            <div class="md:col-span-4">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-2 mb-6">
                    <div class="h-6 w-6 rounded bg-white flex items-center justify-center text-black">
                        <i class="fas fa-terminal text-[10px]"></i>
                    </div>
                    <span class="text-sm font-bold tracking-tight text-white uppercase">IdeaSync</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs mb-8">
                    The precision platform for high-impact campus collaborations. Vetted builders. Production-ready ideas.
                </p>
                <div class="flex gap-5">
                    <a href="#" class="text-zinc-500 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-zinc-500 hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                    <a href="#" class="text-zinc-500 hover:text-white transition-colors"><i class="fab fa-discord"></i></a>
                </div>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-white text-xs font-semibold mb-6">Platform</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Explore</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Builders</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Proof Wall</a></li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-white text-xs font-semibold mb-6">Resources</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Workflow</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                </ul>
            </div>
            <div class="md:col-span-4">
                <h4 class="text-white text-xs font-semibold mb-6">Newsletter</h4>
                <p class="text-sm mb-4">Stay updated with the latest projects.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="you@university.edu" class="bg-white/5 border border-white/10 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:border-white/20 w-full text-white">
                    <button type="button" class="btn-primary !px-4 !py-1.5 text-xs">Join</button>
                </form>
            </div>
        </div>
        <div class="mt-24 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 text-xs font-medium">
            <p>&copy; <?php echo date('Y'); ?> IdeaSync. All rights reserved.</p>
            <div class="flex gap-8">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
