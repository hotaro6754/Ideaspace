<?php
ob_start();
$user = getCurrentUser();
if (!$user) redirect(BASE_URL . '/?page=login');
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-12">
        <a href="<?php echo BASE_URL; ?>/?page=profile" class="text-[10px] font-black text-slate-500 hover:text-primary transition-colors flex items-center gap-2 mb-6 uppercase tracking-[0.2em]">
            <i class="fas fa-arrow-left"></i> My Profile
        </a>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase text-primary italic">Application <span class="text-white not-italic">Vault</span></h1>
        <p class="text-slate-500 mt-2 text-lg font-medium">Managing your outgoing requests for collaboration.</p>
    </div>

    <div class="space-y-6">
        <!-- Application Item -->
        <div class="bento-card border border-white/5 hover:border-primary/20 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 text-primary/5 text-9xl -mr-8 -mt-8">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 rounded-lg bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-widest border border-amber-500/20">Pending Review</span>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest italic">Sent 3 days ago</span>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-tight">Smart Parking System</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6 max-w-xl">Applied as <span class="text-primary font-bold">IoT Expert</span>. Note: "I have experience with LoRaWAN and ultrasonic sensors for real-time monitoring..."</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-6 py-3 bg-surface-container-high hover:bg-red-500/10 hover:text-red-400 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] rounded-xl transition-all border border-white/5">Cancel Request</button>
                        <button class="px-6 py-3 bg-primary text-background text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:scale-105 transition-all">View Project</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Item (Approved) -->
        <div class="bento-card border border-green-500/20 bg-green-500/5 group relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 rounded-lg bg-green-500/20 text-green-400 text-[10px] font-black uppercase tracking-widest border border-green-500/30">Accepted</span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest italic">Approved Mar 14</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-tight">Campus AI Study Buddy</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6 max-w-xl">Role: <span class="text-primary font-bold">Full-Stack Developer</span>. You are now a core member of this team.</p>
                </div>
                <div class="flex gap-3">
                    <button class="px-8 py-3 bg-primary text-background text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:scale-105 transition-all">Open Channel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
