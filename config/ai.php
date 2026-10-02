<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Key
    |--------------------------------------------------------------------------
    | Supports standard GEMINI_API_KEY environment variable.
    | Free tier friendly with automated rate-limit fallbacks.
    */
    'gemini_api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Adaptive AI Model Tiering (Anti-Boncos Protocol)
    |--------------------------------------------------------------------------
    | Tier 1: Lightweight, fast model for Blueprint discovery & proactive guidance.
    | Strictly token-capped (max 400 tokens) to ensure zero cost wastage.
    |
    | Tier 2: Deep reasoning capable model for final PRD & ERD PostgreSQL generation.
    | Only executed once when client officially locks the contract.
    */
    'blueprint_model' => env('GEMINI_BLUEPRINT_MODEL', 'gemini-1.5-flash'),
    'blueprint_max_tokens' => (int) env('GEMINI_BLUEPRINT_MAX_TOKENS', 400),
    'blueprint_temperature' => 0.2,

    'prd_model' => env('GEMINI_PRD_MODEL', 'gemini-1.5-pro'),
    'prd_max_tokens' => (int) env('GEMINI_PRD_MAX_TOKENS', 4000),
    'prd_temperature' => 0.3,
];
