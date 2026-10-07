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
