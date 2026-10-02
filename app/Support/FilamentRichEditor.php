<?php

namespace App\Support;

use Filament\Forms\Components\RichEditor;

class FilamentRichEditor
{
    /**
     * Create a standard, full-featured RichEditor instance with all toolbar
     * capabilities enabled, media file attachments, and shallow directory mapping.
     *
     * @param string $name
     * @param string $directory Map folder penyimpanan (wajib shallow/dangkal, maks 1-2 level)
     * @return RichEditor
     */
    public static function make(string $name, string $directory = 'media'): RichEditor
    {
        // Enforce shallow folder hierarchy (max 1-2 levels) to optimize OS server RAM & inode lookups
        $sanitizedDirectory = self::sanitizeDirectory($directory);

        return RichEditor::make($name)
            ->toolbarButtons([
                'attachFiles',
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
            ])
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory($sanitizedDirectory)
            ->fileAttachmentsVisibility('public');
    }

    /**
     * Sanitize and enforce shallow directory hierarchy (max 1-2 levels).
     * Prevents deep recursive folders that consume server RAM during directory scans.
     */
    public static function sanitizeDirectory(string $directory): string
    {
        $segments = array_filter(explode('/', str_replace('\\', '/', trim($directory, '/'))));

        // Strictly enforce shallow mapping (cap at 2 folder depth)
        if (count($segments) > 2) {
            $segments = array_slice($segments, 0, 2);
        }

        return !empty($segments) ? implode('/', $segments) : 'media';
    }
}
