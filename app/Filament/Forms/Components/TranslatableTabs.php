<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Services\Translation\GoogleTranslateService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class TranslatableTabs
{
    /**
     * Create an in-place Tier-1 bilingual tabbed component (`🇮🇩 Indonesia (ID)` & `🇬🇧 English (EN)`)
     * with 1-click GoogleTranslateService actions (`✨ Terjemahkan ke EN`, `✨ Terjemahkan ke ID`, `🔄 Tarik dari ID`).
     *
     * @param  string  $label  Label for the tab section (e.g. 'Konten Dwibahasa Tier-1')
     * @param  array<int, mixed>  $idSchema  Array of form fields for Indonesian (e.g. name.id, description.id)
     * @param  array<int, mixed>  $enSchema  Array of form fields for English (e.g. name.en, description.en)
     * @param  array<int, string>  $fieldKeys  Optional base JSON attribute names (e.g. ['name', 'description']) for 1-click translation
     */
    public static function make(
        string $label = 'Language Selector',
        array $idSchema = [],
        array $enSchema = [],
        array $fieldKeys = []
    ): Tabs {
        if (empty($fieldKeys)) {
            $fieldKeys = self::inferFieldKeysFromSchemas($idSchema, $enSchema);
        }

        $safeKey = preg_replace('/[^a-z0-9_]/i', '_', strtolower($label)) ?: 'translatable';

        $idToolbar = [];
        $enToolbar = [];

        if (! empty($fieldKeys)) {
            $idToolbar[] = Actions::make([
                Action::make('translate_id_to_en_' . $safeKey)
                    ->label('✨ Terjemahkan ke EN')
                    ->color('success')
                    ->size('xs')
                    ->action(function (Get $get, Set $set) use ($fieldKeys): void {
                        $translatedCount = 0;
                        foreach ($fieldKeys as $field) {
                            $src = trim((string) ($get("{$field}.id") ?? ''));
                            if ($src !== '') {
                                $set("{$field}.en", GoogleTranslateService::idToEn($src));
                                $translatedCount++;
                            }
                        }

                        Notification::make()
                            ->title($translatedCount > 0 ? 'Berhasil diterjemahkan ke Bahasa Inggris (EN)' : 'Isi teks Bahasa Indonesia terlebih dahulu')
                            ->status($translatedCount > 0 ? 'success' : 'warning')
                            ->send();
                    }),
            ]);

            $enToolbar[] = Actions::make([
                Action::make('translate_en_to_id_' . $safeKey)
                    ->label('✨ Terjemahkan ke ID')
                    ->color('success')
                    ->size('xs')
                    ->action(function (Get $get, Set $set) use ($fieldKeys): void {
                        $translatedCount = 0;
                        foreach ($fieldKeys as $field) {
                            $src = trim((string) ($get("{$field}.en") ?? ''));
                            if ($src !== '') {
                                $set("{$field}.id", GoogleTranslateService::enToId($src));
                                $translatedCount++;
                            }
                        }

                        Notification::make()
                            ->title($translatedCount > 0 ? 'Translated to Bahasa Indonesia (ID)' : 'Fill English text first')
                            ->status($translatedCount > 0 ? 'success' : 'warning')
                            ->send();
                    }),
                Action::make('pull_from_id_to_en_' . $safeKey)
                    ->label('🔄 Tarik dari ID')
                    ->color('gray')
                    ->size('xs')
                    ->action(function (Get $get, Set $set) use ($fieldKeys): void {
                        $syncedCount = 0;
                        foreach ($fieldKeys as $field) {
                            $src = trim((string) ($get("{$field}.id") ?? ''));
                            if ($src !== '') {
                                $set("{$field}.en", GoogleTranslateService::idToEn($src));
                                $syncedCount++;
                            }
                        }

                        Notification::make()
                            ->title($syncedCount > 0 ? 'Disinkronkan ulang dari Tab Indonesia (ID)' : 'Tab Indonesia masih kosong')
                            ->status($syncedCount > 0 ? 'success' : 'warning')
                            ->send();
                    }),
            ]);
        }

        return Tabs::make($label)
            ->tabs([
                Tab::make('🇮🇩 Indonesia (ID)')
                    ->icon('heroicon-m-language')
                    ->badge('ID')
                    ->schema(array_merge($idToolbar, $idSchema)),

                Tab::make('🇬🇧 English (EN)')
                    ->icon('heroicon-m-globe-alt')
                    ->badge('EN')
                    ->schema(array_merge($enToolbar, $enSchema)),
            ])
            ->columnSpanFull();
    }

    /**
     * Automatically infer base attribute keys (e.g. `name` from `name.id`) when not explicitly passed.
     *
     * @param  array<int, mixed>  $idSchema
     * @param  array<int, mixed>  $enSchema
     * @return array<int, string>
     */
    private static function inferFieldKeysFromSchemas(array $idSchema, array $enSchema): array
    {
        $keys = [];
        foreach (array_merge($idSchema, $enSchema) as $component) {
            if (is_object($component) && method_exists($component, 'getName')) {
                $name = (string) $component->getName();
                if (str_ends_with($name, '.id')) {
                    $keys[] = substr($name, 0, -3);
                } elseif (str_ends_with($name, '.en')) {
                    $keys[] = substr($name, 0, -3);
                }
            }
        }

        return array_values(array_unique(array_filter($keys)));
    }
}
