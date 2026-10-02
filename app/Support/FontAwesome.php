<?php

namespace App\Support;

class FontAwesome
{
    /**
     * Render a local FontAwesome SVG icon without third-party network dependencies.
     */
    public static function svg(string $name, string $classes = 'w-4 h-4', string $viewBox = '0 0 512 512'): string
    {
        $icons = config('fontawesome.icons', []);
        $path = $icons[$name] ?? ($icons['circle-info'] ?? '');

        if (empty($path)) {
            return '';
        }

        return sprintf(
            '<svg class="%s inline-block shrink-0" viewBox="%s" fill="currentColor" aria-hidden="true">%s</svg>',
            htmlspecialchars($classes, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($viewBox, ENT_QUOTES, 'UTF-8'),
            $path
        );
    }
}
