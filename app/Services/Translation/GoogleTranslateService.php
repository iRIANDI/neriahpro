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
        try {
            $cacheKey = 'gt_' . md5($source . '_' . $target . '_' . $text);
            return Cache::remember($cacheKey, now()->addDays(30), function () use ($text, $target, $source) {
                return self::directTranslate($text, $target, $source);
            });
        } catch (\Throwable $e) {
            // If cache storage is temporarily unavailable, fall back to direct translation
            return self::directTranslate($text, $target, $source);
        }
    }

    /**
     * Direct HTTP translation without relying on cache store.
     */
    public static function directTranslate(string $text, string $target = 'en', string $source = 'id'): string
    {
        $text = trim($text);
        if (empty($text) || $target === $source) {
            return $text;
        }

        try {
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=' . urlencode($source) . '&tl=' . urlencode($target) . '&dt=t&q=' . urlencode($text);
            $raw = null;

            if (class_exists(\Illuminate\Support\Facades\Http::class)) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(3)->get($url);
                    if ($response->successful()) {
                        $raw = $response->body();
                    }
                } catch (\Throwable) {
                    $raw = null;
                }
            }

            if ($raw === null && function_exists('file_get_contents')) {
                $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                $raw = @file_get_contents($url, false, $ctx);
            }

            if (!empty($raw)) {
                $body = json_decode($raw, true);
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
            // Silently fallback to original text
        }

        return $text;
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
