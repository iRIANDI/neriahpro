<x-filament-panels::page>
    @script
    <script>
        // 1. Force manual scroll restoration so browser never restores old scroll positions
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        window.scrollTo(0, 0);

        // 2. Intercept HTMLElement.prototype.focus to ALWAYS prevent auto-scrolling
        var originalFocus = HTMLElement.prototype.focus;
        HTMLElement.prototype.focus = function(options) {
            if (typeof options === 'object' && options !== null) {
                if (options.preventScroll === undefined) {
                    options.preventScroll = true;
                }
            } else {
                options = { preventScroll: true };
            }
            return originalFocus.call(this, options);
        };

        // 3. Shield against unwanted automatic scrollIntoView calls during component hydration
        var originalScrollIntoView = Element.prototype.scrollIntoView;
        Element.prototype.scrollIntoView = function() {
            var isValidationError = this.hasAttribute && (
                this.hasAttribute('data-validation-error') || 
                (this.querySelector && this.querySelector('[data-validation-error], .fi-fo-field-wrp-error-message')) ||
                (this.closest && this.closest('[data-validation-error], .fi-fo-field-wrp-error-message'))
            );
            var isUserGesture = window.event && window.event.isTrusted;
            if (!isValidationError && !isUserGesture) {
                return;
            }
            return originalScrollIntoView.apply(this, arguments);
        };

        // 4. Pin scroll position to top during DOM load, Livewire hydration, and navigation
        var pinTop = function() {
            window.scrollTo(0, 0);
            var mainContent = document.querySelector('.fi-main') || document.querySelector('.fi-page') || document.documentElement;
            if (mainContent && mainContent.scrollTop > 0) {
                mainContent.scrollTop = 0;
            }
        };

        pinTop();
        setTimeout(pinTop, 50);
        setTimeout(pinTop, 150);
        setTimeout(pinTop, 400);

        document.addEventListener('livewire:navigated', pinTop);
        document.addEventListener('DOMContentLoaded', pinTop);

        // 5. Enterprise Browser Extension Noise Shield (MV3 Ephemeral Service Worker Protection)
        var isNoise = function(v) {
            if (!v) return false;
            var s = typeof v === 'string' ? v : (v.message || v.stack || v.description || String(v));
            return s.indexOf('Could not establish connection') !== -1 ||
                   s.indexOf('Receiving end does not exist') !== -1 ||
                   s.indexOf('message port closed') !== -1 ||
                   s.indexOf('Extension context invalidated') !== -1 ||
                   s.indexOf('chrome-extension://') !== -1 ||
                   s.indexOf('moz-extension://') !== -1;
        };

        ['error', 'warn', 'log'].forEach(function(method) {
            var _orig = console[method];
            if (typeof _orig !== 'function') return;
            console[method] = function() {
                for (var i = 0; i < arguments.length; i++) {
                    if (isNoise(arguments[i])) return;
                }
                return _orig.apply(console, arguments);
            };
        });

        window.addEventListener('unhandledrejection', function(e) {
            if (isNoise(e ? e.reason : '')) {
                e.preventDefault();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                if (e.stopPropagation) e.stopPropagation();
                return false;
            }
        }, true);

        window.addEventListener('error', function(e) {
            var text = (e ? e.message : '') + ' ' + (e && e.error ? (e.error.message || e.error.stack) : '');
            if (isNoise(text)) {
                e.preventDefault();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                if (e.stopPropagation) e.stopPropagation();
                return false;
            }
        }, true);
    </script>
    @endscript

    <style>
        /* Neutralize Chromium Scroll Anchoring (prevents auto-jumping on dynamic DOM expansion) */
        html, body, .fi-main, .fi-page, #global-settings-tabs, .fi-sc-tabs, .fi-sc-tabs-tab {
            overflow-anchor: none !important;
        }

        /* Bulletproof stability for Global Settings Tabs */
        #global-settings-tabs {
            min-height: 480px;
            width: 100%;
        }

        #global-settings-tabs .fi-tabs {
            min-height: 46px;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>

    <!-- Top Quick Action Header Bar (No Scrolling Needed) -->
    <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-white/10 mb-4">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300 uppercase">
                Global Settings &amp; Schema.org Hub
            </span>
        </div>

        <x-filament::button type="button" wire:click="submit" size="sm" icon="heroicon-m-check-badge" color="primary">
            Simpan Pengaturan (Save)
        </x-filament::button>
    </div>

    <!-- Visual Operational Status Banner -->
    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-mono flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="font-bold">STATUS PANEL: 100% OPERASIONAL &amp; TERHUBUNG</span>
            <span class="hidden sm:inline text-zinc-500 dark:text-zinc-400">| Laravel 13, Livewire 4, Schema.org Cache</span>
        </div>
        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">
            Ready to Save
        </span>
    </div>

    <form wire:submit="submit" class="space-y-6 notranslate" translate="no">
        {{ $this->form }}

        <div class="fi-form-actions mt-6 flex items-center justify-between">
            <x-filament::button type="submit" size="lg" icon="heroicon-m-check-badge" color="primary">
                Simpan Semua Pengaturan (Save Settings)
            </x-filament::button>

            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                Semua perubahan otomatis membersihkan Cache Forever &amp; Schema.org
            </span>
        </div>
    </form>
</x-filament-panels::page>
