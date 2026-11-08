<?php

namespace App\Services\Prd\Strategies;

class AudioStreamingDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return (bool) preg_match('/(?:audio|language channel|kanal bahasa|channel management|penerjemah|interpreter|broadcast|webrtc|stream|translator|suara|\bchat\b|\bobrolan\b|\bmoderat|\bpesan chat\b|\bmessage\b|toggle chat|mute)/i', $title . ' ' . $desc);
    }

    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array {
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        // Archetype C1: LIVE AUDIO STREAMING & LANGUAGE CHANNEL MANAGEMENT
        if (preg_match('/(?:audio|language channel|kanal bahasa|channel management|penerjemah|interpreter|broadcast|webrtc|stream|translator|suara)/i', $title . ' ' . $desc)) {
            $ac = [
                [
                    'scenario' => $isEn ? 'Ultra-Low Latency (<150ms) Opus Audio Streaming & Channel Handoff' : 'Streaming Audio Latensi Ultra Rendah (<150ms) & Alokasi Kanal Bahasa',
                    'given' => $isEn ? "Assigned interpreter is authenticated on designated language channel with microphone input active." : "Penerjemah terautentikasi pada kanal bahasa yang ditentukan dengan mikrofon aktif.",
                    'when' => $isEn ? "Interpreter toggles broadcast switch ON and speaks sermon translation." : "Penerjemah mengaktifkan tombol siaran ON dan menyampaikan terjemahan khotbah.",
                    'then' => $isEn ? "System encodes audio in Opus 48kHz and streams via WebRTC SFU / WebSocket signaling to connected listeners with measured latency <150ms." : "Sistem mengkodekan audio dalam format Opus 48kHz dan menyiarkan via WebRTC SFU ke jemaat pendengar dengan latensi <150ms.",
                ],
                [
                    'scenario' => $isEn ? 'Channel Concurrency Guard & Duplicate Broadcaster Rejection' : 'Batas Konkurensi Kanal & Penolakan Siaran Ganda Paralel',
                    'given' => $isEn ? "Language channel is currently in active ON-AIR broadcast state by Interpreter A." : "Kanal bahasa sedang dalam status siaran aktif (ON-AIR) oleh Penerjemah A.",
                    'when' => $isEn ? "Another interpreter attempts to publish audio to the same language channel simultaneously." : "Penerjemah lain mencoba mempublikasikan siaran audio ke kanal bahasa yang sama secara bersamaan.",
                    'then' => $isEn ? "System rejects the secondary publish request with HTTP 409 Conflict, preserving existing live audio stream uninterrupted." : "Sistem menolak permintaan siaran kedua dengan respons HTTP 409 Conflict dan mempertahankan keutuhan siaran yang sedang berjalan.",
                ],
                [
                    'scenario' => $isEn ? 'Adaptive Bitrate & Jitter Buffer Recovery on Wi-Fi Fluctuation' : 'Adaptive Bitrate & Pemulihan Jitter Buffer saat Fluktuasi Wi-Fi',
                    'given' => $isEn ? "Mobile listener device is receiving real-time translation over church Wi-Fi." : "Perangkat ponsel jemaat menerima streaming terjemahan suara melalui Wi-Fi gereja.",
                    'when' => $isEn ? "Local network telemetry detects packet loss exceeding 5%." : "Telemetri jaringan mendeteksi packet loss lokal melebihi 5%.",
                    'then' => $isEn ? "Player automatically adjusts jitter buffer and requests downsampled stream bitrate without dropping live audio continuity." : "Pemutar audio otomatis menyesuaikan jitter buffer dan beralih ke bitrate lebih rendah tanpa memutus suara khotbah.",
                ],
            ];
            $integration = [
                'endpoint' => 'POST /api/v1/audio/channels & POST /api/v1/audio/stream/token',
                'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                'request_schema' => json_encode([
                    'channel_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                    'language_code' => 'en',
                    'language_name' => 'English',
                    'bitrate_kbps' => 64,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'response_schema' => json_encode([
                    'success' => true,
                    'code' => 'STREAM_INITIALIZED',
                    'data' => [
                        'channel_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                        'status' => 'LIVE_STREAMING',
                        'stream_token' => 'livekit_jwt_token_sample',
                        'latency_sla_ms' => 150,
                    ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'tasks' => $isEn ? [
                    'Register RESTful language channel and WebRTC SFU token issuance routes.',
                    'Implement channel state machine (INACTIVE, STANDBY, LIVE_STREAMING, MUTED) with atomic locking.',
                    'Configure Opus 48kHz audio ingest pipeline with fallback to WebSocket stream.',
                ] : [
                    'Daftarkan endpoint RESTful kanal bahasa dan penerbitan token WebRTC SFU.',
                    'Terapkan state machine kanal bahasa dengan penguncian atomik anti-konflik.',
                    'Konfigurasikan pipeline ingest audio Opus 48kHz dengan fallback ke WebSocket.',
                ],
            ];
            $targetFiles = ['app/Models/LanguageChannel.php', 'app/Services/Media/AudioChannelService.php', 'app/Http/Controllers/LanguageChannelController.php', 'tests/Feature/LanguageChannelContractTest.php'];
            $mission = "Bangun modul Manajemen Kanal Bahasa & Pipeline Audio Streaming Latensi Rendah WebRTC.";

            return [
                'category' => 'REALTIME_STREAMING',
                'category_label' => $isEn ? 'Live Audio Streaming & Channel Management' : 'Manajemen Kanal Bahasa & Streaming Audio',
                'related_table' => $relatedTable['name'] ?? 'language_channels',
                'related_table_data' => $relatedTable,
                'user_story' => $isEn
                    ? "As a Multimedia Operator / Sound Engineer, I want to manage language channels and monitor live audio streams, so that church attendees can listen to real-time sermon translations seamlessly."
                    : "Sebagai Operator Multimedia / Sound Engineer, saya ingin mengelola kanal bahasa dan memantau siaran audio langsung, sehingga jemaat dapat mendengarkan terjemahan khotbah secara real-time tanpa kendala.",
                'acceptance_criteria' => $ac,
                'frontend' => [
                    'design_tokens' => 'High-contrast channel cards, live audio wave meter SVG indicator, solid status pill, dark/light mode fidelity.',
                    'states' => 'Channel active (emerald), on-air pulsating dot, muted (rose), empty state with quick channel creation button.',
                    'components' => ['LanguageChannelCard.blade.php', 'AudioWaveMeter.js', 'ChannelVolumeSlider.js'],
                    'tasks' => [
                        'Build responsive language channel switcher with instant audio playback trigger.',
                        'Render live audio stream indicator with volume slider and channel mute control.',
                    ],
                ],
                'backend' => [
                    'architecture' => 'Laravel 13 Service Layer with WebRTC SFU / Reverb signaling and atomic state management.',
                    'target_files' => $targetFiles,
                    'tasks' => [
                        'Implement AudioChannelService with atomic channel state transitions.',
                        'Enforce single-broadcaster locking per channel using Redis atomic locks.',
                    ],
                    'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                    'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
                ],
                'integration' => $integration,
                'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-AUDIO', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=LanguageChannelContractTest', $relatedTable, $ac, $integration, $index),
            ];
        }

        // Archetype C2: REAL-TIME CHAT MODERATION & OPEN/CLOSE CHAT TOGGLE
        $ac = [
            [
                'scenario' => $isEn ? 'Operator Toggles Chat Room State (Open <-> Closed)' : 'Operator Mengubah Status Ruang Chat (Buka <-> Tutup)',
                'given' => $isEn ? "Live sermon session is in progress with connected listener clients." : "Sesi ibadah langsung sedang berlangsung dengan jemaat terhubung.",
                'when' => $isEn ? "Multimedia Operator toggles chat switch to CLOSED on the operator desk." : "Operator Multimedia menggeser tombol chat ke posisi TUTUP pada dasbor operator.",
                'then' => $isEn ? "System broadcasts WebSocket event 'chat.state.updated' with state 'CLOSED'; client input box is disabled immediately." : "Sistem menyiarkan event WebSocket 'chat.state.updated' berstatus 'CLOSED' dan menonaktifkan kotak input di sisi pengguna seketika.",
            ],
            [
                'scenario' => $isEn ? 'Toxic Message Filtering & Auto-Drop' : 'Penyaringan Pesan Toksik & Drop Otomatis',
                'given' => $isEn ? "Chat room is in OPEN state with active public participants." : "Ruang chat dalam status TERBUKA dengan partisipan publik aktif.",
                'when' => $isEn ? "User posts message containing blacklisted profane keywords or spam links." : "Pengguna mengirim pesan berisi kata kunci terlarang atau tautan spam.",
                'then' => $isEn ? "System drops message at gateway filter, logs audit event, and returns HTTP 422 Unprocessable Content without broadcasting." : "Sistem mencegat pesan di filter gateway, mencatat log audit, dan mengembalikan HTTP 422 tanpa menyiarkan pesan ke pengguna lain.",
            ],
            [
                'scenario' => $isEn ? 'Operator Pinning Announcement Message' : 'Operator Menyematkan Pesan Pengumuman Penting',
                'given' => $isEn ? "Chat room has active scroll stream of participant messages." : "Ruang chat memiliki aliran pesan aktif dari partisipan.",
                'when' => $isEn ? "Operator pins official service announcement message." : "Operator menyematkan pesan pengumuman resmi ibadah.",
                'then' => $isEn ? "Message is rendered in sticky top bar for all connected clients with priority visual badge." : "Pesan disematkan pada baris sticky atas layar seluruh pengguna terhubung dengan badge visual prioritas.",
            ],
        ];
        $integration = [
            'endpoint' => 'POST /api/v1/chat/messages & PATCH /api/v1/chat/state',
            'middleware' => ['api', 'auth:sanctum', 'throttle:30,1'],
            'request_schema' => json_encode([
                'channel_id' => '01J7K9M0P1Q2R3S4T5U6V7W8AA',
                'is_open' => false,
                'closure_reason' => 'Sermon in progress - Chat closed by operator',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'response_schema' => json_encode([
                'success' => true,
                'chat_state' => 'CLOSED',
                'updated_at' => '2026-11-08T20:00:00Z',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'tasks' => $isEn ? [
                'Create chat message moderation pipeline with profane filter rules.',
                'Broadcast real-time chat open/close state events via Laravel Reverb.',
                'Enforce rate limiting on message creation per user session (max 10 msgs/min).',
            ] : [
                'Bangun pipeline moderasi pesan chat dengan filter kata terlarang.',
                'Siarkan event WebSocket buka/tutup chat secara real-time via Laravel Reverb.',
                'Terapkan pembatasan frekuensi pengiriman pesan per sesi pengguna (maks 10 pesan/menit).',
            ],
        ];
        $targetFiles = ['app/Models/ChatMessage.php', 'app/Services/Chat/ChatModerationService.php', 'app/Http/Controllers/ChatController.php', 'tests/Feature/ChatModerationContractTest.php'];
        $mission = "Implementasikan fitur Moderasi Chat Real-time & Kontrol Buka/Tutup Ruang Chat.";

        return [
            'category' => 'CHAT_MODERATION',
            'category_label' => $isEn ? 'Live Chat Moderation & Control' : 'Moderasi & Kontrol Ruang Chat Real-time',
            'related_table' => $relatedTable['name'] ?? 'chat_messages',
            'related_table_data' => $relatedTable,
            'user_story' => $isEn
                ? "As an Operator, I want to moderate live comments and toggle chat availability, so that service atmosphere remains orderly and free of spam."
                : "Sebagai Operator, saya ingin memoderasi komentar langsung dan mengontrol ketersediaan chat, sehingga suasana ibadah tetap tertib dan bebas spam.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Clean chat message stream, pinned banner box, fluid toggle switch with sound feedback, dark/light mode fidelity.',
                'states' => 'Chat open (input enabled), chat closed banner (input disabled with lock icon), message posting spinner.',
                'components' => ['ChatRoomWidget.blade.php', 'ChatStreamPanel.js', 'ChatModerationBar.js'],
                'tasks' => [
                    'Develop reactive chat stream component with auto-scroll and pause-on-hover.',
                    'Render operator toggle button with immediate UI state reflection.',
                ],
            ],
            'backend' => [
                'architecture' => 'Laravel 13 Reverb WebSocket Broadcasting with strict ULID and Keyset Pagination.',
                'target_files' => $targetFiles,
                'tasks' => [
                    'Implement ChatModerationService with regex keyword blacklist filtering.',
                    'Create ChatStateChanged broadcast event pushed to Redis channel.',
                ],
                'ulid_migration_rules' => 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.',
                'query_strategy' => 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.',
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(sprintf('FEAT-%s-%02d-CHAT', $prefix, $index), $title, $businessName, $mission, $targetFiles, 'php artisan test --filter=ChatModerationContractTest', $relatedTable, $ac, $integration, $index),
        ];
    }
}
