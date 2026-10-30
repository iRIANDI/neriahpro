@php
    try {
        $gateway = app(\App\Services\Ai\LlmGatewayService::class);
        $activeProvider = ucfirst($gateway->getActiveProvider());
    } catch (\Throwable) {
        $activeProvider = 'DeepSeek';
    }
@endphp

<div class="relative my-auto" x-data>
    <button type="button" 
            x-on:click="if (window.Livewire) { window.Livewire.dispatch('toggle-ai-copilot'); } else { window.dispatchEvent(new CustomEvent('toggle-ai-copilot')); }"
            class="relative flex items-center gap-1.5 px-2.5 py-1.5 rounded-xs border border-zinc-200 dark:border-zinc-800 bg-white/90 dark:bg-zinc-900/90 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-2xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all cursor-pointer select-none focus:outline-none"
            title="{{ __('Buka Neriah Pro AI Copilot (:provider • Pintasan: Ctrl+Shift+A)', ['provider' => $activeProvider]) }}">
        
        <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>

        <span class="hidden md:inline font-mono text-[11px] font-bold text-zinc-900 dark:text-zinc-100 tracking-wider uppercase">
            Copilot
        </span>

        <span class="text-[9px] px-1 py-0.2 bg-emerald-100 dark:bg-emerald-950 text-emerald-900 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 rounded-xs font-mono font-black">
            {{ $activeProvider }}
        </span>
    </button>
</div>
