<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleTranslateService
{
    /**
     * Translate text from source language to target language.
     * Supported: 'id' <-> 'en' or any standard ISO-639-1 code.
     */
    public static function translate(string $text, string $target = 'en', string $source = 'id'): string
    {
        $text = trim($text);
        if (empty($text)) {
            return '';
        }

        if ($target === $source) {
            return $text;
        }

        // Cache translation to avoid duplicate network requests
        $cacheKey = 'gt_' . md5($source . '_' . $target . '_' . $text);
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($text, $target, $source) {
            try {
                $response = Http::timeout(6)->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => $source,
                    'tl' => $target,
                    'dt' => 't',
                    'q' => $text,
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    if (is_array($body) && isset($body[0]) && is_array($body[0])) {
                        $translated = '';
                        foreach ($body[0] as $segment) {
                            if (isset($segment[0])) {
                                $translated .= $segment[0];
                            }
                        }
                        if (!empty($translated)) {
                            return trim($translated);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('GoogleTranslateService failed: ' . $e->getMessage());
            }

            // Return original text gracefully if translation fails
            return $text;
        });
    }

    /**
     * Translate Indonesian text to English.
     */
    public static function idToEn(string $text): string
    {
        return self::translate($text, 'en', 'id');
    }

    /**
     * Translate English text to Indonesian.
     */
    public static function enToId(string $text): string
    {
        return self::translate($text, 'id', 'en');
    }
}
