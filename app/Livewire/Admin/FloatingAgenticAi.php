<?php

namespace App\Livewire\Admin;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\AiWorkflowTrace;
use App\Services\Ai\LlmGatewayService;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\MarkItDown\MarkItDownService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class FloatingAgenticAi extends Component
{
    use WithFileUploads;

    // Window states: 'closed' | 'open' | 'minimized' | 'maximized'
    public string $windowState = 'closed';
    public string $activeTab = 'chat'; // 'chat' | 'knowledge' | 'cctv'
    public bool $isDrawerOpen = false;

    // Chat properties
    public string $inputMessage = '';
    public array $messages = [];
    public bool $isLoading = false;

    // Session Management (Multi-Session)
    public string $currentSessionId = '';
    public array $sessions = [];
    public ?string $renamingSessionId = null;
    public string $renamingSessionTitle = '';
    public int $maxSessionMessages = 50;
    public int $sessionMessageCount = 0;

    // Multimodal File Attachments
    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploadedFiles = [];
    public ?string $uploadErrorMessage = null;
    public int $maxFiles = 5;
    public int $maxFileSizeKb = 10240; // 10MB per file

    // Provider & Models (Only providers with configured API keys)
    public string $activeProvider = 'deepseek';
    public string $activeSubModel = 'deepseek-flash';
    public array $availableProviders = [];
    public array $dynamicModels = [];
    public bool $isLoadingModels = false;

    // Quick Stats
    public int $totalKnowledgeChunks = 0;
    public int $activeSprints = 0;

    public function mount(LlmGatewayService $gateway, VectorEmbeddingService $vectorService)
    {
        // 1. Only load providers that have valid API keys
        $this->availableProviders = $gateway->getAvailableProviders(true);

        $savedProvider = session('ai_copilot_selected_provider', $gateway->getActiveProvider());
        $this->activeProvider = (!empty($savedProvider) && isset($this->availableProviders[$savedProvider]))
            ? $savedProvider
            : (array_key_first($this->availableProviders) ?? 'deepseek');

        $savedSubModel = session('ai_copilot_selected_submodel_' . $this->activeProvider);
        $this->activeSubModel = !empty($savedSubModel) ? $savedSubModel : $gateway->getActiveModel($this->activeProvider);
        $this->dynamicModels = $gateway->fetchDynamicModels($this->activeProvider);

        $this->windowState = session('ai_copilot_window_state', 'closed');
        $this->activeTab = session('ai_copilot_active_tab', 'chat');
        $this->isDrawerOpen = false;

        $this->loadSessions();

        try {
            $this->totalKnowledgeChunks = \Illuminate\Support\Facades\DB::table('ai_knowledge_vectors')->count();
            $this->activeSprints = \App\Models\VisionBlueprint::where('project_status', 'Active Sprint')->count();
        } catch (\Throwable) {}
    }

    public function toggleDrawer(): void
    {
        $this->isDrawerOpen = !$this->isDrawerOpen;
    }

    public function closeDrawer(): void
    {
        $this->isDrawerOpen = false;
        $this->dispatch('close-ai-drawer');
    }

    public function loadSessions(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $dbSessions = AiChatSession::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get();

        $this->sessions = $dbSessions->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'created_at' => $s->created_at?->diffForHumans(),
            'message_count' => $s->message_count,
        ])->toArray();

        if (empty($this->currentSessionId) && !empty($this->sessions)) {
            $this->switchSession($this->sessions[0]['id']);
        } elseif (empty($this->sessions)) {
            $this->createNewChat(false);
        }
    }

    public function switchSession(string $sessionId): void
    {
        $this->currentSessionId = $sessionId;
        $dbMessages = AiChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->messages = $dbMessages->map(fn ($m) => [
            'id' => $m->id,
            'role' => $m->role,
            'content' => $m->content,
            'attachments' => is_array($m->attachments) ? $m->attachments : [],
            'tool_calls' => $m->tool_calls,
            'tokens_used' => $m->tokens_used,
            'created_at' => $m->created_at?->format('H:i'),
        ])->toArray();

        $this->sessionMessageCount = count(array_filter($this->messages, fn ($m) => $m['role'] === 'user'));
        $this->uploadedFiles = [];
        $this->uploadErrorMessage = null;
        $this->isDrawerOpen = false;

        $user = Auth::user();
        if ($user) {
            session(['ai_copilot_session_id_' . $user->id => $sessionId]);
        }

        $this->dispatch('close-ai-drawer');
        $this->dispatch('scroll-ai-to-bottom');
    }

    public function createNewChat(bool $closeDrawer = true): void
    {
        $user = Auth::user();
        if (!$user) return;

        $session = AiChatSession::create([
            'user_id' => $user->id,
            'title' => 'Sesi Konsultasi ' . now()->format('d M H:i'),
            'provider' => $this->activeProvider,
            'status' => 'active',
            'message_count' => 1,
            'max_messages' => $this->maxSessionMessages,
        ]);

        $this->currentSessionId = $session->id;
        $welcomeContent = "Halo " . ($user->name ?? 'Lead Architect') . "! Saya **Autonomous Copilot Neriah Pro Software Factory OS**.\n\n" .
            "Saya terhubung langsung ke basis data PostgreSQL Strict ULID, dapat memeriksa PRD 26 parameter, membuat blueprint arsitektur baru, mengunci kontrak dengan segel SHA-256 (Scope Lock), atau memantau status domain & hosting.\n\n" .
            "📎 **Dukungan Multi-Berkas & MarkItDown**: Anda dapat mengunggah berkas teks, dokumen (.pdf, .docx, .xlsx, .pptx, .csv, .txt, .md), dan gambar (.png, .jpg) maksimal 5 berkas/10MB. Seluruh dokumen akan otomatis dikonversi ke format Markdown murni dan konteksnya tetap tersimpan di sesi percakapan ini sampai Anda menghapusnya.";

        $welcomeMsgId = (string) Str::ulid();
        $this->messages = [
            [
                'id' => $welcomeMsgId,
                'role' => 'assistant',
                'content' => $welcomeContent,
                'attachments' => [],
                'tool_calls' => null,
                'tokens_used' => 0,
                'created_at' => now()->format('H:i'),
            ]
        ];

        AiChatMessage::create([
            'id' => $welcomeMsgId,
            'session_id' => $session->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $welcomeContent,
            'attachments' => [],
            'provider' => $this->activeProvider,
        ]);

        $this->sessionMessageCount = 0;
        $this->uploadedFiles = [];
        $this->uploadErrorMessage = null;

        $this->loadSessions();

        if ($closeDrawer) {
            $this->isDrawerOpen = false;
            $this->dispatch('close-ai-drawer');
        }

        $this->dispatch('scroll-ai-to-bottom');
    }

    public function deleteSession(string $sessionId): void
    {
        $user = Auth::user();
        if (!$user) return;

        AiChatSession::where('id', $sessionId)->where('user_id', $user->id)->delete();

        $this->sessions = array_values(array_filter($this->sessions, fn ($s) => $s['id'] !== $sessionId));

        if ($this->currentSessionId === $sessionId) {
            if (!empty($this->sessions)) {
                $this->switchSession($this->sessions[0]['id']);
            } else {
                $this->createNewChat(false);
            }
        }

        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Sesi Dihapus',
            'message' => 'Percakapan berhasil dihapus dari sistem.',
        ]);
    }

    public function startRenameSession(string $sessionId): void
    {
        $this->renamingSessionId = $sessionId;
        foreach ($this->sessions as $s) {
            if ($s['id'] === $sessionId) {
                $this->renamingSessionTitle = $s['title'] ?? '';
                break;
            }
        }
    }

    public function cancelRenameSession(): void
    {
        $this->renamingSessionId = null;
        $this->renamingSessionTitle = '';
    }

    public function saveRenameSession(): void
    {
        if (!$this->renamingSessionId) return;

        $this->renameSession($this->renamingSessionId, $this->renamingSessionTitle);
        $this->renamingSessionId = null;
        $this->renamingSessionTitle = '';
    }

    public function renameSession(string $sessionId, string $newTitle): void
    {
        $newTitle = trim($newTitle);
        if (empty($newTitle)) return;

        $shortTitle = Str::limit($newTitle, 60);

        $user = Auth::user();
        if ($user) {
            AiChatSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->update(['title' => $shortTitle]);
        }

        foreach ($this->sessions as $idx => $sess) {
            if ($sess['id'] === $sessionId) {
                $this->sessions[$idx]['title'] = $shortTitle;
                break;
            }
        }
    }

    // Multimodal File Upload Validation & Handlers
    public function updatedUploadedFiles(): void
    {
        $this->uploadErrorMessage = null;

        if (count($this->uploadedFiles) > $this->maxFiles) {
            $this->uploadErrorMessage = "Maksimal {$this->maxFiles} berkas per pesan. Silakan hapus sebagian berkas.";
            $this->uploadedFiles = array_slice($this->uploadedFiles, 0, $this->maxFiles);
            return;
        }

        $allowedExtensions = [
            'txt', 'csv', 'tsv', 'pdf', 'docx', 'xlsx', 'pptx', 'json', 'md', 'html',
            'jpg', 'jpeg', 'png', 'webp', 'gif',
        ];

        foreach ($this->uploadedFiles as $index => $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExtensions)) {
                $this->uploadErrorMessage = "Format file .{$ext} tidak didukung. Harap unggah dokumen teks, dokumen kantor, atau gambar.";
                unset($this->uploadedFiles[$index]);
                continue;
            }

            if ($file->getSize() > ($this->maxFileSizeKb * 1024)) {
                $this->uploadErrorMessage = "Ukuran file '{$file->getClientOriginalName()}' melebihi batas 10MB.";
                unset($this->uploadedFiles[$index]);
            }
        }

        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    public function removeUploadedFile(int $index): void
    {
        if (isset($this->uploadedFiles[$index])) {
            unset($this->uploadedFiles[$index]);
            $this->uploadedFiles = array_values($this->uploadedFiles);
        }
        $this->uploadErrorMessage = null;
    }

    #[On('toggle-ai-copilot')]
    public function toggleWindow(): void
    {
        $this->windowState = ($this->windowState === 'closed') ? 'open' : 'closed';
        $this->isDrawerOpen = false;
        session(['ai_copilot_window_state' => $this->windowState]);
    }

    #[On('open-ai-copilot')]
    public function openWindow($prompt = null): void
    {
        $this->windowState = 'open';
        $this->activeTab = 'chat';
        $this->isDrawerOpen = false;
        session(['ai_copilot_window_state' => 'open']);
        session(['ai_copilot_active_tab' => 'chat']);

        if (!empty($prompt)) {
            $this->inputMessage = trim($prompt);
        }
    }

    #[On('close-ai-copilot')]
    public function closeWindow(): void
    {
        $this->windowState = 'closed';
        $this->isDrawerOpen = false;
        session(['ai_copilot_window_state' => 'closed']);
    }

    public function minimizeWindow(): void
    {
        $this->windowState = ($this->windowState === 'minimized') ? 'open' : 'minimized';
        session(['ai_copilot_window_state' => $this->windowState]);
    }

    public function maximizeWindow(): void
    {
        $this->windowState = ($this->windowState === 'maximized') ? 'open' : 'maximized';
        session(['ai_copilot_window_state' => $this->windowState]);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['chat', 'knowledge', 'cctv']) ? $tab : 'chat';
        session(['ai_copilot_active_tab' => $this->activeTab]);
    }

    public function switchProvider(string $provider): void
    {
        $gateway = app(LlmGatewayService::class);
        $this->activeProvider = $provider;
        session(['ai_copilot_selected_provider' => $provider]);

        $this->activeSubModel = session('ai_copilot_selected_submodel_' . $provider, $gateway->getActiveModel($provider));
        $this->dynamicModels = $gateway->fetchDynamicModels($provider);
        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Provider Dialihkan',
            'message' => 'Provider AI aktif beralih ke: ' . strtoupper($provider),
        ]);
    }

    public function switchSubModel(string $model): void
    {
        $gateway = app(LlmGatewayService::class);
        $this->activeSubModel = trim($model);
        session(['ai_copilot_selected_submodel_' . $this->activeProvider => $this->activeSubModel]);
        try {
            $gateway->setActiveModel($this->activeProvider, $this->activeSubModel);
        } catch (\Throwable) {}

        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Model Berubah',
            'message' => "Model aktif diubah ke: {$this->activeSubModel}",
        ]);
    }

    public function refreshDynamicModels(): void
    {
        $this->isLoadingModels = true;
        $gateway = app(LlmGatewayService::class);
        $this->dynamicModels = $gateway->fetchDynamicModels($this->activeProvider, null, true);
        if (!empty($this->dynamicModels)) {
            $ids = array_column($this->dynamicModels, 'id');
            if (!in_array($this->activeSubModel, $ids, true)) {
                $this->switchSubModel($this->dynamicModels[0]['id']);
            }
        }
        $this->isLoadingModels = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Model Disinkronkan',
            'message' => "Daftar model server " . strtoupper($this->activeProvider) . " berhasil diperbarui.",
        ]);
    }

    public function sendSuggestedPrompt(string $prompt): void
    {
        $this->inputMessage = $prompt;
        $this->sendMessage(app(LlmGatewayService::class), app(MarkItDownService::class));
    }

    public function sendMessage(LlmGatewayService $gateway, MarkItDownService $markitdown): void
    {
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        $text = trim($this->inputMessage);
        if (empty($text) && empty($this->uploadedFiles)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'title' => 'Input Kosong',
                'message' => 'Harap ketik pertanyaan atau lampirkan berkas sebelum mengirim.',
            ]);
            return;
        }

        $user = Auth::user();
        if (!$user) return;

        if (empty($this->currentSessionId)) {
            $this->createNewChat(false);
        }

        // Check Turn Limit
        if ($this->sessionMessageCount >= $this->maxSessionMessages) {
            $this->uploadErrorMessage = "Batas maksimum {$this->maxSessionMessages} pesan per sesi telah tercapai. Silakan klik '+' untuk membuka sesi baru.";
            return;
        }

        $processedAttachments = [];
        $fileContextPrompt = "";

        // 1. Process New Uploaded Files with MarkItDown
        if (!empty($this->uploadedFiles)) {
            $sessionDir = "ai_attachments/{$this->currentSessionId}";

            foreach ($this->uploadedFiles as $uploaded) {
                $originalName = $uploaded->getClientOriginalName();
                $ext = strtolower($uploaded->getClientOriginalExtension());
                $sizeBytes = $uploaded->getSize();
                $sizeFormatted = round($sizeBytes / 1024, 1) . ' KB';
                if ($sizeBytes >= 1048576) {
                    $sizeFormatted = round($sizeBytes / 1048576, 2) . ' MB';
                }

                $fileType = match (true) {
                    in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) => 'image',
                    default => 'document',
                };

                $filename = Str::random(16) . '.' . $ext;
                $path = $uploaded->storeAs($sessionDir, $filename, 'public');
                $fileUrl = asset('storage/' . $path);

                $attachmentData = [
                    'name' => $originalName,
                    'path' => $path,
                    'url'  => $fileUrl,
                    'type' => $fileType,
                    'ext'  => $ext,
                    'size' => $sizeFormatted,
                ];

                // Otomatis konversi ke Markdown murni via MarkItDown
                try {
                    $physPath = storage_path('app/public/' . $path);
                    $convResult = $markitdown->convert($physPath, ['filename' => $originalName]);
                    $markdownContent = $convResult['markdown'] ?? '';

                    if (!empty(trim($markdownContent))) {
                        // Simpan file .md fisik di disk agar tersimpan permanen di chat
                        $mdPath = preg_replace('/\.[^.]+$/', '.md', $path);
                        Storage::disk('public')->put($mdPath, $markdownContent);
                        $attachmentData['md_path'] = $mdPath;

                        // Sertakan teks Markdown lengkap ke context prompt
                        $condensedMarkdown = Str::limit($markdownContent, 40000);
                        $fileContextPrompt .= "\n\n[📄 Lampiran Dikonversi ke Markdown: {$originalName} ({$sizeFormatted})]\n{$condensedMarkdown}\n";
                    }
                } catch (\Throwable $e) {
                    Log::warning("FloatingAgenticAi MarkItDown convert error ({$originalName}): " . $e->getMessage());
                    $fileContextPrompt .= "\n[Lampiran Berkas: {$originalName} (Tipe: {$fileType}, Ukuran: {$sizeFormatted})]";
                }

                $processedAttachments[] = $attachmentData;
            }
        }

        // 2. Multi-Turn File Persistence: Muat berkas dari pesan sebelumnya pada sesi ini
        if (empty($fileContextPrompt) && !empty($this->messages)) {
            $existingAttachments = [];
            foreach ($this->messages as $prevMsg) {
                if (!empty($prevMsg['attachments']) && is_array($prevMsg['attachments'])) {
                    foreach ($prevMsg['attachments'] as $att) {
                        if (is_array($att) && !empty($att['path']) && !isset($att['_metadata'])) {
                            $existingAttachments[$att['path']] = $att;
                        }
                    }
                }
            }

            if (!empty($existingAttachments)) {
                $disk = Storage::disk('public');
                foreach ($existingAttachments as $sAtt) {
                    $attName = $sAtt['name'] ?? 'Dokumen';
                    $attSize = $sAtt['size'] ?? '';
                    $mdContent = null;

                    // Prioritas 1: Baca file .md yang tersimpan di disk
                    if (!empty($sAtt['md_path']) && $disk->exists($sAtt['md_path'])) {
                        $mdContent = $disk->get($sAtt['md_path']);
                    }
                    // Prioritas 2: Baca file .md berdampingan
                    if (empty($mdContent) && !empty($sAtt['path'])) {
                        $derivedMdPath = preg_replace('/\.[^.]+$/', '.md', $sAtt['path']);
                        if ($disk->exists($derivedMdPath)) {
                            $mdContent = $disk->get($derivedMdPath);
                        }
                    }
                    // Prioritas 3: Konversi ulang dari file fisik jika belum ada file .md
                    if (empty($mdContent) && !empty($sAtt['path'])) {
                        $physPath = storage_path('app/public/' . $sAtt['path']);
                        if (file_exists($physPath)) {
                            try {
                                $conv = $markitdown->convert($physPath, ['filename' => $attName]);
                                $mdContent = $conv['markdown'] ?? '';
                                if (!empty($mdContent)) {
                                    $derivedMdPath = preg_replace('/\.[^.]+$/', '.md', $sAtt['path']);
                                    $disk->put($derivedMdPath, $mdContent);
                                }
                            } catch (\Throwable) {}
                        }
                    }

                    if (!empty(trim((string)$mdContent))) {
                        $condensed = Str::limit($mdContent, 40000);
                        $fileContextPrompt .= "\n\n[📄 Dokumen Terlampir Pada Sesi Ini: {$attName} ({$attSize})]\n{$condensed}\n";
                        $processedAttachments[] = $sAtt;
                    }
                }
            }
        }

        // Reset file upload state
        $this->uploadedFiles = [];
        $this->uploadErrorMessage = null;
        $this->inputMessage = '';
        $this->isLoading = true;

        // Build User Message
        $userMsgId = (string) Str::ulid();
        $userMsg = [
            'id' => $userMsgId,
            'role' => 'user',
            'content' => $text ?: '(Mengirim lampiran berkas)',
            'attachments' => $processedAttachments,
            'tool_calls' => null,
            'tokens_used' => 0,
            'created_at' => now()->format('H:i'),
        ];
        $this->messages[] = $userMsg;
        $this->sessionMessageCount++;

        // Save User Message to Database
        AiChatMessage::create([
            'id' => $userMsgId,
            'session_id' => $this->currentSessionId,
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $userMsg['content'],
            'attachments' => $processedAttachments,
            'provider' => $this->activeProvider,
        ]);

        // Update Session title & message_count
        $session = AiChatSession::find($this->currentSessionId);
        if ($session) {
            $session->increment('message_count');
            $session->touch();
            if ($session->message_count <= 2 && !empty($text)) {
                $session->update(['title' => Str::limit($text, 35)]);
            }
        }

        try {
            $fullPrompt = $text . ($fileContextPrompt ? "\n" . $fileContextPrompt : "");

            // Collect image paths for vision
            $imagePaths = [];
            foreach ($processedAttachments as $att) {
                if (($att['type'] ?? '') === 'image' && !empty($att['path'])) {
                    $physImg = storage_path('app/public/' . $att['path']);
                    if (file_exists($physImg)) {
                        $imagePaths[] = $physImg;
                    }
                }
            }

            // Build history payload for Copilot
            $messagesPayload = $this->messages;
            $messagesPayload[count($messagesPayload) - 1]['content'] = $fullPrompt;

            $response = $gateway->chatWithCopilot($messagesPayload, [
                'session_id' => $this->currentSessionId,
                'provider' => $this->activeProvider,
                'sub_model' => $this->activeSubModel,
                'images' => $imagePaths,
            ]);

            $reply = $response['reply'] ?? 'Tidak ada respons dari AI.';
            $toolCalls = $response['tool_calls'] ?? null;
            $tokensUsed = $response['tokens_used'] ?? 0;

            $assistantMsgId = (string) Str::ulid();
            $modelMsg = [
                'id' => $assistantMsgId,
                'role' => 'assistant',
                'content' => $reply,
                'attachments' => [],
                'tool_calls' => $toolCalls,
                'tokens_used' => $tokensUsed,
                'created_at' => now()->format('H:i'),
            ];
            $this->messages[] = $modelMsg;

            AiChatMessage::create([
                'id' => $assistantMsgId,
                'session_id' => $this->currentSessionId,
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $reply,
                'tool_calls' => $toolCalls,
                'tokens_used' => $tokensUsed,
                'provider' => $this->activeProvider,
            ]);
        } catch (\Throwable $e) {
            Log::error("FloatingAgenticAi sendMessage error: " . $e->getMessage());
            $errId = (string) Str::ulid();
            $this->messages[] = [
                'id' => $errId,
                'role' => 'assistant',
                'content' => "Terjadi kendala saat memproses permintaan: " . $e->getMessage(),
                'attachments' => [],
                'tool_calls' => null,
                'tokens_used' => 0,
                'created_at' => now()->format('H:i'),
            ];
        } finally {
            $this->isLoading = false;
            $this->loadSessions();
            $this->dispatch('scroll-ai-to-bottom');
        }
    }

    public function syncKnowledge(VectorEmbeddingService $vectorService): void
    {
        try {
            $res = $vectorService->syncSystemKnowledgeBase(true);
            $this->totalKnowledgeChunks = \Illuminate\Support\Facades\DB::table('ai_knowledge_vectors')->count();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'Knowledge Base Terupdate',
                'message' => $res['message'] ?? 'Sinkronisasi berhasil.',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('syncKnowledge error: ' . $e->getMessage());
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'Gagal Sinkronisasi',
                'message' => 'Terjadi kesalahan sinkronisasi: ' . $e->getMessage(),
            ]);
        }
    }

    public function clearCctvTraces(): void
    {
        try {
            AiWorkflowTrace::where('session_id', $this->currentSessionId)->delete();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'CCTV Dibersihkan',
                'message' => 'Rekaman jejak telemetri sesi ini berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'Gagal Hapus Jejak',
                'message' => 'Terjadi kendala: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $recentTraces = AiWorkflowTrace::orderBy('created_at', 'desc')->limit(12)->get();

        return view('livewire.admin.floating-agentic-ai', [
            'recentTraces' => $recentTraces,
        ]);
    }
}
