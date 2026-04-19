<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');

// Featured Tracks for Lendi IIC
$tracks = [
    ['id' => 1, 'title' => 'AI Student Monitoring', 'domain' => 'AI/ML', 'status' => 'Phase 1', 'icon' => 'fa-microchip', 'color' => 'primary'],
    ['id' => 2, 'title' => 'Smart Attendance', 'domain' => 'IoT/PHP', 'status' => 'Testing', 'icon' => 'fa-fingerprint', 'color' => 'secondary'],
    ['id' => 3, 'title' => 'Resource Scheduler', 'domain' => 'Algorithms', 'status' => 'In Design', 'icon' => 'fa-calendar-check', 'color' => 'green-600']
];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 animate-fade-up">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-[10px] font-bold uppercase tracking-widest mb-4">
                <i class="fas fa-shield-alt"></i> Verified Student Session
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                Welcome, <?php echo sanitize(explode(' ', $user['name'])[0]); ?>
            </h1>
            <p class="mt-2 text-slate-500 font-medium"><?php echo sanitize($user['branch']); ?> Dept • <?php echo sanitize($user['year']); ?>nd Year • Roll: <?php echo sanitize($user['roll_number']); ?></p>
        </div>
        <div class="flex items-center gap-4">
             <div class="px-5 py-3 rounded-2xl bg-white border border-slate-100 shadow-subtle flex items-center gap-4">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Builder Rank</span>
                    <span class="text-sm font-black text-primary uppercase">Contributor</span>
                </div>
                <div class="h-8 w-[1px] bg-slate-100"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">IIC Points</span>
                    <span class="text-sm font-black text-slate-900">120</span>
                </div>
             </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16 animate-fade-up">
        <div class="premium-card p-6 border-l-4 border-l-primary">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Active Projects</p>
            <h3 class="text-2xl font-black text-slate-900">02</h3>
        </div>
        <div class="premium-card p-6 border-l-4 border-l-secondary">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Collaborations</p>
            <h3 class="text-2xl font-black text-slate-900">05</h3>
        </div>
        <div class="premium-card p-6 border-l-4 border-l-green-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Impact Score</p>
            <h3 class="text-2xl font-black text-slate-900">88%</h3>
        </div>
        <div class="premium-card p-6 border-l-4 border-l-amber-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">IIC Rank</p>
            <h3 class="text-2xl font-black text-slate-900">#42</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Center/Left: Innovation Pipeline -->
        <div class="lg:col-span-2 space-y-12 animate-fade-up">
            <section>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Innovation Pipeline</h2>
                    <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-bold text-primary uppercase tracking-widest hover:underline">View All Tracks</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach($tracks as $track): ?>
                    <div class="premium-card p-8 group">
                        <div class="flex items-start justify-between mb-6">
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-<?php echo $track['color']; ?> text-xl group-hover:scale-110 transition-transform shadow-inner">
                                <i class="fas <?php echo $track['icon']; ?>"></i>
                            </div>
                            <span class="badge badge-primary"><?php echo $track['status']; ?></span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors"><?php echo $track['title']; ?></h3>
                        <p class="text-sm text-slate-500 font-medium mb-6">Core innovation track for <?php echo $track['domain']; ?> department development.</p>
                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <div class="flex -space-x-2">
                                <div class="h-7 w-7 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[8px] font-bold uppercase text-slate-400">JD</div>
                                <div class="h-7 w-7 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[8px] font-bold uppercase text-slate-400">RS</div>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/?page=ideas&id=<?php echo $track['id']; ?>" class="text-[10px] font-black uppercase tracking-widest text-slate-900 group-hover:translate-x-1 transition-transform">
                                Explore <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <!-- Sidebar: IIC Activity -->
        <div class="space-y-12 animate-fade-up">
            <!-- IIC Events -->
            <section>
                <h3 class="text-sm font-black text-slate-900 mb-6 uppercase tracking-[0.2em]">Upcoming IIC Events</h3>
                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-subtle hover:border-primary/20 transition-all cursor-pointer"><form action="<?php echo BASE_URL; ?>/src/controllers/events.php?action=rsvp" method="POST" class="absolute inset-0 opacity-0"><input type="hidden" name="event_id" value="1"><button type="submit" class="w-full h-full cursor-pointer"></button></form>
                        <div class="flex gap-4 items-center">
                            <div class="h-10 w-10 rounded-xl bg-secondary/5 flex flex-col items-center justify-center text-secondary">
                                <span class="text-xs font-black leading-none">22</span>
                                <span class="text-[8px] font-bold uppercase">Apr</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Talent Hunt Day</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Main Auditorium • 09:00 AM</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-subtle hover:border-primary/20 transition-all cursor-pointer"><form action="<?php echo BASE_URL; ?>/src/controllers/events.php?action=rsvp" method="POST" class="absolute inset-0 opacity-0"><input type="hidden" name="event_id" value="1"><button type="submit" class="w-full h-full cursor-pointer"></button></form>
                        <div class="flex gap-4 items-center">
                            <div class="h-10 w-10 rounded-xl bg-primary/5 flex flex-col items-center justify-center text-primary">
                                <span class="text-xs font-black leading-none">15</span>
                                <span class="text-[8px] font-bold uppercase">May</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Innovation Workshop</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">AI Lab 4 • 02:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Community Hub -->
            <section class="premium-card p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
                <h3 class="text-lg font-bold mb-4 relative z-10">Collaboration Hub</h3>
                <p class="text-white/60 text-xs font-medium mb-6 relative z-10">Connect with other builders in real-time to discuss tracks and IIC initiatives.</p>
                <a href="<?php echo BASE_URL; ?>/?page=messages" class="btn-primary !w-full !py-3 !text-xs !bg-white !text-slate-900">
                    Open Messages
                </a>
            </section>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
