<?php
ob_start();
$user = getCurrentUser();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2 uppercase">Builder <span class="text-primary italic">Directory</span></h1>
            <p class="text-slate-500 font-medium">Manage and vet members of the IdeaSync ecosystem.</p>
        </div>
        <div class="flex gap-3">
             <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-primary transition-colors"></i>
                <input type="text" placeholder="Search builders..." class="pl-11 pr-4 py-3 bg-surface-container-high border border-white/5 rounded-2xl w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm text-white transition-all">
             </div>
        </div>
    </div>

    <div class="bg-surface-container-low rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50 border-b border-white/5">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Builder</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Designation</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Activity</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php for($i=1; $i<=8; $i++): ?>
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-surface-container-high flex items-center justify-center font-bold text-slate-400 group-hover:text-primary transition-colors border border-white/5">
                                    <?php echo ['AS', 'MK', 'RV', 'SK', 'IS', 'AM', 'PK', 'NS'][$i-1]; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white"><?php echo ['Aryan Sharma', 'Meera Kapoor', 'Rahul Verma', 'Sneha Kapur', 'Ishaan Shah', 'Ananya Misra', 'Priya Kant', 'Nitin Singh'][$i-1]; ?></p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Joined 2mo ago</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-slate-400"><?php echo ['Full-Stack Dev', 'UI Designer', 'IoT Expert', 'Data Scientist', 'Backend Dev', 'Mobile Dev', 'Product Manager', 'Security Lead'][$i-1]; ?></span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">Verified</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-white"><?php echo rand(10, 50); ?> Commits</span>
                        </td>
                        <td class="px-8 py-6">
                            <button class="h-8 w-8 rounded-lg bg-surface-container-high text-slate-500 hover:text-white flex items-center justify-center transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
?>
