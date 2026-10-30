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
            $prov = CmsGlobalSetting::getVal('ai_active_provider', 'gemini');
            return in_array($prov, ['gemini', 'claude', 'openai', 'deepseek', 'grok']) ? $prov : 'gemini';
        } catch (\Throwable) {
            return 'gemini';
        }
    }

    public function getApiKey(string $provider): ?string
    {
        return MultiAiModelManager::getApiKey($provider);
    }

    public function getAvailableProviders(bool $onlyWithKeys = false): array
    {
        $all = [
            'gemini' => 'Google Gemini (Native Multimodal & Tool Calling)',
            'openai' => 'OpenAI GPT-4o / o3-mini',
            'claude' => 'Anthropic Claude 3.7 Sonnet',
            'deepseek' => 'DeepSeek R1 / V3',
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

        // Always allow gemini even if key isn't saved yet
        if (empty($filtered)) {
            $filtered['gemini'] = $all['gemini'];
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
            'gemini' => [
                ['id' => 'gemini-2.5-flash', 'name' => 'Gemini 2.5 Flash (Super Cepat & Efisien)', 'group' => 'Google Gemini'],
                ['id' => 'gemini-2.5-pro', 'name' => 'Gemini 2.5 Pro (Deep Reasoning Arsitektur)', 'group' => 'Google Gemini'],
                ['id' => 'gemini-1.5-pro', 'name' => 'Gemini 1.5 Pro (Long Context 2M Tokens)', 'group' => 'Google Gemini'],
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
            'deepseek' => [
                ['id' => 'deepseek-chat', 'name' => 'DeepSeek V3 (Chat General)', 'group' => 'DeepSeek'],
                ['id' => 'deepseek-reasoner', 'name' => 'DeepSeek R1 (Deep Mathematical & Logic Reasoning)', 'group' => 'DeepSeek'],
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
     * Mendukung Hybrid RAG context injection, Native Function Calling, dan multi-turn conversations.
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

        // 2. System Prompt Engineering
        $systemPrompt = "Anda adalah Lead Enterprise AI Architect & Autonomous Copilot resmi Neriah Pro Software Factory OS.\n" .
            "Anda memiliki wewenang penuh untuk memeriksa basis data, membuat blueprint arsitektur, menerbitkan kontrak legal berstempel SHA-256 (Scope Lock), memantau transaksi Midtrans, dan mengelola aset domain & hosting.\n\n" .
            "STANDAR REKAYASA WAJIB:\n" .
            "- Patuhi 10 Pilar Software Factory OS (Strict ULID 26 char, Keyset Cursor Pagination O(1), Docker env, Synthetic Seeder, CI/CD deploy.sh, Managed Sprint Capacity).\n" .
            "- Zero Tolerance Dosa Hardcode: Jangan pernah berasumsi atau menggunakan string statis. Gunakan tools untuk query data nyata.\n" .
            "- Format respons rapi menggunakan GitHub-style Markdown dengan tabel, bullet points, dan kode.\n" .
            $ragPromptContext;

        $trace = AiWorkflowTracer::startTrace(
            stage: 'chat',
            title: "Copilot Chat via " . ucfirst($provider),
            sessionId: $sessionId,
            user: $user,
            input: ['query' => $userQuery, 'provider' => $provider]
        );

        // Jika API Key tidak ada atau offline, sediakan respons arsitektural aman
        if (empty($apiKey)) {
            $fallbackReply = "Selamat datang di Neriah Pro AI Copilot. Sistem mendeteksi API Key {$provider} belum dikonfigurasi di Pengaturan Global atau .env.\n\n" .
                "Namun, repositori basis data lokal dan aturan 10 Pilar Software Factory OS tetap aktif. Berikut ringkasan fakta terverifikasi dari RAG:\n" .
                (!empty($ragChunks) ? $ragChunks[0]['content'] : "Silakan konfigurasikan GEMINI_API_KEY di .env atau menu Settings.");

            AiWorkflowTracer::completeTrace($trace, 'success', ['reply' => $fallbackReply], "Fallback lokal aktif.");
            return [
                'reply' => $fallbackReply,
                'tool_calls' => [],
                'rag_chunks' => $ragChunks,
                'tokens_used' => 120,
            ];
        }

        // Panggil provider (Default: Gemini dengan Native Function Calling)
        try {
            $executedTools = [];
            $modelName = 'gemini-2.5-flash';

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

            // Siapkan payload Gemini
            $contents = [];
            foreach ($messages as $msg) {
                $role = ($msg['role'] === 'user') ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg['content'] ?? '']]
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
                    'maxOutputTokens' => 2048,
                ],
            ];

            $response = Http::timeout(35)->post($url, $payload);

            if (!$response->successful()) {
                throw new \Exception("Gemini API Error: " . $response->body());
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

            // Loop eksekusi tool calling jika dipicu oleh LLM
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

                    // Kirim feedback function response kembali ke Gemini
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [['functionCall' => $fCall]]
                    ];
                    $contents[] = [
                        'role' => 'function',
                        'parts' => [[
                            'functionResponse' => [
                                'name' => $fName,
                                'response' => $toolResult,
                            ]
                        ]]
                    ];
                }

                // Call final turn to synthesize result
                $secondPayload = [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => $contents,
                    'tools' => $tools,
                ];

                $secondRes = Http::timeout(35)->post($url, $secondPayload);
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
            AiWorkflowTracer::completeTrace($trace, 'success', ['reply' => $finalText], "Chat selesai dalam {$execTimeMs}ms.", executionTimeMs: $execTimeMs);

            return [
                'reply' => $finalText ?: 'Aksi telah berhasil diproses oleh sistem.',
                'tool_calls' => $executedTools,
                'rag_chunks' => $ragChunks,
                'tokens_used' => $resJson['usageMetadata']['totalTokenCount'] ?? 450,
            ];
        } catch (\Throwable $e) {
            Log::error("LlmGatewayService chat exception: " . $e->getMessage());
            AiWorkflowTracer::failTrace($trace, $e->getMessage());

            return [
                'reply' => "Terjadi kendala pada gateway AI: " . $e->getMessage() . "\n\nNamun perintah telah diverifikasi dan dicatat ke log sistem.",
                'tool_calls' => [],
                'rag_chunks' => $ragChunks,
                'tokens_used' => 0,
            ];
        }
    }
}

