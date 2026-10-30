<?php

namespace App\Livewire\Admin;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\AiWorkflowTrace;
use App\Services\Ai\LlmGatewayService;
use App\Services\Ai\VectorEmbeddingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class FloatingAgenticAi extends Component
{
    // Window states: 'closed' | 'open' | 'minimized' | 'maximized'
    public string $windowState = 'closed';
    public string $activeTab = 'chat'; // 'chat' | 'knowledge' | 'cctv'

    // Chat properties
    public string $inputMessage = '';
    public array $messages = [];
    public bool $isLoading = false;

    // Session Management
    public string $currentSessionId = '';
    public array $sessions = [];

    // Provider & Models
    public string $activeProvider = 'gemini';
    public array $availableProviders = [];

    // Quick Stats
    public int $totalKnowledgeChunks = 0;
    public int $activeSprints = 0;

    public function mount(LlmGatewayService $gateway, VectorEmbeddingService $vectorService)
    {
        $this->activeProvider = $gateway->getActiveProvider();
        $this->availableProviders = $gateway->getAvailableProviders();
        $this->windowState = session('ai_floating_window_state', 'closed');
        $this->loadSessions();

        try {
            $this->totalKnowledgeChunks = \Illuminate\Support\Facades\DB::table('ai_knowledge_vectors')->count();
            $this->activeSprints = \App\Models\VisionBlueprint::where('project_status', 'Active Sprint')->count();
        } catch (\Throwable) {}
    }

    public function loadSessions(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $dbSessions = AiChatSession::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $this->sessions = $dbSessions->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'created_at' => $s->created_at?->diffForHumans(),
        ])->toArray();

        if (empty($this->currentSessionId) && !empty($this->sessions)) {
            $this->switchSession($this->sessions[0]['id']);
        } elseif (empty($this->sessions)) {
            $this->createNewChat();
        }
    }

    public function switchSession(string $sessionId): void
    {
        $this->currentSessionId = $sessionId;
        $dbMessages = AiChatMessage::where('session_id', $sessionId)
            ->orderBy('id', 'asc')
            ->get();

        $this->messages = $dbMessages->map(fn ($m) => [
            'id' => $m->id,
            'role' => $m->role,
            'content' => $m->content,
            'tool_calls' => $m->tool_calls,
            'tokens_used' => $m->tokens_used,
            'created_at' => $m->created_at?->format('H:i'),
        ])->toArray();
    }

    public function createNewChat(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $session = AiChatSession::create([
            'user_id' => $user->id,
            'title' => 'Sesi Konsultasi ' . now()->format('d M H:i'),
            'provider' => $this->activeProvider,
            'status' => 'active',
        ]);

        $this->currentSessionId = $session->id;
        $this->messages = [
            [
                'id' => (string) Str::ulid(),
                'role' => 'model',
                'content' => "Halo! Saya Autonomous Copilot Neriah Pro Software Factory OS.\n\nSaya terhubung langsung ke database PostgreSQL Strict ULID, dapat memeriksa PRD 26 parameter, membuat blueprint baru, mengunci kontrak dengan segel SHA-256, atau memantau status domain & hosting. Ada yang bisa saya bantu hari ini?",
                'tool_calls' => null,
                'tokens_used' => 0,
                'created_at' => now()->format('H:i'),
            ]
        ];

        $this->loadSessions();
    }

    public function toggleWindow(): void
    {
        $this->windowState = ($this->windowState === 'closed') ? 'open' : 'closed';
        session(['ai_floating_window_state' => $this->windowState]);
    }

    #[On('open-ai-copilot')]
    public function openWindow(): void
    {
        $this->windowState = 'open';
        session(['ai_floating_window_state' => 'open']);
    }

    public function closeWindow(): void
    {
        $this->windowState = 'closed';
        session(['ai_floating_window_state' => 'closed']);
    }

    public function sendMessage(LlmGatewayService $gateway): void
    {
        $text = trim($this->inputMessage);
        if (empty($text) || $this->isLoading) {
            return;
        }

        $this->inputMessage = '';
        $this->isLoading = true;

        // Tambah pesan user ke list
        $userMsg = [
            'id' => (string) Str::ulid(),
            'role' => 'user',
            'content' => $text,
            'tool_calls' => null,
            'tokens_used' => 0,
            'created_at' => now()->format('H:i'),
        ];
        $this->messages[] = $userMsg;

        // Simpan ke DB
        if (!empty($this->currentSessionId)) {
            AiChatMessage::create([
                'session_id' => $this->currentSessionId,
                'role' => 'user',
                'content' => $text,
            ]);
        }

        try {
            $response = $gateway->chatWithCopilot($this->messages, [
                'session_id' => $this->currentSessionId,
                'provider' => $this->activeProvider,
            ]);

            $modelMsg = [
                'id' => (string) Str::ulid(),
                'role' => 'model',
                'content' => $response['reply'],
                'tool_calls' => $response['tool_calls'] ?? null,
                'tokens_used' => $response['tokens_used'] ?? 0,
                'created_at' => now()->format('H:i'),
            ];
            $this->messages[] = $modelMsg;

            if (!empty($this->currentSessionId)) {
                AiChatMessage::create([
                    'session_id' => $this->currentSessionId,
                    'role' => 'model',
                    'content' => $response['reply'],
                    'tool_calls' => $response['tool_calls'] ?? null,
                    'tokens_used' => $response['tokens_used'] ?? 0,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("FloatingAgenticAi sendMessage error: " . $e->getMessage());
            $this->messages[] = [
                'id' => (string) Str::ulid(),
                'role' => 'model',
                'content' => "Terjadi kesalahan saat memproses permintaan: " . $e->getMessage(),
                'tool_calls' => null,
                'tokens_used' => 0,
                'created_at' => now()->format('H:i'),
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function syncKnowledge(VectorEmbeddingService $vectorService): void
    {
        $res = $vectorService->syncSystemKnowledgeBase(true);
        $this->totalKnowledgeChunks = \Illuminate\Support\Facades\DB::table('ai_knowledge_vectors')->count();
        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Knowledge Base Terupdate',
            'message' => $res['message'] ?? 'Sinkronisasi berhasil.',
        ]);
    }

    public function render()
    {
        $recentTraces = [];
        if ($this->activeTab === 'cctv') {
            $recentTraces = AiWorkflowTrace::orderBy('id', 'desc')->limit(12)->get();
        }

        return view('livewire.admin.floating-agentic-ai', [
            'recentTraces' => $recentTraces,
        ]);
    }
}

