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
    public static function translate(string $text, string $target = 'en', string $source = 'id', bool $allowAiFallback = false): string
    {
        $text = trim($text);
        if (empty($text)) {
            return '';
        }

        if ($target === $source) {
            return $text;
        }

        // Cache translation to avoid duplicate network requests (only cache when translation actually occurred)
        try {
            $cacheKey = 'gt_' . md5($source . '_' . $target . '_' . $text);
            $cached = Cache::get($cacheKey);
            if (!empty($cached) && $cached !== $text) {
                return $cached;
            }

            $translated = self::directTranslate($text, $target, $source, $allowAiFallback);
            if (!empty($translated) && $translated !== $text) {
                Cache::put($cacheKey, $translated, now()->addDays(30));
            }
            return $translated;
        } catch (\Throwable $e) {
            return self::directTranslate($text, $target, $source, $allowAiFallback);
        }
    }

    /**
     * Direct HTTP translation without relying on cache store.
     */
    public static function directTranslate(string $text, string $target = 'en', string $source = 'id', bool $allowAiFallback = false): string
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
                    $response = \Illuminate\Support\Facades\Http::timeout(2)->get($url);
                    if ($response->successful()) {
                        $raw = $response->body();
                    }
                } catch (\Throwable) {
                    $raw = null;
                }
            }

            if ($raw === null && function_exists('file_get_contents')) {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
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
            // Log for monitoring
            Log::debug("GoogleTranslateService directTranslate HTTP error: " . $e->getMessage());
        }

        // Secondary Fallback: Multi-AI Model Failover (only if explicitly enabled to prevent blocking document generators)
        if ($allowAiFallback) {
            try {
                if (class_exists(\App\Services\Ai\MultiAiModelManager::class)) {
                    $langMap = ['en' => 'English', 'id' => 'Indonesian'];
                    $targetName = $langMap[$target] ?? $target;
                    $sourceName = $langMap[$source] ?? $source;
                    $prompt = "Translate the following {$sourceName} text into clear, professional {$targetName}. Output ONLY the translated text, preserving original numbering, list structure, and technical terminology. Do NOT add quotes, markdown fences, preambles, or conversational text:\n\n" . $text;
                    $aiRes = \App\Services\Ai\MultiAiModelManager::executeWithFailover(
                        $prompt,
                        "You are a professional technical software specification translator.",
                        'discovery'
                    );

                    if (!empty($aiRes['text'])) {
                        $cleaned = trim($aiRes['text']);
                        if (preg_match('/^```(?:json|markdown)?\s*([\s\S]*?)\s*```$/i', $cleaned, $m)) {
                            $cleaned = trim($m[1]);
                        }
                        if (preg_match('/^["\']([\s\S]*)["\']$/s', $cleaned, $m) && !str_contains($m[1], "\n")) {
                            $cleaned = trim($m[1]);
                        }
                        if (!empty($cleaned)) {
                            return $cleaned;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("GoogleTranslateService MultiAi failover error: " . $e->getMessage());
            }
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
