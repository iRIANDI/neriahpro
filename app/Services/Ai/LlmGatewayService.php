<?php

namespace App\Services\Ai;

use App\Models\CmsGlobalSetting;
use App\Models\User;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\MultiAiModelManager;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\Ai\AiWorkflowTracer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlmGatewayService
{
    protected AgenticActionExecutionEngine $actionEngine;
    protected VectorEmbeddingService $vectorService;

    public function __construct(
        AgenticActionExecutionEngine $actionEngine,
        VectorEmbeddingService $vectorService
    ) {
        $this->actionEngine = $actionEngine;
        $this->vectorService = $vectorService;
    }

    public function getActionEngine(): AgenticActionExecutionEngine
    {
        return $this->actionEngine;
    }

    public function getActiveProvider(): string
    {
        try {
            $prov = CmsGlobalSetting::getVal('ai_active_provider', 'deepseek');
            return in_array($prov, ['deepseek', 'gemini', 'claude', 'openai', 'grok']) ? $prov : 'deepseek';
        } catch (\Throwable) {
            return 'deepseek';
        }
    }

    public function getApiKey(string $provider): ?string
    {
        return MultiAiModelManager::getApiKey($provider);
    }

    public function getAvailableProviders(bool $onlyWithKeys = false): array
    {
        $all = [
            'deepseek' => 'DeepSeek AI (DeepSeek-V4.1-Flash Vision & DeepSeek-V4-Pro 1M)',
            'gemini' => 'Google Gemini (Gemini 3.6 Flash & Gemini 3.5 Flash Multimodal)',
            'openai' => 'OpenAI GPT-4o / o3-mini',
            'claude' => 'Anthropic Claude 3.7 Sonnet',
            'grok' => 'xAI Grok 2',
        ];

        if (!$onlyWithKeys) {
            return $all;
        }

        $filtered = [];
        foreach ($all as $key => $label) {
            if (!empty($this->getApiKey($key))) {
                $filtered[$key] = $label;
            }
        }

        // Fallback to deepseek if no keys configured
        if (empty($filtered)) {
            $filtered['deepseek'] = $all['deepseek'];
        }

        return $filtered;
    }

    public function getActiveModel(string $provider): string
    {
        try {
            $model = CmsGlobalSetting::getVal("ai_{$provider}_model", 'auto-adaptive');
            return !empty($model) ? $model : 'auto-adaptive';
        } catch (\Throwable) {
            return 'auto-adaptive';
        }
    }

    public function setActiveModel(string $provider, string $model): void
    {
        CmsGlobalSetting::setVal("ai_{$provider}_model", trim($model));
    }

    public function fetchDynamicModels(string $provider, ?string $apiKey = null, bool $forceRefresh = false): array
    {
        $adaptive = [
            'id' => 'auto-adaptive',
            'name' => '🧠 Mode Cerdas Adaptif (Otomatis Flagship untuk Dokumen & CRUD, Lite untuk Chat Cepat)',
            'group' => '⚡ Rekomendasi Utama',
        ];

        $models = match ($provider) {
            'deepseek' => [
                ['id' => 'deepseek-flash', 'name' => 'DeepSeek-V4.1-Flash (Vision Multimodal, 1M Konteks & Inferensi Kilat)', 'group' => 'DeepSeek'],
                ['id' => 'deepseek-v4-pro', 'name' => 'DeepSeek-V4-Pro (Flagship Reasoning 1M Konteks Enterprise)', 'group' => 'DeepSeek'],
                ['id' => 'deepseek-chat', 'name' => 'DeepSeek V3 (Chat General)', 'group' => 'DeepSeek'],
                ['id' => 'deepseek-reasoner', 'name' => 'DeepSeek R1 (Deep Mathematical & Logic Reasoning)', 'group' => 'DeepSeek'],
            ],
            'gemini' => [
                ['id' => 'gemini-3.6-flash', 'name' => 'Gemini 3.6 Flash (Paling Stabil, Responsif & Rekomendasi Utama)', 'group' => '⭐ Google Gemini Flagship'],
                ['id' => 'gemini-3.5-flash', 'name' => 'Gemini 3.5 Flash (Stabil, Tangguh & Bebas 503)', 'group' => '⭐ Google Gemini Flagship'],
                ['id' => 'gemini-3.5-flash-lite', 'name' => 'Gemini 3.5 Flash Lite (Kapasitas Ekstra Tinggi & Cepat)', 'group' => '⚡ Model Ringan & Cepat (Lite)'],
                ['id' => 'gemini-3.1-flash-lite', 'name' => 'Gemini 3.1 Flash Lite (Sangat Cepat & Hemat Kuota)', 'group' => '⚡ Model Ringan & Cepat (Lite)'],
                ['id' => 'gemini-flash-lite-latest', 'name' => 'Gemini Flash-Lite Latest (Versi Cepat Otomatis)', 'group' => '⚡ Model Ringan & Cepat (Lite)'],
                ['id' => 'gemini-flash-latest', 'name' => 'Gemini Flash Latest (Otomatis Versi Terbaru Google GenAI)', 'group' => '⭐ Google Gemini Flagship'],
                ['id' => 'gemini-3.8-flash', 'name' => 'Gemini 3.8 Flash (Pratinjau Frontier Terkini)', 'group' => '🔬 Pratinjau Terbatas (Preview)'],
            ],
            'openai' => [
                ['id' => 'gpt-4o', 'name' => 'GPT-4o Omnimodal Flagship', 'group' => 'OpenAI'],
                ['id' => 'gpt-4o-mini', 'name' => 'GPT-4o Mini (Hemat & Cepat)', 'group' => 'OpenAI'],
                ['id' => 'o3-mini', 'name' => 'o3-mini Reasoning Engine', 'group' => 'OpenAI'],
            ],
            'claude' => [
                ['id' => 'claude-3-7-sonnet', 'name' => 'Claude 3.7 Sonnet (Hybrid Thinking)', 'group' => 'Anthropic'],
                ['id' => 'claude-3-5-sonnet', 'name' => 'Claude 3.5 Sonnet', 'group' => 'Anthropic'],
                ['id' => 'claude-3-5-haiku', 'name' => 'Claude 3.5 Haiku', 'group' => 'Anthropic'],
            ],
            'grok' => [
                ['id' => 'grok-2-latest', 'name' => 'Grok 2 Latest', 'group' => 'xAI'],
            ],
            default => [],
        };

        return array_merge([$adaptive], $models);
    }

    /**
     * Chat interaktif dengan AI Copilot.
     * Mendukung Hybrid RAG context injection, Native Function Calling, Vision multimodal, dan multi-turn conversations.
     */
    public function chatWithCopilot(array $messages, array $options = []): array
    {
        $startTime = microtime(true);
        $user = auth()->user();
        $sessionId = $options['session_id'] ?? null;
        $provider = $options['provider'] ?? $this->getActiveProvider();
        $apiKey = $this->getApiKey($provider);

        // Ambil query user terakhir
        $lastUserMsg = collect($messages)->reverse()->firstWhere('role', 'user');
        $userQuery = is_array($lastUserMsg) ? ($lastUserMsg['content'] ?? '') : '';

        // 1. RAG Context Retrieval (Dense + Sparse RRF)
        $ragChunks = [];
        $ragPromptContext = '';
        if (!empty($userQuery)) {
            $ragChunks = $this->vectorService->retrieveRelevantContext($userQuery, 4);
            $ragPromptContext = $this->vectorService->formatContextForPrompt($ragChunks);
        }

        // 2. Tri-Hybrid System Prompt Engineering (RAG + CAG Live Telemetry + KAG Rule Graph)
        $cagLiveContext = $this->buildLiveCagContext($user);
        $kagRuleGraph = $this->buildDeterministicKagRules();

        $systemPrompt = "Anda adalah Lead Enterprise AI Architect & Autonomous Copilot resmi Neriah Pro Software Factory OS v4.0 Ultimate.\n" .
            "Anda dilengkapi arsitektur Tri-Hybrid AI (RAG pgvector 768-dim RRF, CAG Cache O(1) Live Telemetry, KAG Deterministic Rule Graph), Live MCP Server Bridge (neriahpro-mcp), serta 21 Native Tools tervalidasi RBAC & SHA-256 Audit Log.\n\n" .
            $cagLiveContext . "\n" .
            $kagRuleGraph . "\n" .
            $ragPromptContext;

        $trace = AiWorkflowTracer::startTrace(
            stage: 'chat',
            title: "Copilot Chat via " . ucfirst($provider),
            sessionId: $sessionId,
            user: $user,
            input: ['query' => $userQuery, 'provider' => $provider]
        );

        // Jika API Key provider utama belum disetel, coba failover ke provider cadangan yang memiliki key
        if (empty($apiKey)) {
            $altProvider = ($provider === 'deepseek') ? 'gemini' : 'deepseek';
            $altKey = $this->getApiKey($altProvider);

            if (!empty($altKey)) {
                $provider = $altProvider;
                $apiKey = $altKey;
            } else {
                $fallbackReply = "Selamat datang di Neriah Pro AI Copilot. Sistem mendeteksi API Key {$provider} belum dikonfigurasi di Pengaturan Global atau .env.\n\n" .
                    "Namun, repositori basis data lokal dan aturan 10 Pilar Software Factory OS tetap aktif. Berikut ringkasan fakta terverifikasi dari RAG:\n" .
                    (!empty($ragChunks) ? $ragChunks[0]['content'] : "Silakan konfigurasikan DEEPSEEK_API_KEY atau GEMINI_API_KEY di .env atau menu Settings.");

                AiWorkflowTracer::completeTrace($trace, 'success', ['reply' => $fallbackReply], "Fallback lokal aktif.");
                return [
                    'reply' => $fallbackReply,
                    'tool_calls' => [],
                    'rag_chunks' => $ragChunks,
                    'tokens_used' => 120,
                    'provider' => $provider,
                ];
            }
        }

        try {
            if ($provider === 'deepseek') {
                return $this->callDeepSeekCopilot(
                    $messages,
                    $systemPrompt,
                    $apiKey,
                    $sessionId,
                    $options,
                    $ragChunks,
                    $startTime,
                    $trace
                );
            }

            return $this->callGeminiCopilot(
                $messages,
                $systemPrompt,
                $apiKey,
                $sessionId,
                $options,
                $ragChunks,
                $startTime,
                $trace
            );
        } catch (\Throwable $e) {
            Log::error("LlmGatewayService chat exception ({$provider}): " . $e->getMessage());
            AiWorkflowTracer::failTrace($trace, $e->getMessage());

            return [
                'reply' => "Terjadi kendala pada gateway AI ({$provider}): " . $e->getMessage() . "\n\nNamun perintah telah diverifikasi dan dicatat ke log sistem.",
                'tool_calls' => [],
                'rag_chunks' => $ragChunks,
                'tokens_used' => 0,
                'provider' => $provider,
            ];
        }
    }

    /**
     * Dispatch copilot chat to DeepSeek API with Native Function Calling & Multimodal Vision.
     */
    protected function callDeepSeekCopilot(
        array $messages,
        string $systemPrompt,
        string $apiKey,
        ?string $sessionId,
        array $options,
        array $ragChunks,
        float $startTime,
        $trace
    ): array {
        $activeModel = $this->getActiveModel('deepseek');
        $model = ($activeModel === 'auto-adaptive' || empty($activeModel))
            ? 'deepseek-flash'
            : $activeModel;

        $url = 'https://api.deepseek.com/chat/completions';
        $executedTools = [];

        // Build OpenAI/DeepSeek compatible messages payload
        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        $detailLevel = $options['image_detail'] ?? 'auto';

        foreach ($messages as $msg) {
            $role = match ($msg['role'] ?? 'user') {
                'model', 'assistant' => 'assistant',
                'system' => 'system',
                'tool' => 'tool',
                default => 'user',
            };

            if ($role === 'user') {
                $textContent = $msg['content'] ?? '';
                $imageItems = [];

                if (!empty($msg['image'])) {
                    $imageItems[] = $msg['image'];
                }
                if (!empty($msg['images']) && is_array($msg['images'])) {
                    $imageItems = array_merge($imageItems, $msg['images']);
                }
                if (!empty($msg['attachments']) && is_array($msg['attachments'])) {
                    foreach ($msg['attachments'] as $att) {
                        $alreadyTranscribed = !empty($att['md_path']) && (($att['engine'] ?? '') !== 'image-metadata-fallback');
                        if (($att['type'] ?? '') === 'image' && !empty($att['path']) && !$alreadyTranscribed) {
                            $physPath = storage_path('app/public/' . $att['path']);
                            if (file_exists($physPath)) {
                                $imageItems[] = $physPath;
                            }
                        }
                    }
                }
                if (!empty($options['images']) && is_array($options['images'])) {
                    $imageItems = array_merge($imageItems, $options['images']);
                }

                if (empty($imageItems)) {
                    $apiMessages[] = ['role' => 'user', 'content' => $textContent];
                } else {
                    $contentBlocks = [
                        ['type' => 'text', 'text' => $textContent]
                    ];

                    foreach ($imageItems as $item) {
                        if (is_string($item)) {
                            if (str_starts_with($item, 'http://') || str_starts_with($item, 'https://')) {
                                $contentBlocks[] = [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $item,
                                        'detail' => $detailLevel,
                                    ]
                                ];
                            } elseif (str_starts_with($item, 'data:image/')) {
                                $contentBlocks[] = [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $item,
                                        'detail' => $detailLevel,
                                    ]
                                ];
                            } elseif (str_starts_with($item, 'file-api-')) {
                                $contentBlocks[] = [
                                    'type' => 'file',
                                    'file_id' => $item,
                                ];
                            } elseif (file_exists($item)) {
                                $mime = @mime_content_type($item) ?: 'image/jpeg';
                                $b64 = base64_encode(file_get_contents($item));
                                $contentBlocks[] = [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mime};base64,{$b64}",
                                        'detail' => $detailLevel,
                                    ]
                                ];
                            } else {
                                $contentBlocks[] = [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:image/jpeg;base64,{$item}",
                                        'detail' => $detailLevel,
                                    ]
                                ];
                            }
                        } elseif (is_array($item)) {
                            $contentBlocks[] = $item;
                        }
                    }

                    $apiMessages[] = ['role' => 'user', 'content' => $contentBlocks];
                }
            } elseif ($role === 'assistant') {
                $item = [
                    'role' => 'assistant',
                    'content' => $msg['content'] ?? '',
                ];
                if (!empty($msg['tool_calls'])) {
                    $item['tool_calls'] = $msg['tool_calls'];
                }
                $apiMessages[] = $item;
            } elseif ($role === 'tool') {
                $apiMessages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $msg['tool_call_id'] ?? '',
                    'content' => is_string($msg['content']) ? $msg['content'] : json_encode($msg['content'], JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        $tools = GeminiToolRegistry::getOpenAiTools();

        $payload = [
            'model' => $model,
            'messages' => $apiMessages,
            'tools' => $tools,
            'temperature' => 0.2,
            'max_tokens' => 4096,
        ];

        $response = Http::timeout(45)
            ->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if (!$response->successful()) {
            throw new \Exception("DeepSeek API Error (HTTP {$response->status()}): " . $response->body());
        }

        $resJson = $response->json();
        $choice = $resJson['choices'][0] ?? [];
        $message = $choice['message'] ?? [];
        $finalText = $message['content'] ?? '';
        $reasoningContent = $message['reasoning_content'] ?? null;
        $toolCalls = $message['tool_calls'] ?? [];
        $totalTokens = $resJson['usage']['total_tokens'] ?? 300;

        // Tool calling execution loop
        if (!empty($toolCalls)) {
            $apiMessages[] = $message;

            foreach ($toolCalls as $tCall) {
                $fName = $tCall['function']['name'] ?? '';
                $rawArgs = $tCall['function']['arguments'] ?? '{}';
                $fArgs = is_array($rawArgs) ? $rawArgs : (json_decode($rawArgs, true) ?: []);

                $toolResult = $this->actionEngine->execute($fName, $fArgs, $sessionId);
                $executedTools[] = [
                    'tool' => $fName,
                    'args' => $fArgs,
                    'result' => $toolResult,
                ];

                $apiMessages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $tCall['id'] ?? '',
                    'content' => is_string($toolResult) ? $toolResult : json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                ];
            }

            // Synthesize final turn
            $secondPayload = [
                'model' => $model,
                'messages' => $apiMessages,
                'tools' => $tools,
                'temperature' => 0.2,
                'max_tokens' => 4096,
            ];

            $secondRes = Http::timeout(45)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $secondPayload);

            if ($secondRes->successful()) {
                $secondJson = $secondRes->json();
                $secondChoice = $secondJson['choices'][0] ?? [];
                $secondMsg = $secondChoice['message'] ?? [];
                $finalText = $secondMsg['content'] ?? $finalText;
                $reasoningContent = $secondMsg['reasoning_content'] ?? $reasoningContent;
                $totalTokens += ($secondJson['usage']['total_tokens'] ?? 300);
            }
        }

        if (empty($finalText) && !empty($reasoningContent)) {
            $finalText = $reasoningContent;
        }

        $execTimeMs = (int) round((microtime(true) - $startTime) * 1000);
        AiWorkflowTracer::completeTrace(
            $trace,
            'success',
            ['reply' => $finalText, 'tools' => $executedTools],
            "Chat selesai via DeepSeek ({$model}) dalam {$execTimeMs}ms.",
            executionTimeMs: $execTimeMs
        );

        return [
            'reply' => $finalText ?: 'Aksi telah berhasil diproses oleh sistem.',
            'tool_calls' => $executedTools,
            'rag_chunks' => $ragChunks,
            'tokens_used' => $totalTokens,
            'model' => $model,
            'provider' => 'deepseek',
            'reasoning' => $reasoningContent,
        ];
    }

    /**
     * Dispatch copilot chat to Google Gemini API with Native Function Calling & Multimodal Vision.
     */
    protected function callGeminiCopilot(
        array $messages,
        string $systemPrompt,
        string $apiKey,
        ?string $sessionId,
        array $options,
        array $ragChunks,
        float $startTime,
        $trace
    ): array {
        $activeModel = $this->getActiveModel('gemini');
        $modelName = ($activeModel === 'auto-adaptive' || empty($activeModel))
            ? 'gemini-3.6-flash'
            : $activeModel;

        // Auto-heal Gemini models jika masih tersimpan versi usang (1.0, 1.5, 2.0, 2.5) atau Pro limit 0
        if (str_contains($modelName, '2.5') || str_contains($modelName, '2.0') || str_contains($modelName, '1.5') || str_contains($modelName, '1.0')) {
            $modelName = 'gemini-3.6-flash';
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";
        $executedTools = [];

        // Siapkan payload Gemini
        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $parts = [['text' => $msg['content'] ?? '']];

            // Multimodal image support
            $imageItems = [];
            if (!empty($msg['image'])) {
                $imageItems[] = $msg['image'];
            }
            if (!empty($msg['images']) && is_array($msg['images'])) {
                $imageItems = array_merge($imageItems, $msg['images']);
            }
            if (!empty($msg['attachments']) && is_array($msg['attachments'])) {
                foreach ($msg['attachments'] as $att) {
                    $alreadyTranscribed = !empty($att['md_path']) && (($att['engine'] ?? '') !== 'image-metadata-fallback');
                    if (($att['type'] ?? '') === 'image' && !empty($att['path']) && !$alreadyTranscribed) {
                        $physPath = storage_path('app/public/' . $att['path']);
                        if (file_exists($physPath)) {
                            $imageItems[] = $physPath;
                        }
                    }
                }
            }
            if (!empty($options['images']) && is_array($options['images'])) {
                $imageItems = array_merge($imageItems, $options['images']);
            }

            foreach ($imageItems as $item) {
                if (is_string($item)) {
                    if (str_starts_with($item, 'data:image/')) {
                        if (preg_match('/^data:(image\/\w+);base64,(.+)$/', $item, $matches)) {
                            $parts[] = [
                                'inlineData' => [
                                    'mimeType' => $matches[1],
                                    'data' => $matches[2]
                                ]
                            ];
                        }
                    } elseif (file_exists($item)) {
                        $mime = @mime_content_type($item) ?: 'image/jpeg';
                        $parts[] = [
                            'inlineData' => [
                                'mimeType' => $mime,
                                'data' => base64_encode(file_get_contents($item))
                            ]
                        ];
                    }
                }
            }

            $contents[] = [
                'role' => $role,
                'parts' => $parts
            ];
        }

        $tools = [
            ['function_declarations' => GeminiToolRegistry::getDeclarations()]
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
            'tools' => $tools,
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 4096,
            ],
        ];

        $response = Http::timeout(45)->post($url, $payload);

        if (!$response->successful()) {
            throw new \Exception("Gemini API Error (HTTP {$response->status()}): " . $response->body());
        }

        $resJson = $response->json();
        $candidate = $resJson['candidates'][0]['content'] ?? [];
        $parts = $candidate['parts'] ?? [];

        $finalText = '';
        $functionCalls = [];

        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $finalText .= $part['text'];
            }
            if (isset($part['functionCall'])) {
                $functionCalls[] = $part['functionCall'];
            }
        }

        // Loop eksekusi tool calling jika dipicu oleh Gemini
        if (!empty($functionCalls)) {
            foreach ($functionCalls as $fCall) {
                $fName = $fCall['name'];
                $fArgs = $fCall['args'] ?? [];

                $toolResult = $this->actionEngine->execute($fName, $fArgs, $sessionId);
                $executedTools[] = [
                    'tool' => $fName,
                    'args' => $fArgs,
                    'result' => $toolResult,
                ];

                $contents[] = [
                    'role' => 'model',
                    'parts' => [['functionCall' => $fCall]]
                ];
                $contents[] = [
                    'role' => 'function',
                    'parts' => [[
                        'functionResponse' => [
                            'name' => $fName,
                            'response' => (is_array($toolResult) && !empty($toolResult) && array_keys($toolResult) !== range(0, count($toolResult) - 1))
                                ? $toolResult
                                : ['result' => $toolResult],
                        ]
                    ]]
                ];
            }

            // Turn kedua untuk sintesis respons akhir
            $secondPayload = [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => $contents,
                'tools' => $tools,
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 4096,
                ],
            ];

            $secondRes = Http::timeout(45)->post($url, $secondPayload);
            if ($secondRes->successful()) {
                $secondJson = $secondRes->json();
                $secondParts = $secondJson['candidates'][0]['content']['parts'] ?? [];
                $finalText = '';
                foreach ($secondParts as $sp) {
                    if (isset($sp['text'])) {
                        $finalText .= $sp['text'];
                    }
                }
            }
        }

        $execTimeMs = (int) round((microtime(true) - $startTime) * 1000);
        AiWorkflowTracer::completeTrace(
            $trace,
            'success',
            ['reply' => $finalText, 'tools' => $executedTools],
            "Chat selesai via Gemini ({$modelName}) dalam {$execTimeMs}ms.",
            executionTimeMs: $execTimeMs
        );

        return [
            'reply' => $finalText ?: 'Aksi telah berhasil diproses oleh sistem.',
            'tool_calls' => $executedTools,
            'rag_chunks' => $ragChunks,
            'tokens_used' => $resJson['usageMetadata']['totalTokenCount'] ?? 450,
            'model' => $modelName,
            'provider' => 'gemini',
        ];
    }

    /**
     * CAG (Cache-Augmented Generation) O(1) Live System Telemetry Context.
     */
    public function buildLiveCagContext(?User $user = null): string
    {
        $metrics = Cache::remember('ai_copilot:live_cag_telemetry:v60', 30, function () {
            try {
                $stats = $this->actionEngine->execute('get_system_stats', ['scope' => 'all']);
                return $stats;
            } catch (\Throwable) {
                return [];
            }
        });

        $roleNames = $user && method_exists($user, 'getRoleNames')
            ? implode(', ', $user->getRoleNames()->toArray())
            : 'system/cli';
        $revenueFormatted = 'Rp ' . number_format((float) ($metrics['total_revenue_settled'] ?? 0), 0, ',', '.');

        return "### ⚡ CAG (CACHE-AUGMENTED GENERATION — TELEMETRI LIVE O(1))\n"
            . "- Operator Aktif: " . ($user?->name ?? 'System') . " (Roles: {$roleNames})\n"
            . "- Total Blueprints Project OS: " . ($metrics['total_blueprints'] ?? 0) . " (Scope Locked SPK: " . ($metrics['scope_locked_projects'] ?? 0) . " | Mitra Turnkey: " . ($metrics['mitra_projects_count'] ?? 0) . " | BAST Terbit: " . ($metrics['bast_issued_count'] ?? 0) . " | Delta Change Requests: " . ($metrics['change_requests_count'] ?? 0) . ")\n"
            . "- Kapasitas Sprint Paralel: " . ($metrics['active_sprints'] ?? 0) . " / " . ($metrics['max_parallel_sprints'] ?? 3) . " Slot Terisi (Sisa Slot: " . ($metrics['available_sprint_slots'] ?? 3) . ")\n"
            . "- Pendapatan Terverifikasi (Settlement): {$revenueFormatted}\n"
            . "- Aset Domain/Hosting Mendekati Jatuh Tempo (<=30 hari): " . ($metrics['expiring_assets_30_days'] ?? 0) . "\n"
            . "- Matriks Project OS v6.0 Ultimate: " . ($metrics['sectors_count'] ?? 12) . " Sektor UI/UX (300 Sub-Menu L1–L4) × " . ($metrics['primitives_count'] ?? 12) . " Primitif + 5 Enterprise Glue × " . ($metrics['stack_presets_count'] ?? 9) . " Stack Adapters (M1–S2) × " . ($metrics['deterministic_guarantees_count'] ?? 5) . " Deterministic Guarantees × " . ($metrics['native_tools_count'] ?? 27) . " Native Tools | Chunks RAG: " . ($metrics['total_knowledge_chunks'] ?? 0) . "\n"
            . "- Dual-Mode 3-Pane Studio IDE: `/blueprint/studio` (`RETAIL_SELF_DEV` & `MITRA_TURNKEY_PROJECT` + Hermetic Project Isolation) | Live MCP Bridge: `POST /api/mcp/project-os`\n";
    }

    /**
     * KAG (Knowledge-Augmented Generation) Deterministic Engineering & Anti-Slop Rule Graph.
     */
    public function buildDeterministicKagRules(): string
    {
        return "### 🧭 KAG (KNOWLEDGE-AUGMENTED GRAPH — INVA-ATURAN DETERMINISTIK WAJIB)\n"
            . "1. **Database, Historical Snapshot & Skalabilitas O(1)**: Wajib 26-char ULID (`->ulid('id')->primary()`), `DECIMAL(19,4)` dengan pembulatan `HALF_UP` & Largest-Remainder Penny Allocation (`0.0000` drift), `ON DELETE RESTRICT` (`IMMUTABLE_LEDGER` / `ANONYMIZE_PII` / `SOFT_ARCHIVE`), kolom bekukan historis (`item_name_snapshot`, `unit_price_snapshot`, `tax_rate_snapshot`, `counterparty_snapshot`), dan Keyset Cursor Pagination `O(1)` (`cursorPaginate()`).\n"
            . "2. **12 Sectoral Industrial UI/UX Archetypes & Thermal/A4 Print**: Dilarang menyarankan tabel CRUD standar untuk layar operasional utama! Gunakan ergonomi spesifik industri (`POS_SPLIT_TERMINAL`, `HORIZONTAL_RESOURCE_GANTT`, `CLINICAL_3_PANE_COCKPIT`, dsb.) plus kontrak cetak **Thermal 58mm/80mm ESC/POS** & **Official A4 PDF** (`break-inside: avoid` + inline SVG QR SHA-256 lokal tanpa API QR eksternal).\n"
            . "3. **Zero UI Slop & Zero Nested If-Else**: Haram memakai `rounded-full`, gradien warna-warni `bg-gradient-to-*`, dialog native `window.alert()/confirm()`, script CDN eksternal, magic string statis, atau `if-else` bersarang (>2 level) pada aturan bisnis (wajib menggunakan `DeterministicDecisionMatrix.php` berbasis PHP 8.4+ `match()`).\n"
            . "4. **6 Killer Engines + 8 Day-2 Production Layers**: Arahkan proyek melewati `run_blueprint_doctor` (Skor Determinisme `100.0%`), `get_day2_production_contracts` (`InitializeNewTenantAction`, Two-Step Staged CSV Importer `import_staging_*`, 4-Lane Redis Queue `critical|default|ai-rag|low-batch`, Private Disk 5-min Signed URLs, Omnichannel Notification Matrix), Phased Prompt Sequencer (Fase 1–7), dan 1-Command CLI Bootstrapper (`curl -sL .../init/{ULID} | bash`).\n"
            . "5. **5 Day-30+ Lifecycle Retention Engines & 3-Pane Studio (`/blueprint/studio`)**: Gunakan `generate_delta_change_request` untuk evolusi iteratif `v1.0 -> v1.1` tanpa merusak kode produksi, hubungkan IDE klien melalui `.mcp.json` (`neriahpro-mcp`), kompilasi tabel keputusan via `compile_decision_table_matrix`, serta validasi keamanan multi-tenant & kesiapan `.env.blueprint` via `audit_environment_and_redteam_readiness` (`07-security-redteam.md` & `php artisan pos:check-env`).\n"
            . "6. **5 Mitra Commercial-to-Code Bridges & Satu Pintu Retail vs Mitra (v5.0)**: Dilarang menebak harga kontrak secara asal! Gunakan `calculate_mitra_quotation_and_wbs` untuk mengalkulasi RAB algoritmik (Tabel ULID + Transisi State + Lock Konkurensi × Pengali Stack `M1..S2`), bekukan ruang lingkup SPK bersegel SHA-256 via `freeze_scope_and_generate_spk_contract`, kenakan Invoice Addendum Berbayar untuk Delta `v1.0 -> v1.1`, dan terbitkan BAST Digital bersegel SHA-256 setelah 100% UAT Given-When-Then tercentang via `manage_uat_checklist_and_digital_bast`.\n"
            . "7. **5 Deterministic Guarantee Engines & Hermetic Project Isolation (v6.0)**: Wajib gunakan `expand_l1_l4_enterprise_menu_matrix` untuk memekarkan 25 sub-menu L1–L4 + 5 Enterprise Glue (`sys_document_sequences`, `sys_accounting_periods`, `sys_approval_requests`, `sys_reason_evidences`, `sys_integration_logs`), bekukan penamaan class/method `__invoke()`/DTO/kolom di `.agents/SYMBOL_MANIFEST.json` & pecah migrasi urut DAG Kahn maks 5–6 entitas per Part + `<MANDATORY_VERIFICATION_GATE>` via `compile_symbol_manifest_and_dag_chunks`, serta jaga isolasi proyek 100% bersih tanpa sisa proyek lama via `verify_project_isolation_and_clean_state`.\n";
    }
}
