<x-filament-panels::page>
    <style>
        /* Bulletproof stability for Global Settings Tabs (anti-collapse & anti-vanishing) */
        #global-settings-tabs {
            min-height: 540px;
            width: 100%;
        }

        #global-settings-tabs .fi-tabs {
            min-height: 46px;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        #global-settings-tabs .fi-sc-tabs-tab.fi-active {
            min-height: 480px;
            display: block !important;
            visibility: visible !important;
            position: relative !important;
            height: auto !important;
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
