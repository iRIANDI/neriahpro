<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <div class="fi-form-actions mt-6">
            <x-filament::button type="submit" size="lg">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
