<?php

namespace App\Services\Ai;

use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MultiAiModelManager
{
    /**
     * Get configured API key for a specific provider.
     * Prioritizes Admin Panel (CmsGlobalSetting) over environment variables.
     */
    public static function getApiKey(string $provider): ?string
    {
        try {
            $settingKey = "ai_{$provider}_api_key";
            $dbKey = CmsGlobalSetting::where('key', $settingKey)->value('value');
            if (!empty($dbKey) && is_string($dbKey) && trim($dbKey) !== '') {
                return trim($dbKey);
            }

            // Also check alternate alias if provider is xai / grok
            if ($provider === 'xai') {
                $grokKey = CmsGlobalSetting::where('key', 'ai_grok_api_key')->value('value');
                if (!empty($grokKey) && is_string($grokKey) && trim($grokKey) !== '') {
                    return trim($grokKey);
                }
            }
        } catch (\Throwable $e) {
            // Database might be during migration or cached
        }

        $envKey = config("ai.providers.{$provider}.api_key");
        return (!empty($envKey) && is_string($envKey) && trim($envKey) !== '') ? trim($envKey) : null;
    }

    /**
     * Get model name for a provider and tier, checking CmsGlobalSetting first.
     */
    public static function getModel(string $provider, string $tier = 'discovery'): string
    {
        try {
            $dbModel = CmsGlobalSetting::where('key', "ai_{$provider}_{$tier}_model")->value('value');
            if (!empty($dbModel) && is_string($dbModel) && trim($dbModel) !== '') {
                return trim($dbModel);
            }
        } catch (\Throwable) {}

        $providerConfig = config("ai.providers.{$provider}");
        return $providerConfig['models'][$tier] ?? ($providerConfig['models']['discovery'] ?? 'default');
    }

    /**
     * Get base URL for an AI provider, checking CmsGlobalSetting first.
     */
    public static function getBaseUrl(string $provider): string
    {
        try {
            $dbUrl = CmsGlobalSetting::where('key', "ai_{$provider}_base_url")->value('value');
            if (!empty($dbUrl) && is_string($dbUrl) && trim($dbUrl) !== '') {
                return rtrim(trim($dbUrl), '/');
            }
        } catch (\Throwable) {}

        $config = config("ai.providers.{$provider}");
        return rtrim($config['base_url'] ?? 'https://api.openai.com/v1', '/');
    }

    /**
     * Get real-time health and token limit status for all AI models.
     */
    public static function getCatalog(): array
    {
        $providers = config('ai.providers', []);
        $catalog = [];

        foreach ($providers as $key => $info) {
            $apiKey = self::getApiKey($key);
            $hasKey = !empty($apiKey);
            $cachedStatus = Cache::get("ai_model_status_{$key}");

            // STRICT RULE: If NO API Key is configured in backend admin, it is UNCONFIGURED.
            // Under NO circumstance can a provider be 'healthy' or 'ready' without an API key!
            if (!$hasKey) {
                $status = 'unconfigured';
                $statusLabel = 'Belum Ada Key';
                $statusColor = 'zinc';
            } elseif ($cachedStatus === 'exhausted') {
                $status = 'exhausted';
                $statusLabel = 'Limit Habis (Cooldown)';
                $statusColor = 'rose';
            } elseif ($cachedStatus === 'warning') {
                $status = 'warning';
                $statusLabel = 'Mendekati Limit';
                $statusColor = 'amber';
            } else {
                $status = 'healthy';
                $statusLabel = 'Sehat (Aktif)';
                $statusColor = 'emerald';
            }

            $cooldownSec = 0;
            if ($status === 'exhausted') {
                $cooldownUntil = Cache::get("ai_cooldown_until_{$key}");
                if ($cooldownUntil) {
                    $cooldownSec = max(0, $cooldownUntil - time());
                }
            }

            $catalog[$key] = [
                'key' => $key,
                'name' => $info['name'],
                'is_free' => (bool) ($info['is_free_tier'] ?? false),
                'has_key' => $hasKey,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'cooldown_sec' => $cooldownSec,
                'models' => $info['models'] ?? [],
                'recommended_for' => self::getRecommendation($key),
            ];
        }

        return $catalog;
    }

    protected static function getRecommendation(string $key): string
    {
        return match ($key) {
            'deepseek' => 'DeepSeek-R1 SOTA Reasoning & Ekstra Hemat (Sangat Direkomendasikan untuk PRD)',
            'gemini' => 'Gemini 2.5 Pro / Flash (Konteks Raksasa 2M Tokens & Cepat)',
            'anthropic' => 'Claude 3.7 Sonnet (Hybrid Thinking & Standar Tertinggi Rekayasa Software)',
            'openai' => 'ChatGPT GPT-4o / o3-mini (High Precision Architectural Standard)',
            'xai' => 'Grok-2 (Real-Time Knowledge & High Accuracy Reasoning)',
            'groq' => 'Groq LPU Llama 3.3 70B (Inference 500 Tokens/Detik & Free Tier)',
            'openrouter' => 'OpenRouter Hub (Akses Model Gratis & Fleksibilitas Tinggi)',
            'relayrouter' => 'RelayRouter AI (Universal Aggregator Shopee API Key: Akses Fleksibel Claude 3.7, GPT-4o & DeepSeek dalam 1 Key)',
            default => 'General AI Engine',
        };
    }

    /**
     * Record failure and apply circuit breaker cooldown if token exhausted or rate-limited.
     */
    public static function recordFailure(string $provider, string $error, int $statusCode = 0): void
    {
        Log::warning("AI Provider [{$provider}] failed with code {$statusCode}: {$error}");

        $cooldownMinutes = (int) config('ai.cooldown_minutes', 15);
        $cooldownUntil = time() + ($cooldownMinutes * 60);

        // 429 = Rate Limit Exceeded, 402/403 = Quota / Balance Exhausted
        if ($statusCode === 429 || $statusCode === 402 || $statusCode === 403 || str_contains(strtolower($error), 'quota') || str_contains(strtolower($error), 'rate')) {
            Cache::put("ai_model_status_{$provider}", 'exhausted', now()->addMinutes($cooldownMinutes));
            Cache::put("ai_cooldown_until_{$provider}", $cooldownUntil, now()->addMinutes($cooldownMinutes));
        } else {
            // General failure, mark as warning
            Cache::put("ai_model_status_{$provider}", 'warning', now()->addMinutes(5));
        }
    }

    /**
     * Record success and mark provider as healthy.
     */
    public static function recordSuccess(string $provider): void
    {
        Cache::put("ai_model_status_{$provider}", 'healthy', now()->addHours(1));
        Cache::forget("ai_cooldown_until_{$provider}");
    }

    /**
     * Orchestrated execution with automatic graceful token failover.
     *
     * @param string $prompt
     * @param string $systemInstruction
     * @param string $tier 'discovery' | 'prd'
     * @param string|null $preferredProvider
     * @return array
     */
    public static function executeWithFailover(
        string $prompt,
        string $systemInstruction = '',
        string $tier = 'discovery',
        ?string $preferredProvider = null
    ): array {
        // Auto-detect preferred provider from backend admin settings if not explicitly passed
        if (empty($preferredProvider) || $preferredProvider === 'auto') {
            try {
                $savedDefault = CmsGlobalSetting::where('key', 'ai_default_provider')->value('value');
                if (!empty($savedDefault) && $savedDefault !== 'auto') {
                    $preferredProvider = $savedDefault;
                }
            } catch (\Throwable) {}
        }

        $defaultChain = config('ai.failover_chain', ['deepseek', 'gemini', 'anthropic', 'openai', 'relayrouter', 'xai', 'groq', 'openrouter']);

        // STRICT RULE: Only consider providers that actually have configured API keys in the backend admin / config
        $configuredProviders = array_values(array_filter($defaultChain, fn($p) => !empty(self::getApiKey($p))));

        // If preferred provider requested and configured, prioritize it; otherwise use configured chain
        if ($preferredProvider && $preferredProvider !== 'auto' && in_array($preferredProvider, $configuredProviders)) {
            $chain = array_unique(array_merge([$preferredProvider], $configuredProviders));
        } else {
            $chain = $configuredProviders;
        }

        $failedAttempts = [];

        // If no providers are configured at all, return deterministic engine immediately without failing remote calls
        if (empty($chain)) {
            return [
                'success' => false,
                'text' => null,
                'provider' => 'deterministic_heuristic',
                'provider_name' => 'Neriah Pro Deterministic Architecture Engine',
                'model' => 'expert-rule-engine-v1',
                'fallback_occurred' => false,
                'failed_attempts' => [
                    [
                        'provider' => 'none',
                        'reason' => 'Belum ada API Key provider AI yang diisi di Admin Panel.',
                    ]
                ],
                'notification' => 'Belum ada API Key provider AI yang dikonfigurasi di Admin Panel. Menggunakan Deterministic Architecture Engine.',
            ];
        }

        foreach ($chain as $provider) {
            $status = Cache::get("ai_model_status_{$provider}");
            if ($status === 'exhausted') {
                $failedAttempts[] = [
                    'provider' => $provider,
                    'reason' => 'Sedang dalam masa cooldown (token limit / 429)',
                ];
                continue;
            }

            $apiKey = self::getApiKey($provider);
            if (empty($apiKey)) {
                continue;
            }

            $providerConfig = config("ai.providers.{$provider}");
            $modelName = self::getModel($provider, $tier);

            try {
                $resultText = match ($provider) {
                    'gemini' => self::callGemini($apiKey, $modelName, $prompt, $systemInstruction, $tier),
                    'anthropic' => self::callAnthropic($apiKey, $modelName, $prompt, $systemInstruction, $tier),
                    default => self::callOpenAiCompatible($provider, $apiKey, $modelName, $prompt, $systemInstruction, $tier),
                };

                if (!empty($resultText)) {
                    self::recordSuccess($provider);

                    $realFailures = array_filter($failedAttempts, fn($f) => $f['reason'] !== 'API Key belum dikonfigurasi');
                    $fallbackOccurred = count($realFailures) > 0;
                    $notification = null;

                    if ($fallbackOccurred) {
                        $failedNames = implode(', ', array_map(fn($f) => strtoupper($f['provider']), $realFailures));
                        $notification = "Model [{$failedNames}] tidak tersedia / habis token. Sistem otomatis mengalihkan sintesis ke [{$providerConfig['name']} - {$modelName}]!";
                    }

                    return [
                        'success' => true,
                        'text' => $resultText,
                        'provider' => $provider,
                        'provider_name' => $providerConfig['name'],
                        'model' => $modelName,
                        'fallback_occurred' => $fallbackOccurred,
                        'failed_attempts' => $failedAttempts,
                        'notification' => $notification,
                    ];
                }
            } catch (\Throwable $e) {
                self::recordFailure($provider, $e->getMessage(), (int) $e->getCode());
                $failedAttempts[] = [
                    'provider' => $provider,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        // All configured remote AI providers were exhausted, in cooldown, or failed
        $realFailures = array_filter($failedAttempts, fn($f) => $f['reason'] !== 'API Key belum dikonfigurasi');
        $fallbackOccurred = count($realFailures) > 0;
        $notification = $fallbackOccurred
            ? 'Seluruh remote AI model yang terkonfigurasi sedang cooldown / batas token tercapai. Sistem otomatis menggunakan Deterministic Architecture Engine berstandar industri tanpa downtime.'
            : null;

        return [
            'success' => false,
            'text' => null,
            'provider' => 'deterministic_heuristic',
            'provider_name' => 'Neriah Pro Deterministic Architecture Engine',
            'model' => 'expert-rule-engine-v1',
            'fallback_occurred' => $fallbackOccurred,
            'failed_attempts' => $failedAttempts,
            'notification' => $notification,
        ];
    }

    /**
     * Call Google Gemini API
     */
    protected static function callGemini(string $apiKey, string $model, string $prompt, string $system, string $tier): ?string
    {
        $maxTokens = $tier === 'prd' ? 4000 : 800;
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => $maxTokens,
            ]
        ];

        if (!empty($system)) {
            $payload['systemInstruction'] = ['parts' => [['text' => $system]]];
        }

        $timeout = $tier === 'prd' ? 30 : 10;
        $response = Http::timeout($timeout)->post($url, $payload);

        if ($response->successful()) {
            $candidates = $response->json('candidates', []);
            return $candidates[0]['content']['parts'][0]['text'] ?? null;
        }

        self::recordFailure('gemini', $response->body(), $response->status());
        return null;
    }

    /**
     * Call Anthropic Claude API
     */
    protected static function callAnthropic(string $apiKey, string $model, string $prompt, string $system, string $tier): ?string
    {
        $maxTokens = $tier === 'prd' ? 4000 : 800;
        $url = "https://api.anthropic.com/v1/messages";

        $payload = [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2,
        ];

        if (!empty($system)) {
            $payload['system'] = $system;
        }

        $timeout = $tier === 'prd' ? 30 : 10;
        $response = Http::timeout($timeout)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            $content = $response->json('content', []);
            return $content[0]['text'] ?? null;
        }

        self::recordFailure('anthropic', $response->body(), $response->status());
        return null;
    }

    /**
     * Call OpenAI-compatible providers: OpenAI, DeepSeek, xAI Grok, Groq, OpenRouter
     */
    protected static function callOpenAiCompatible(string $provider, string $apiKey, string $model, string $prompt, string $system, string $tier): ?string
    {
        $baseUrl = self::getBaseUrl($provider);
        $url = "{$baseUrl}/chat/completions";

        $maxTokens = $tier === 'prd' ? 4000 : 800;

        $messages = [];
        if (!empty($system)) {
            $messages[] = ['role' => 'system', 'content' => $system];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => 0.2,
        ];

        $headers = [
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ];

        if ($provider === 'openrouter') {
            $headers['HTTP-Referer'] = 'https://neriahpro.com';
            $headers['X-Title'] = 'Neriah Pro Studio OS';
        }

        $timeout = $tier === 'prd' ? 35 : 12;
        $response = Http::timeout($timeout)
            ->withHeaders($headers)
            ->post($url, $payload);

        if ($response->successful()) {
            $choices = $response->json('choices', []);
            return $choices[0]['message']['content'] ?? null;
        }

        self::recordFailure($provider, $response->body(), $response->status());
        return null;
    }

    /**
     * Fetch available models dynamically from provider API endpoint.
     * Caches models for 24 hours to eliminate redundant network overhead.
     *
     * @param string $provider
     * @param string|null $apiKey
     * @param string|null $baseUrl
     * @param bool $forceRefresh
     * @return array ['success' => bool, 'models' => string[], 'count' => int, 'from_cache' => bool, 'error' => ?string]
     */
    public static function fetchAvailableModels(
        string $provider,
        ?string $apiKey = null,
        ?string $baseUrl = null,
        bool $forceRefresh = false
    ): array {
        $apiKey = $apiKey ?: self::getApiKey($provider);
        $baseUrl = $baseUrl ?: self::getBaseUrl($provider);

        $cacheKey = "ai_models_cache_{$provider}_" . substr(md5(($apiKey ?? '') . '|' . ($baseUrl ?? '')), 0, 16);

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey, []);
            if (!empty($cached) && is_array($cached)) {
                return [
                    'success' => true,
                    'models' => array_values($cached),
                    'count' => count($cached),
                    'from_cache' => true,
                    'error' => null,
                ];
            }
        }

        if (empty($apiKey)) {
            return [
                'success' => false,
                'models' => self::getFallbackModels($provider),
                'count' => count(self::getFallbackModels($provider)),
                'from_cache' => false,
                'error' => 'API Key belum dikonfigurasi.',
            ];
        }

        try {
            $models = [];

            if ($provider === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
                $response = Http::timeout(8)->get($url);

                if ($response->successful()) {
                    $rawModels = $response->json('models', []);
                    foreach ($rawModels as $item) {
                        $methods = $item['supportedGenerationMethods'] ?? [];
                        if (in_array('generateContent', $methods)) {
                            $name = $item['name'] ?? '';
                            $cleanName = preg_replace('/^models\//', '', $name);
                            if (!empty($cleanName)) {
                                $models[] = $cleanName;
                            }
                        }
                    }
                } else {
                    throw new \RuntimeException($response->json('error.message', 'Gagal memuat model dari Google Gemini API'));
                }
            } elseif ($provider === 'anthropic') {
                $url = "https://api.anthropic.com/v1/models";
                $response = Http::timeout(8)
                    ->withHeaders([
                        'x-api-key' => $apiKey,
                        'anthropic-version' => '2023-06-01',
                    ])
                    ->get($url);

                if ($response->successful()) {
                    $rawModels = $response->json('data', []);
                    foreach ($rawModels as $item) {
                        if (!empty($item['id'])) {
                            $models[] = $item['id'];
                        }
                    }
                } else {
                    throw new \RuntimeException($response->json('error.message', 'Gagal memuat model dari Anthropic API'));
                }
            } else {
                // OpenAI-compatible format (RelayRouter, OpenAI, Groq, OpenRouter, DeepSeek, xAI, OneAPI, etc.)
                $endpoint = rtrim($baseUrl, '/') . '/models';
                $headers = [
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ];

                if ($provider === 'openrouter') {
                    $headers['HTTP-Referer'] = 'https://neriahpro.com';
                    $headers['X-Title'] = 'Neriah Pro Studio OS';
                }

                $response = Http::timeout(10)
                    ->withHeaders($headers)
                    ->get($endpoint);

                if ($response->successful()) {
                    $json = $response->json();
                    $rawData = $json['data'] ?? (is_array($json) && isset($json[0]) ? $json : []);
                    foreach ($rawData as $item) {
                        if (is_array($item) && !empty($item['id'])) {
                            $models[] = trim($item['id']);
                        } elseif (is_string($item) && !empty($item)) {
                            $models[] = trim($item);
                        }
                    }
                } else {
                    $msg = $response->json('error.message') ?? $response->body();
                    throw new \RuntimeException("HTTP {$response->status()}: " . substr($msg, 0, 200));
                }
            }

            $models = array_values(array_unique(array_filter($models)));
            natcasesort($models);
            $models = array_values($models);

            if (!empty($models)) {
                Cache::put($cacheKey, $models, now()->addHours(24));
                Cache::put("ai_models_fallback_{$provider}", $models, now()->addDays(7));

                return [
                    'success' => true,
                    'models' => $models,
                    'count' => count($models),
                    'from_cache' => false,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'models' => self::getFallbackModels($provider),
                'count' => count(self::getFallbackModels($provider)),
                'from_cache' => false,
                'error' => 'Tidak ada model yang ditemukan dalam respons API.',
            ];
        } catch (\Throwable $e) {
            Log::warning("Gagal fetch models dari provider [{$provider}]: " . $e->getMessage());

            $fallback = Cache::get("ai_models_fallback_{$provider}");
            if (!empty($fallback) && is_array($fallback)) {
                return [
                    'success' => false,
                    'models' => array_values($fallback),
                    'count' => count($fallback),
                    'from_cache' => true,
                    'error' => $e->getMessage(),
                ];
            }

            $defaults = self::getFallbackModels($provider);
            return [
                'success' => false,
                'models' => $defaults,
                'count' => count($defaults),
                'from_cache' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get array of model IDs for HTML datalist or auto-complete.
     */
    public static function getDatalistOptions(string $provider, ?string $apiKey = null, ?string $baseUrl = null): array
    {
        $result = self::fetchAvailableModels($provider, $apiKey, $baseUrl, forceRefresh: false);
        return $result['models'] ?? self::getFallbackModels($provider);
    }

    /**
     * Curated fallback models when API key is missing or offline.
     */
    public static function getFallbackModels(string $provider): array
    {
        return match ($provider) {
            'relayrouter' => [
                'deepseek-chat',
                'deepseek-v3',
                'gpt-4o-mini',
                'gpt-4o',
                'claude-3-5-haiku',
                'claude-3-7-sonnet-20250219',
                'claude-sonnet-4-5-20250929',
                'gemini-2.0-flash-lite',
                'gemini-2.5-flash',
                'qwen-2.5-72b',
            ],
            'deepseek' => ['deepseek-chat', 'deepseek-reasoner'],
            'gemini' => ['gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-1.5-flash', 'gemini-2.5-flash'],
            'anthropic' => ['claude-3-7-sonnet-20250219', 'claude-3-5-sonnet-20241022', 'claude-3-5-haiku-20241022'],
            'openai' => ['gpt-4o-mini', 'gpt-4o', 'o3-mini', 'o1'],
            'groq' => ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'mixtral-8x7b-32768'],
            'openrouter' => ['meta-llama/llama-3.3-70b-instruct:free', 'deepseek/deepseek-r1:free', 'anthropic/claude-3.7-sonnet'],
            'xai' => ['grok-2-1212', 'grok-beta'],
            default => ['default'],
        };
    }
}
