<?php
ob_start();
$user = getCurrentUser();
// Mock recommendations for high-end look
$recommendations = [
    ['id' => 1, 'title' => 'Campus AI Study Buddy', 'match_percentage' => 94, 'creator_name' => 'Aryan Sharma', 'creator_rank' => 'Elite', 'total_upvotes' => 156, 'applicant_count' => 12, 'domain' => 'AI / ML'],
    ['id' => 2, 'title' => 'Decentralized P2P Loans', 'match_percentage' => 88, 'creator_name' => 'Ravi Kumar', 'creator_rank' => 'Builder', 'total_upvotes' => 89, 'applicant_count' => 5, 'domain' => 'Fintech'],
    ['id' => 3, 'title' => 'Smart Attendance System', 'match_percentage' => 82, 'creator_name' => 'Priya Das', 'creator_rank' => 'Legend', 'total_upvotes' => 210, 'applicant_count' => 18, 'domain' => 'Engineering']
];
?>

<div class="max-w-screen-xl mx-auto px-6 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16 animate-fade-in">
        <div>
            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] mb-3">Builder Dashboard</p>
            <h1 class="text-4xl font-bold text-white tracking-tight">Welcome back, <?php echo $user ? explode(' ', $user['name'])[0] : 'Builder'; ?></h1>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 flex items-center gap-3">
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Builder Rank</span>
                <span class="text-xs font-bold text-white">#12 Global</span>
             </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="mb-20 animate-fade-up animate-delay-100">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-semibold text-white">Recommended for you</h2>
            <a href="<?php echo BASE_URL; ?>/?page=ideas" class="text-xs font-medium text-zinc-400 hover:text-white transition-colors">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach($recommendations as $idea): ?>
            <div class="premium-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                            <?php echo $idea['domain']; ?>
                        </span>
                        <span class="text-xs font-bold text-white"><?php echo $idea['match_percentage']; ?>% Match</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2"><?php echo $idea['title']; ?></h3>
                    <p class="text-xs text-zinc-500 mb-6">By <?php echo $idea['creator_name']; ?> • <?php echo $idea['creator_rank']; ?></p>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <a href="<?php echo BASE_URL; ?>/?page=idea-detail&id=<?php echo $idea['id']; ?>" class="btn-primary w-full !text-xs !py-2">Apply Now</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
        <!-- Left: Collaborations -->
        <div class="md:col-span-8 animate-fade-up animate-delay-200">
            <h2 class="text-xl font-semibold text-white mb-8">Active Collaborations</h2>
            <div class="space-y-4">
                <?php for($i=0; $i<2; $i++): ?>
                <div class="premium-card p-6 flex items-center justify-between group">
                    <div class="flex items-center gap-6">
                        <div class="h-12 w-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white">
                            <i class="fas fa-code text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white mb-1">AI-Powered Study Platform</h4>
                            <p class="text-xs text-zinc-500">3 members • 12 commits this week</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="px-2 py-0.5 rounded bg-zinc-800 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Active</span>
                        <i class="fas fa-chevron-right text-zinc-700 group-hover:text-white transition-colors text-xs"></i>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <div class="mt-12 p-8 rounded-2xl bg-white/[0.02] border border-white/5 text-center">
                <p class="text-sm text-zinc-500 mb-6">You have 2 pending applications waiting for review.</p>
                <a href="<?php echo BASE_URL; ?>/?page=profile&action=applications" class="text-xs font-bold text-white uppercase tracking-widest hover:underline underline-offset-4">View Applications</a>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="md:col-span-4 space-y-10 animate-fade-up animate-delay-300">
            <!-- Progress -->
            <div class="premium-card p-8">
                <h3 class="text-sm font-bold text-white mb-8 uppercase tracking-widest">Your Progress</h3>
                <div class="flex flex-col items-center text-center mb-10">
                    <div class="h-24 w-24 rounded-full border-4 border-white/5 border-t-white flex items-center justify-center mb-6">
                        <span class="text-2xl font-bold text-white">70<span class="text-xs text-zinc-500">%</span></span>
                    </div>
                    <p class="text-sm font-bold text-white mb-2 uppercase tracking-widest">Builder</p>
                    <p class="text-xs text-zinc-500">30 points to ARCHITECT</p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-zinc-500">Ideas Posted</span>
                        <span class="text-white font-medium">12</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-zinc-500">Collaborations</span>
                        <span class="text-white font-medium">5</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-zinc-500">Upvotes</span>
                        <span class="text-white font-medium">156</span>
                    </div>
                </div>
            </div>

            <!-- Events -->
            <div>
                <h3 class="text-sm font-bold text-white mb-6 uppercase tracking-widest">Upcoming Events</h3>
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <p class="text-xs font-bold text-white mb-1">Hackathon 2024</p>
                        <p class="text-[10px] text-zinc-500 uppercase font-semibold">March 15 • Auditorium</p>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <p class="text-xs font-bold text-white mb-1">Mentorship Roundtable</p>
                        <p class="text-[10px] text-zinc-500 uppercase font-semibold">March 20 • Online</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
