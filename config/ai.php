<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-AI Model Providers & API Keys
    |--------------------------------------------------------------------------
    | Supports multiple AI vendors with automated token failover and health telemetry.
    | Reads from CmsGlobalSetting first (configured in Admin Panel) with fallback to env.
    */
    'providers' => [
        'gemini' => [
            'name' => 'Google Gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'models' => [
                'discovery' => env('GEMINI_DISCOVERY_MODEL', 'gemini-2.5-flash'),
                'prd' => env('GEMINI_PRD_MODEL', 'gemini-2.5-pro'),
                'fallback' => 'gemini-1.5-flash',
            ],
            'is_free_tier' => true,
        ],
        'deepseek' => [
            'name' => 'DeepSeek AI',
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => 'https://api.deepseek.com',
            'models' => [
                'discovery' => 'deepseek-chat',
                'prd' => 'deepseek-reasoner', // DeepSeek-R1 SOTA Reasoning
                'fallback' => 'deepseek-chat',
            ],
            'is_free_tier' => true, // Extremely economical / Free credits
        ],
        'anthropic' => [
            'name' => 'Anthropic Claude',
            'api_key' => env('ANTHROPIC_API_KEY'),
            'base_url' => 'https://api.anthropic.com/v1',
            'models' => [
                'discovery' => 'claude-3-5-haiku-20241022',
                'prd' => 'claude-3-7-sonnet-20250219', // Claude 3.7 Sonnet Hybrid Thinking
                'fallback' => 'claude-3-5-sonnet-20241022',
            ],
            'is_free_tier' => false,
        ],
        'openai' => [
            'name' => 'OpenAI ChatGPT',
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1',
            'models' => [
                'discovery' => 'gpt-4o-mini',
                'prd' => 'gpt-4o', // Flagship multimodal
                'fallback' => 'o3-mini',
            ],
            'is_free_tier' => false,
        ],
        'xai' => [
            'name' => 'xAI Grok',
            'api_key' => env('GROK_API_KEY'),
            'base_url' => 'https://api.x.ai/v1',
            'models' => [
                'discovery' => 'grok-beta',
                'prd' => 'grok-2-1212',
                'fallback' => 'grok-beta',
            ],
            'is_free_tier' => false,
        ],
        'groq' => [
            'name' => 'Groq LPU (Ultra-Fast & Free Tier)',
            'api_key' => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
            'models' => [
                'discovery' => 'llama-3.3-70b-versatile',
                'prd' => 'llama-3.3-70b-versatile',
                'fallback' => 'mixtral-8x7b-32768',
            ],
            'is_free_tier' => true,
        ],
        'openrouter' => [
            'name' => 'OpenRouter Universal Hub',
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => 'https://openrouter.ai/api/v1',
            'models' => [
                'discovery' => 'meta-llama/llama-3.3-70b-instruct:free',
                'prd' => 'deepseek/deepseek-r1:free',
                'fallback' => 'qwen/qwen-2.5-72b-instruct',
            ],
            'is_free_tier' => true,
        ],
        'relayrouter' => [
            'name' => 'RelayRouter AI (Universal Aggregator / Shopee Key)',
            'api_key' => env('RELAYROUTER_API_KEY'),
            'base_url' => env('RELAYROUTER_BASE_URL', 'https://api.relayrouter.ai/v1'),
            'models' => [
                'discovery' => env('RELAYROUTER_DISCOVERY_MODEL', 'gpt-4o-mini'),
                'prd' => env('RELAYROUTER_PRD_MODEL', 'claude-3-7-sonnet-20250219'),
                'fallback' => 'deepseek-chat',
            ],
            'is_free_tier' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failover Pipeline Order
    |--------------------------------------------------------------------------
    | When a model exhausts its token budget or hits 429 rate limit,
    | the orchestrator automatically cascades down this chain.
    */
    'failover_chain' => [
        'deepseek',
        'gemini',
        'anthropic',
        'openai',
        'relayrouter',
        'xai',
        'groq',
        'openrouter',
    ],

    'cooldown_minutes' => 15,
];
