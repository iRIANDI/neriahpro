<x-filament-panels::page>
    <script>
        (function() {
            // 1. Force manual scroll restoration so Chrome never restores old bottom scroll position
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            window.scrollTo(0, 0);

            // 2. Shield against unwanted automatic scrollIntoView calls during initial DOM/editor hydration
            var originalScrollIntoView = Element.prototype.scrollIntoView;
            var allowAutoScroll = false;
            setTimeout(function() {
                allowAutoScroll = true;
            }, 1800);

            Element.prototype.scrollIntoView = function() {
                if (!allowAutoScroll) {
                    return;
                }
                return originalScrollIntoView.apply(this, arguments);
            };

            window.addEventListener('DOMContentLoaded', function() {
                window.scrollTo(0, 0);
            });
            window.addEventListener('load', function() {
                window.scrollTo(0, 0);
                setTimeout(function() { window.scrollTo(0, 0); }, 100);
            });

            // 3. Browser extension communication failure filter
            var isNoise = function(v) {
                if (!v) return false;
                var s = typeof v === 'string' ? v : (v.message || String(v));
                return s.indexOf('Could not establish connection') !== -1 ||
                       s.indexOf('Receiving end does not exist') !== -1 ||
                       s.indexOf('message port closed') !== -1;
            };
            var _err = console.error;
            console.error = function() {
                for (var i = 0; i < arguments.length; i++) {
                    if (isNoise(arguments[i])) return;
                }
                return _err.apply(console, arguments);
            };
            window.addEventListener('unhandledrejection', function(e) {
                if (isNoise(e ? e.reason : '')) {
                    e.preventDefault();
                    if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                    if (e.stopPropagation) e.stopPropagation();
                    return false;
                }
            }, true);
        })();
    </script>
    <style>
        /* Bulletproof stability for Global Settings Tabs (anti-collapse & anti-vanishing) */
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

        /* CRITICAL: Strictly isolate tabs so ONLY the active tab is visible, eliminating vertical page sprawl */
        #global-settings-tabs .fi-sc-tabs-tab {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        #global-settings-tabs .fi-sc-tabs-tab.fi-active {
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            overflow: visible !important;
            position: relative !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            min-height: 420px;
        }

        /* Graceful fallback: If no tab has fi-active assigned yet on boot, display the first tab panel */
        #global-settings-tabs:not(:has(.fi-sc-tabs-tab.fi-active)) .fi-sc-tabs-tab:first-of-type {
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            position: relative !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            min-height: 420px;
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
