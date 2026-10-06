<?php

namespace App\Services\Ai;

class GeminiAdaptiveService
{
    /**
     * Tier 1: Fast discovery & proactive ideation.
     * Automatically failovers across DeepSeek, Gemini, Claude, ChatGPT, Grok, and Groq.
     */
    public static function callBlueprintTier(string $prompt, string $systemInstruction = '', ?string $preferredProvider = null): ?string
    {
        $res = MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'discovery', $preferredProvider);
        return $res['text'] ?? null;
    }

    /**
     * Tier 2: Deep reasoning capable model for full PRD synthesis and ERD PostgreSQL schema.
     * Uses flagship reasoning models (DeepSeek-R1, Gemini 2.5 Pro, Claude 3.7 Sonnet, o3-mini) with automatic failover.
     */
    public static function callPrdTier(string $prompt, string $systemInstruction = '', ?string $preferredProvider = null): ?string
    {
        $res = MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'prd', $preferredProvider);
        return $res['text'] ?? null;
    }
}
