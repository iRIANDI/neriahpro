<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAdaptiveService
{
    /**
     * Tier 1: Lightweight, fast model for Blueprint discovery & proactive guidance.
     * Capped strictly at ~400 output tokens to ensure zero token waste on free/low-tier plans.
     */
    public static function callBlueprintTier(string $prompt, string $systemInstruction = ''): ?string
    {
        $apiKey = config('ai.gemini_api_key');
        if (empty($apiKey)) {
            return null; // Gracefully fallback to deterministic heuristics
        }

        $model = config('ai.blueprint_model', 'gemini-1.5-flash');
        $maxTokens = config('ai.blueprint_max_tokens', 400);
        $temperature = config('ai.blueprint_temperature', 0.2);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ]
        ];

        if (!empty($systemInstruction)) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        try {
            $response = Http::timeout(8)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $candidates = $response->json('candidates', []);
                return $candidates[0]['content']['parts'][0]['text'] ?? null;
            }

            Log::warning("Gemini Blueprint tier returned non-200 status: " . $response->status(), [
                'body' => $response->body()
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::warning("Gemini Blueprint tier call failed gracefully: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tier 2: Deep reasoning capable model for full PRD synthesis and ERD PostgreSQL schema.
     * Only executed upon final contract locking.
     */
    public static function callPrdTier(string $prompt, string $systemInstruction = ''): ?string
    {
        $apiKey = config('ai.gemini_api_key');
        if (empty($apiKey)) {
            return null;
        }

        $model = config('ai.prd_model', 'gemini-1.5-pro');
        $maxTokens = config('ai.prd_max_tokens', 4000);
        $temperature = config('ai.prd_temperature', 0.3);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ]
        ];

        if (!empty($systemInstruction)) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        try {
            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $candidates = $response->json('candidates', []);
                return $candidates[0]['content']['parts'][0]['text'] ?? null;
            }

            Log::warning("Gemini PRD tier returned non-200 status: " . $response->status(), [
                'body' => $response->body()
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::warning("Gemini PRD tier call failed gracefully: " . $e->getMessage());
            return null;
        }
    }
}
