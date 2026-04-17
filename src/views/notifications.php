<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Notifications</h1>
            <p class="text-slate-500 mt-1">Stay updated with your campus projects.</p>
        </div>
        <button class="text-sm font-bold text-accent-600 hover:text-accent-500 transition-colors bg-accent-50 px-4 py-2 rounded-xl border border-accent-100">Mark all as read</button>
    </div>

    <div class="space-y-4">
        <?php
        $notifications = [
            ['type' => 'upvote', 'user' => 'Rahul Verma', 'project' => 'Campus AI Assistant', 'time' => '2 hours ago', 'icon' => 'fa-arrow-up', 'color' => 'accent'],
            ['type' => 'comment', 'user' => 'Sneha Kapur', 'project' => 'Smart Parking System', 'time' => '5 hours ago', 'icon' => 'fa-comment', 'color' => 'blue'],
            ['type' => 'accept', 'user' => 'Team Alpha', 'project' => 'Health Tracking App', 'time' => 'Yesterday', 'icon' => 'fa-check-circle', 'color' => 'green'],
            ['type' => 'event', 'user' => 'Campus Hub', 'project' => 'Hackathon 2024', 'time' => '2 days ago', 'icon' => 'fa-calendar-star', 'color' => 'purple'],
            ['type' => 'mention', 'user' => 'Ishaan Shah', 'project' => 'AI Resume Optimizer', 'time' => '3 days ago', 'icon' => 'fa-at', 'color' => 'amber'],
        ];
        foreach($notifications as $notif):
        ?>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all group cursor-pointer relative overflow-hidden">
            <div class="flex gap-5">
                <div class="h-12 w-12 rounded-2xl bg-<?php echo $notif['color']; ?>-50 text-<?php echo $notif['color']; ?>-600 flex-shrink-0 flex items-center justify-center text-xl ring-1 ring-<?php echo $notif['color']; ?>-100 group-hover:scale-110 transition-transform">
                    <i class="fas <?php echo $notif['icon']; ?>"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-slate-900 leading-tight">
                            <span class="font-bold"><?php echo $notif['user']; ?></span>
                            <?php
                            echo [
                                'upvote' => 'upvoted your project',
                                'comment' => 'commented on',
                                'accept' => 'accepted your request for',
                                'event' => 'posted a new event for',
                                'mention' => 'mentioned you in'
                            ][$notif['type']];
                            ?>
                            <span class="font-bold text-accent-600">"<?php echo $notif['project']; ?>"</span>
                        </p>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4 flex-shrink-0"><?php echo $notif['time']; ?></span>
                    </div>
                    <?php if($notif['type'] == 'comment'): ?>
                        <p class="text-xs text-slate-500 mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100 italic">"I'd love to help with the UI design of this project. Let's connect!"</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Indicator for unread -->
            <?php if(rand(0,1)): ?>
                <div class="absolute top-4 right-4 h-2 w-2 bg-accent-600 rounded-full"></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-12 text-center">
        <button class="px-8 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all text-sm">Load older notifications</button>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
