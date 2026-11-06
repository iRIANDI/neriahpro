<?php

namespace App\Support;

class BlueprintPdfHelper
{
    /**
     * Parse and format raw text/array into cleanly indented, readable bullet/numbered points.
     * Supports both DomPDF table layout and responsive Web/Tailwind layout.
     */
    public static function formatPoints(string|array|null $content, bool $ordered = true, bool $isPdf = true): string
    {
        if (empty($content)) {
            return $isPdf 
                ? '<span style="color: #94a3b8; font-style: italic;">-</span>'
                : '<span class="text-zinc-400 italic text-xs">-</span>';
        }

        $items = self::extractItems($content);

        if (empty($items)) {
            return $isPdf 
                ? '<span style="color: #94a3b8; font-style: italic;">-</span>'
                : '<span class="text-zinc-400 italic text-xs">-</span>';
        }

        if (count($items) === 1) {
            $escaped = htmlspecialchars((string)$items[0]);
            if (str_contains($escaped, ':')) {
                $parts = explode(':', $escaped, 2);
                return $isPdf
                    ? '<div style="line-height: 1.45;"><strong>' . $parts[0] . ':</strong>' . $parts[1] . '</div>'
                    : '<div class="leading-relaxed"><strong class="font-bold text-zinc-900 dark:text-zinc-100">' . $parts[0] . ':</strong>' . $parts[1] . '</div>';
            }
            return $isPdf
                ? '<div style="line-height: 1.45;">' . $escaped . '</div>'
                : '<div class="leading-relaxed">' . $escaped . '</div>';
        }

        if ($isPdf) {
            $html = '<table style="width: 100%; border-collapse: collapse; border: none; margin: 1px 0;">';
            foreach ($items as $idx => $item) {
                $num = $idx + 1;
                $escaped = htmlspecialchars((string)$item);

                // Auto-bold label before colon if present (e.g. "Fragmentasi: Penjelasan...")
                if (str_contains($escaped, ':')) {
                    $parts = explode(':', $escaped, 2);
                    $formattedText = '<strong>' . $parts[0] . ':</strong>' . $parts[1];
                } else {
                    $formattedText = $escaped;
                }

                $bullet = $ordered
                    ? '<span style="font-weight: bold; color: #047857; font-family: monospace; font-size: 8px;">' . $num . '.</span>'
                    : '<span style="font-weight: bold; color: #047857; font-size: 9px; line-height: 1;">&bull;</span>';

                $html .= '<tr>';
                $html .= '<td style="width: 16px; border: none; padding: 2px 0 3px 0; vertical-align: top; text-align: left;">' . $bullet . '</td>';
                $html .= '<td style="border: none; padding: 2px 0 3px 2px; vertical-align: top; font-size: 8.5px; line-height: 1.4; color: #1e293b;">' . $formattedText . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            return $html;
        }

        // Web / Tailwind responsive mode
        $html = '<ul class="space-y-2 my-1 text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">';
        foreach ($items as $idx => $item) {
            $num = $idx + 1;
            $escaped = htmlspecialchars((string)$item);

            if (str_contains($escaped, ':')) {
                $parts = explode(':', $escaped, 2);
                $formattedText = '<strong class="font-bold text-zinc-900 dark:text-zinc-100">' . $parts[0] . ':</strong>' . $parts[1];
            } else {
                $formattedText = $escaped;
            }

            $bullet = $ordered
                ? '<span class="font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0 select-none w-5">' . $num . '.</span>'
                : '<span class="text-emerald-500 mt-0.5 shrink-0 select-none">&bull;</span>';

            $html .= '<li class="flex items-start gap-2">';
            $html .= $bullet;
            $html .= '<div class="flex-1">' . $formattedText . '</div>';
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Web-friendly alias for formatPoints($content, $ordered, false).
     */
    public static function formatWebPoints(string|array|null $content, bool $ordered = true): string
    {
        return self::formatPoints($content, $ordered, false);
    }

    /**
     * Extract individual items from raw string or array.
     */
    public static function extractItems(string|array $content): array
    {
        if (is_array($content)) {
            $flat = [];
            foreach ($content as $c) {
                if (is_array($c)) {
                    $flat[] = $c['name'] ?? $c['title'] ?? $c['task'] ?? json_encode($c);
                } elseif (!empty(trim((string)$c))) {
                    $flat[] = trim((string)$c);
                }
            }
            return $flat;
        }

        $text = trim((string)$content);
        if (empty($text)) {
            return [];
        }

        // 1. Check arrow workflows (e.g. "Step A -> Step B -> Step C")
        if (preg_match('/(?:\s*->\s*|\s*→\s*)/u', $text) && !preg_match('/^\d+[\.\)]/u', $text)) {
            $steps = preg_split('/(?:\s*->\s*|\s*→\s*)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            if (count($steps) > 1) {
                return array_map('trim', $steps);
            }
        }

        // 2. Check newlines
        if (str_contains($text, "\n")) {
            $lines = preg_split('/\r\n|\r|\n/', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $clean[] = preg_replace('/^(?:[-*•]\s+|\d+[\.\)]\s*)/u', '', $line);
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        // 3. Check numbered list embedded in string (e.g. "1. Foo... 2. Bar..." or "1. Foo, 2. Bar")
        if (preg_match('/(?:^|\s+|,\s*|\.\s*)\d+[\.\)]\s+/u', $text)) {
            $parts = preg_split('/(?:^|\s+|,\s*|\.\s*)(?=\d+[\.\)]\s+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($parts as $p) {
                $p = trim($p, " \t\n\r\0\x0B,;");
                $p = preg_replace('/^\d+[\.\)]\s*/u', '', $p);
                if (!empty($p)) {
                    $clean[] = $p;
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        // 4. Check bullet points (e.g. "- Foo - Bar" or "• Foo • Bar")
        if (preg_match('/(?:^|\s+)[-•*]\s+/u', $text)) {
            $parts = preg_split('/(?:^|\s+)(?=[-•*]\s+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $clean = [];
            foreach ($parts as $p) {
                $p = trim($p, " \t\n\r\0\x0B,;");
                $p = preg_replace('/^[-•*]\s*/u', '', $p);
                if (!empty($p)) {
                    $clean[] = $p;
                }
            }
            if (count($clean) > 1) {
                return $clean;
            }
        }

        return [$text];
    }
}
