<div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
    <!-- One-Click Credential Assistant Card -->
    <div style="padding: 0.75rem 0.875rem; border-radius: 0.75rem; background: rgba(244, 244, 245, 0.8); border: 1px solid rgba(228, 228, 231, 1); text-align: left;" class="dark:bg-zinc-900/80 dark:border-zinc-800">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; padding-bottom: 0.375rem; border-bottom: 1px solid rgba(228, 228, 231, 0.8);" class="dark:border-zinc-800">
            <div style="display: flex; align-items: center; gap: 0.375rem;">
                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                <span style="font-size: 11px; font-family: monospace; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #27272a;" class="dark:text-zinc-200">
                    Quick Access Assistant
                </span>
            </div>
            <span style="font-size: 9px; font-family: monospace; color: #71717a;" class="dark:text-zinc-400">
                1-Click Populate
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.5rem;">
            <!-- Super Admin Button -->
            <button
                type="button"
                onclick="fillAdminCredentials('yoseph.iriandi.tambunan@gmail.com', '#T4mbun4n#')"
                style="display: flex; flex-direction: column; text-align: left; padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid rgba(228, 228, 231, 0.9); cursor: pointer; transition: all 0.2s;"
                class="dark:bg-zinc-950 dark:border-zinc-800 hover:border-amber-500"
            >
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <span style="font-size: 11px; font-weight: 700; color: #18181b;" class="dark:text-white">
                        👑 Super Admin
                    </span>
                    <span style="font-size: 9px; font-family: monospace; padding: 0.125rem 0.375rem; border-radius: 0.25rem; background: #f4f4f5; color: #52525b;" class="dark:bg-zinc-800 dark:text-zinc-300">
                        FILL
                    </span>
                </div>
                <span style="font-size: 10px; font-family: monospace; color: #71717a; margin-top: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%;" class="dark:text-zinc-400">
                    yoseph.iriandi.tambunan@gmail.com
                </span>
            </button>

            <!-- QA Reviewer Button -->
            <button
                type="button"
                onclick="fillAdminCredentials('reviewer.midtrans@neriahpro.com', 'MidtransDemo2026#')"
                style="display: flex; flex-direction: column; text-align: left; padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid rgba(228, 228, 231, 0.9); cursor: pointer; transition: all 0.2s;"
                class="dark:bg-zinc-950 dark:border-zinc-800 hover:border-emerald-500"
            >
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <span style="font-size: 11px; font-weight: 700; color: #18181b;" class="dark:text-white">
                        🛡️ QA Reviewer
                    </span>
                    <span style="font-size: 9px; font-family: monospace; padding: 0.125rem 0.375rem; border-radius: 0.25rem; background: #f4f4f5; color: #52525b;" class="dark:bg-zinc-800 dark:text-zinc-300">
                        FILL
                    </span>
                </div>
                <span style="font-size: 10px; font-family: monospace; color: #71717a; margin-top: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%;" class="dark:text-zinc-400">
                    reviewer.midtrans@neriahpro.com
                </span>
            </button>
        </div>
    </div>

    <!-- System Telemetry & Mission Badges -->
    <div style="padding: 0.625rem 0.75rem; border-radius: 0.75rem; background: #18181b; color: #d4d4d8; border: 1px solid #27272a; font-family: monospace; font-size: 10px; text-align: left;">
        <div style="display: flex; align-items: center; justify-content: space-between; color: #a1a1aa; border-bottom: 1px solid #27272a; padding-bottom: 0.375rem; margin-bottom: 0.375rem;">
            <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #f4f4f5;">System Telemetry</span>
            <span style="color: #34d399; font-weight: 600; display: inline-flex; align-items: center; gap: 0.375rem;">
                <span style="width: 6px; height: 6px; border-radius: 9999px; background: #34d399; display: inline-block;"></span>
                ONLINE
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.25rem 0.5rem; color: #a1a1aa;">
            <div>DB: <strong style="color: #f4f4f5;">PostgreSQL 16 ULID</strong></div>
            <div>Cursor: <strong style="color: #34d399;">Keyset O(1)</strong></div>
            <div>Shield: <strong style="color: #f4f4f5;">AI Honeypot</strong></div>
            <div>Stack: <strong style="color: #f4f4f5;">Laravel 13 &bull; Filament 5</strong></div>
        </div>
    </div>

    <!-- Quick Navigation Links -->
    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; font-family: monospace; color: #71717a; padding: 0 0.25rem;" class="dark:text-zinc-400">
        <a href="/" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" class="hover:text-amber-500">
            <span>&larr; Beranda</span>
        </a>
        <a href="/cv-pro" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" class="hover:text-amber-500">
            <span>CV Pro Studio &rarr;</span>
        </a>
        <a href="/blueprint" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" class="hover:text-amber-500">
            <span>Blueprint &rarr;</span>
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
