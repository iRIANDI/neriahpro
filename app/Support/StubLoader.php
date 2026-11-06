<?php

namespace App\Support;

class StubLoader
{
    /**
     * Load a stub file from resources/stubs/ and replace placeholders.
     * Placeholders can be in {{ KEY }} or {$KEY} format.
     */
    public static function load(string $stubPath, array $replacements = []): string
    {
        $fullPath = resource_path('stubs/' . ltrim($stubPath, '/\\'));

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Stub file not found at: {$fullPath}");
        }

        $content = file_get_contents($fullPath);

        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        }

        return $content;
    }

    /**
     * Check if a stub file exists in resources/stubs/.
     */
    public static function exists(string $stubPath): bool
    {
        return file_exists(resource_path('stubs/' . ltrim($stubPath, '/\\')));
    }
}
