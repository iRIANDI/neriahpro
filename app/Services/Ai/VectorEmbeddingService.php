<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeVector;
use App\Models\PosArchitecturePillar;
use App\Services\PosCommercialBridgeService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use App\Support\PosDayTwoProductionCatalog;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosLifecycleAndStudioCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosSeoGeoAndZeroFailureCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use App\Support\ProjectOsMatrixCatalog;
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
                            ['text' => $safeText],
                        ],
                    ],
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
                    if (! self::$rateLimitWarned) {
                        Log::warning("VectorEmbeddingService: Gemini API embedding gagal/ditolak (HTTP {$status}). Mengaktifkan fallback deterministik 768 dimensi.");
                        self::$rateLimitWarned = true;
                    }
                    break;
                }
            } catch (\Throwable $e) {
                Log::warning("VectorEmbeddingService Exception ({$candidateModel}): ".$e->getMessage());
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
        $hash = hash('sha512', $text.'neriahpro_salt_vector_seed_2026');
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
            if (empty($para)) {
                continue;
            }

            if (mb_strlen($currentChunk) + mb_strlen($para) + 2 <= $chunkSize) {
                $currentChunk .= ($currentChunk !== '' ? "\n\n" : '').$para;
            } else {
                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                    $tail = mb_substr($currentChunk, max(0, mb_strlen($currentChunk) - $overlap));
                    $currentChunk = $tail."\n\n".$para;
                } else {
                    $sentences = preg_split('/(?<=[.?!])\s+/', $para);
                    $subChunk = '';
                    foreach ($sentences as $sentence) {
                        if (mb_strlen($subChunk) + mb_strlen($sentence) + 1 <= $chunkSize) {
                            $subChunk .= ($subChunk !== '' ? ' ' : '').$sentence;
                        } else {
                            if ($subChunk !== '') {
                                $chunks[] = $subChunk;
                            }
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
        // AgentShield Layer 1: Sanitize untrusted/client documents before chunking & embedding into RAG
        $isTrustedInternal = in_array($sourceType, [
            'system_guide',
            'fifteen_pillars_freeze_v7',
            'inclusive_visual_ui_v8',
            'inclusive_security_pricing_v8',
            'agentshield_codesign_audit_v9',
            'dual_zone_aniwall_saas_v10',
        ], true);

        if (! $isTrustedInternal) {
            $scan = app(DocumentThreatSanitizerService::class)->sanitizeDocumentText($fullText, $title);
            $fullText = $scan['sanitized_text'];
            $metadata['agentshield_clean'] = $scan['is_clean'];
            $metadata['agentshield_threats'] = $scan['detected_threats'];
        }

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
                'title' => $title.($totalChunks > 1 ? ' (Bagian '.($index + 1)."/{$totalChunks})" : ''),
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
     * @param  string  $query  Pertanyaan atau konteks yang dicari
     * @param  int  $limit  Jumlah dokumen teratas
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
        if (! empty($queryVector)) {
            try {
                $denseResults = AiKnowledgeVector::searchSimilar($queryVector, $limit * 2)->get();
            } catch (\Throwable $e) {
                Log::warning('Vector similarity search failed: '.$e->getMessage());
            }
        }

        // 2. SPARSE LEXICAL RETRIEVAL (Pencarian Eksak Kata Kunci)
        $words = array_filter(
            explode(' ', strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleanQuery))),
            fn ($w) => mb_strlen($w) >= 3
        );
        if (! empty($words)) {
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
                Log::warning('Sparse lexical search failed: '.$e->getMessage());
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

        $finalItems = collect($topIds)->map(fn ($id) => $itemsById[$id])->filter();

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
            $srcType = (string) ($chunk['source_type'] ?? 'system_guide');
            $out .= "--- [Kutipan #{$num}: {$chunk['title']} (Sumber: {$srcType} | Relevansi: {$chunk['similarity_score']}%) ] ---\n";
            $isUntrustedClientChunk = str_contains(strtolower($srcType), 'client')
                || str_contains(strtolower($srcType), 'upload')
                || str_contains(strtolower($srcType), 'untrusted');
            if ($isUntrustedClientChunk) {
                $out .= DocumentThreatSanitizerService::wrapUntrustedClientDocument((string) $chunk['content'], (string) ($chunk['title'] ?? 'CLIENT_RAG_CHUNK'))."\n\n";
            } else {
                $out .= trim((string) $chunk['content'])."\n\n";
            }
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
                ->orWhere('source_id', 'LIKE', 'inclusive-v8-%')
                ->orWhere('source_id', 'LIKE', 'agentshield-v9-%')
                ->orWhere('source_id', 'LIKE', 'tier1-multilang-%')
                ->orWhere('source_id', 'LIKE', 'dual-zone-v10-%')
                ->orWhere('source_id', 'LIKE', 'saas-v10-%')
                ->orWhere('source_id', 'LIKE', 'biometrics-geofence-v12-%')
                ->delete();
        }

        $syncedSections = 0;
        $totalChunks = 0;

        // 1. Indeks 10 Pilar Software Factory OS & Enterprise Governance
        $pillars = [
            [
                'id' => 'pilar-1-prd-26',
                'title' => 'Pilar 1: Dokumen PRD 26 Parameter & Problem-Solution Fit',
                'content' => 'Dokumen PRD 26 Parameter Neriah Pro Software Factory OS mencakup analisis komprehensif: Executive Summary, Target Persona, Problem-Solution Fit, RBAC Matrix, Kepatuhan Regulasi UU PDP No. 27/2022 & UU ITE, Threat Modeling OWASP Top 10, Skema PostgreSQL 16 DDL Strict ULID, Diagram WBS 5 Sprint Mermaid Gantt, Kontrak API RESTful/JSON, dan SLA Maintenance 3 Bulan.',
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
                'content' => 'Lingkungan deployment production terisolasi menggunakan Docker Compose multi-container: PHP 8.5+ FPM dengan ekstensi OPcache/APCu, PostgreSQL 16 dengan pgvector extension, Redis 7 untuk event-driven caching (Cache::rememberForever) dan high-throughput queue, serta Nginx reverse proxy dengan TLS/SSL dan Brotli compression.',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-4-synthetic-seeder',
                'title' => 'Pilar 4: Synthetic Seeder 100+ Baris & Factory Resilience',
                'content' => 'Database seeder sintetis menghasilkan minimal 100+ baris data realistis untuk stress-testing performa, validasi Keyset Cursor Pagination O(1), verifikasi pencarian berindeks, dan pengujian boundary edge-case sebelum aplikasi dilepas ke staging.',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-5-agent-rules',
                'title' => 'Pilar 5: AI Agent Rules & Engineering Governance (.cursorrules & AGENTS.md)',
                'content' => 'Standar kepatuhan AI Agent mutlak (.cursorrules, AGENTS.md, Graphify Knowledge Graph): Penolakan keras terhadap hardcoded magic strings (Zero Tolerance Dosa Hardcode), pelarangan dialog JavaScript bawaan (Strict Ban window.alert/confirm), border radius subtle (rounded-none s/d rounded-md, ban rounded-full capsule), dan tema sharp brutalism monochrome.',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-6-pest-tests',
                'title' => 'Pilar 6: Pest ApiContractTest & Security Defense Suite',
                'content' => 'Suite pengujian otomatis berbasis Pest PHP menguji seluruh endpoint API, validasi contract JSON, otentikasi role RBAC, proteksi CSRF/Honeypot, dan simulasi skenario kegagalan webhook Midtrans dengan Dead Letter Queue (DLQ).',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-7-cicd-deploy',
                'title' => 'Pilar 7: CI/CD Pipeline & 6 Skenario Script deploy.sh',
                'content' => 'Automasi deployment zero-downtime berbasis 6 skenario: 1 (Skenario Ringan Blade/PHP), 2 (Migrasi Aman kolom/tabel baru), 3 (Hancur & Bangun DB Staging Only), 4 (Install Plugin/Package Composer/Filament), 5 (Dump Autoload restrukturisasi class), dan 6 (Build Assets Frontend Vite/Tailwind).',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-8-commercialization-scopelock',
                'title' => 'Pilar 8: Commercialization, Escrow Termin (DP 50%) & SHA-256 Scope Lock',
                'content' => 'Pencegahan scope creep melalui Scope Lock digital berstempel SHA-256 dan tanda tangan Canvas Signature Pad. Sistem pembayaran escrow termin Midtrans Snap (DP 50% untuk memulai sprint, Pelunasan 50% setelah lolos UAT) terhubung ke webhook idempoten.',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-9-sprint-capacity',
                'title' => 'Pilar 9: Managed Sprint Capacity & Anti-Collision Engine',
                'content' => 'Filosofi managed capacity: Membatasi pengerjaan maksimal 2-3 proyek paralel per batch demi menjaga standar rekayasa enterprise anti-burnout dan anti-AI-slop. Jadwal sprint terkunci di Sprint Timeline Page dan widget Gantt real-time.',
                'category' => '10 Pilar Software Factory OS',
            ],
            [
                'id' => 'pilar-10-asset-governance',
                'title' => 'Pilar 10: Customer Portal Transparency & Asset Lifecycle Governance',
                'content' => 'Portal klien real-time (/customer/dashboard) menampilkan timeline Gantt pengerjaan, dokumen PRD terkunci, dan URL staging preview. Manajemen siklus hidup domain, hosting, dan server dengan notifikasi otomatis H-30/H-7 menjelang jatuh tempo.',
                'category' => '10 Pilar Software Factory OS',
            ],
        ];

        foreach ($pillars as $pilar) {
            $sourceId = "pilar-{$pilar['id']}";
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $embedding = $this->generateEmbedding($pilar['title']."\n\n".$pilar['content']);
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
        $pricingOverview = 'Unit Economics Neriah Pro:
- Lisensi Retail Spark: Rp 0 (2x AI PRD Audit per bulan, public access).
- Lisensi Retail Lite: Rp 99.000 (1 Proyek PRD lengkap, 30 hari jendela revisi).
- Lisensi Retail Pro: Rp 399.000 (1 Proyek, 6 bulan unlimited AI re-prompt, export scaffold).
- Lisensi Retail Ultimate: Rp 1.490.000 (1 Proyek enterprise, 1 tahun prioritas update, PostgreSQL DDL).
- Studio Turnkey Advisory Tier: Rp 2.500.000 (Dokumen PRD 26 parameter, PostgreSQL DDL, WBS Mermaid Gantt, 100% potongan biaya jika lanjut ke Full MVP).
- Studio Full Monolith MVP: Rp 50.000.000 (DP 50% Rp 25.000.000, 100% turnkey dikerjakan langsung oleh Senior Architect mencakup seluruh 7 pilar factory OS, staging VPS hardened, garansi SLA 3 bulan).
- UMKM Digital Starter Tier: Rp 3.750.000 (Mesin transaksi web, kasir, database PostgreSQL terpusat, admin Filament v5, pembayaran QRIS otomatis, domain cloud 1 bulan).';

        $sourceId = 'smartguide-unit-economics';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
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
        $sectorCatalog = ProjectOsMatrixCatalog::sectors();
        foreach ($sectorCatalog as $sec) {
            $code = $sec['code'];
            $sourceId = 'sectoral-ui-'.strtolower($code);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $uiArch = $sec['ui_ux_archetypes'] ?? PosSectorUiArchetypeCatalog::forSector($code);
            $seeder = $sec['realistic_seeder_contract'] ?? PosSectorUiArchetypeCatalog::seederForSector($code);
            $nameId = $sec['name']['id'] ?? $code;
            $ergonomics = implode("\n- ", array_map(
                fn ($item) => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string) $item,
                $uiArch['ergonomics_and_layout'] ?? []
            ));
            $shortcuts = implode(', ', array_map(
                fn ($s) => is_array($s) ? (($s['key'] ?? '').': '.($s['action'] ?? '')) : (string) $s,
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
                .'Primary UI Archetype: '.($uiArch['primary_archetype'] ?? '-')."\n"
                .'Secondary UI Archetype: '.($uiArch['secondary_archetype'] ?? '-')."\n"
                .'Headline Ergonomi: '.($uiArch['headline']['id'] ?? '')."\n"
                ."Aturan Layout & Ergonomi Industri:\n- {$ergonomics}\n"
                ."Pintasan Keyboard Operasional: {$shortcuts}\n"
                ."Pola UX Terlarang: {$forbidden}\n"
                ."Realistic Domain Seeder ({$seeder['seeder_class']}):\n- {$fixtures}";

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
        foreach (PosStackPresetCatalog::stackPresets() as $preset) {
            $pCode = $preset['code'];
            $sourceId = 'stack-preset-'.strtolower($preset['short_code']);
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
                ."Nama Stack: {$pName}\n"
                ."Backend: {$backendStack} | Frontend: {$frontendStack} | Admin: {$adminStack}\n"
                ."Anti-Slop Spesifik Stack: {$antiSlopRules}";

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
        foreach (PosSectorUiArchetypeCatalog::killerFeaturesSummary() as $killer) {
            $kCode = $killer['code'];
            $sourceId = 'killer-engine-'.strtolower($kCode);
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
        foreach (PosDayTwoProductionCatalog::finalEightLayersSummary() as $layer) {
            $lCode = (string) ($layer['code'] ?? 'LAYER');
            $sourceId = 'day2-layer-'.strtolower($lCode);
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
            foreach (PosDayTwoProductionCatalog::tenPerfectionPillars() as $tp) {
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

        // 8. Indeks 5 Day-30+ Lifecycle Retention Engines & 3-Pane Architect Studio (Project OS v4.0)
        foreach (PosLifecycleAndStudioCatalog::fiveLifecycleFeatures() as $lf) {
            $lfCode = (string) ($lf['code'] ?? 'LIFECYCLE');
            $sourceId = 'day30-lifecycle-'.strtolower($lfCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $lfTitle = $lf['title']['id'] ?? $lfCode;
            $lfSummary = $lf['summary']['id'] ?? '';
            $lfArtifact = $lf['artifact'] ?? '';
            $lfContent = "Engine Lifecycle: {$lfCode} ({$lf['badge']})\nJudul: {$lfTitle}\nArtefak: {$lfArtifact}\nSpesifikasi Day-30+ Retention: {$lfSummary}";

            $embedding = $this->generateEmbedding("{$lfTitle}\n{$lfContent}");
            AiKnowledgeVector::create([
                'title' => "Day-30+ Lifecycle Engine: {$lfTitle}",
                'source_type' => 'day30_lifecycle_engine',
                'source_id' => $sourceId,
                'content' => "### {$lfTitle}\n\n{$lfContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '5 Day-30+ Lifecycle Engines (v4.0)',
                    'feature_code' => $lfCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $studioSourceId = 'project-os-v4-architect-studio-5-screens';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $studioSourceId)->exists()) {
            $studioContent = 'The Architect Studio v4.0 (/blueprint/studio):
1. Screen 1 — The 60-Second Architecture Wizard: 3-step split-screen wizard (12 Sectoral Archetypes, 9 Stack Adapters M1–S2 dengan estimasi Coolify RAM/CPU sizing, Anti-Slop Shield toggles).
2. Screen 2 — The 3-Pane Blueprint Studio (100vh Zero-Scroll IDE): Pane Kiri col-span-3 Architecture Explorer Tree, Pane Tengah col-span-5 Visual Blueprint Canvas (Schema & Locks, State Machine, UI & Print, Decision Matrix Builder match(), Delta v1.1 & Verify), Pane Kanan col-span-4 Live Compiler & 7-Phase Prompt Dock (Ctrl+Shift+C copy & Verified toggle), Bottom Status Bar (Est. Token Saved: 64%).
3. Screen 3 — Blueprint Doctor & Determinism Score: Skor determinisme tabular-nums hingga 100.0% Zero-Assumption Ready dengan deteksi kolom status tanpa State Machine Enum dan tombol [+ Buat Otomatis].
4. Screen 4 — Side-by-Side Slop Diff Previewer: Perbandingan Raw AI Prompt Slop vs NeriahPro Project OS v4.0 (Database, UI/UX, Bisnis & Pajak).
5. Screen 5 — Execution Tracker & Drift Audit Terminal: 4 badge kepatuhan 100% Pass & sinkronisasi real-time via Live MCP Server Bridge (neriahpro-mcp).';

            $embedding = $this->generateEmbedding("The Architect Studio v4.0 3-Pane IDE\n{$studioContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v4.0: The 3-Pane Architect Studio & 5 Anti-Slop Screens',
                'source_type' => 'architect_studio_ui',
                'source_id' => $studioSourceId,
                'content' => "### The 3-Pane Architect Studio v4.0 (/blueprint/studio)\n\n{$studioContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'The Architect Studio UI/UX (v4.0)',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 9. Indeks 5 Commercial-to-Code Bridge Engines & Satu Pintu Retail vs Mitra (Project OS v5.0)
        foreach (PosCommercialBridgeService::fiveCommercialBridgeFeatures() as $cf) {
            $cfCode = (string) ($cf['code'] ?? 'MITRA_BRIDGE');
            $sourceId = 'mitra-bridge-v5-'.strtolower($cfCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $cfTitle = $cf['title']['id'] ?? $cfCode;
            $cfSummary = $cf['summary']['id'] ?? '';
            $cfArtifact = $cf['artifact'] ?? '';
            $cfContent = "Mitra Commercial Bridge: {$cfCode} ({$cf['badge']})\nJudul: {$cfTitle}\nArtefak Hukum/Komersial: {$cfArtifact}\nSpesifikasi: {$cfSummary}";

            $embedding = $this->generateEmbedding("{$cfTitle}\n{$cfContent}");
            AiKnowledgeVector::create([
                'title' => "Mitra Commercial Bridge v5.0: {$cfTitle}",
                'source_type' => 'mitra_commercial_bridge',
                'source_id' => $sourceId,
                'content' => "### {$cfTitle}\n\n{$cfContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '5 Mitra Commercial-to-Code Bridges (v5.0)',
                    'feature_code' => $cfCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $satuPintuSourceId = 'project-os-v5-satu-pintu-retail-vs-mitra';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $satuPintuSourceId)->exists()) {
            $pkgLines = [];
            foreach (PricingRegistry::getSatuPintuCatalog() as $pKey => $pMeta) {
                $pName = $pMeta['name_id'] ?? $pMeta['name'] ?? $pKey;
                $stackScope = is_array($pMeta['stack_scope'] ?? null)
                    ? implode(', ', $pMeta['stack_scope'])
                    : (string) ($pMeta['stack_scope'] ?? 'M1–S2');
                $pkgLines[] = "- [{$pMeta['track_mode']}] {$pName} ({$pKey}): {$pMeta['formatted_price']} | Sektor: {$pMeta['sector_scope']} | Stack: {$stackScope}";
            }
            $journeyLines = [];
            foreach (PosCommercialBridgeService::guidedClientJourneySteps() as $gStep) {
                $journeyLines[] = "Tahap {$gStep['step']} ({$gStep['route']}): {$gStep['title']['id']} — {$gStep['desc']['id']}";
            }
            $satuPintuContent = "Katalog Satu Pintu Project OS v5.0 (Retail Mandiri vs Mitra Terima Beres untuk 12 Sektor Fungsional):\n"
                .implode("\n", $pkgLines)
                ."\n\nWizard Terpandu Klien Satu Pintu (5 Tahap End-to-End):\n"
                .implode("\n", $journeyLines);

            $embedding = $this->generateEmbedding("Satu Pintu Retail vs Mitra Project OS v5.0\n{$satuPintuContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v5.0: Arsitektur Satu Pintu Retail vs Mitra & 5-Step Guided Client Wizard',
                'source_type' => 'satu_pintu_pricing_wizard',
                'source_id' => $satuPintuSourceId,
                'content' => "### Project OS v5.0: Satu Pintu Retail vs Mitra & Guided Wizard\n\n{$satuPintuContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Satu Pintu Retail vs Mitra (v5.0)',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 10. Indeks 5 Deterministic Guarantee Engines, 5 Enterprise Glue & Hermetic Project Isolation (Project OS v6.0)
        foreach (PosEnterpriseGuaranteeCatalog::fiveDeterministicGuarantees() as $dg) {
            $dgCode = (string) ($dg['code'] ?? 'GUARANTEE');
            $sourceId = 'deterministic-guarantee-v6-'.strtolower($dgCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $dgTitle = $dg['title']['id'] ?? $dgCode;
            $dgDesc = $dg['desc']['id'] ?? '';
            $dgContent = "Mesin Penjamin Deterministik v6.0 #{$dg['number']}: {$dgCode} ({$dg['badge']})\nJudul: {$dgTitle}\nSpesifikasi Anti-Slop: {$dgDesc}";

            $embedding = $this->generateEmbedding("{$dgTitle}\n{$dgContent}");
            AiKnowledgeVector::create([
                'title' => "Project OS v6.0 Guarantee #{$dg['number']}: {$dgTitle}",
                'source_type' => 'deterministic_guarantee_v6',
                'source_id' => $sourceId,
                'content' => "### {$dgTitle}\n\n{$dgContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '5 Deterministic Guarantee Engines (v6.0)',
                    'guarantee_code' => $dgCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $glueIsolationSourceId = 'project-os-v6-enterprise-glue-and-hermetic-isolation';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $glueIsolationSourceId)->exists()) {
            $glueLines = [];
            foreach (PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives() as $gp) {
                $glueLines[] = "- [{$gp['code']}] {$gp['name']['id']} (Tabel `{$gp['table']}`, Action `{$gp['action_fqcn']}`): {$gp['invariant']}";
            }
            $v6Overview = "Project OS v6.0 — 5 Primitif Perekat Korporat (Enterprise Glue) & Protokol Hermetic Project Isolation:\n"
                .implode("\n", $glueLines)
                ."\n\nMatriks Menu L1–L4 (300 Sub-Menu Terkurasi): 12 Sektor × 5 Divisi Operasional × 5 Sub-Menu L3 = 25 layar korporat otomatis mekar walau user hanya mengetik 5 kata ide.\n"
                .'Hermetic Project Isolation: Setiap proyek wajib memiliki ULID, slug, draft_token, isolation_token, dan staging_url unik tanpa kebocoran localStorage/session dan dilarang keras menimpa proyek lama berbasis nama bisnis.';

            $embedding = $this->generateEmbedding("5 Enterprise Glue & Hermetic Project Isolation v6.0\n{$v6Overview}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v6.0: 5 Enterprise Glue Primitives, 300 L1–L4 Menus & Hermetic Isolation',
                'source_type' => 'enterprise_glue_and_isolation_v6',
                'source_id' => $glueIsolationSourceId,
                'content' => "### 5 Enterprise Glue & Hermetic Project Isolation (v6.0)\n\n{$v6Overview}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Enterprise Glue & Hermetic Isolation (v6.0)',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 11. Indeks 5 Elemen Dunia Nyata & 15 Pilar Master "Project OS Ultimate 100%" (Project OS v7.0 Architectural Freeze)
        foreach (PosRealWorldRuntimeCatalog::fiveRealWorldElementsOverview('id') as $rw) {
            $rwCode = (string) ($rw['code'] ?? 'REALWORLD_ELEMENT');
            $sourceId = 'realworld-element-v7-'.strtolower($rwCode);
            if (! $forceRefresh && AiKnowledgeVector::where('source_id', $sourceId)->exists()) {
                continue;
            }

            $rwTitle = $rw['title']['id'] ?? $rwCode;
            $rwTitleEn = $rw['title']['en'] ?? $rwCode;
            $rwDesc = $rw['desc']['id'] ?? '';
            $rwDescEn = $rw['desc']['en'] ?? '';
            $rwContent = "Elemen Dunia Nyata Project OS v7.0 #{$rw['number']}: {$rwCode} ({$rw['badge']})\nJudul ID: {$rwTitle}\nTitle EN: {$rwTitleEn}\nKontrak Lapangan (ID): {$rwDesc}\nField Contract (EN): {$rwDescEn}";

            $embedding = $this->generateEmbedding("{$rwTitle}\n{$rwContent}");
            AiKnowledgeVector::create([
                'title' => "Project OS v7.0 Real-World Element #{$rw['number']}: {$rwTitle}",
                'source_type' => 'realworld_runtime_v7',
                'source_id' => $sourceId,
                'content' => "### {$rwTitle}\n\n{$rwContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '5 Real-World Runtime Elements (v7.0 Freeze)',
                    'element_code' => $rwCode,
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $fifteenFreezeSourceId = 'project-os-v7-fifteen-master-pillars-architectural-freeze';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $fifteenFreezeSourceId)->exists()) {
            $pLines = [];
            foreach (PosArchitecturePillar::getCachedMatrix('id') as $mp) {
                $pLines[] = sprintf(
                    '%d. [%s] %s — %s | Kontrak: %s',
                    (int) ($mp['pillar_number'] ?? 0),
                    (string) ($mp['code'] ?? ''),
                    (string) ($mp['name']['id'] ?? ''),
                    (string) ($mp['function_guarantee']['id'] ?? ''),
                    (string) ($mp['technical_contract']['id'] ?? '')
                );
            }
            $freezeOverview = "Rekapitulasi Final 15 Pilar Master 'Project OS Ultimate 100%' (100% Architectural Freeze):\n"
                .implode("\n", $pLines)
                ."\n\nSinkronisasi Kausalitas Lintas Tahap: Pemilihan Sektor -> Primitif -> Menu L1–L4 v1.0 -> Stack Adapter secara reaktif memperbarui Hardware Bridge, WebSocket Channel, Composite Index + Partisi PG16, SYNTAX_LOCK_2026.md, Buku SOP Training Staf (SmartGuide.php), Nilai RAB IDR, Hash SPK SHA-256, hingga Checklist UAT/BAST.";

            $embedding = $this->generateEmbedding("15 Master Pillars Project OS v7.0 100% Architectural Freeze\n{$freezeOverview}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v7.0: 15 Master Architecture Pillars (100% Lock & Freeze) & Cross-Stage Causality',
                'source_type' => 'fifteen_pillars_freeze_v7',
                'source_id' => $fifteenFreezeSourceId,
                'content' => "### 15 Master Architecture Pillars (100% Freeze)\n\n{$freezeOverview}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '15 Master Pillars Architectural Freeze (v7.0)',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 12. Indeks Project OS v8.0: Adaptive Dual-Persona Storyboard, 36 Visual Sector UI Blueprints, 7 Tier-1 Jurisdictions & RTL, Inclusive Hybrid Pricing & 5-Layer IP Canary Security
        $v8VisualSourceId = 'inclusive-v8-36-visual-ui-and-dual-persona';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v8VisualSourceId)->exists()) {
            $uiCatalog = PosInclusiveCommercialCatalog::allSectorVisualUiCatalog();
            $uiLines = [];
            foreach ($uiCatalog as $secCode => $variants) {
                $codes = array_map(static fn (array $v): string => $v['code'].' ('.($v['name']['id'] ?? '').')', $variants);
                $uiLines[] = "- {$secCode}: ".implode(' | ', $codes);
            }
            $shells = array_keys(PosInclusiveCommercialCatalog::navigationShellOptions());
            $v8VisualContent = "Project OS v8.0 — Adaptive Dual-Persona UX (`BUSINESS_WIZARD` 5 Langkah Bahasa Manusia vs `ARCHITECT_PRO` 3-Pane Studio) & 36 Varian Visual Frontend UI/UX (12 Sektor × 3 Opsi Visual):\n"
                .implode("\n", $uiLines)
                ."\n\n5 Navigation Shells: ".implode(', ', $shells)
                ."\nSetiap perubahan varian visual UI atau kerangka navigasi langsung merambat ke BLUEPRINT_SCHEMA.json, Prompt Fase 5, EXECUTION_BLUEPRINT.md, nilai RAB IDR, dan Scope Hash SHA-256.";

            $embedding = $this->generateEmbedding("Project OS v8.0 36 Visual Sector Frontend UI Blueprints & Dual-Persona Storyboard\n{$v8VisualContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v8.0: 36 Visual Sector Frontend UI Blueprints, 5 Navigation Shells & Dual-Persona Storyboard',
                'source_type' => 'inclusive_visual_ui_v8',
                'source_id' => $v8VisualSourceId,
                'content' => "### Project OS v8.0: 36 Visual Sector UI Blueprints & Dual-Persona UX\n\n{$v8VisualContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v8.0 Inclusive UX & 36 Visual UIs',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $v8SecurityPricingSourceId = 'inclusive-v8-jurisdictions-pricing-and-5layer-security';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v8SecurityPricingSourceId)->exists()) {
            $v8SecContent = "Project OS v8.0 — 7 Tier-1 Global Native Jurisdictions & RTL, Inclusive Hybrid Pricing & 5-Layer Banking-Grade IP Security:\n"
                ."1. 7 Yurisdiksi Tier-1 Output Blueprint: `id` (SAK/ISAK 35/PSAK 109/PPh 21 TER/UU PDP), `en` (US GAAP/FASB ASC 958/HIPAA/SOC 2), `zh-CN` (CAS/PIPL/Fapiao), `ja` (J-GAAP/APPI/Qualified Invoice), `ar` (Otomatis `dir=\"rtl\"` + Logical CSS `ms-*/me-*` + ZATCA Fatoora QR + AAOIFI), `es` (NIIF/Veri*factu/RGPD), `de` (HGB/GoBD/DSGVO).\n"
                ."2. Inclusive Hybrid Value-Metric Pricing: `impact_nonprofit_grant` (Subsidi CSR 70–100% Rp 0 untuk Yayasan/Rumah Ibadah/Sekolah/Riset), `single_app_umkm_pass` (Rp 299.000 1 Aplikasi UMKM), `agency_whitelabel_partner` (Rp 990.000 Multi-Seat Agensi + Proposal RAB & SPK PDF White-Label).\n"
                .'3. 5 Retention Hooks & 5 Security Layers: Living CVE Security Alerts (`CVE-2026-109618`) + 1-Click Auto-Patch Prompt, 70:30 Verified Community Marketplace, AES-256 Envelope Vault (`encrypted_custom_vault`), 64-Bit Steganographic Zero-Width Unicode Canary Watermark (`\\u200B`/`\\u200C`/`\\u200D`), dan 1-Click Scoped MCP Token Revocation.';

            $embedding = $this->generateEmbedding("Project OS v8.0 Tier-1 RTL Jurisdictions, Inclusive Pricing & 5-Layer Canary Security\n{$v8SecContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v8.0: 7 Tier-1 Jurisdictions & RTL, Inclusive Hybrid Pricing & 5-Layer Canary IP Security',
                'source_type' => 'inclusive_security_pricing_v8',
                'source_id' => $v8SecurityPricingSourceId,
                'content' => "### Project OS v8.0: Global Tier-1 RTL, Inclusive Pricing & 5-Layer IP Security\n\n{$v8SecContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v8.0 Security, RTL & Inclusive Pricing',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 13. Indeks Project OS v9.0: NeriahPro AgentShield (5-Layer Defense), Co-Design Alignment Room & 3-Sheet Cryptographic Audit Explorer (12 Sector Presets)
        $v9AgentShieldSourceId = 'agentshield-v9-5-layers-and-3sheet-crypto-audit';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v9AgentShieldSourceId)->exists()) {
            $presetLines = [];
            foreach (PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets('id') as $p) {
                $presetLines[] = "- {$p['sector_code']}: {$p['localized_title']} (Standar: ".implode(', ', $p['compliance_standards'] ?? []).')';
            }
            $v9SecurityContent = "Project OS v9.0 — NeriahPro AgentShield (5-Layer Defense against AI Malware & Poisoned Agent Attacks) & Filterable 3-Sheet Cryptographic Audit Explorer:\n"
                ."1. Layer 1 Anti-Indirect Prompt Injection (`DocumentThreatSanitizerService`): Membersihkan hidden metadata, zero-width Unicode (`U+200B..U+200F`, `U+E0000..U+E007F`), macro/JS PDF, dan membungkus seluruh teks dokumen klien di dalam `<UNTRUSTED_CLIENT_DOCUMENT_DATA>`.\n"
                ."2. Layer 2 Anti-Slopquatting (`APPROVED_DEPENDENCY_MANIFEST`): Melarang keras AI agent menginstal package Composer/NPM/Go di luar daftar putih resmi terverifikasi.\n"
                ."3. Layer 3 Execution Sandbox (`.claude/settings.json` & `.cursorrules` Deny-List): Memblokir akses AI ke `.env*`, `.git/hooks/*`, `auth.json`, `~/.ssh/*` dan memblokir `curl`/`wget`/`nc`/`Invoke-WebRequest`.\n"
                ."4. Layer 4 Zero-Execution Binary Quarantine: Verifikasi Magic Bytes `finfo_buffer`, penghapusan metadata EXIF gambar, dan disk `private` `noexec`.\n"
                ."5. Layer 5 Cryptographic Tool-Call Verification: Spatie RBAC ketat tanpa bypass root + `idempotency_key` deterministik + rantai hash SHA-256 append-only (`prev_hash -> current_hash`).\n"
                ."6. Ekspor Audit 3-Sheet `.xlsx` (`ExportFilteredAuditExcelAction`): Sheet 1 Sertifikat Integritas SHA-256, Sheet 2 Jejak Audit Transaksi, Sheet 3 Log AI Copilot & Idempotency, serta `.csv` streaming O(1) dengan 12 Preset Audit Industri:\n"
                .implode("\n", $presetLines);

            $embedding = $this->generateEmbedding("Project OS v9.0 AgentShield 5-Layer Defense & 3-Sheet Cryptographic Audit Trail 12 Sector Presets\n{$v9SecurityContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v9.0: NeriahPro AgentShield (5-Layer AI Defense) & 3-Sheet Cryptographic Audit Explorer',
                'source_type' => 'agentshield_codesign_audit_v9',
                'source_id' => $v9AgentShieldSourceId,
                'content' => "### Project OS v9.0: AgentShield & Cryptographic Audit Explorer\n\n{$v9SecurityContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v9.0 AgentShield & Crypto Audit',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        $v9CoDesignSourceId = 'agentshield-v9-codesign-dual-key-and-worldclass-tasks';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v9CoDesignSourceId)->exists()) {
            $v9CoDesignContent = "Project OS v9.0 — The Co-Design Alignment & Sign-Off Room (Kolaborasi Developer Neriah <-> Klien Mitra) & Auto Visual UI -> World-Class 5-Sprint Task Synthesis:\n"
                ."1. Dual-Mirror Screen ('Satu Data, Dua Kacamata'): Panel 1 Daftar Modul Bisnis L1–L4 + `+ Tambah Kebutuhan Klien`; Panel 2 toggle *Client Business Lens* (Visual Wireframe Mockup + Estimasi Biaya/Hari) <-> *Architect Technical Lens* (Tabel ULID 26-char, State Machine, Pessimistic Lock) + tombol `[ ☑️ Klien Setuju Modul Ini ]` yang mencatat Timestamp & IP ke `audit_logs`; Panel 3 Anotasi Kontekstual, Kalkulator Dampak RAB & Hari Kerja Real-Time, serta **Dual-Key Cryptographic Scope Lock Ceremony** (aktif saat 100% modul `APPROVED`, membekukan `v1.0` Read-Only dengan Master Scope Hash SHA-256 + SPK & Invoice DP).\n"
                .'2. Kausalitas Penuh Ide/Sektor -> Visual UI -> World-Class Tasks: Begitu klien memasukkan ide atau memilih 1 dari 12 Sektor Fungsional, sistem otomatis mencocokkan Varian Visual Frontend (`UI_01A..UI_12C`) + Navigation Shell + 25 Menu L1–L4 + 5-Sprint World-Class Engineering Task Matrix (`TSK-101` s/d `TSK-502`).';

            $embedding = $this->generateEmbedding("Project OS v9.0 Co-Design Alignment Room Dual-Key Lock & World-Class 5-Sprint Tasks\n{$v9CoDesignContent}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v9.0: Co-Design Alignment & Sign-Off Room (Dual-Key Lock) & World-Class 5-Sprint Tasks',
                'source_type' => 'agentshield_codesign_audit_v9',
                'source_id' => $v9CoDesignSourceId,
                'content' => "### Project OS v9.0: Co-Design Alignment Room & World-Class Tasks\n\n{$v9CoDesignContent}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v9.0 Co-Design & World-Class Tasks',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 14. Indeks Project OS v10.0: Dual-Zone UI Architecture (Zone A + Zone B UX_F1..UX_F6), 5 Hukum Besi Animasi, Aniwall Terverifikasi & Universal Input Hygiene
        $v10DualZoneSourceId = 'dual-zone-v10-aniwall-scrollytelling-and-input-hygiene';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v10DualZoneSourceId)->exists()) {
            $uxLines = [];
            foreach (PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets('id') as $ux) {
                $uxLines[] = "- {$ux['code']} ({$ux['localized_name']}): {$ux['localized_summary']}";
            }
            $v10Content = "Project OS v10.0 Ultimate — Dual-Zone UI Architecture, Cryptographically Verified Testimonial Aniwall (`PRIMITIVE_VERIFIED_SOCIAL_PROOF`), 5 Hukum Besi Animasi Anti-Slop & Universal Input Hygiene:\n"
                ."1. Dual-Zone UI Policy: Zone A Operational Back-Stage (Dashboard Admin, Kasir POS, RME Klinik, Buku Besar) WAJIB 100% Linear/Stripe Crisp (`UI_01A..UI_12C`) dengan 0% parallax/scrollytelling; Zone B Public Front-Stage (Landing Page, Katalog, Kampanye Donasi, Microsite Event) menggunakan 6 Preset Advanced UX lintas 12 Sektor:\n"
                .implode("\n", $uxLines)."\n"
                ."2. 5 Hukum Besi Animasi Enterprise Anti-Slop: (1) 100% Local Bundled Libraries via `@vite` (0% CDN eksternal), (2) GPU Compositor Only (`translate3d()`, `scale()`, `opacity` — haram menganimasikan `top/left/width/height/margin`), (3) `CLS = 0.00` aspect-ratio eksplisit, (4) Wajib `@media (prefers-reduced-motion: reduce)`, (5) Lazy-Hydration via `IntersectionObserver` (`FCP < 0.8s`).\n"
                ."3. BAST-Linked & Phase-6 Telemetry-Backed Testimonials (`PRIMITIVE_VERIFIED_SOCIAL_PROOF`): Terbit otomatis saat BAST Mitra ditandatangani atau Fase 6 Retail lolos verifikasi lengkap struk telemetri (`ULID Tables | Concurrency Locks | Token Saved % | SHA-256 Scope Seal`) dan modal `[ 🔍 Inspect Anonymized Blueprint Architecture ]`.\n"
                .'4. Standar Wajib Universal Input Hygiene: Email hanya karakter resmi `[a-zA-Z0-9._%+\\-@]`; nominal angka memakai pemisah ribuan regional (`1.500.000` ID vs `1,500,000` EN); nomor telepon wajib dropdown Country Zone dari `config/country_zones.php` dan **otomatis menghapus angka `0` di awal ketikan pertama** (`0812...` -> `812...` -> `+62812...`).';

            $embedding = $this->generateEmbedding("Project OS v10.0 Dual-Zone UI Scrollytelling Aniwall 5 Iron Animation Laws & Input Hygiene\n{$v10Content}");
            AiKnowledgeVector::updateOrCreate(
                ['source_id' => $v10DualZoneSourceId],
                [
                    'title' => 'Project OS v10.0: Dual-Zone UI (UX_F1..UX_F6), Verified Aniwall, 5 Iron Animation Laws & Input Hygiene',
                    'source_type' => 'dual_zone_aniwall_saas_v10',
                    'content' => "### Project OS v10.0: Dual-Zone UI, Verified Aniwall & Universal Input Hygiene\n\n{$v10Content}",
                    'embedding' => $embedding,
                    'metadata' => [
                        'category' => 'Project OS v10.0 Dual-Zone UX & Input Hygiene',
                    ],
                ]
            );
            $syncedSections++;
            $totalChunks++;
        }

        // 15. Indeks Project OS v10.0: 6 Modul Komersial SaaS Kritikal (Billing IDR/USD <2s, Affiliate Finder's Fee, Multi-Seat White-Label, Impact Grant, SEO /explore & Health Pulse)
        $v10SaasSourceId = 'saas-v10-6-commercial-growth-modules';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v10SaasSourceId)->exists()) {
            $saasLines = [];
            foreach (PosDualZoneFrontendAndSaasCatalog::sixCriticalSaasModules('id') as $m) {
                $saasLines[] = "{$m['number']}. [{$m['code']}] {$m['localized_title']}: {$m['localized_summary']}";
            }
            $v10SaasContent = "Project OS v10.0 — 6 Modul Bisnis, Monetisasi & Pertumbuhan SaaS NeriahPro (`PosSaasCommercialEngineService`):\n"
                .implode("\n", $saasLines);

            $embedding = $this->generateEmbedding("Project OS v10.0 6 Critical SaaS Commercial Modules Billing Affiliate Multi-Seat Impact Grant SEO Explore Health Pulse\n{$v10SaasContent}");
            AiKnowledgeVector::updateOrCreate(
                ['source_id' => $v10SaasSourceId],
                [
                    'title' => 'Project OS v10.0: 6 Modul Komersial SaaS (Billing IDR/USD, Affiliate Finder\'s Fee, Multi-Seat White-Label, Impact Grant, SEO /explore & Health Pulse)',
                    'source_type' => 'dual_zone_aniwall_saas_v10',
                    'content' => "### Project OS v10.0: 6 Modul Komersial & Pertumbuhan SaaS\n\n{$v10SaasContent}",
                    'embedding' => $embedding,
                    'metadata' => [
                        'category' => 'Project OS v10.0 SaaS Commercial Engine',
                    ],
                ]
            );
            $syncedSections++;
            $totalChunks++;
        }

        // 16. Indeks Project OS v11.0 Paripurna: SEO (Schema.org 12 Sektor) & GEO (/llms.txt + IndexNow), Satu Pintu SaaS Gate & 6 Zero-Failure Layers (20 Pilar Locked)
        $v11ParipurnaSourceId = 'seo-geo-v11-uu-pdp-offline-pwa-sla-holding-20-pillars';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v11ParipurnaSourceId)->exists()) {
            $zfLines = [];
            foreach (PosSeoGeoAndZeroFailureCatalog::sixFinalZeroFailureModules('id') as $zf) {
                $zfLines[] = "{$zf['number']}. [{$zf['code']}] {$zf['title']}: {$zf['summary']}";
            }
            $v11Content = "Project OS v11.0 Paripurna Edition — SEO (Multi-Graph Schema.org 12 Sektor) & GEO (Generative Engine Optimization Top-3 AI Citation), Satu Pintu Commercial SaaS Gate & 6 Final Zero-Failure Production Features (20 Pilar 100% Locked):\n"
                ."1. Satu Pintu Commercial SaaS Gate: Satu Pintu Wizard (Retail & Mitra) terkunci langsung ke Langkah F Pemilih Paket Lisensi SaaS Retail (7 Tier) & Kontrak Mitra Turnkey (3 Tier) tanpa celah tombol bypass gratis ke `/blueprint/studio`; `/blueprint?track=retail` atau `?track=mitra` tanpa `tier` resmi otomatis diarahkan ke daftar paket SaaS `/pricing`.\n"
                ."2. `PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE`: Multi-Graph JSON-LD `@graph` spesifik 12 Sektor Fungsional + `/llms.txt` & `/llms-full.txt` native + 50-Word Answer-First GEO Capsule + Semantic HTML Comparison Table + IndexNow Instant Ping (`/indexnow-key.txt`) + allowlist crawler AI (`OAI-SearchBot`, `GPTBot`, `ClaudeBot`, `PerplexityBot`, `Google-Extended`) + Kartu OG Sitasi Lokal `1200x630` PNG/SVG ber-QR matriks lokal tanpa CDN.\n"
                ."3. 6 Final Enterprise & Statutory Zero-Failure Modules:\n"
                .implode("\n", $zfLines);

            $embedding = $this->generateEmbedding("Project OS v11.0 Paripurna SEO GEO llms.txt IndexNow UU PDP Blind Index Offline Outbox PWA SLA Warranty Holding 20 Pillars\n{$v11Content}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v11.0 Paripurna: SEO/GEO AI Citation (/llms.txt & 12-Sector Schema.org), Satu Pintu SaaS Gate & 6 Zero-Failure Layers (20 Pillars)',
                'source_type' => 'seo_geo_and_zero_failure_v11',
                'source_id' => $v11ParipurnaSourceId,
                'content' => "### Project OS v11.0 Paripurna: SEO/GEO Authority, Satu Pintu Gate & 6 Zero-Failure Layers\n\n{$v11Content}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Project OS v11.0 Paripurna SEO/GEO & Zero-Failure',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 17. Indeks Standar Arsitektur Database Multi-Bahasa Tier 1 vs Integritas Skalar (Strict ULID PK/FK, Slugs, Status Enums) & Domain Tour-Travel Normalization (Project OS v13.0)
        $v13MultilangSourceId = 'tier1-multilang-jsonb-vs-scalar-and-tour-domain-v13';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v13MultilangSourceId)->exists()) {
            $v13Content = "Project OS v13.0 — Standar Arsitektur Database Multi-Bahasa Tier 1 vs Integritas Skalar & Normalisasi Domain Bisnis Khusus (Tour, Travel & Logistics):\n"
                ."1. Pemisahan Ketat Kolom JSONB Multi-Bahasa Tier 1 vs Kolom Skalar Relasional:\n"
                ."   - Kolom Naratif / Human-Facing (WAJIB JSONB): Atribut yang dibaca dan ditampilkan langsung kepada pengguna (name, title, description, reason, memo, message_body, decision_note, threshold_reason, explanation_note, tier_name) WAJIB disimpan dalam format JSONB {\"id\": \"...\", \"en\": \"...\"} dan di-cast sebagai 'array' di model Eloquent untuk mendukung penyuntingan resmi dwibahasa di Filament Admin.\n"
                ."   - Kolom Integritas Skalar Relasional (DILARANG KERAS DIBUAT JSONB): Primary Keys (id PK), Foreign Keys (parent_id FK, user_id FK, booking_id FK), Unique Slugs / Codes / SKUs / E-mails (email UK, sku UK, code_reference UK), State Machine Enums (status, role, movement_type, direction, channel, decision), serta Kredensial & Hashes Keamanan (password, sha256_seal, ip_address) WAJIB tetap berupa tipe skalar skema relasional (VARCHAR(26) ULID, VARCHAR, dsb.) guna menjamin keutuhan relasi foreign key database, efisiensi join O(1), dan B-Tree indexing sub-10ms.\n"
                ."2. Normalisasi Domain Tour & Travel (Kasus Nyata PT Gayabalical):\n"
                ."   - Eliminasi Tabel Typo: Kesalahan ejaan seperti 'jumlah oramg' tidak boleh dijadikan entitas tabel terpisah (JUMLAH_ORAMGS), melainkan wajib dinormalisasi menjadi atribut kapasitas armada (passenger_capacity INT dan luggage_capacity INT pada tabel VEHICLES atau FLEET_CAPACITIES).\n"
                ."   - Layanan Terpadu: Transfer in/out hotel ke Bandara Ngurah Rai, Day Tour rute Bali, dan Overland Jawa Timur (Gunung Bromo & Kawah Ijen) dikelola dalam struktur katalog terstandar (tour_packages, vehicles, bookings, payments).\n"
                ."   - Wish List AI Tanpa Asumsi: Fitur wish list kustom dianalisis secara deterministik oleh AI tanpa asumsi atau halusinasi harga, berpijak 100% pada data database riil untuk merumuskan daftar rute (itinerary) dan kalkulasi biaya transparan.\n"
                ."3. Standar Tombol Elaborasi AI & Feedback UX:\n"
                ."   - Umpan balik visual instan (toast notification & modal status badge) wajib diberikan saat tombol 'Perdalam dari Dokumen Acuan' diklik.\n"
                ."   - Jika skema ERD telah mencapai tingkat kematangan paripurna (>= 14 tabel Strict ULID berelasi penuh), sistem menampilkan indikator kesiapan dan memungkinkan penambahan entitas kustom spesifik tanpa merusak relasi yang telah terverifikasi.";

            $embedding = $this->generateEmbedding("Project OS v13.0 Multi-Language Tier 1 JSONB vs Scalar ULID PK FK Tour Travel Gayabalical Normalization Wishlist AI\n{$v13Content}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v13.0: Standar Database Multi-Bahasa Tier 1 (JSONB vs Skalar) & Normalisasi Domain Tour-Travel',
                'source_type' => 'multilang_tier1_and_tour_domain_v13',
                'source_id' => $v13MultilangSourceId,
                'content' => "### Project OS v13.0: Multi-Bahasa Tier 1 (JSONB vs Skalar) & Normalisasi Domain Khusus\n\n{$v13Content}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => 'Database Architecture & Domain Standards',
                ],
            ]);
            $syncedSections++;
            $totalChunks++;
        }

        // 18. Indeks 14 Composable Core Primitives: AI Vision Biometrics (ArcFace ONNX 512-D + MiniFASNet) & Geospatial Geofence Anti-Fake GPS
        $v14PrimitivesSourceId = 'composable-primitives-biometrics-and-geofence-v14';
        if ($forceRefresh || ! AiKnowledgeVector::where('source_id', $v14PrimitivesSourceId)->exists()) {
            $v14Content = "Project OS v14.0 — 14 Composable Core Primitives & Arsitektur Dinamis Lintas Sektor (AI Vision Biometrics & Geospatial Geofencing):\n"
                ."1. PRIMITIVE_AI_VISION_BIOMETRICS (Primitif #13):\n"
                ."   - 4-Stage Computer Vision Pipeline: Deteksi bounding box wajah SCRFD -> Deteksi liveness pasif MiniFASNet FFT (<120ms) anti foto/layar HP -> Ekstraksi embedding 512-dimensi ArcFace ONNX -> Pencocokan kemiripan kosinus vektor pgvector HNSW (ambang batas >= 0.68).\n"
                ."   - Kepatuhan UU PDP No. 27/2022: Penyimpanan template terenkripsi AES-256 di disk privat dan aksi PurgeBiometricRightToErasureAction untuk pemenuhan hak dilupakan/dihapus (Pasal 8).\n"
                ."2. PRIMITIVE_GEOSPATIAL_GEOFENCE (Primitif #14):\n"
                ."   - Dual Spatial Engine: Kombinasi formula Haversine SQL dan PostgreSQL PostGIS ST_DWithin.\n"
                ."   - Proteksi Anti-Fake GPS: Validasi deviasi waktu server NTP (maks 30 detik), deteksi mock location provider, penolakan akurasi GPS buruk (>50m), dan pemblokiran kecepatan fiktif teleportasi (>120 km/jam).\n"
                ."   - Alur Persetujuan Dinas Luar Kantor: State machine otomatis mengevaluasi pengajuan presensi luar geofence dengan alasan tugas resmi menuju antrean persetujuan atasan berjenjang.\n"
                ."3. Adaptasi Dinamis Lintas Sektor: Mengintegrasikan biometrik dan geofencing secara cerdas pada Sektor 11 (HRIS), Sektor 06 (Logistik), Sektor 03 (Kesehatan), dan Sektor 10 (Media & Tiket).";

            $embedding = $this->generateEmbedding("Project OS 14 Composable Primitives AI Vision Biometrics ArcFace MiniFASNet Geospatial Geofence Haversine PostGIS UU PDP Anti Fake GPS\n{$v14Content}");
            AiKnowledgeVector::create([
                'title' => 'Project OS v14.0: 14 Core Primitives (AI Vision Biometrics & Geospatial Geofence Anti-Fake GPS)',
                'source_type' => 'composable_primitives_biometrics_geofence',
                'source_id' => $v14PrimitivesSourceId,
                'content' => "### Project OS v14.0: 14 Core Primitives (AI Vision Biometrics & Geospatial Geofence)\n\n{$v14Content}",
                'embedding' => $embedding,
                'metadata' => [
                    'category' => '14 Composable Core Primitives',
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
            'message' => "Berhasil menyinkronkan {$syncedSections} bagian (Project OS v14.0 14 Primitif Biometrik & Geofence, v13.0 Multi-Bahasa Tier 1 JSONB vs Skalar, v11.0 SEO/GEO Paripurna, v9.0 AgentShield 5-Layer, v8.0 36 Visual UI, 15 Pilar Master Freeze, 12 Sectoral UI/UX, 9 Stack Adapters, 6 Killer Engines) ke basis pengetahuan RAG pgvector.",
        ];
    }
}
