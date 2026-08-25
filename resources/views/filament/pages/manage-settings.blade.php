<x-filament-panels::page>
    <x-filament::form wire:submit="submit">
        {{ $this->form }}

        <div class="fi-form-actions">
            <x-filament::button type="submit" size="lg">
                Save Settings
            </x-filament::button>
        </div>
    </x-filament::form>
</x-filament-panels::page>
