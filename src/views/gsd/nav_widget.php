<div class="premium-card bg-white p-6 shadow-sm border border-slate-100 rounded-2xl mb-8">
    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Documentation</h3>
    <div class="space-y-3">
        <a href="<?php echo BASE_URL; ?>/?page=project-brief&id=<?php echo $idea['id']; ?>" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-primary hover:text-white transition-all group">
            <div class="flex items-center gap-3">
                <i class="fas fa-file-contract text-primary group-hover:text-white transition-colors"></i>
                <span class="text-xs font-bold">Project Brief</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] opacity-30 group-hover:opacity-100"></i>
        </a>
        <a href="<?php echo BASE_URL; ?>/?page=decision-log&id=<?php echo $idea['id']; ?>" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-primary hover:text-white transition-all group">
            <div class="flex items-center gap-3">
                <i class="fas fa-history text-primary group-hover:text-white transition-colors"></i>
                <span class="text-xs font-bold">Decision Log</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] opacity-30 group-hover:opacity-100"></i>
        </a>
    </div>
</div>
