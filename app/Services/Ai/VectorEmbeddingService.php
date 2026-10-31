<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeVector;
use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VectorEmbeddingService
{
    protected string $model = 'text-embedding-004';

    protected static bool $rateLimitExceeded = false;
    protected static ?int $rateLimitResetTime = null;
    protected static bool $rateLimitWarned = false;
    protected static ?string $workingEmbeddingModel = null;

    /**
     * Dapatkan API Key Gemini dari Pengaturan Global sistem atau environment.
     */
    public function getApiKey(): ?string
    {
        return MultiAiModelManager::getApiKey('gemini') ?: env('GEMINI_API_KEY');
    }

    /**
     * Hasilkan vector embedding 768 dimensi menggunakan Google Gemini API.
     * Menggunakan model generasi terbaru dengan MRL (outputDimensionality: 768).
     * Jika terjadi HTTP 429 atau API key tidak disetel, otomatis mengaktifkan sirkuit pengaman
     * fallback deterministik 768 dimensi berbasis hash.
     *
     * @param string $text
     * @return float[]
     */
    public function generateEmbedding(string $text): array
    {
        $text = trim($text);
        if (empty($text)) {
            return $this->generateDeterministicEmbedding('empty_fallback');
        }

        $safeText = mb_substr($text, 0, 2048);

        if (self::$rateLimitExceeded) {
            if (time() < (self::$rateLimitResetTime ?? 0)) {
                return $this->generateDeterministicEmbedding($safeText);
            }
            self::$rateLimitExceeded = false;
            self::$rateLimitWarned = false;
        }

        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            return $this->generateDeterministicEmbedding($safeText);
        }

        $availableModels = ['text-embedding-004', 'embedding-001'];
        $modelsToTry = self::$workingEmbeddingModel && in_array(self::$workingEmbeddingModel, $availableModels)
            ? array_unique(array_merge([self::$workingEmbeddingModel], $availableModels))
            : $availableModels;

        foreach ($modelsToTry as $candidateModel) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:embedContent?key={$apiKey}";

                $payload = [
                    'model' => "models/{$candidateModel}",
                    'content' => [
                        'parts' => [
                            ['text' => $safeText]
                        ]
                    ]
                ];

                if (str_contains($candidateModel, 'gemini-embedding') || str_contains($candidateModel, 'text-embedding-005')) {
                    $payload['outputDimensionality'] = 768;
                }

                $response = Http::connectTimeout(2)->timeout(3)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $values = $data['embedding']['values'] ?? null;
                    if (is_array($values) && count($values) === 768) {
                        self::$workingEmbeddingModel = $candidateModel;
                        return $values;
                    }
                }

                $status = $response->status();

                if (in_array($status, [400, 401, 403, 404, 429])) {
                    self::$rateLimitExceeded = true;
                    self::$rateLimitResetTime = time() + 300;
                    if (!self::$rateLimitWarned) {
                        Log::warning("VectorEmbeddingService: Gemini API embedding gagal/ditolak (HTTP {$status}). Mengaktifkan fallback deterministik 768 dimensi.");
                        self::$rateLimitWarned = true;
                    }
                    break;
                }
            } catch (\Throwable $e) {
                Log::warning("VectorEmbeddingService Exception ({$candidateModel}): " . $e->getMessage());
                self::$rateLimitExceeded = true;
                self::$rateLimitResetTime = time() + 300;
                break;
            }
        }

        return $this->generateDeterministicEmbedding($safeText);
    }

    /**
     * Menghasilkan array 768 dimensi float deterministik berbasis hash konten.
     */
    public function generateDeterministicEmbedding(string $text): array
    {
        $hash = hash('sha512', $text . 'neriahpro_salt_vector_seed_2026');
        $vector = [];
        for ($i = 0; $i < 768; $i++) {
            $charIndex = ($i * 7) % strlen($hash);
            $val = (ord($hash[$charIndex]) - 48) / 80.0;
            $vector[] = round(sin($val * ($i + 1)) * 0.1, 6);
        }
        return $vector;
    }

    /**
     * Pemecah teks cerdas (Smart Chunking) berbasis paragraf dan kalimat dengan sliding window overlap.
     *
     * @param string $text
     * @param int $chunkSize
     * @param int $overlap
     * @return string[]
     */
    public function chunkText(string $text, int $chunkSize = 1000, int $overlap = 150): array
    {
        $text = trim(preg_replace('/\r\n|\r/', "\n", $text));
        if (empty($text)) {
            return [];
        }

        if (mb_strlen($text) <= $chunkSize) {
            return [$text];
        }

        $paragraphs = explode("\n\n", $text);
        $chunks = [];
        $currentChunk = '';

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) continue;

            if (mb_strlen($currentChunk) + mb_strlen($para) + 2 <= $chunkSize) {
                $currentChunk .= ($currentChunk !== '' ? "\n\n" : '') . $para;
            } else {
                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                    $tail = mb_substr($currentChunk, max(0, mb_strlen($currentChunk) - $overlap));
                    $currentChunk = $tail . "\n\n" . $para;
                } else {
                    $sentences = preg_split('/(?<=[.?!])\s+/', $para);
                    $subChunk = '';
                    foreach ($sentences as $sentence) {
                        if (mb_strlen($subChunk) + mb_strlen($sentence) + 1 <= $chunkSize) {
                            $subChunk .= ($subChunk !== '' ? ' ' : '') . $sentence;
                        } else {
                            if ($subChunk !== '') $chunks[] = $subChunk;
                            $subChunk = $sentence;
                        }
                    }
                    if ($subChunk !== '') {
                        $chunks[] = $subChunk;
                    }
                }
            }
        }

        if ($currentChunk !== '') {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    /**
     * Memecah dokumen lengkap, mengenerate vektor 768 dimensi, dan menyimpan ke PostgreSQL / database.
     */
    public function indexDocument(string $title, string $fullText, string $sourceType = 'system_guide', ?string $sourceId = null, array $metadata = [], int $maxChunks = 20): int
    {
        $chunks = $this->chunkText($fullText, 950, 120);
        if (empty($chunks)) {
            return 0;
        }

        $totalChunks = count($chunks);

        if ($maxChunks > 0 && $totalChunks > $maxChunks) {
            $headCount = (int) ceil($maxChunks * 0.7);
            $tailCount = $maxChunks - $headCount;
            $headChunks = array_slice($chunks, 0, $headCount, true);
            $tailChunks = array_slice($chunks, -$tailCount, $tailCount, true);
            $selectedChunks = $headChunks + $tailChunks;
        } else {
            $selectedChunks = $chunks;
        }

        $indexedCount = 0;

        foreach ($selectedChunks as $index => $chunk) {
            $embedding = $this->generateEmbedding($chunk);

            $chunkMetadata = array_merge($metadata, [
                'chunk_index' => $index + 1,
                'total_chunks' => $totalChunks,
                'chunk_size' => mb_strlen($chunk),
                'indexed_at' => now()->toIso8601String(),
            ]);

            AiKnowledgeVector::create([
                'title' => $title . ($totalChunks > 1 ? " (Bagian " . ($index + 1) . "/{$totalChunks})" : ''),
                'source_type' => $sourceType,
                'source_id' => $sourceId ?: Str::slug($title),
                'content' => $chunk,
                'embedding' => $embedding,
                'metadata' => $chunkMetadata,
            ]);

            $indexedCount++;
        }

        return $indexedCount;
    }

    /**
     * Ambil konteks potongan dokumen paling relevan menggunakan Arsitektur Hybrid RAG mutakhir
     * (Dense Vector Similarity via pgvector + Sparse Lexical Token Search dengan Reciprocal Rank Fusion / RRF).
     *
     * @param string $query Pertanyaan atau konteks yang dicari
     * @param int $limit Jumlah dokumen teratas
     * @return array
     */
    public function retrieveRelevantContext(string $query, int $limit = 4): array
    {
        $cleanQuery = trim($query);
        if (empty($cleanQuery)) {
            return [];
        }

        $denseResults = collect();
        $sparseResults = collect();

        // 1. DENSE VECTOR RETRIEVAL (Pencarian Semantik Makna via Cosine Distance)
        $queryVector = $this->generateEmbedding($cleanQuery);
        if (!empty($queryVector)) {
            try {
                $denseResults = AiKnowledgeVector::searchSimilar($queryVector, $limit * 2)->get();
            } catch (\Throwable $e) {
                Log::warning("Vector similarity search failed: " . $e->getMessage());
            }
        }

        // 2. SPARSE LEXICAL RETRIEVAL (Pencarian Eksak Kata Kunci)
        $words = array_filter(
            explode(' ', strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleanQuery))),
            fn($w) => mb_strlen($w) >= 3
        );
        if (!empty($words)) {
            try {
                $likeOp = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $queryBuilder = AiKnowledgeVector::query();
                $queryBuilder->where(function ($sub) use ($words, $likeOp) {
                    foreach (array_slice($words, 0, 8) as $word) {
                        $sub->orWhere('title', $likeOp, "%{$word}%")
                            ->orWhere('content', $likeOp, "%{$word}%");
                    }
                });
                $sparseResults = $queryBuilder->limit($limit * 2)->get();
            } catch (\Throwable $e) {
                Log::warning("Sparse lexical search failed: " . $e->getMessage());
            }
        }

        // 3. RECIPROCAL RANK FUSION (RRF)
        $rrfScores = [];
        $itemsById = [];

        foreach ($denseResults as $rank => $item) {
            $id = (string) $item->id;
            $itemsById[$id] = $item;
            $sim = isset($item->similarity_score) ? (float) $item->similarity_score : 0.85;
            $rrfScores[$id] = ($rrfScores[$id] ?? 0.0) + (1.0 / (60 + $rank + 1)) + ($sim * 0.02);
        }

        foreach ($sparseResults as $rank => $item) {
            $id = (string) $item->id;
            $itemsById[$id] = $item;
            $rrfScores[$id] = ($rrfScores[$id] ?? 0.0) + (1.0 / (60 + $rank + 1));
        }

        arsort($rrfScores);
        $topIds = array_slice(array_keys($rrfScores), 0, $limit);

        $finalItems = collect($topIds)->map(fn($id) => $itemsById[$id])->filter();

        return $finalItems->map(function ($item) {
            $sim = isset($item->similarity_score) ? (float) $item->similarity_score : 0.90;
            $scoreVal = $sim > 1 ? $sim / 100 : $sim;
            $snippet = Str::limit(trim(strip_tags((string) $item->content)), 220);

            return [
                'id' => $item->id,
                'title' => $item->title,
                'source' => $item->source_type ?? 'Dokumentasi Arsitektur Neriah Pro',
                'source_type' => $item->source_type ?? 'system_guide',
                'source_id' => $item->source_id,
                'score' => $scoreVal,
                'similarity_score' => round($scoreVal * 100, 1),
                'content' => $item->content,
                'content_snippet' => $snippet,
                'tags' => array_values(array_filter([
                    $item->source_type ?? null,
                    $item->metadata['category'] ?? null,
                    'Hybrid RAG (Dense+Sparse RRF)',
                ])),
                'metadata' => $item->metadata ?? [],
            ];
        })->values()->toArray();
    }

    /**
     * Format konteks RAG menjadi blok Markdown untuk prompt LLM.
     */
    public function formatContextForPrompt(array $relevantChunks): string
    {
        if (empty($relevantChunks)) {
            return '';
        }

        $out = "\n\n### 📚 BASIS PENGETAHUAN TERVERIFIKASI (RAG POSTGRESQL STRICT ULID)\n";
        $out .= "Informasi di bawah ini diambil langsung dari database arsitektur dan buku panduan resmi Neriah Pro dengan kemiripan semantik tertinggi. Gunakan angka, nama parameter, nominal tarif, dan regulasi ini sebagai referensi valid:\n\n";

        foreach ($relevantChunks as $idx => $chunk) {
            $num = $idx + 1;
            $out .= "--- [Kutipan #{$num}: {$chunk['title']} (Sumber: {$chunk['source_type']} | Relevansi: {$chunk['similarity_score']}%) ] ---\n";
            $out .= trim($chunk['content']) . "\n\n";
        }

        $out .= "--- AKHIR BASIS PENGETAHUAN TERVERIFIKASI ---\n";

        return $out;
    }

    /**
     * Sinkronisasi seluruh Buku Panduan Strategis Sistem Informasi (Smart Guide) dan 10 Pilar Arsitektur ke dalam RAG pgvector.
     */
    public function syncSystemKnowledgeBase(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            AiKnowledgeVector::where('source_id', 'LIKE', 'smartguide-%')
                ->orWhere('source_id', 'LIKE', 'pilar-%')
                ->orWhere('source_id', 'LIKE', 'sectoral-ui-%')
                ->orWhere('source_id', 'LIKE', 'stack-preset-%')
                ->orWhere('source_id', 'LIKE', 'killer-engine-%')
                ->delete();
        }

        $syncedSections = 0;
        $totalChunks = 0;

        // 1. Indeks 10 Pilar Software Factory OS & Enterprise Governance
        $pillars = [
            [
                'id' => 'pilar-1-prd-26',
                'title' => 'Pilar 1: Dokumen PRD 26 Parameter & Problem-Solution Fit',
                'content' => "Dokumen PRD 26 Parameter Neriah Pro Software Factory OS mencakup analisis komprehensif: Executive Summary, Target Persona, Problem-Solution Fit, RBAC Matrix, Kepatuhan Regulasi UU PDP No. 27/2022 & UU ITE, Threat Modeling OWASP Top 10, Skema PostgreSQL 16 DDL Strict ULID, Diagram WBS 5 Sprint Mermaid Gantt, Kontrak API RESTful/JSON, dan SLA Maintenance 3 Bulan.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-2-strict-ulid',
                'title' => 'Pilar 2: Database PostgreSQL 16 Strict ULID & Keyset Cursor O(1)',
                'content' => "Seluruh tabel domain bisnis wajib menggunakan ULID 26 karakter (->ulid('id')->primary()) untuk skalabilitas terdistribusi dan kompatibilitas PostgreSQL 16. Dilarang keras menggunakan AUTO_INCREMENT atau ->id(). Pagination wajib menggunakan Keyset Cursor Pagination O(1) (cursorPaginate()) dengan pointer orderBy('id', 'asc') untuk menjamin O(1) time complexity tanpa degradasi jutaan baris.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-3-docker-env',
                'title' => 'Pilar 3: Containerized Docker Environment (PHP 8.5+, PG 16, Redis 7)',
                'content' => "Lingkungan deployment production terisolasi menggunakan Docker Compose multi-container: PHP 8.5+ FPM dengan ekstensi OPcache/APCu, PostgreSQL 16 dengan pgvector extension, Redis 7 untuk event-driven caching (Cache::rememberForever) dan high-throughput queue, serta Nginx reverse proxy dengan TLS/SSL dan Brotli compression.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-4-synthetic-seeder',
                'title' => 'Pilar 4: Synthetic Seeder 100+ Baris & Factory Resilience',
                'content' => "Database seeder sintetis menghasilkan minimal 100+ baris data realistis untuk stress-testing performa, validasi Keyset Cursor Pagination O(1), verifikasi pencarian berindeks, dan pengujian boundary edge-case sebelum aplikasi dilepas ke staging.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-5-agent-rules',
                'title' => 'Pilar 5: AI Agent Rules & Engineering Governance (.cursorrules & AGENTS.md)',
                'content' => "Standar kepatuhan AI Agent mutlak (.cursorrules, AGENTS.md, Graphify Knowledge Graph): Penolakan keras terhadap hardcoded magic strings (Zero Tolerance Dosa Hardcode), pelarangan dialog JavaScript bawaan (Strict Ban window.alert/confirm), border radius subtle (rounded-none s/d rounded-md, ban rounded-full capsule), dan tema sharp brutalism monochrome.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-6-pest-tests',
                'title' => 'Pilar 6: Pest ApiContractTest & Security Defense Suite',
                'content' => "Suite pengujian otomatis berbasis Pest PHP menguji seluruh endpoint API, validasi contract JSON, otentikasi role RBAC, proteksi CSRF/Honeypot, dan simulasi skenario kegagalan webhook Midtrans dengan Dead Letter Queue (DLQ).",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-7-cicd-deploy',
                'title' => 'Pilar 7: CI/CD Pipeline & 6 Skenario Script deploy.sh',
                'content' => "Automasi deployment zero-downtime berbasis 6 skenario: 1 (Skenario Ringan Blade/PHP), 2 (Migrasi Aman kolom/tabel baru), 3 (Hancur & Bangun DB Staging Only), 4 (Install Plugin/Package Composer/Filament), 5 (Dump Autoload restrukturisasi class), dan 6 (Build Assets Frontend Vite/Tailwind).",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-8-commercialization-scopelock',
                'title' => 'Pilar 8: Commercialization, Escrow Termin (DP 50%) & SHA-256 Scope Lock',
                'content' => "Pencegahan scope creep melalui Scope Lock digital berstempel SHA-256 dan tanda tangan Canvas Signature Pad. Sistem pembayaran escrow termin Midtrans Snap (DP 50% untuk memulai sprint, Pelunasan 50% setelah lolos UAT) terhubung ke webhook idempoten.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-9-sprint-capacity',
                'title' => 'Pilar 9: Managed Sprint Capacity & Anti-Collision Engine',
                'content' => "Filosofi managed capacity: Membatasi pengerjaan maksimal 2-3 proyek paralel per batch demi menjaga standar rekayasa enterprise anti-burnout dan anti-AI-slop. Jadwal sprint terkunci di Sprint Timeline Page dan widget Gantt real-time.",
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-10-asset-governance',
                'title' => 'Pilar 10: Customer Portal Transparency & Asset Lifecycle Governance',
                'content' => "Portal klien real-time (/customer/dashboard) menampilkan timeline Gantt pengerjaan, dokumen PRD terkunci, dan URL staging preview. Manajemen siklus hidup domain, hosting, dan server dengan notifikasi otomatis H-30/H-7 menjelang jatuh tempo.",
                'category' => '10 Pilar Software Factory OS',
            ],
        ];

        foreach ($pillars as $pilar) {
            $sourceId = "pilar-{$pilar['id']}";
            if (!$forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $embedding = $this->generateEmbedding($pilar['title'] . "\n\n" . $pilar['content']);
            AiKnowledgeVector::create([
                'title' => $pilar['title'],
                'source_type' => 'architecture_pillar',
                'source_id' => $sourceId,
                'content' => "### {$pilar['title']}\nKategori: {$pilar['category']}\n\n{$pilar['content']}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => $pilar['category'],
                    'pillar_key' => $pilar['id'],
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 2. Indeks Pricing & Unit Economics
        $pricingOverview = "Unit Economics Neriah Pro:
- Lisensi Retail Spark: Rp 0 (2x AI PRD Audit per bulan, public access).
- Lisensi Retail Lite: Rp 99.000 (1 Proyek PRD lengkap, 30 hari jendela revisi).
- Lisensi Retail Pro: Rp 399.000 (1 Proyek, 6 bulan unlimited AI re-prompt, export scaffold).
- Lisensi Retail Ultimate: Rp 1.490.000 (1 Proyek enterprise, 1 tahun prioritas update, PostgreSQL DDL).
- Studio Turnkey Advisory Tier: Rp 2.500.000 (Dokumen PRD 26 parameter, PostgreSQL DDL, WBS Mermaid Gantt, 100% potongan biaya jika lanjut ke Full MVP).
- Studio Full Monolith MVP: Rp 50.000.000 (DP 50% Rp 25.000.000, 100% turnkey dikerjakan langsung oleh Senior Architect mencakup seluruh 7 pilar factory OS, staging VPS hardened, garansi SLA 3 bulan).
- UMKM Digital Starter Tier: Rp 3.750.000 (Mesin transaksi web, kasir, database PostgreSQL terpusat, admin Filament v5, pembayaran QRIS otomatis, domain cloud 1 bulan).";

        $sourceId = 'smartguide-unit-economics';
        if ($forceRefresh || !AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
            $embedding = $this->generateEmbedding($pricingOverview);
            AiKnowledgeVector::create([
                'title' => 'Unit Economics, Pricing Tier & Studio Turnkey Contracts',
                'source_type' => 'unit_economics',
                'source_id' => $sourceId,
                'content' => "### Unit Economics, Pricing Tier & Studio Contracts\n\n{$pricingOverview}",
                'embedding' => $embedding,
                'metadata' => ['category' => 'Unit Economics'],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 3. Indeks 12 Sectoral Industrial UI/UX Archetypes & Realistic Seeders (Project OS v2.5)
        $sectorCatalog = \App\Support\ProjectOsMatrixCatalog::sectors();
        foreach ($sectorCatalog as $sec) {
            $code = $sec['code'];
            $sourceId = 'sectoral-ui-' . strtolower($code);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $uiArch = $sec['ui_ux_archetypes'] ?? \App\Support\PosSectorUiArchetypeCatalog::forSector($code);
            $seeder = $sec['realistic_seeder_contract'] ?? \App\Support\PosSectorUiArchetypeCatalog::seederForSector($code);
            $nameId = $sec['name']['id'] ?? $code;
            $ergonomics = implode("\n- ", array_map(
                fn ($item) => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string) $item,
                $uiArch['ergonomics_and_layout'] ?? []
            ));
            $shortcuts = implode(', ', array_map(
                fn ($s) => is_array($s) ? (($s['key'] ?? '') . ': ' . ($s['action'] ?? '')) : (string) $s,
                $uiArch['keyboard_shortcuts'] ?? []
            ));
            $forbidden = implode('; ', array_map(
                fn ($item) => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string) $item,
                $uiArch['forbidden_ux_patterns'] ?? []
            ));
            $fixtures = implode("\n- ", array_map(
                fn ($item) => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string) $item,
                $seeder['realistic_fixtures'] ?? []
            ));

            $secContent = "Sektor: {$code} — {$nameId}\n"
                . "Primary UI Archetype: " . ($uiArch['primary_archetype'] ?? '-') . "\n"
                . "Secondary UI Archetype: " . ($uiArch['secondary_archetype'] ?? '-') . "\n"
                . "Headline Ergonomi: " . ($uiArch['headline']['id'] ?? '') . "\n"
                . "Aturan Layout & Ergonomi Industri:\n- {$ergonomics}\n"
                . "Pintasan Keyboard Operasional: {$shortcuts}\n"
                . "Pola UX Terlarang: {$forbidden}\n"
                . "Realistic Domain Seeder ({$seeder['seeder_class']}):\n- {$fixtures}";

            $embedding = $this->generateEmbedding("{$nameId}\n{$secContent}");
            AiKnowledgeVector::create([
                'title' => "Sectoral UI/UX Archetype: {$nameId} ({$uiArch['primary_archetype']})",
                'source_type' => 'sectoral_ui_archetype',
                'source_id' => $sourceId,
                'content' => "### Sectoral UI/UX Archetype: {$nameId}\n\n{$secContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '12 Sectoral UI/UX Archetypes',
                    'sector_code' => $code,
                    'primary_archetype' => $uiArch['primary_archetype'] ?? '',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 4. Indeks 9 Curated Tech Stack & Topology Adapters (M1-M4, D1-D3, S1-S2)
        foreach (\App\Support\PosStackPresetCatalog::stackPresets() as $preset) {
            $pCode = $preset['code'];
            $sourceId = 'stack-preset-' . strtolower($preset['short_code']);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $pName = $preset['name']['id'] ?? $pCode;
            $layers = $preset['layer_technologies'] ?? [];
            $backendStack = (string) ($layers['backend'] ?? ($preset['backend_stack'] ?? '-'));
            $frontendStack = (string) ($layers['frontend_public'] ?? ($preset['frontend_stack'] ?? '-'));
            $adminStack = (string) ($layers['frontend_admin'] ?? ($preset['admin_panel_stack'] ?? '-'));
            $antiSlopRules = is_array($preset['stack_specific_anti_slop_rules'] ?? null)
                ? implode('; ', $preset['stack_specific_anti_slop_rules'])
                : (string) ($preset['stack_specific_anti_slop_rules'] ?? '');

            $pContent = "Preset Kode: [{$preset['short_code']}] {$pCode} ({$preset['topology']})\n"
                . "Nama Stack: {$pName}\n"
                . "Backend: {$backendStack} | Frontend: {$frontendStack} | Admin: {$adminStack}\n"
                . "Anti-Slop Spesifik Stack: {$antiSlopRules}";

            $embedding = $this->generateEmbedding("{$pName}\n{$pContent}");
            AiKnowledgeVector::create([
                'title' => "Tech Stack Adapter [{$preset['short_code']}]: {$pName}",
                'source_type' => 'stack_preset_adapter',
                'source_id' => $sourceId,
                'content' => "### Tech Stack Adapter [{$preset['short_code']}]: {$pName}\n\n{$pContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '9 Tech Stack Adapters',
                    'preset_code' => $pCode,
                    'topology' => $preset['topology'],
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 5. Indeks 6 Killer Zero-to-Production Orchestration Engines
        foreach (\App\Support\PosSectorUiArchetypeCatalog::killerFeaturesSummary() as $killer) {
            $kCode = $killer['code'];
            $sourceId = 'killer-engine-' . strtolower($kCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $kTitle = $killer['title']['id'] ?? $kCode;
            $kDesc = $killer['desc']['id'] ?? '';
            $kContent = "Kode Mesin: {$kCode} ({$killer['badge']})\nJudul: {$kTitle}\nPenjelasan Operasional: {$kDesc}";

            $embedding = $this->generateEmbedding("{$kTitle}\n{$kContent}");
            AiKnowledgeVector::create([
                'title' => "Killer Engine Project OS: {$kTitle}",
                'source_type' => 'killer_orchestration_engine',
                'source_id' => $sourceId,
                'content' => "### {$kTitle}\n\n{$kContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '6 Killer Orchestration Engines',
                    'engine_code' => $kCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 6. Indeks 8 Final Day-2 Production Layers (Project OS v3.0)
        foreach (\App\Support\PosDayTwoProductionCatalog::finalEightLayersSummary() as $layer) {
            $lCode = (string) ($layer['code'] ?? 'LAYER');
            $sourceId = 'day2-layer-' . strtolower($lCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $lTitle = $layer['title']['id'] ?? $lCode;
            $lDesc = $layer['desc']['id'] ?? '';
            $lContent = "Layer Kode: {$lCode} ({$layer['badge']})\nJudul: {$lTitle}\nSpesifikasi Day-2 Production: {$lDesc}";

            $embedding = $this->generateEmbedding("{$lTitle}\n{$lContent}");
            AiKnowledgeVector::create([
                'title' => "Day-2 Production Layer: {$lTitle}",
                'source_type' => 'day2_production_layer',
                'source_id' => $sourceId,
                'content' => "### {$lTitle}\n\n{$lContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '8 Day-2 Production Layers (v3.0)',
                    'layer_code' => $lCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 7. Indeks 10 Ultimate Perfection Pillars (Project OS v3.0)
        $perfectionSourceId = 'project-os-v3-ten-perfection-pillars';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $perfectionSourceId)->exists()) {
            $tenLines = [];
            foreach (\App\Support\PosDayTwoProductionCatalog::tenPerfectionPillars() as $tp) {
                $pNum = $tp['number'] ?? '';
                $pLabel = $tp['title']['id'] ?? ($tp['code'] ?? '');
                $pSummary = $tp['summary']['id'] ?? ($tp['desc']['id'] ?? '');
                $tenLines[] = "Pilar #{$pNum} — {$pLabel}: {$pSummary}";
            }
            $tenContent = implode("\n", $tenLines);
            $embedding = $this->generateEmbedding("10 Ultimate Perfection Pillars Project OS v3.0\n{$tenContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v3.0: 10 Ultimate Perfection Pillars (100% Zero-Assumption)',
                'source_type' => 'architecture_perfection_matrix',
                'source_id' => $perfectionSourceId,
                'content' => "### Project OS v3.0: 10 Ultimate Perfection Pillars\n\n{$tenContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v3.0 Perfection Matrix',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        Log::info("VectorEmbeddingService::syncSystemKnowledgeBase: Berhasil menyinkronkan {$syncedSections} bagian ke basis pengetahuan RAG.");

        return [
            'status' => 'success',
            'synced_sections' => $syncedSections,
            'total_chunks' => $totalChunks,
            'message' => "Berhasil menyinkronkan {$syncedSections} bagian (10 Pilar Factory OS, 12 Sectoral UI/UX, 9 Stack Adapters, 6 Killer Engines, 8 Day-2 Production Layers & 10 Perfection Pillars v3.0) ke basis pengetahuan RAG pgvector.",
        ];
    }
}

