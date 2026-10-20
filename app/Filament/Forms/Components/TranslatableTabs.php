<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use App\Services\Translation\GoogleTranslateService;

class TranslatableTabs
{
    /**
     * Create an in-place bilingual tabbed component for forms.
     *
     * @param string $label Label for the tab section (e.g. 'Konten Multi-Bahasa')
     * @param array $idSchema Array of form fields for Indonesian (e.g. name.id, description.id)
     * @param array $enSchema Array of form fields for English (e.g. name.en, description.en)
     */
    public static function make(string $label = 'Language Selector', array $idSchema = [], array $enSchema = []): Tabs
    {
        return Tabs::make($label)
            ->tabs([
                Tab::make('Bahasa Indonesia')
                    ->icon('heroicon-m-language')
                    ->badge('ID')
                    ->schema($idSchema),

                Tab::make('English')
                    ->icon('heroicon-m-globe-alt')
                    ->badge('EN')
                    ->schema($enSchema),
            ])
            ->columnSpanFull();
    }
}
