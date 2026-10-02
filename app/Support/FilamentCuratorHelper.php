<?php

namespace App\Support;

use Filament\Forms\Components\FileUpload;

class FilamentCuratorHelper
{
    /**
     * Build a standardized image picker component adhering to Curator standards
     * and strictly shallow folder hierarchy (max 1-2 levels) to optimize OS server RAM.
     *
     * @param string $name
     * @param string $directory Map folder penyimpanan (wajib shallow/dangkal)
     * @param string $label
     * @return mixed
     */
    public static function picker(string $name, string $directory = 'media', string $label = 'Gambar')
    {
        $shallowDirectory = FilamentRichEditor::sanitizeDirectory($directory);

        // If Awcodes CuratorPicker is available, use it natively
        if (class_exists('\\Awcodes\\Curator\\Components\\Forms\\CuratorPicker')) {
            return forward_static_call(['\\Awcodes\\Curator\\Components\\Forms\\CuratorPicker', 'make'], $name)
                ->label($label)
                ->directory($shallowDirectory);
        }

        // Standard Filament fallback adhering to shallow directory rule
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($shallowDirectory)
            ->visibility('public');
    }
}
