<footer class="bg-slate-50 text-slate-500 py-24 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16">
            <div class="md:col-span-4">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center gap-3 mb-6 group">
                    <div class="h-8 w-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-subtle group-hover:scale-105 transition-transform">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <span class="text-base font-bold tracking-tight text-slate-900 uppercase">IdeaSync</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs mb-8 font-medium">
                    The central collaboration portal for Lendi Institute. Connecting technical builders with visionary innovation tracks.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-primary hover:border-primary transition-all shadow-subtle"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-primary hover:border-primary transition-all shadow-subtle"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-primary hover:border-primary transition-all shadow-subtle"><i class="fas fa-globe"></i></a>
                </div>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-slate-900 text-xs font-bold uppercase tracking-widest mb-6">Platform</h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="#" class="hover:text-primary transition-colors">Innovation Tracks</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Talent Hunt</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Project Wall</a></li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-slate-900 text-xs font-bold uppercase tracking-widest mb-6">Resources</h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="#" class="hover:text-primary transition-colors">IIC Guidelines</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Documentation</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Academic Credits</a></li>
                </ul>
            </div>
            <div class="md:col-span-4">
                <h4 class="text-slate-900 text-xs font-bold uppercase tracking-widest mb-6">IIC Updates</h4>
                <p class="text-sm mb-6 font-medium">Stay notified about upcoming Talent Hunts and Innovation Workshops.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="student@lendi.edu.in" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary w-full text-slate-900 shadow-inner">
                    <button type="button" class="btn-primary !px-5 !py-2.5 text-xs">Notify Me</button>
                </form>
            </div>
        </div>
        <div class="mt-24 pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-6 text-xs font-bold uppercase tracking-widest">
            <p>&copy; <?php echo date('Y'); ?> Lendi Innovation Council Cell. Built for Excellence.</p>
            <div class="flex gap-8">
                <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-primary transition-colors">Student Terms</a>
            </div>
        </div>
    </div>
</footer>
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>
</html>
