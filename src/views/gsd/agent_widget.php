<div class="premium-card bg-slate-900 p-6 text-white overflow-hidden relative mb-8">
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-secondary/20 rounded-full blur-3xl"></div>

    <div class="relative z-10">
        <div class="flex items-center gap-4 mb-6">
            <div class="h-12 w-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                <i class="fas fa-robot text-xl text-primary"></i>
            </div>
            <div>
                <h3 class="font-bold text-white" id="agent-name">Agent Assistant</h3>
                <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest" id="agent-persona">Loading persona...</p>
            </div>
        </div>

        <div class="space-y-4" id="agent-chat">
            <div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-sm italic text-white/80">
                "Hello! I'm analyzing your project health and progress. One moment..."
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-white/10 flex gap-2">
            <button onclick="getAgentSuggestions('researcher')" class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-bold uppercase transition-colors border border-white/10">Researcher</button>
            <button onclick="getAgentSuggestions('advisor')" class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-bold uppercase transition-colors border border-white/10">Advisor</button>
            <button onclick="getAgentSuggestions('lead')" class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-bold uppercase transition-colors border border-white/10">Project Lead</button>
        </div>
    </div>
</div>

<script>
async function getAgentSuggestions(type = 'researcher') {
    const chat = document.getElementById('agent-chat');
    chat.innerHTML = '<div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-sm animate-pulse">Thinking...</div>';

    try {
        const res = await fetch(`<?php echo BASE_URL; ?>/src/controllers/agents.php?action=get_suggestions&idea_id=<?php echo $idea['id']; ?>&type=${type}`);
        const data = await res.json();

        if (data.success) {
            document.getElementById('agent-name').textContent = data.persona.name;
            document.getElementById('agent-persona').textContent = data.persona.goals;

            chat.innerHTML = data.suggestions.map(s => `
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 text-sm text-white/90 mb-3 animate-fade-in">
                    ${s}
                </div>
            `).join('');
        }
    } catch (e) {
        chat.innerHTML = '<div class="p-4 bg-red-500/10 text-red-400 rounded-2xl text-xs">Failed to connect to agent.</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => getAgentSuggestions('researcher'));
</script>
