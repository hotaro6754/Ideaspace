<div class="premium-card bg-white p-6 shadow-sm border border-slate-100 rounded-2xl mb-8">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <i class="fas fa-scroll text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Idea Charter</h3>
                <p class="text-xs text-slate-500 font-medium">Define the core vision and scope.</p>
            </div>
        </div>
        <div id="charter-status" class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold uppercase rounded-full">
            Incomplete
        </div>
    </div>

    <form id="charter-form" class="space-y-4">
        <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo Security::getCsrfToken(); ?>">

        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 block">Vision (What are we building?)</label>
            <textarea name="vision" class="form-input text-sm" placeholder="e.g. The most intuitive campus events platform..." rows="2"></textarea>
        </div>

        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 block">Mission (How will we build it?)</label>
            <textarea name="mission" class="form-input text-sm" placeholder="e.g. By leveraging modern web tech and student feedback..." rows="2"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 block">Success Criteria</label>
                <textarea name="success_criteria" class="form-input text-sm" placeholder="e.g. 500+ active users..." rows="2"></textarea>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 block">Scope Limitations</label>
                <textarea name="scope_limitations" class="form-input text-sm" placeholder="e.g. Limited to LIET campus initially..." rows="2"></textarea>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full py-3 text-xs font-bold uppercase tracking-widest">
            Save Charter
        </button>
    </form>
</div>

<script>
document.getElementById('charter-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('<?php echo BASE_URL; ?>/src/controllers/gsd.php?action=save_charter', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();
    if (data.success) {
        alert('Charter saved successfully!');
        document.getElementById('charter-status').textContent = 'Complete';
        document.getElementById('charter-status').className = 'px-3 py-1 bg-green-100 text-green-600 text-[10px] font-bold uppercase rounded-full';
    } else {
        alert(data.error || 'Failed to save charter');
    }
});
</script>
