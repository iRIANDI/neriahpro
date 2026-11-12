<style>
    .np-save-locked {
        pointer-events: none !important;
        opacity: 0.78 !important;
        cursor: wait !important;
    }

    .np-save-locked svg:not(.np-save-spinner) {
        display: none !important;
    }

    @keyframes npSpinAnimation {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .np-save-spinner {
        width: 1.125rem;
        height: 1.125rem;
        flex-shrink: 0;
        display: inline-block;
        animation: npSpinAnimation 0.75s linear infinite;
    }
</style>

<script>
(function () {
    if (window.__npAdminSaveGuardInit) return;
    window.__npAdminSaveGuardInit = true;

    const SAVE_ACTION_REGEX = /\b(save|submit|create|createAnother|saveTransaction|saveBatchesSettings|confirmSetup|update)\b/i;
    const lockedButtons = new Set();

    function isSaveOrSubmitButton(btn) {
        if (!btn || btn.tagName !== 'BUTTON') return false;
        if (btn.hasAttribute('data-np-no-guard')) return false;
        if (btn.hasAttribute('data-np-save-guard')) return true;

        if (btn.type === 'submit') {
            return true;
        }

        const wireClick = btn.getAttribute('wire:click') || '';
        if (SAVE_ACTION_REGEX.test(wireClick)) {
            return true;
        }

        if (btn.closest('.fi-form-actions') && /simpan|save|create|buat|perbarui|update|submit/i.test(btn.textContent || '')) {
            return true;
        }

        return false;
    }

    function createSpinnerSvg() {
        const ns = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('aria-hidden', 'true');
        svg.classList.add('np-save-spinner', 'fi-btn-icon');

        const circle = document.createElementNS(ns, 'circle');
        circle.setAttribute('cx', '12');
        circle.setAttribute('cy', '12');
        circle.setAttribute('r', '10');
        circle.setAttribute('stroke', 'currentColor');
        circle.setAttribute('stroke-width', '3');
        circle.setAttribute('stroke-opacity', '0.25');

        const path = document.createElementNS(ns, 'path');
        path.setAttribute('fill', 'currentColor');
        path.setAttribute('d', 'M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z');

        svg.appendChild(circle);
        svg.appendChild(path);
        return svg;
    }

    function lockSaveButton(btn) {
        if (!btn || btn.dataset.npSaving === '1') return;

        btn.dataset.npSaving = '1';
        btn.setAttribute('aria-busy', 'true');
        btn.classList.add('np-save-locked');

        // Immediately inject loader icon if no custom spinner already exists inside the button
        if (!btn.querySelector('.np-save-spinner')) {
            const existingVisibleLoading = btn.querySelector('.fi-btn-loader:not([style*="display: none"]), [wire\\:loading]:not([style*="display: none"])');
            if (!existingVisibleLoading) {
                btn.prepend(createSpinnerSvg());
            }
        }

        // Disable after current click/submit dispatch settles so browser native submit is never aborted
        const isLivewireBtn = btn.closest('[wire\\:id]') !== null;
        if (isLivewireBtn) {
            setTimeout(() => {
                if (btn.dataset.npSaving === '1') {
                    btn.disabled = true;
                }
            }, 10);
        }

        // Safety auto-unlock timer in case network drops or validation stays client-side
        if (btn.__npUnlockTimer) {
            clearTimeout(btn.__npUnlockTimer);
        }
        btn.__npUnlockTimer = setTimeout(() => unlockSaveButton(btn), 20000);

        lockedButtons.add(btn);
    }

    function unlockSaveButton(btn) {
        if (!btn) return;
        if (btn.__npUnlockTimer) {
            clearTimeout(btn.__npUnlockTimer);
            btn.__npUnlockTimer = null;
        }
        delete btn.dataset.npSaving;
        btn.removeAttribute('aria-busy');
        btn.classList.remove('np-save-locked');
        btn.disabled = false;

        btn.querySelectorAll('.np-save-spinner').forEach((el) => el.remove());
        lockedButtons.delete(btn);
    }

    function unlockAllSaveButtons() {
        Array.from(lockedButtons).forEach((btn) => unlockSaveButton(btn));
    }

    // Intercept double clicks in capture phase at 0ms
    document.addEventListener('click', function (event) {
        const btn = event.target?.closest ? event.target.closest('button') : null;
        if (!btn || !isSaveOrSubmitButton(btn)) return;

        if (btn.dataset.npSaving === '1') {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return false;
        }

        lockSaveButton(btn);
    }, true);

    // Also guard form submit events (e.g. Enter key or Ctrl+S shortcut)
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!form || !form.querySelectorAll) return;

        const submitBtn = event.submitter || form.querySelector('button[type="submit"]');
        if (submitBtn && isSaveOrSubmitButton(submitBtn)) {
            if (submitBtn.dataset.npSaving === '1') {
                // Already locked from click: allow first submit, block repeat submits
                if (form.dataset.npFormSubmitted === '1') {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                }
            }
            form.dataset.npFormSubmitted = '1';
            setTimeout(() => { delete form.dataset.npFormSubmitted; }, 1200);
            lockSaveButton(submitBtn);
        }
    }, true);

    function registerLivewireHooks() {
        if (!window.Livewire || window.__npLivewireSaveHooksRegistered) return;
        window.__npLivewireSaveHooksRegistered = true;

        try {
            window.Livewire.hook('commit', ({ succeed, fail }) => {
                succeed(() => unlockAllSaveButtons());
                fail(() => unlockAllSaveButtons());
            });
        } catch (e) {}
    }

    document.addEventListener('invalid', unlockAllSaveButtons, true);
    document.addEventListener('livewire:init', registerLivewireHooks);
    document.addEventListener('livewire:initialized', registerLivewireHooks);
    document.addEventListener('livewire:navigated', unlockAllSaveButtons);
    if (window.Livewire) {
        registerLivewireHooks();
    }
})();
</script>
