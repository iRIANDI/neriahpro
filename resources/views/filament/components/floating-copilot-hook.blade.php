@if(auth()->check())
@persist('floating-agentic-ai')
    <livewire:admin.floating-agentic-ai wire:key="persistent-floating-agentic-ai" />
@endpersist
@endif

<script>
    if (!window.__aiCopilotShortcutRegistered) {
        window.__aiCopilotShortcutRegistered = true;
        window.addEventListener("keydown", function(e) {
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === "A" || e.key === "a")) {
                e.preventDefault();
                if (window.Livewire) {
                    window.Livewire.dispatch("toggle-ai-copilot");
                } else {
                    window.dispatchEvent(new CustomEvent("toggle-ai-copilot"));
                }
            }
        });
    }
</script>
