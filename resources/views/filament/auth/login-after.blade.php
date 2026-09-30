<div class="mt-5 space-y-4">
    <!-- One-Click Credential Assistant Card -->
    <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 text-left">
        <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-zinc-200/60 dark:border-zinc-800/60">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-800 dark:text-zinc-200">
                    Quick Access Assistant
                </span>
            </div>
            <span class="text-[10px] font-mono text-zinc-600 dark:text-zinc-300">
                1-Click Populate
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Super Admin Button -->
            <button
                type="button"
                onclick="fillAdminCredentials('yoseph.iriandi.tambunan@gmail.com', '#T4mbun4n#')"
                class="flex flex-col text-left p-2 rounded-lg bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-amber-500 hover:shadow-sm transition group cursor-pointer"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-zinc-900 dark:text-white group-hover:text-amber-500 font-sans">
                        👑 Super Admin
                    </span>
                    <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        FILL
                    </span>
                </div>
                <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 truncate w-full mt-0.5">
                    yoseph.iriandi.tambunan@gmail.com
                </span>
            </button>

            <!-- QA Reviewer Button -->
            <button
                type="button"
                onclick="fillAdminCredentials('reviewer.midtrans@neriahpro.com', 'MidtransDemo2026#')"
                class="flex flex-col text-left p-2 rounded-lg bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500 hover:shadow-sm transition group cursor-pointer"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-zinc-900 dark:text-white group-hover:text-emerald-500 font-sans">
                        🛡️ QA Reviewer
                    </span>
                    <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                        FILL
                    </span>
                </div>
                <span class="text-[10px] font-mono text-zinc-500 dark:text-zinc-400 truncate w-full mt-0.5">
                    reviewer.midtrans@neriahpro.com
                </span>
            </button>
        </div>
    </div>

    <!-- System Telemetry & Mission Badges -->
    <div class="p-3 rounded-xl bg-zinc-900 text-zinc-300 border border-zinc-800 space-y-2 text-left font-mono text-[10px]">
        <div class="flex items-center justify-between text-zinc-400 border-b border-zinc-800/80 pb-1.5">
            <span class="font-bold uppercase tracking-wider text-zinc-200">System Telemetry & Protection</span>
            <span class="text-emerald-400 font-semibold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                ALL ENGINES ONLINE
            </span>
        </div>

        <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-zinc-400">
            <div class="flex items-center justify-between">
                <span>Database:</span>
                <span class="text-zinc-200 font-semibold">PostgreSQL 16 ULID</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Pagination:</span>
                <span class="text-emerald-400 font-semibold">Keyset O(1) Cursor</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Threat Defense:</span>
                <span class="text-zinc-200 font-semibold">AI Honeypot Active</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Core Framework:</span>
                <span class="text-zinc-200 font-semibold">Laravel 13 &bull; Filament v5</span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Links -->
    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 pt-1 font-mono">
        <a href="/" class="hover:text-amber-500 hover:underline transition flex items-center gap-1">
            <span>&larr; Landing Page</span>
        </a>
        <a href="/cv-pro" class="hover:text-amber-500 hover:underline transition flex items-center gap-1">
            <span>CV Pro Studio &rarr;</span>
        </a>
        <a href="/blueprint" class="hover:text-amber-500 hover:underline transition flex items-center gap-1">
            <span>Vision Blueprint &rarr;</span>
        </a>
    </div>
</div>

<script>
    function fillAdminCredentials(email, password) {
        const emailInput = document.querySelector('input[type="email"]') ||
                           document.querySelector('input[name="data.email"]') ||
                           document.querySelector('input[id*="email"]');
        const passwordInput = document.querySelector('input[type="password"]') ||
                              document.querySelector('input[name="data.password"]') ||
                              document.querySelector('input[id*="password"]');

        if (emailInput) {
            emailInput.value = email;
            emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            emailInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (passwordInput) {
            passwordInput.value = password;
            passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
            passwordInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        try {
            const formEl = (emailInput || passwordInput)?.closest('[wire\\:id]');
            if (formEl && window.Livewire) {
                const component = window.Livewire.find(formEl.getAttribute('wire:id'));
                if (component) {
                    component.set('data.email', email, false);
                    component.set('data.password', password, false);
                }
            }
        } catch (e) {
            console.warn('Livewire direct set warning:', e);
        }
    }
</script>
