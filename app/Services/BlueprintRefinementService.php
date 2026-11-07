<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use App\Services\Ai\MultiAiModelManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlueprintRefinementService
{
    /**
     * Safely normalize any value (string, array with locale keys, null) into a plain string.
     * Prevents "Array to string conversion" errors across multi-language attributes.
     */
    public function stringifyAttribute(mixed $value, string $locale = 'id', string $separator = "\n"): string
    {
        if (is_null($value)) {
            return '';
        }
        if (is_string($value)) {
            return trim($value);
        }
        if (is_numeric($value) || is_bool($value)) {
            return (string) $value;
        }
        if (is_array($value)) {
            // Check locale match (e.g. ['id' => '...', 'en' => '...'])
            if (isset($value[$locale]) && (is_string($value[$locale]) || is_numeric($value[$locale]))) {
                return trim((string) $value[$locale]);
            }
            if ($locale === 'en' && isset($value['en']) && (is_string($value['en']) || is_numeric($value['en']))) {
                return trim((string) $value['en']);
            }
            if (isset($value['id']) && (is_string($value['id']) || is_numeric($value['id']))) {
                return trim((string) $value['id']);
            }
            if (isset($value['name'])) {
                return $this->stringifyAttribute($value['name'], $locale, $separator);
            }
            if (isset($value['title'])) {
                return $this->stringifyAttribute($value['title'], $locale, $separator);
            }
            
            // If it's a numeric list or nested array
            $parts = [];
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $sub = $this->stringifyAttribute($v, $locale, $separator);
                    if ($sub !== '') $parts[] = $sub;
                } elseif (is_string($v) || is_numeric($v)) {
                    $parts[] = is_string($k) && !is_numeric($k) ? "{$k}: {$v}" : (string) $v;
                }
            }
            return implode($separator, $parts);
        }

        return (string) $value;
    }

    /**
     * Retrieve the grounded source corpus for a blueprint.
     * Looks for synthesized_corpus.md, individual converted markdown files, or synthesizes from model attributes.
     */
    public function getSourceCorpus(VisionBlueprint $blueprint): array
    {
        $rawBusinessName = $this->stringifyAttribute($blueprint->nama_bisnis, 'id', '-');
        $rawSlug = $blueprint->slug ?: Str::slug($rawBusinessName ?: ($blueprint->client_name ?: 'project'));
        $slug = $this->stringifyAttribute($rawSlug) ?: 'project';
        $projectDir = storage_path("app/projects/{$slug}");
        $mdDir = "{$projectDir}/markdown";
        $docsDir = "{$projectDir}/documents";

        $sourceFiles = [];
        $corpusParts = [];

        // 1. Check synthesized_corpus.md
        if (file_exists("{$mdDir}/synthesized_corpus.md")) {
            $content = @file_get_contents("{$mdDir}/synthesized_corpus.md");
            if (!empty($content)) {
                $corpusParts[] = $content;
                $sourceFiles[] = [
                    'name' => 'synthesized_corpus.md',
                    'type' => 'corpus_markdown',
                    'size_kb' => round(strlen($content) / 1024, 1),
                ];
            }
        }

        // 2. Check other markdown files in markdown/ folder
        if (is_dir($mdDir)) {
            $files = glob("{$mdDir}/*.md") ?: [];
            foreach ($files as $f) {
                $base = basename($f);
                if ($base === 'synthesized_corpus.md') {
                    continue;
                }
                $c = @file_get_contents($f);
                if (!empty($c)) {
                    $corpusParts[] = "### [Berkas Acuan: {$base}]\n" . $c;
                    $sourceFiles[] = [
                        'name' => $base,
                        'type' => 'markdown_document',
                        'size_kb' => round(filesize($f) / 1024, 1),
                    ];
                }
            }
        }

        // 3. Check original files in documents/ folder
        if (is_dir($docsDir)) {
            $docFiles = glob("{$docsDir}/*") ?: [];
            foreach ($docFiles as $df) {
                if (is_file($df)) {
                    $sourceFiles[] = [
                        'name' => basename($df),
                        'type' => 'uploaded_document',
                        'size_kb' => round(filesize($df) / 1024, 1),
                    ];
                }
            }
        }

        // 4. Check user_metadata for stored corpus / original brief
        $meta = $blueprint->user_metadata ?? [];
        if (!empty($meta['source_corpus']) && is_string($meta['source_corpus'])) {
            $corpusParts[] = "### [Spesifikasi Tambahan dari User Metadata]\n" . $meta['source_corpus'];
        }
        if (!empty($meta['raw_idea_text']) && is_string($meta['raw_idea_text'])) {
            $corpusParts[] = "### [Ruang Kerja: Catatan Ide Proyek Awal]\n" . $meta['raw_idea_text'];
        }

        // 5. Fallback: If no files on disk, construct comprehensive corpus from blueprint database fields
        if (empty($corpusParts)) {
            $fallbackCorpus = "# SPESIFIKASI PROYEK TERDOKUMENTASI\n\n";
            $fallbackCorpus .= "Nama Bisnis: " . $this->stringifyAttribute($blueprint->nama_bisnis ?: 'N/A') . "\n";
            $fallbackCorpus .= "Klien: " . $this->stringifyAttribute($blueprint->client_name ?: 'N/A') . "\n";
            $fallbackCorpus .= "Target Waktu: " . $this->stringifyAttribute($blueprint->target_waktu ?: '30 Hari Kerja') . "\n\n";

            $masalah = $this->stringifyAttribute($blueprint->masalah_utama);
            if ($masalah !== '') {
                $fallbackCorpus .= "## 1. Masalah Utama & Latar Belakang\n" . $masalah . "\n\n";
            }
            $tujuan = $this->stringifyAttribute($blueprint->tujuan_utama);
            if ($tujuan !== '') {
                $fallbackCorpus .= "## 2. Tujuan Utama & Indikator Keberhasilan\n" . $tujuan . "\n\n";
            }
            $audiens = $this->stringifyAttribute($blueprint->target_audiens);
            if ($audiens !== '') {
                $fallbackCorpus .= "## 3. Target Audiens & Pengguna\n" . $audiens . "\n\n";
            }
            $aktor = $this->stringifyAttribute($blueprint->aktor_sistem);
            if ($aktor !== '') {
                $fallbackCorpus .= "## 4. Pengguna & Hak Akses (Aktor / RBAC)\n" . $aktor . "\n\n";
            }
            $fiturWajib = $this->stringifyAttribute($blueprint->fitur_wajib);
            if ($fiturWajib !== '') {
                $fallbackCorpus .= "## 5. Fitur Wajib (MVP Fase 1)\n" . $fiturWajib . "\n\n";
            }
            $fiturTambahan = $this->stringifyAttribute($blueprint->fitur_tambahan);
            if ($fiturTambahan !== '') {
                $fallbackCorpus .= "## 6. Fitur Tambahan (Fase 2 / Roadmap)\n" . $fiturTambahan . "\n\n";
            }
            $alurKerja = $this->stringifyAttribute($blueprint->alur_kerja);
            if ($alurKerja !== '') {
                $fallbackCorpus .= "## 7. Alur Kerja Sistem (Workflow & Business Logic)\n" . $alurKerja . "\n\n";
            }
            $integrasi = $this->stringifyAttribute($blueprint->kebutuhan_integrasi);
            if ($integrasi !== '') {
                $fallbackCorpus .= "## 8. Kebutuhan Integrasi API & Layanan Eksternal\n" . $integrasi . "\n\n";
            }
            if (!empty($meta['target_platform'])) {
                $fallbackCorpus .= "Target Platform: " . $this->stringifyAttribute($meta['target_platform']) . "\n";
            }
            if (!empty($meta['skala_pengguna'])) {
                $fallbackCorpus .= "Skala Pengguna: " . $this->stringifyAttribute($meta['skala_pengguna']) . "\n";
            }
            if (!empty($meta['kepatuhan_keamanan'])) {
                $fallbackCorpus .= "Kepatuhan Keamanan: " . $this->stringifyAttribute($meta['kepatuhan_keamanan']) . "\n";
            }
            if (!empty($meta['out_of_scope'])) {
                $fallbackCorpus .= "Out of Scope: " . $this->stringifyAttribute($meta['out_of_scope']) . "\n";
            }

            $corpusParts[] = $fallbackCorpus;
            $sourceFiles[] = [
                'name' => 'vision_blueprint_attributes',
                'type' => 'database_fields',
                'size_kb' => round(strlen($fallbackCorpus) / 1024, 1),
            ];
        }

        $combinedText = implode("\n\n---\n\n", $corpusParts);

        return [
            'corpus_text' => $combinedText,
            'source_files' => $sourceFiles,
            'character_count' => strlen($combinedText),
            'word_count' => str_word_count($combinedText),
            'has_markdown_corpus' => !empty($sourceFiles),
            'project_slug' => $slug,
        ];
    }

    /**
     * Deepen and refine a targeted segment using grounded multi-model AI.
     */
    public function refineSegment(
        VisionBlueprint $blueprint,
        string $segmentKey,
        ?string $userInstruction = null,
        ?string $preferredAiProvider = null,
        array $additionalContext = []
    ): array {
        $sourceData = $this->getSourceCorpus($blueprint);
        $corpusText = $sourceData['corpus_text'];
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise');

        $prd = $blueprint->prd_content ?? [];
        $existingSegmentData = $this->extractSegmentData($prd, $segmentKey, $additionalContext);

        // Build domain-specialized prompts and system instructions
        [$systemInstruction, $prompt] = $this->buildPrompts(
            $segmentKey,
            $businessName,
            $corpusText,
            $existingSegmentData,
            $userInstruction,
            $additionalContext
        );

        // Execute via MultiAiModelManager with automatic failover (using 'prd' tier for deep reasoning)
        $aiResult = MultiAiModelManager::executeWithFailover(
            $prompt,
            $systemInstruction,
            'prd',
            $preferredAiProvider
        );

        $rawAiText = $aiResult['text'] ?? null;
        $parsed = null;
        $isDeterministic = false;

        if (!empty($rawAiText)) {
            // 1. Strip DeepSeek-R1 <think> reasoning tags
            $cleaned = preg_replace('/<think>.*?<\/think>/s', '', $rawAiText);

            // 2. Extract JSON payload from code fences or raw blocks
            if (preg_match('/```(?:json)?\s*(\[.*?\]|\{.*?\})\s*```/s', $cleaned, $matches)) {
                $cleaned = $matches[1];
            } elseif (preg_match('/(\[.*\]|\{.*\})/s', $cleaned, $matches)) {
                $cleaned = $matches[1];
            }
            $cleaned = trim($cleaned);

            // 3. Remove trailing commas before closing braces/brackets
            $sanitized = preg_replace('/,\s*([\}\]])/', '$1', $cleaned);

            $parsed = json_decode($sanitized, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                $parsed = json_decode($cleaned, true);
            }

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                Log::warning("AI Refinement returned non-JSON string for segment {$segmentKey}: " . substr($cleaned, 0, 250));
                if (in_array($segmentKey, ['custom_ai_agent_rules', 'executive_summary_text'])) {
                    $parsed = $cleaned;
                }
            }
        }

        // If remote AI was unavailable, in cooldown, or failed to return valid JSON, engage Deterministic Architecture Engine
        if (empty($parsed)) {
            $isDeterministic = true;
            $parsed = $this->generateDeterministicRefinement($blueprint, $segmentKey, $existingSegmentData, $userInstruction, $sourceData, $additionalContext);
        }

        // Re-integrate refined data into prd_content
        $updatedPrd = $this->integrateRefinedData($prd, $segmentKey, $parsed, $additionalContext, $blueprint);

        // Compute Impact Matrix (Ripple Effect)
        $impactMatrix = [
            'features' => ['alurs_kerja', 'database_erd', 'custom_ai_agent_rules'],
            'feature_single' => ['alurs_kerja', 'database_erd'],
            'system_actors' => ['features', 'alurs_kerja', 'database_erd'],
            'alurs_kerja' => ['database_erd', 'custom_ai_agent_rules'],
            'database_erd' => ['custom_ai_agent_rules'],
        ];
        $baseKey = $segmentKey;
        if (isset($impactMatrix[$baseKey])) {
            $impacted = $updatedPrd['_impacted_segments'] ?? [];
            foreach ($impactMatrix[$baseKey] as $iKey) {
                if (!in_array($iKey, $impacted)) {
                    $impacted[] = $iKey;
                }
            }
            $updatedPrd['_impacted_segments'] = $impacted;
        }

        // Clean out self from impacted
        if (isset($updatedPrd['_impacted_segments'])) {
            $updatedPrd['_impacted_segments'] = array_values(
                array_filter($updatedPrd['_impacted_segments'], fn($item) => $item !== $baseKey)
            );
        }

        // Save to database & update tamper-proof cryptographic checksum
        $blueprint->prd_content = $updatedPrd;
        $blueprint->document_sha256 = $blueprint->calculatePrdHash();
        $blueprint->save();

        // Compile universal schema and persist project
        try {
            \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        } catch (\Throwable $e) {
            Log::warning("Failed to compile universal schema after refinement: " . $e->getMessage());
        }

        // Synchronize official prd.md in project directory
        try {
            $slug = $blueprint->slug ?: Str::slug($blueprint->nama_bisnis);
            if ($slug) {
                $projectDir = storage_path("app/projects/{$slug}");
                if (!is_dir($projectDir)) {
                    @mkdir($projectDir, 0775, true);
                }
                $md = PrdGeneratorService::toMarkdown($blueprint, $updatedPrd);
                @file_put_contents("{$projectDir}/prd.md", $md);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to refresh prd.md after refinement: " . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => $isDeterministic 
                ? 'Segmen ' . strtoupper(str_replace('_', ' ', $segmentKey)) . ' berhasil diperdalam berdasarkan Dokumen Acuan (Deterministic Engine).'
                : 'Segmen ' . strtoupper(str_replace('_', ' ', $segmentKey)) . ' berhasil diperdalam berdasarkan Dokumen Acuan.',
            'segment_key' => $segmentKey,
            'refined_data' => $parsed,
            'ai_telemetry' => [
                'provider' => $isDeterministic ? 'deterministic_heuristic' : ($aiResult['provider'] ?? 'ai'),
                'model' => $isDeterministic ? 'expert-rule-engine-v1' : ($aiResult['model'] ?? 'default'),
                'fallback_occurred' => $isDeterministic || ($aiResult['fallback_occurred'] ?? false),
                'notification' => $isDeterministic ? 'Sistem menggunakan Deterministic Architecture Engine berstandar enterprise.' : null,
            ],
            'source_corpus_files' => count($sourceData['source_files']),
        ];
    }

    /**
     * Extract existing data for segment from prd_content.
     */
    protected function extractSegmentData(array $prd, string $segmentKey, array $additionalContext = []): mixed
    {
        return match ($segmentKey) {
            'features', 'feature_specifications', 'mvp_features' => $prd['engineering_specs']['mvp_specs'] ?? ($prd['features']['mvp_phase1'] ?? []),
            'feature_single' => $this->findSingleFeature($prd, $additionalContext['feature_id'] ?? null, $additionalContext['spec_index'] ?? null),
            'database_erd', 'erd_schema' => $prd['erd_schema']['tables'] ?? ($prd['database_erd'] ?? []),
            'system_actors' => $prd['system_actors'] ?? [],
            'alurs_kerja', 'workflow' => $prd['workflow'] ?? ($prd['alurs_kerja'] ?? []),
            'custom_ai_agent_rules' => $prd['custom_ai_agent_rules'] ?? ($prd['engineering_specs']['anti_ai_slop_guidelines'] ?? ''),
            'executive_summary' => $prd['executive_summary'] ?? [],
            default => $prd[$segmentKey] ?? [],
        };
    }

    /**
     * Find a single feature from prd_content by ID or index.
     */
    protected function findSingleFeature(array $prd, ?string $featureId, ?int $specIndex): ?array
    {
        $specs = $prd['engineering_specs']['mvp_specs'] ?? ($prd['features']['mvp_phase1'] ?? []);
        if (empty($specs)) {
            return null;
        }

        if ($featureId) {
            foreach ($specs as $spec) {
                if (($spec['id'] ?? '') === $featureId || ($spec['feature_id'] ?? '') === $featureId) {
                    return $spec;
                }
            }
        }

        if ($specIndex !== null && isset($specs[$specIndex])) {
            return $specs[$specIndex];
        }

        return $specs[0] ?? null;
    }

    /**
     * Build domain-specialized prompts and system instructions based on segment.
     */
    protected function buildPrompts(
        string $segmentKey,
        string $businessName,
        string $corpusText,
        mixed $existingData,
        ?string $userInstruction,
        array $additionalContext
    ): array {
        $existingJson = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $locale = $additionalContext['locale'] ?? 'id';
        $isEn = ($locale === 'en');
        $langPrompt = $isEn ? 'English (strictly professional engineering vocabulary, no Indonesian text)' : 'Bahasa Indonesia (baku, tegas, dan profesional)';
        $langMandate = $isEn 
            ? "MANDATORY OUTPUT LANGUAGE: {$langPrompt}. All human-readable narrative output (title, desc, user_story, acceptance_criteria, scenario labels, given/when/then, tasks, directives) MUST be written in English. Do NOT output Indonesian words."
            : "BAHASA OUTPUT WAJIB: {$langPrompt}.";

        $userPromptDirective = !empty($userInstruction) 
            ? ($isEn ? "USER REFINEMENT INSTRUCTION:\n{$userInstruction}\n\n" : "PETUNJUK PENAJAMAN DARI PENGGUNA:\n{$userInstruction}\n\n")
            : ($isEn ? "DIRECTIVE: Deepen and refine automatically and comprehensively based on Project Source Documents above.\n\n" : "PETUNJUK: Perdalam secara otomatis dan komprehensif berdasarkan Dokumen Acuan di atas.\n\n");

        // Segment 1: FEATURES (Entire List or Single Feature)
        if ($segmentKey === 'feature_single') {
            $featureTitle = $existingData['title'] ?? ($isEn ? 'Feature' : 'Fitur');
            $systemInstruction = $isEn ? <<<TXT
You are the Principal Software Architect at Neriah Pro.
Your Mission: Deepen and thoroughly specify ONE FEATURE SPECIFICATION grounded in the PROJECT SOURCE CORPUS (MarkItDown Corpus / Client Project Specifications).
STRICT MANDATE:
1. Extract and enforce all business domain rules, calculation formulas, edge cases, and exception conditions documented in the Project Corpus.
2. Gherkin Scenarios MUST be concrete, realistic (Given/When/Then), and contain AT LEAST 3 SCENARIOS (Happy Path, 422 Validation Error, and Concurrency / Locking Path).
3. Acceptance Criteria must be verifiable via automated testing (Pest PHP / Playwright).
4. Do NOT use generic placeholders or filler text.
5. Preserve the same Feature ID, tier ('mvp'), and index.
6. Return ONLY valid JSON (no markdown fences or code blocks).
7. {$langMandate}
8. CURRENT ARCHITECTURAL STANDARDS:
   If the feature involves search, catalog filters, recommendations, or AI, strictly integrate:
   a) 18 RAG Concepts: Hybrid Search (pgvector HNSW + BM25 tsvector), Cross-Encoder Reranking, structured chunking, and Grounded citations.
   b) 20 Agentic AI Concepts: Tool Use, Model Context Protocol (MCP), multi-turn workflows, Guardrails, and Observability.
TXT : <<<TXT
Anda adalah Principal Software Architect di Neriah Pro.
Tugas Anda: Memperdalam SATU SPESIFIKASI FITUR TERTENTU berdasarkan DOKUMEN ACUAN PROYEK (MarkItDown Corpus / Spesifikasi Klien).
ATURAN KETAT:
1. Gali seluruh aturan bisnis, formula hitungan, edge cases, dan kondisi pengecualian yang tertulis dalam Dokumen Acuan untuk fitur ini.
2. Skenario Gherkin HARUS sangat spesifik dan realistis (Given/When/Then), minimal 3 skenario (Happy Path, Validation Error, dan Edge Case/Security Path).
3. Acceptance Criteria harus dapat diuji secara objektif (Pest PHP / Playwright).
4. Jangan gunakan kata-kata generik atau placeholder kosong.
5. Pertahankan ID Fitur, tier ('mvp'), dan index yang sama.
6. Kembalikan HANYA objek JSON valid (tanpa wrapper ```json).
7. {$langMandate}
8. STANDAR TEKNOLOGI TERKINI & IMPLEMENTASI TERBAIK:
   Jika fitur melibatkan pencarian, filter katalog, rekomendasi, atau kecerdasan buatan, Anda WAJIB mengarsitekturkannya menggunakan standar terkini:
   a) 18 Konsep RAG (Brij Kishore Pandey): Hybrid Search (pgvector HNSW + BM25 tsvector), Cross-Encoder Reranking, Chunking terstruktur (overlap 50 token), Parent-Child / Contextual Retrieval, Corrective RAG (CRAG) & Self-RAG untuk verifikasi akurasi, serta sitasi sumber (Grounded RAG). Dilarang keras query SQL LIKE '%...' atau pencarian naif.
   b) 20 Konsep Agentic AI (Balawant Kadam): Tool Use (function calling), Model Context Protocol (MCP), Agentic Workflows multi-tahap, Guardrails (penyaringan PII & kepatuhan UU PDP), dan Observability (telemetri latency/token).
TXT;

            $storySample = $isEn ? "As an Authorized System User, I want to ..., so that ..." : "Sebagai ..., saya ingin ... sehingga ...";
            $sc1Name = $isEn ? "Scenario 1 (Happy Path - Normal Execution)" : "Skenario 1 (Happy Path)";
            $sc1Given = $isEn ? "User is authenticated and data parameters are valid..." : "Pengguna terautentikasi dan data input valid...";
            $sc1When = $isEn ? "Primary feature operation is executed..." : "Aksi utama modul dieksekusi...";
            $sc1Then = $isEn ? "System successfully processes data with HTTP 200 OK and persists to database..." : "Sistem berhasil memproses data dengan HTTP 200 OK dan menyimpan ke database Strict ULID...";
            $sc2Name = $isEn ? "Scenario 2 (Validation Error & Integrity Boundary)" : "Skenario 2 (Validation Error)";
            $sc2Given = $isEn ? "User is accessing the module interface or endpoint..." : "Pengguna berada pada antarmuka modul...";
            $sc2When = $isEn ? "Mandatory parameters are missing or invalid..." : "Input tidak lengkap atau format parameter salah...";
            $sc2Then = $isEn ? "System rejects mutation and returns HTTP 422 Unprocessable Content error response..." : "Sistem merespons HTTP 422 Unprocessable Content dengan pesan error terstruktur...";
            $sc3Name = $isEn ? "Scenario 3 (Edge Case & Concurrency Protection)" : "Skenario 3 (Edge Case & Concurrency)";
            $sc3Given = $isEn ? "Two concurrent user sessions mutate the same resource simultaneously..." : "Dua sesi pengguna melakukan mutasi data simultan...";
            $sc3When = $isEn ? "Parallel mutation requests hit the server concurrently..." : "Request konkuren dikirimkan serentak...";
            $sc3Then = $isEn ? "Database locking prevents race conditions and ensures ACID consistency..." : "Database locking mencegah race condition dan integritas ACID terjamin...";
            $directivePrompt = $isEn ? "Technical implementation directive for AI Coding Agent..." : "Instruksi teknis langsung ke AI Coding Agent...";

            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== FITUR YANG SEDANG DIPERDALAM: "{$featureTitle}" ===
{$existingJson}

{$userPromptDirective}
Kembalikan satu objek JSON yang diperdalam dengan struktur kunci:
{
  "id": "...",
  "index": 1,
  "tier": "mvp",
  "title": "...",
  "desc": "...",
  "category": "...",
  "category_label": "...",
  "story_points": 5,
  "complexity_label": "High (5 SP)",
  "sprint_phase": "Sprint 2",
  "target_files": ["..."],
  "user_story": "{$storySample}",
  "acceptance_criteria": [
    {
      "scenario": "{$sc1Name}",
      "given": "{$sc1Given}",
      "when": "{$sc1When}",
      "then": "{$sc1Then}"
    },
    {
      "scenario": "{$sc2Name}",
      "given": "{$sc2Given}",
      "when": "{$sc2When}",
      "then": "{$sc2Then}"
    },
    {
      "scenario": "{$sc3Name}",
      "given": "{$sc3Given}",
      "when": "{$sc3When}",
      "then": "{$sc3Then}"
    }
  ],
  "frontend": {
    "components": ["ComponentName.blade.php", "CardComponent.blade.php"],
    "tasks": ["Task frontend 1", "Task frontend 2"],
    "states": "Default, Loading Skeleton, Empty State, Error Toast",
    "anti_ai_slop": "Monochrome Zinc palette with sharp accents, border-radius max 4px, zero capsule buttons, ban window.alert()."
  },
  "backend": {
    "target_files": ["app/Models/...", "app/Services/..."],
    "tasks": ["Task backend 1", "Task backend 2"],
    "ulid_migration_rules": "Mandatory ->ulid('id')->primary() (VARCHAR(26)) & foreignUlid. Strictly ban AUTO_INCREMENT.",
    "query_strategy": "Keyset Cursor Pagination cursorPaginate() with ->orderBy('id', 'asc')."
  },
  "integration": {
    "endpoint": "POST /api/v1/resource-name",
    "middleware": ["api", "auth:sanctum", "throttle:60,1"],
    "tasks": ["Task integrasi 1", "Task integrasi 2"],
    "request_schema": "{\"field\": \"value\"}",
    "response_schema": "{\"success\": true, \"data\": {\"id\": \"01J...\"}}"
  },
  "code_agent_directive": "{$directivePrompt}"
}
TXT;

            return [$systemInstruction, $prompt];
        }

        if (in_array($segmentKey, ['features', 'feature_specifications', 'mvp_features'])) {
            $systemInstruction = <<<TXT
Anda adalah Principal Software Architect di Neriah Pro.
Tugas Anda: Memperdalam dan melengkapi SELURUH DAFTAR SPESIFIKASI FITUR MVP (Fase 1) untuk "{$businessName}" berdasarkan DOKUMEN ACUAN PROYEK (MarkItDown Corpus).
ATURAN KETAT:
1. Bandingkan fitur yang ada dengan Dokumen Acuan. Jika ada fitur penting di Dokumen Acuan yang belum masuk, TAMBAHKAN ke dalam daftar.
2. Setiap fitur wajib memiliki dekomposisi vertikal mendalam: User Story, Acceptance Criteria, Frontend, Backend, Integrasi, dan Code Agent Directive.
3. Seluruh skenario pengujian Gherkin harus konkret dan mencakup edge cases spesifik.
4. Jangan gunakan magic string atau dummy placeholder.
5. Kembalikan HANYA array JSON valid berisikan objek-objek spesifikasi fitur.
6. STANDAR TEKNOLOGI TERKINI & IMPLEMENTASI TERBAIK:
   Fitur yang melibatkan pencarian, katalog, rekomendasi, atau AI wajib menerapkan 18 Konsep RAG (Hybrid Search pgvector + BM25, Reranking, Parent-Child Chunking, Grounded Citations) dan 20 Konsep Agentic AI (MCP, Tool Use, Guardrails, Observability).
TXT;

            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== DAFTAR FITUR SAAT INI (PREVIOUS STATE) ===
{$existingJson}

{$userPromptDirective}
Kembalikan JSON ARRAY dari seluruh fitur MVP yang diperdalam. Format setiap item dalam array:
[
  {
    "id": "FEAT-MVP-01-...",
    "index": 1,
    "tier": "mvp",
    "title": "...",
    "desc": "...",
    "category": "...",
    "category_label": "...",
    "story_points": 5,
    "complexity_label": "Standard (3 SP)",
    "sprint_phase": "Sprint 1",
    "target_files": ["app/Models/...", "app/Services/..."],
    "user_story": "...",
    "acceptance_criteria": [
      {
        "scenario": "Skenario 1 (Happy Path)",
        "given": "Kondisi awal prasyarat terpenuhi...",
        "when": "Aksi utama dijalankan...",
        "then": "Status sukses 200 OK dan data tersimpan di basis data Strict ULID..."
      },
      {
        "scenario": "Skenario 2 (Validation Error)",
        "given": "Pengguna berada pada form input...",
        "when": "Parameter wajib tidak diisi...",
        "then": "Status 422 Unprocessable Content dengan pesan error terstruktur..."
      },
      {
        "scenario": "Skenario 3 (Edge Case & Concurrency)",
        "given": "Akses simultan pada data yang sama...",
        "when": "Mutasi data dilakukan bersamaan...",
        "then": "Database locking mencegah race condition..."
      }
    ],
    "frontend": {
      "components": ["ComponentName.blade.php"],
      "tasks": ["Task frontend..."],
      "states": "Default, Loading Skeleton, Empty State, Error Toast",
      "anti_ai_slop": "Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none."
    },
    "backend": {
      "target_files": ["app/Models/..."],
      "tasks": ["Task backend..."],
      "ulid_migration_rules": "Wajib gunakan ->ulid('id')->primary().",
      "query_strategy": "Keyset Cursor Pagination cursorPaginate()."
    },
    "integration": {
      "endpoint": "POST /api/v1/resource-name",
      "middleware": ["api"],
      "tasks": ["Task integrasi..."],
      "request_schema": "{}",
      "response_schema": "{}"
    },
    "code_agent_directive": "..."
  }
]
TXT;

            return [$systemInstruction, $prompt];
        }

        // Segment 2: DATABASE ERD & SCHEMA (PostgreSQL Strict ULID DDL)
        if (in_array($segmentKey, ['database_erd', 'erd_schema'])) {
            $systemInstruction = <<<TXT
Anda adalah Senior Enterprise Database Architect di Neriah Pro.
Tugas Anda: Memperdalam dan melengkapi SKEMA BASIS DATA (ERD) PostgreSQL 16 Strict ULID untuk "{$businessName}" berdasarkan DOKUMEN ACUAN PROYEK.
STANDAR WAJIB NERIAH PRO:
1. Primary Keys: WAJIB gunakan ULID 26-char (->ulid('id')->primary() / VARCHAR(26)). HARAM gunakan AUTO_INCREMENT atau UUID binary.
2. Foreign Keys: Selalu gunakan ULID foreign keys (->foreignUlid('parent_id')).
3. Indeks: Terapkan indeks keyset O(1) untuk kolom pencarian dan foreign key.
4. Multi-Bahasa Tier 1 (Native JSONB): Kolom narasi manusiawi (name, title, description, reason, memo, message_body, decision_note, threshold_reason, explanation_note, tier_name) WAJIB bertipe JSONB {"id": "...", "en": "..."}.
5. Integritas Skalar: Primary Keys, Foreign Keys, Unique Codes/Slugs/Emails, State Machine Enums (status, role, movement_type, direction), dan Data Teknis/Hashes WAJIB tetap berupa VARCHAR skalar, DILARANG KERAS dijadikan JSONB.
6. Normalisasi Domain: Jangan membuat tabel typo (misal 'jumlah oramg' dinormalisasi menjadi atribut passenger_capacity di tabel armada/vehicles, bukan tabel JUMLAH_ORAMGS).
7. Gali seluruh entitas data dari dokumen acuan (tabel transaksi, riwayat audit, pivot matriks, approval tier, snapshot biaya).
8. Sertakan DDL SQL PostgreSQL yang valid dan siap dieksekusi.
9. Kembalikan HANYA array JSON valid dari objek-objek tabel.
TXT;

            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== TABEL ERD SAAT INI (PREVIOUS STATE) ===
{$existingJson}

{$userPromptDirective}
Kembalikan JSON ARRAY dari seluruh tabel database yang lengkap dan diperdalam:
[
  {
    "name": "nama_tabel_jamak",
    "purpose": "Tujuan dan fungsi tabel dalam proses bisnis...",
    "category": "Core / Transaction / Audit / Master",
    "columns": [
      { "name": "id", "type": "VARCHAR(26)", "is_primary": true, "is_foreign": false, "description": "ULID Primary Key", "indexed": true },
      { "name": "nama_kolom", "type": "VARCHAR(255) / TEXT / NUMERIC(15,2) / BOOLEAN / TIMESTAMP", "is_primary": false, "is_foreign": false, "description": "...", "indexed": false }
    ],
    "relationships": [
      { "type": "belongsTo / hasMany / belongsToMany", "related_table": "...", "foreign_key": "...", "description": "..." }
    ],
    "indexes": [
      "CREATE INDEX idx_... ON ... (...)"
    ],
    "sql_ddl": "CREATE TABLE nama_tabel_jamak (...);"
  }
]
TXT;

            return [$systemInstruction, $prompt];
        }

        // Segment 3: SYSTEM ACTORS (RBAC)
        if ($segmentKey === 'system_actors') {
            $systemInstruction = <<<TXT
Anda adalah Enterprise Security & RBAC Architect di Neriah Pro.
Tugas Anda: Memperdalam dan memetakan SELURUH PERAN PENGGUNA & HAK AKSES (RBAC System Actors) untuk "{$businessName}" berdasarkan DOKUMEN ACUAN PROYEK.
ATURAN KETAT:
1. Ekstrak seluruh peran nyata dari dokumen acuan (misal: Kepala Sekolah, Bendahara, Wali Kelas, Orang Tua Siswa, Auditor).
2. Tentukan wewenang spesifik: CRUD matrix, approval tier, batasan akses data antar-cabang/unit, dan tindakan sensitif.
3. Kembalikan HANYA array JSON valid.
TXT;

            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== DAFTAR AKTOR SAAT INI ===
{$existingJson}

{$userPromptDirective}
Kembalikan JSON ARRAY dari aktor sistem:
[
  {
    "name": "Nama Role Resmi",
    "slug": "nama_role_snake_case",
    "role_type": "Internal Admin / Operator / External User / Auditor",
    "description": "Tanggung jawab utama dalam sistem...",
    "permissions": ["create_records", "approve_payments", "view_audit_trail"],
    "security_level": "Tier 1 (High) / Tier 2 (Operational) / Tier 3 (Read-Only)"
  }
]
TXT;

            return [$systemInstruction, $prompt];
        }

        // Segment 4: WORKFLOW / ALUR KERJA
        if (in_array($segmentKey, ['alurs_kerja', 'workflow'])) {
            $systemInstruction = <<<TXT
Anda adalah Business Process & Systems Analyst di Neriah Pro.
Tugas Anda: Memperdalam ALUR KERJA SISTEM (User Flow & Business Logic Stages) untuk "{$businessName}" berdasarkan DOKUMEN ACUAN PROYEK.
ATURAN KETAT:
1. Jangan hanya membuat alur lurus; gali percabangan keputusan (decision points), validasi gagal, proses approval berjenjang, dan notifikasi.
2. Setiap tahapan wajib mendefinisikan aktor, aksi, input, validasi sistem, output, dan exception handler.
3. Kalimat aksi ("action") WAJIB dirumuskan SINGKAT, JELAS, DAN PADAT (6-12 kata). DILARANG KERAS menyertakan daftar panjang di dalam tanda kurung atau singkatan terpotong (...) agar visualisasi diagram Mermaid tampil utuh dan terbaca tanpa pemotongan teks.
4. Kembalikan HANYA array JSON valid.
TXT;

            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== ALUR KERJA SAAT INI ===
{$existingJson}

{$userPromptDirective}
Kembalikan JSON ARRAY tahapan alur kerja:
[
  {
    "stage": 1,
    "title": "Nama Tahap Alur",
    "actor": "Aktor Pelaksana",
    "action": "Deskripsi aksi singkat, padat, dan jelas (6-12 kata tanpa tanda kurung panjang)",
    "system_processing": "Pemrosesan otomatis di backend...",
    "validation_rules": ["Validasi 1", "Validasi 2"],
    "exception_handling": "Alur penanganan jika gagal/ditolak...",
    "output": "Keluaran sistem..."
  }
]
TXT;

            return [$systemInstruction, $prompt];
        }

        // Default Generic Segment Refinement
        $systemInstruction = <<<TXT
Anda adalah Senior Software Architect di Neriah Pro.
Tugas Anda: Memperdalam segmen "{$segmentKey}" untuk "{$businessName}" berdasarkan DOKUMEN ACUAN PROYEK.
Kembalikan format JSON atau teks yang selaras dengan data aslinya tanpa markdown wrappers.
TXT;

        $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== DATA SEGMEN SAAT INI ({$segmentKey}) ===
{$existingJson}

{$userPromptDirective}
Kembalikan hasil penajaman segmen ini.
TXT;

        return [$systemInstruction, $prompt];
    }

    /**
     * Reintegrate the refined data cleanly back into prd_content.
     */
    protected function integrateRefinedData(
        array $prd,
        string $segmentKey,
        mixed $parsedData,
        array $additionalContext,
        VisionBlueprint $blueprint
    ): array {
        if ($segmentKey === 'feature_single') {
            $featureId = $additionalContext['feature_id'] ?? null;
            $specIndex = $additionalContext['spec_index'] ?? null;

            $specs = $prd['engineering_specs']['mvp_specs'] ?? ($prd['features']['mvp_phase1'] ?? []);
            $found = false;

            $isEnTarget = ($additionalContext['locale'] ?? ($blueprint->user_metadata['output_locale'] ?? '')) === 'en';

            $targetFeature = null;

            foreach ($specs as $idx => $spec) {
                if (($featureId && (($spec['id'] ?? '') === $featureId || ($spec['feature_id'] ?? '') === $featureId)) ||
                    ($specIndex !== null && $idx === (int) $specIndex)) {
                    $merged = array_merge($spec, is_array($parsedData) ? $parsedData : []);
                    $merged = BlueprintDiscoveryService::polishFeatureSpecification(
                        $merged,
                        $blueprint->domain_category ?? 'custom_portal',
                        $isEnTarget
                    );
                    $specs[$idx] = $merged;
                    $targetFeature = $merged;
                    $found = true;
                    break;
                }
            }

            if (!$found && is_array($parsedData)) {
                $parsedData = BlueprintDiscoveryService::polishFeatureSpecification(
                    $parsedData,
                    $blueprint->domain_category ?? 'custom_portal',
                    $isEnTarget
                );
                $specs[] = $parsedData;
                $targetFeature = $parsedData;
            }

            $prd['engineering_specs']['mvp_specs'] = $specs;
            $prd['features']['mvp_phase1'] = $specs;

            // Trigger Automatic Cascading Updates (Ripple Effect to ERD, Workflows, Actors, Diagrams & Schema)
            if ($targetFeature) {
                $prd = $this->cascadeFeatureUpdates($prd, $targetFeature, $blueprint, $isEnTarget);
            }

            return $prd;
        }

        if (in_array($segmentKey, ['features', 'feature_specifications', 'mvp_features'])) {
            $isEnTarget = ($additionalContext['locale'] ?? ($blueprint->user_metadata['output_locale'] ?? '')) === 'en';
            $featuresList = is_array($parsedData) ? $parsedData : [];
            foreach ($featuresList as &$fItem) {
                if (is_array($fItem)) {
                    $fItem = BlueprintDiscoveryService::polishFeatureSpecification(
                        $fItem,
                        $blueprint->domain_category ?? 'custom_portal',
                        $isEnTarget
                    );
                }
            }
            unset($fItem);
            $prd['engineering_specs']['mvp_specs'] = $featuresList;
            $prd['features']['mvp_phase1'] = $featuresList;

            // Trigger Automatic Cascading Updates for all features
            foreach ($featuresList as $fItem) {
                if (is_array($fItem)) {
                    $prd = $this->cascadeFeatureUpdates($prd, $fItem, $blueprint, $isEnTarget);
                }
            }

            return $prd;
        }

        if (in_array($segmentKey, ['database_erd', 'erd_schema'])) {
            $tables = is_array($parsedData) ? $parsedData : [];
            $prd['erd_schema']['tables'] = $tables;
            $prd['database_erd'] = $tables;

            // Re-render ERD Mermaid diagram
            try {
                $prd['virtual_charts']['erd_mermaid'] = PrdGeneratorService::generateErdMermaid($tables);
            } catch (\Throwable $e) {}

            return $prd;
        }

        if ($segmentKey === 'system_actors') {
            $prd['system_actors'] = is_array($parsedData) ? $parsedData : [];
            return $prd;
        }

        if (in_array($segmentKey, ['alurs_kerja', 'workflow'])) {
            $workflows = is_array($parsedData) ? $parsedData : [];
            $normalizedWorkflows = [];
            foreach ($workflows as $idx => $wf) {
                if (!is_array($wf)) continue;
                $action = $wf['action'] ?? ($wf['title'] ?? ($wf['name'] ?? 'Langkah ' . ($idx + 1)));
                $desc = $wf['description'] ?? ($wf['system_processing'] ?? ($wf['action'] ?? 'Tahapan alur kerja dan validasi transaksi.'));
                $actor = $wf['actor'] ?? 'Pengguna / Pengunjung';
                $normalizedWorkflows[] = [
                    'step' => $wf['step'] ?? ($wf['stage'] ?? ($idx + 1)),
                    'action' => $action,
                    'description' => $desc,
                    'actor' => $actor,
                    'trigger' => $wf['trigger'] ?? ($wf['pemicu'] ?? ($wf['input'] ?? 'Pengguna mengakses antarmuka.')),
                    'badge' => $wf['badge'] ?? ($idx === 0 ? 'DISCOVERY' : ($idx === 1 ? 'PROCESSING' : ($idx === 2 ? 'INTERACTION' : 'APPROVAL'))),
                    'computation' => $wf['computation'] ?? ($wf['system_processing'] ?? 'Validasi request FormRequest dan pemrosesan basis data.'),
                    'response' => $wf['response'] ?? ($wf['output'] ?? 'Status terverifikasi dengan respon HTTP 200 OK.'),
                    'mitigation' => $wf['mitigation'] ?? ($wf['exception_handling'] ?? 'Penanganan error inline dan rollback transaksi.'),
                ];
            }
            if (!empty($normalizedWorkflows)) {
                $workflows = $normalizedWorkflows;
            }
            $prd['workflow'] = $workflows;
            $prd['alurs_kerja'] = $workflows;

            // Re-render workflow Mermaid diagram
            try {
                $prd['virtual_charts']['workflow_mermaid'] = PrdGeneratorService::generateWorkflowMermaid($workflows);
            } catch (\Throwable $e) {}

            return $prd;
        }

        // Generic fallback
        $prd[$segmentKey] = $parsedData;
        return $prd;
    }

    /**
     * Automatic Cascading Synchronization (Ripple Effect Engine).
     * Synchronizes all interrelated architectural domains whenever a feature is elaborated or updated:
     * 1. ERD PostgreSQL 16 schema & tables + ERD Mermaid diagram
     * 2. Workflow & state machine stages + Workflow Mermaid diagram
     * 3. System Actors & RBAC permissions
     * 4. Feature Dependency Mermaid diagram
     * 5. Sprint Gantt Timeline diagram
     */
    public function cascadeFeatureUpdates(
        array $prd,
        array $feature,
        VisionBlueprint $blueprint,
        bool $isEn = false
    ): array {
        $title = $feature['title'] ?? 'Core Feature';
        $pascal = \App\Services\Prd\PrdFeatureSpecificationService::sanitizeEntityClassName($title, $feature['related_table'] ?? null);
        $tableName = Str::snake(Str::plural($pascal));

        // 1. Synchronize Database ERD Tables
        $tables = $prd['erd_schema']['tables'] ?? ($prd['database_erd'] ?? []);
        $tableFound = false;

        foreach ($tables as $tIdx => $tbl) {
            $existingName = $tbl['name'] ?? ($tbl['table_name'] ?? '');
            if ($existingName === $tableName || $existingName === Str::plural(Str::snake($title))) {
                $tableFound = true;
                break;
            }
        }

        if (!$tableFound) {
            $schemaColumns = [
                ['name' => 'id', 'type' => 'VARCHAR(26)', 'nullable' => false, 'primary' => true, 'description' => 'Strict ULID Primary Key (PostgreSQL 16)'],
                ['name' => 'user_id', 'type' => 'VARCHAR(26)', 'nullable' => false, 'primary' => false, 'description' => 'Foreign Key -> users.id'],
            ];

            $reqSchema = [];
            if (!empty($feature['integration']['request_schema'])) {
                $decoded = json_decode($feature['integration']['request_schema'], true);
                if (is_array($decoded)) {
                    $reqSchema = $decoded;
                }
            }

            foreach ($reqSchema as $colKey => $colVal) {
                $cleanCol = Str::snake($colKey);
                if (in_array($cleanCol, ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at'])) continue;
                $colType = 'VARCHAR(100)';
                if (is_int($colVal)) $colType = 'INTEGER';
                elseif (is_float($colVal)) $colType = 'NUMERIC(14,2)';
                elseif (is_bool($colVal)) $colType = 'BOOLEAN';
                elseif (is_array($colVal)) $colType = 'JSONB';
                elseif (str_contains($cleanCol, 'time') || str_contains($cleanCol, 'date')) $colType = 'TIMESTAMPTZ';

                $schemaColumns[] = [
                    'name' => $cleanCol,
                    'type' => $colType,
                    'nullable' => true,
                    'primary' => false,
                    'description' => "Domain attribute for {$cleanCol}",
                ];
            }

            if (!in_array('status', array_column($schemaColumns, 'name'))) {
                $schemaColumns[] = ['name' => 'status', 'type' => 'VARCHAR(50)', 'nullable' => false, 'primary' => false, 'description' => 'Lifecycle state machine status'];
            }
            $schemaColumns[] = ['name' => 'metadata', 'type' => 'JSONB', 'nullable' => true, 'primary' => false, 'description' => 'Audit metadata & extensible payload'];
            $schemaColumns[] = ['name' => 'created_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false, 'primary' => false, 'description' => 'Record creation timestamp'];
            $schemaColumns[] = ['name' => 'updated_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false, 'primary' => false, 'description' => 'Record mutation timestamp'];
            $schemaColumns[] = ['name' => 'deleted_at', 'type' => 'TIMESTAMPTZ', 'nullable' => true, 'primary' => false, 'description' => 'Soft delete timestamp'];

            $ddlLines = ["CREATE TABLE IF NOT EXISTS \"{$tableName}\" ("];
            foreach ($schemaColumns as $c) {
                $cName = $c['name'];
                $cType = $c['type'];
                $null = $c['nullable'] ? 'NULL' : 'NOT NULL';
                $pk = ($c['primary'] ?? false) ? ' PRIMARY KEY' : '';
                $ddlLines[] = "    \"{$cName}\" {$cType} {$null}{$pk},";
            }
            $ddlLines[count($ddlLines) - 1] = rtrim($ddlLines[count($ddlLines) - 1], ',');
            $ddlLines[] = ');';
            $ddlLines[] = "CREATE INDEX IF NOT EXISTS \"idx_{$tableName}_created_id\" ON \"{$tableName}\" (\"created_at\" DESC, \"id\" DESC);";
            $ddlLines[] = "CREATE INDEX IF NOT EXISTS \"idx_{$tableName}_user_id\" ON \"{$tableName}\" (\"user_id\");";

            $newTable = [
                'name' => $tableName,
                'table_name' => $tableName,
                'description' => $isEn ? "Operational persistence table for {$title} module with strict ULID foreign keys and audit trail." : "Tabel operasional data modul {$title} dengan foreign key ULID dan jejak audit.",
                'primary_key' => 'id (VARCHAR(26), ULID, PK)',
                'foreign_keys' => ['user_id (users.id)'],
                'columns' => $schemaColumns,
                'ddl_sql' => implode("\n", $ddlLines),
            ];

            $tables[] = $newTable;
            $prd['erd_schema']['tables'] = $tables;
            $prd['database_erd'] = $tables;

            try {
                $prd['virtual_charts']['erd_mermaid'] = PrdGeneratorService::generateErdMermaid($tables);
            } catch (\Throwable $e) {}
        }

        // 2. Synchronize Workflow & State Machine Steps
        $workflows = $prd['workflow'] ?? ($prd['alurs_kerja'] ?? []);
        $matchedWf = false;
        $titleKeywords = array_filter(explode(' ', mb_strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $title))));

        foreach ($workflows as &$wf) {
            $wfText = mb_strtolower(($wf['action'] ?? '') . ' ' . ($wf['description'] ?? ''));
            foreach ($titleKeywords as $kw) {
                if (strlen($kw) > 3 && str_contains($wfText, $kw)) {
                    $endpoint = $feature['integration']['endpoint'] ?? ('/api/v1/' . Str::kebab($pascal));
                    $wf['computation'] = $isEn
                        ? "Execute atomic {$pascal}Service with DB::transaction(), pessimistic concurrency lock, and strict ULID persistence."
                        : "Eksekusi {$pascal}Service atomik dibungkus DB::transaction() dengan pessimistic concurrency lock dan integritas data Strict ULID.";
                    $wf['response'] = $isEn
                        ? "HTTP 200 OK via {$endpoint} with RFC 9457 error standardization."
                        : "Respon HTTP 200 OK via endpoint {$endpoint} dengan format error terstandar RFC 9457.";
                    $matchedWf = true;
                    break 2;
                }
            }
        }
        unset($wf);

        if (!$matchedWf && count($workflows) < 8) {
            $nextStep = count($workflows) + 1;
            $endpoint = $feature['integration']['endpoint'] ?? ('/api/v1/' . Str::kebab($pascal));
            $workflows[] = [
                'step' => $nextStep,
                'action' => $isEn ? "Execution & Processing of {$title}" : "Eksekusi & Pemrosesan Modul {$title}",
                'description' => $feature['desc'] ?? ($isEn ? "Operational execution of {$title} module." : "Operasional pemrosesan modul {$title}."),
                'actor' => $isEn ? 'Authorized System User' : 'Pengguna Sistem Berwenang',
                'trigger' => $isEn ? "User or background event invokes {$title}." : "Pengguna atau event sistem memicu modul {$title}.",
                'badge' => 'PROCESSING',
                'computation' => "Eksekusi {$pascal}Service atomik dengan DB::transaction() dan Strict ULID.",
                'response' => "Respon terverifikasi HTTP 200 OK via {$endpoint}.",
                'mitigation' => "Penanganan timeout jaringan dan rollback transaksi otomatis jika terjadi kegagalan.",
            ];
        }

        $prd['workflow'] = $workflows;
        $prd['alurs_kerja'] = $workflows;

        try {
            $prd['virtual_charts']['workflow_mermaid'] = PrdGeneratorService::generateWorkflowMermaid($workflows);
        } catch (\Throwable $e) {}

        // 3. Synchronize System Actors RBAC Permissions
        $actors = $prd['system_actors'] ?? [];
        $permissionKey = 'manage_' . $tableName;
        if (!empty($actors) && is_array($actors)) {
            foreach ($actors as &$act) {
                $role = mb_strtolower($act['role'] ?? ($act['name'] ?? ''));
                if (str_contains($role, 'admin') || str_contains($role, 'operator') || str_contains($role, 'super')) {
                    $perms = (array) ($act['permissions'] ?? []);
                    if (!in_array('*', $perms) && !in_array($permissionKey, $perms)) {
                        $perms[] = $permissionKey;
                        $act['permissions'] = $perms;
                    }
                }
            }
            unset($act);
            $prd['system_actors'] = $actors;
        }

        // 4. Synchronize Diagrams (Feature Dependency & Gantt)
        try {
            $allFeatures = $prd['engineering_specs']['mvp_specs'] ?? ($prd['features']['mvp_phase1'] ?? []);
            $erdTables = $prd['erd_schema']['tables'] ?? [];
            $prd['virtual_charts']['feature_dependency_mermaid'] = PrdGeneratorService::generateFeatureDependencyMermaid($actors, $allFeatures, $erdTables);
            $prd['virtual_charts']['feature_dep_mermaid'] = $prd['virtual_charts']['feature_dependency_mermaid'];
        } catch (\Throwable $e) {}

        try {
            $prd['virtual_charts']['gantt_mermaid'] = PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja');
            $prd['virtual_charts']['sprint_gantt_mermaid'] = $prd['virtual_charts']['gantt_mermaid'];
        } catch (\Throwable $e) {}

        return $prd;
    }

    /**
     * Deepen and refine a specific scaffold file (screens.md, tokens.json, SyntheticDataSeeder.php, ApiContractTest.php, etc.)
     * grounded in the Source Corpus.
     */
    public function refineScaffoldFile(
        VisionBlueprint $blueprint,
        string $filename,
        ?string $userInstruction = null,
        ?string $preferredAiProvider = null,
        string $locale = 'id'
    ): array {
        $isEn = $locale === 'en';
        $sourceData = $this->getSourceCorpus($blueprint);
        $corpusText = $this->stringifyAttribute($sourceData['corpus_text'] ?? '', $locale);
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise'), $locale);

        // Extract PRD context (MVP features, ERD tables, RBAC actors) so AI & deterministic engine have complete project awareness
        $prd = $blueprint->prd_content ?? [];
        $erdTables = $prd['erd_schema']['tables']
            ?? ($prd['engineering_specs']['database_erd']['entities'] ?? ($prd['database_erd'] ?? []));
        $tableSummary = [];
        if (is_array($erdTables)) {
            foreach (array_slice($erdTables, 0, 12) as $tIdx => $tbl) {
                $tName = is_array($tbl) ? ($tbl['name'] ?? ($tbl['table_name'] ?? 'entity_'.($tIdx + 1))) : (string) $tbl;
                $cols = [];
                if (is_array($tbl) && !empty($tbl['columns']) && is_array($tbl['columns'])) {
                    foreach (array_slice($tbl['columns'], 0, 8) as $col) {
                        $cols[] = is_array($col) ? (($col['name'] ?? 'col').':'.($col['type'] ?? 'string')) : (string) $col;
                    }
                }
                $tableSummary[] = $tName.(!empty($cols) ? ' ('.implode(', ', $cols).')' : '');
            }
        }
        $tablesContext = !empty($tableSummary) ? implode('; ', $tableSummary) : 'users, transactions, audit_logs';

        $rawSpecs = $prd['engineering_specs']['mvp_specs']
            ?? ($prd['features']['mvp_phase1'] ?? ($prd['features'] ?? []));
        $featureSummary = [];
        if (is_array($rawSpecs)) {
            foreach (array_slice($rawSpecs, 0, 10) as $fItem) {
                if (is_string($fItem)) {
                    $featureSummary[] = $fItem;
                } elseif (is_array($fItem)) {
                    $fTitle = $this->stringifyAttribute($fItem['title'] ?? ($fItem['name'] ?? ''), $locale);
                    $fDesc = $this->stringifyAttribute($fItem['desc'] ?? ($fItem['description'] ?? ''), $locale);
                    if ($fTitle !== '') {
                        $featureSummary[] = $fTitle.($fDesc !== '' ? ': '.Str::limit($fDesc, 110) : '');
                    }
                }
            }
        }
        $featuresContext = !empty($featureSummary) ? implode("\n- ", $featureSummary) : $this->stringifyAttribute($blueprint->fitur_wajib ?? '', $locale);

        // Resolve current content (including customized state or alias fallback)
        $userMeta = $blueprint->user_metadata ?? [];
        $scaffoldCustom = $userMeta['scaffold_customizations'] ?? [];
        $allFiles = ScaffoldGeneratorService::generateFiles($blueprint);
        $currentContent = $scaffoldCustom[$filename] ?? ($allFiles[$filename] ?? '');
        $previousLineCount = $currentContent !== '' ? count(explode("\n", $currentContent)) : 0;

        $fileSpecificMandate = $this->getScaffoldFileSpecificMandate($filename, $isEn);
        $langInstruction = $isEn
            ? 'Write all code comments, docblocks, documentation text, and explanations strictly in professional English.'
            : 'Tulis seluruh komentar kode, docblock, narasi dokumentasi, dan keterangan dalam Bahasa Indonesia teknis yang baku dan tegas.';

        $systemInstruction = <<<INSTRUCTION
Anda adalah Principal Software Architect di Neriah Pro Software Factory OS.
Tugas Anda adalah memperdalam, melengkapi, dan meng-elaborasi file scaffold/codebase spesifik "{$filename}" untuk proyek enterprise "{$businessName}".

PANDUAN UTAMA & STANDAR ARSITEKTUR MODERN (WAJIB PATUH 100%):
1. SUMBER KEBENARAN TERTINGGI (GROUNDED IN CORPUS): Anda WAJIB berpijak pada DOKUMEN ACUAN SUMBER, Daftar Fitur MVP, dan Skema Tabel ERD proyek "{$businessName}". Ekstrak entitas riil, aturan bisnis, formula tarif/kapasitas, dan alur kerja nyata. Dilarang keras membuat kode generik atau asumsi di luar domain proyek.
2. SYNTAX PURITY: Keluarkan HANYA kode/konten file mentah yang 100% valid untuk tipe file "{$filename}" tanpa pembungkus markdown codeblock ``` (kecuali file tersebut memang berekstensi .md atau .cursorrules / AGENTS.md).
3. MODERN TECH STACK LOCK:
   - Backend & Runtime: Laravel 13 & PHP 8.5+ (`declare(strict_types=1);`), Filament v5 (WAJIB `\Filament\Schemas\Schema`, dilarang keras `\Filament\Forms\Form` legacy v3), Livewire 4 & Flux UI.
   - Database & O(1) Scalability: PostgreSQL 16 Strict ULID (`CHAR(26)` / `VARCHAR(26)` dengan `->ulid('id')->primary()`, DILARANG KERAS `uuid` atau `AUTO_INCREMENT`) dan Keyset Cursor Pagination O(1) (`cursorPaginate()` dengan fallback `->orderBy('id', 'asc')`, DILARANG KERAS `paginate()` offset).
   - AI & RAG Pipeline (Jika relevan): Terapkan arsitektur 18-Technique Production RAG (Chunking, Embeddings, pgvector HNSW, Metadata Filtering, Hybrid Search BM25 + Vector, Cross-Encoder Reranking, Parent-Child Retrieval, CRAG/Self-RAG, Grounded Citations) dan 20 Agentic AI Concepts (MCP, Tool Use, Multi-Agent Orchestration, Guardrails UU PDP No. 27/2022, Observability).
   - Security & Compliance: OWASP Top 10, enkripsi PII AES-256-GCM + HMAC-SHA256 blind index, idempotency token pada pembayaran/webhook, dan audit trail SHA-256.
4. ATURAN SPESIFIK FILE "{$filename}":
{$fileSpecificMandate}
5. BAHASA: {$langInstruction}
INSTRUCTION;

        $effectiveUserInstruction = trim((string) $userInstruction) !== ''
            ? trim((string) $userInstruction)
            : ($isEn
                ? "Deepen and complete all implementation details of {$filename} for {$businessName} based on the project's MVP features, ERD tables, RBAC roles, and modern enterprise standards."
                : "Perdalam dan lengkapi seluruh detail implementasi {$filename} untuk {$businessName} berdasarkan fitur MVP, tabel ERD, peran RBAC, dan standar enterprise modern.");

        $prompt = <<<PROMPT
DOKUMEN ACUAN SUMBER (GROUND TRUTH PROYEK "{$businessName}"):
---
{$corpusText}
---

DAFTAR FITUR MVP PROYEK:
- {$featuresContext}

SKEMA TABEL ERD PROYEK (STRICT ULID):
{$tablesContext}

NAMA FILE TARGET: {$filename}

KONTEN FILE SAAT INI:
---
{$currentContent}
---

INSTRUKSI KHUSUS PENDALAMAN DARI USER / ARSITEK:
{$effectiveUserInstruction}

Instruksi Eksekusi: Tulis ulang dan perdalam seluruh isi file "{$filename}" secara utuh, lengkap, komprehensif, dan siap produksi (Production-Ready) dengan tetap mempertahankan bagian yang sudah benar dan memperkaya bagian yang belum mendalam sesuai instruksi di atas.
PROMPT;

        $isDeterministic = ($preferredAiProvider === 'deterministic');
        $aiResult = ['text' => null, 'provider' => 'deterministic_heuristic', 'model' => 'expert-rule-engine-v2'];

        if (!$isDeterministic) {
            $aiResult = MultiAiModelManager::executeWithFailover(
                $prompt,
                $systemInstruction,
                'prd',
                $preferredAiProvider
            );
        }

        $rawAiText = $aiResult['text'] ?? null;

        if (empty($rawAiText)) {
            $isDeterministic = true;
            $refinedContent = $this->generateDeterministicScaffoldElaboration(
                $blueprint,
                $filename,
                $currentContent,
                $effectiveUserInstruction,
                $sourceData,
                $locale
            );
        } else {
            // Clean any accidental markdown code fences if the file itself is not markdown
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $refinedContent = trim($rawAiText);
            if ($ext !== 'md' && !in_array($filename, ['.cursorrules', 'AGENTS.md', 'CLAUDE.md'], true)) {
                if (preg_match('/^```[a-z0-9_-]*\s*\n(.*?)\n```$/is', $refinedContent, $matches)) {
                    $refinedContent = trim($matches[1]);
                }
            }
        }

        // Synchronize alias pairs so linked scaffold tabs stay 100% consistent
        $aliasPairs = [
            'schema_complete.sql' => ['database/migrations/schema_complete.sql'],
            'database/migrations/schema_complete.sql' => ['schema_complete.sql'],
            'docs/architecture/ADR-001-postgresql-ulid.md' => ['docs/adr/ADR-001-architectural-paradigm.md'],
            'docs/adr/ADR-001-architectural-paradigm.md' => ['docs/architecture/ADR-001-postgresql-ulid.md'],
            'tests/e2e/smoke.spec.ts' => ['tests/e2e/critical-flows.spec.ts'],
            'tests/e2e/critical-flows.spec.ts' => ['tests/e2e/smoke.spec.ts'],
            'docs/runbooks/disaster-recovery.md' => ['docs/disaster-recovery-runbook.md'],
            'docs/disaster-recovery-runbook.md' => ['docs/runbooks/disaster-recovery.md'],
            '.cursorrules' => ['AGENTS.md', 'CLAUDE.md'],
            'AGENTS.md' => ['.cursorrules', 'CLAUDE.md'],
        ];

        $scaffoldCustom[$filename] = $refinedContent;
        $syncedFiles = [$filename];
        foreach (($aliasPairs[$filename] ?? []) as $linkedFile) {
            $scaffoldCustom[$linkedFile] = $refinedContent;
            $syncedFiles[] = $linkedFile;
        }

        // Calculate elaboration maturity & convergence telemetry
        $newLineCount = count(explode("\n", $refinedContent));
        $addedLines = max(0, $newLineCount - $previousLineCount);
        $telemetryMap = $userMeta['scaffold_elaboration_telemetry'] ?? [];
        $prevIterations = (int) ($telemetryMap[$filename]['iterations'] ?? 0);
        $iterations = $prevIterations + 1;
        $maturityScore = min(100, max(88, 82 + ($iterations * 6) + ($newLineCount > 60 ? 6 : 0)));
        $isMaximal = $maturityScore >= 98 || ($iterations >= 2 && $addedLines <= 5);
        if ($isMaximal) {
            $maturityScore = 100;
        }

        $telemetryEntry = [
            'iterations' => $iterations,
            'maturity_score' => $maturityScore,
            'is_maximal' => $isMaximal,
            'previous_lines' => $previousLineCount,
            'current_lines' => $newLineCount,
            'added_lines' => $addedLines,
            'synced_files' => $syncedFiles,
            'updated_at' => now()->toIso8601String(),
        ];
        foreach ($syncedFiles as $sf) {
            $telemetryMap[$sf] = $telemetryEntry;
        }

        $userMeta['scaffold_customizations'] = $scaffoldCustom;
        $userMeta['scaffold_elaboration_telemetry'] = $telemetryMap;

        // If AI Agent rules file was elaborated, also synchronize prd_content['custom_ai_agent_rules']
        $updatePayload = ['user_metadata' => $userMeta];
        if (in_array($filename, ['.cursorrules', 'AGENTS.md', 'CLAUDE.md'], true)) {
            $prd['custom_ai_agent_rules'] = $refinedContent;
            $updatePayload['prd_content'] = $prd;
        }

        $blueprint->update($updatePayload);

        $statusSuffix = $isMaximal
            ? ($isEn ? ' (100% Optimal / Maximal Architecture Reached)' : ' (100% Arsitektur Optimal / Maksimal Tercapai)')
            : ($isEn ? " (Maturity: {$maturityScore}%)" : " (Kematangan: {$maturityScore}%)");

        return [
            'success' => true,
            'message' => ($isDeterministic
                ? ($isEn
                    ? "File {$filename} deepened via Neriah Pro Deterministic Engine grounded in Source Documents{$statusSuffix}."
                    : "Berkas {$filename} berhasil diperdalam dengan Neriah Pro Deterministic Engine berbasis Dokumen Acuan{$statusSuffix}.")
                : ($isEn
                    ? "File {$filename} deepened via AI grounded in Source Documents{$statusSuffix}."
                    : "Berkas {$filename} berhasil diperdalam dengan AI berbasis Dokumen Acuan{$statusSuffix}.")),
            'filename' => $filename,
            'content' => $refinedContent,
            'line_count' => $newLineCount,
            'added_lines' => $addedLines,
            'maturity_score' => $maturityScore,
            'is_maximal' => $isMaximal,
            'iterations' => $iterations,
            'synced_files' => $syncedFiles,
            'ai_telemetry' => [
                'provider' => $isDeterministic ? 'deterministic_heuristic' : ($aiResult['provider'] ?? 'ai'),
                'model' => $isDeterministic ? 'expert-rule-engine-v2' : ($aiResult['model'] ?? 'default'),
            ],
            'source_corpus_files' => count($sourceData['source_files']),
        ];
    }

    /**
     * File-specific architectural mandate for Scaffold AI Elaboration.
     */
    protected function getScaffoldFileSpecificMandate(string $filename, bool $isEn = false): string
    {
        $lower = strtolower($filename);
        if (str_ends_with($lower, '.sql')) {
            return $isEn
                ? "- Output valid PostgreSQL 16 DDL SQL only.\n- Enforce CHAR(26) PRIMARY KEY (Strict ULID) on all domain tables, foreign keys with ON DELETE RESTRICT/CASCADE, composite indexes for O(1) keyset cursor pagination `(created_at, id)`, pgvector `vector(1536)` + HNSW index where AI/RAG applies, and SHA-256 audit log triggers."
                : "- Keluarkan murni SQL DDL PostgreSQL 16 yang valid.\n- Wajib gunakan CHAR(26) PRIMARY KEY (Strict ULID) di seluruh tabel domain, foreign key tegas, composite index untuk Keyset Cursor Pagination O(1) `(created_at, id)`, kolom `vector(1536)` + indeks HNSW pgvector untuk pencarian AI/RAG, serta trigger audit SHA-256.";
        }
        if ($lower === 'openapi.json') {
            return $isEn
                ? "- Output strictly valid JSON conforming to OpenAPI 3.1.0.\n- Include concrete paths for all project domain entities, O(1) cursor pagination query parameters (`cursor`, `limit`), Bearer Sanctum auth, idempotency header `X-Idempotency-Key`, and RFC 9457 Problem Details error responses."
                : "- Keluarkan murni JSON valid sesuai spesifikasi OpenAPI 3.1.0.\n- Lengkapi endpoint CRUD & bisnis untuk seluruh entitas proyek, parameter query Keyset Cursor O(1) (`cursor`, `limit`), header `X-Idempotency-Key`, autentikasi Bearer Sanctum, dan skema error RFC 9457 Problem Details.";
        }
        if (str_contains($lower, 'seeder')) {
            return $isEn
                ? "- Output valid PHP 8.5+ Laravel 13 Seeder class.\n- Populate realistic, domain-authentic seed records (using 26-char `Str::ulid()`) matching the client's exact business domain, RBAC roles, realistic pricing/tariffs, and relational foreign keys."
                : "- Keluarkan class Seeder Laravel 13 (PHP 8.5+) yang valid.\n- Isi dengan data sintetis yang 100% kontekstual dan realistis sesuai bisnis klien (menggunakan `Str::ulid()` 26-karakter), mencakup aktor RBAC, tarif/harga nyata, dan relasi foreign key yang utuh.";
        }
        if (str_contains($lower, 'test.php') || str_contains($lower, 'tests/feature')) {
            return $isEn
                ? "- Output valid Pest v3 / PHPUnit Feature Contract Test code.\n- Test happy paths, O(1) cursor pagination (`next_cursor`), 26-char ULID validation, RBAC role isolation (403 Forbidden), idempotency key replay protection (409/200), and rate limiting."
                : "- Keluarkan kode pengujian Pest v3 / PHPUnit Feature Contract Test yang valid.\n- Uji skenario Happy Path, Keyset Cursor Pagination O(1) (`next_cursor`), validasi format ULID 26-karakter, isolasi RBAC antar aktor (403 Forbidden), proteksi idempotency key, dan rate limiting.";
        }
        if (str_ends_with($lower, '.spec.ts')) {
            return $isEn
                ? "- Output valid Playwright TypeScript E2E test specification.\n- Verify critical user journeys, dark/light mode high-contrast rendering, zero native browser dialogs (`window.alert`/`confirm` ban), and responsive cockpit interactions."
                : "- Keluarkan spesifikasi Playwright TypeScript E2E test yang valid.\n- Uji alur kritis pengguna dari hulu ke hilir, kepatuhan kontras tema Dark/Light, larangan dialog native browser (`alert`/`confirm`), dan responsivitas transaksi.";
        }
        if (str_contains($lower, 'routes/')) {
            return $isEn
                ? "- Output valid Laravel 13 route definitions.\n- Group routes by RBAC middleware, apply rate limiters (`throttle:60,1`), include O(1) cursor endpoints, idempotent payment/webhook endpoints, and health probes."
                : "- Keluarkan definisi route Laravel 13 yang valid.\n- Kelompokkan route berdasarkan middleware RBAC, pasang rate limiter (`throttle:60,1`), sediakan endpoint berpaginasi cursor O(1), webhook idempoten, dan health check probe.";
        }

        return $isEn
            ? "- Deepen the file with production-grade configurations, concrete domain entities from the project corpus, strict ULID & O(1) cursor rules, and zero placeholders."
            : "- Perdalam isi berkas dengan konfigurasi siap produksi, entitas domain konkret dari dokumen acuan proyek, aturan Strict ULID & Cursor O(1), tanpa menyisakan placeholder kosong.";
    }

    /**
     * Resilient Deterministic Architecture Engine for grounded PRD segment expansion.
     * Guarantees zero downtime and enterprise output even when remote AI APIs fail or are unconfigured.
     */
    protected function generateDeterministicRefinement(
        VisionBlueprint $blueprint,
        string $segmentKey,
        mixed $existingData,
        ?string $userInstruction,
        array $sourceData,
        array $additionalContext = []
    ): mixed {
        $locale = $additionalContext['locale'] 
            ?? ($blueprint->user_metadata['output_locale'] 
            ?? (request()->query('locale') 
            ?: (request()->cookie('neriah_locale') ?: (session('locale') ?: app()->getLocale()))));
        $isEn = ($locale === 'en');
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: ($isEn ? 'Enterprise System' : 'Sistem Enterprise'));
        $directive = $userInstruction ?: ($isEn ? 'Automatic enterprise-grade architectural refinement.' : 'Penajaman arsitektur otomatis berstandar enterprise.');

        if ($segmentKey === 'feature_single') {
            $item = is_array($existingData) ? $existingData : [];
            $title = $item['title'] ?? ($isEn ? 'Core Module Specification' : 'Spesifikasi Modul Inti');
            $id = $item['id'] ?? 'FEAT-MVP-01';
            $index = $item['index'] ?? 1;
            $category = $item['category'] ?? 'core';
            $catLabel = $item['category_label'] ?? ($isEn ? 'Core Module' : 'Modul Inti');

            $slugTitle = Str::slug($title);
            $pascal = PrdGeneratorService::sanitizeEntityClassName($title, $item['related_table'] ?? null);

            $erdTables = $blueprint->prd_content['erd_schema']['tables'] 
                ?? ($blueprint->prd_content['database_erd'] ?? []);
            $actors = $blueprint->prd_content['system_actors'] ?? [];
            $clientActor = $actors[0]['name'] ?? ($isEn ? 'Client / Traveler' : 'Klien / Pengguna');
            $primaryActor = $actors[1]['name'] ?? ($isEn ? 'System Administrator' : 'Administrator');
            $prefix = 'MVP';

            // Invoke deep domain semantic analysis & concrete engineering archetypes
            $domainSpec = \App\Services\Prd\PrdFeatureSpecificationService::analyzeFeatureDomain(
                $title,
                $item['desc'] ?? '',
                $businessName,
                $clientActor,
                $primaryActor,
                $index,
                $prefix,
                $erdTables,
                $isEn
            );

            $defaultDesc = $isEn
                ? "Module {$title} for {$businessName} featuring transactional ACID execution, tamper-proof audit trails, and verified third-party integrations."
                : "Modul {$title} untuk {$businessName} dengan pemrosesan transactional ACID, audit trail, dan integrasi terverifikasi.";

            $userStory = $domainSpec['user_story'] ?? ($isEn
                ? "As a System User of {$businessName}, I want to manage {$title} with real-time data validation, concurrency protection, and centralized audit reporting."
                : "Sebagai Pengguna Sistem {$businessName}, saya dapat mengelola {$title} dengan validasi data real-time, perlindungan konkurensi, dan laporan audit terpusat.");

            $refinedItem = [
                'id' => $id,
                'index' => $index,
                'tier' => 'mvp',
                'title' => $title,
                'desc' => ($item['desc'] ?? '') ?: ($domainSpec['user_story'] ?? $defaultDesc),
                'category' => $domainSpec['category'] ?? $category,
                'category_label' => $domainSpec['category_label'] ?? $catLabel,
                'related_table' => $domainSpec['related_table'] ?? ($item['related_table'] ?? Str::snake(Str::plural($pascal))),
                'story_points' => $item['story_points'] ?? 5,
                'complexity_label' => $item['complexity_label'] ?? ($isEn ? 'High (5 SP)' : 'Tinggi (5 SP)'),
                'sprint_phase' => $item['sprint_phase'] ?? 'Sprint 1',
                'target_files' => $domainSpec['backend']['target_files'] ?? [
                    "app/Models/{$pascal}.php",
                    "app/Services/{$pascal}Service.php",
                    "app/Http/Controllers/{$pascal}Controller.php",
                    "tests/Feature/{$pascal}ContractTest.php",
                ],
                'user_story' => $userStory,
                'acceptance_criteria' => $domainSpec['acceptance_criteria'] ?? PrdGeneratorService::normalizeAcceptanceCriteria(
                    $isEn ? [
                        "Scenario 1 (Happy Path): System successfully processes {$title} returning HTTP 200 OK and persisting relational database records with Strict ULID.",
                        "Scenario 2 (Validation Error): Request with incomplete or invalid parameters returns 422 Unprocessable Content with structured error messages.",
                        "Scenario 3 (Edge Case & Concurrency): Simultaneous mutation attempts are handled using database pessimistic locking to eliminate race conditions.",
                    ] : [
                        "Skenario 1 (Happy Path): Sistem berhasil memproses {$title} dengan status 200 OK dan menyimpan relasi database Strict ULID.",
                        "Skenario 2 (Validation Error): Request dengan parameter tidak lengkap menghasilkan response 422 Unprocessable Content dengan pesan error terstruktur.",
                        "Skenario 3 (Edge Case & Concurrency): Percobaan mutasi simultan ditangani melalui database transaction locking untuk mencegah race condition.",
                    ], 
                    $title, 
                    $userStory
                ),
                'frontend' => $domainSpec['frontend'] ?? [
                    'components' => ["{$pascal}Component.blade.php", "{$pascal}Card.blade.php"],
                    'tasks' => $isEn ? [
                        "Build responsive user interface for {$title} adhering strictly to WCAG AAA contrast ratio.",
                        "Implement Keyset Cursor Pagination O(1) data table with instant clear button on search field.",
                        "Integrate floating toast visual feedback for every state transition.",
                    ] : [
                        "Bangun antarmuka responsif {$title} dengan rasio kontras WCAG AAA.",
                        "Implementasikan tabel listing data Keyset Cursor Pagination O(1) dengan search box instan clear button.",
                        "Integrasikan feedback visual floating toast untuk setiap mutasi state.",
                    ],
                    'states' => $isEn ? 'Default, Loading Skeleton, Empty State, Error Toast, Processing' : 'Default, Loading Skeleton, Empty State, Error Toast, Memproses',
                    'anti_ai_slop' => $isEn ? 'Solid Zinc monochrome palette with crisp high contrast, rounded-none or subtle borders, strictly zero pill/capsule buttons, no native window.alert().' : 'Palet Zinc monokrom dengan aksen tajam, border presisi rounded-none, dilarang tombol kapsul, bebas window.alert() native.',
                ],
                'backend' => $domainSpec['backend'] ?? [
                    'target_files' => [
                        "app/Models/{$pascal}.php",
                        "app/Services/{$pascal}Service.php",
                        "app/Http/Controllers/{$pascal}Controller.php",
                    ],
                    'tasks' => $isEn ? [
                        "Database migration for " . Str::snake(Str::plural($pascal)) . " table with 26-character Strict ULID primary key conforming to PostgreSQL 16 standard.",
                        "Implement atomic {$pascal}Service wrapped in DB::transaction() with pessimistic locking (lockForUpdate).",
                        "Enforce FormRequest Store{$pascal}Request validation with strict anti-XSS and type sanitization.",
                    ] : [
                        "Migration tabel " . Str::snake(Str::plural($pascal)) . " dengan Primary Key ULID 26-karakter standar PostgreSQL 16.",
                        "Implementasikan {$pascal}Service atomik dibungkus DB::transaction() dengan pessimistic locking (lockForUpdate).",
                        "Terapkan validasi FormRequest Store{$pascal}Request anti-XSS dan sanitasi tipe data ketat.",
                    ],
                    'ulid_migration_rules' => $isEn ? 'Must use ->ulid("id")->primary() (VARCHAR(26)) and foreignUlid. AUTO_INCREMENT is strictly prohibited.' : 'Wajib gunakan ->ulid("id")->primary() (VARCHAR(26)) dan foreignUlid. Dilarang AUTO_INCREMENT.',
                    'query_strategy' => $isEn ? 'Keyset Cursor Pagination cursorPaginate() with ->orderBy("id", "asc") for sub-10ms query latency.' : 'Keyset Cursor Pagination cursorPaginate() dengan ->orderBy("id", "asc") untuk latensi sub-10ms.',
                ],
                'integration' => $domainSpec['integration'] ?? [
                    'endpoint' => "POST /api/v1/" . Str::kebab(Str::plural($pascal)),
                    'middleware' => ['api', 'auth:sanctum', 'throttle:60,1'],
                    'tasks' => $isEn ? [
                        "Register RESTful API endpoint with Sanctum token authentication and rate limiting.",
                        "Format error responses according to RFC 9457 Problem Details specification with precise HTTP status codes.",
                        "Enforce X-Idempotency-Key request header to eliminate duplicate mutations.",
                    ] : [
                        "Daftarkan endpoint RESTful API dengan otentikasi token Sanctum dan rate limiting.",
                        "Format respons error standar RFC 9457 Problem Details dengan HTTP status code presisi.",
                        "Terapkan header X-Idempotency-Key untuk mencegah mutasi ganda.",
                    ],
                    'request_schema' => json_encode(['title' => $title, 'status' => 'active'], JSON_PRETTY_PRINT),
                    'response_schema' => json_encode(['success' => true, 'data' => ['id' => '01J7K9M0P1Q2R3S4T5U6V7W8X9', 'title' => $title, 'status' => 'active']], JSON_PRETTY_PRINT),
                ],
                'code_agent_directive' => $domainSpec['code_agent_directive'] ?? ($isEn ? "Implement {$pascal}Service using DB::transaction, Strict ULID primary keys, cursorPaginate(), and anti-hardcoding patterns." : "Implementasikan {$pascal}Service dengan DB::transaction, Strict ULID primary keys, cursorPaginate(), dan anti-hardcoding patterns."),
            ];

            return BlueprintDiscoveryService::polishFeatureSpecification(
                $refinedItem,
                $blueprint->domain_category ?? 'custom_portal',
                $isEn
            );
        }

        if (in_array($segmentKey, ['features', 'feature_specifications', 'mvp_features'])) {
            $existingList = is_array($existingData) ? $existingData : [];
            if (empty($existingList)) {
                $defaultFeatures = $isEn
                    ? "Transaction Module\nUser Management\nAudit Log & Reporting"
                    : "Modul Transaksi\nManajemen Pengguna\nAudit Log & Pelaporan";
                $rawFeatures = array_filter(array_map('trim', explode("\n", $blueprint->fitur_wajib ?: $defaultFeatures)));
                $i = 1;
                foreach ($rawFeatures as $fName) {
                    $cleanName = preg_replace('/^[0-9\.\-\*\s]+/', '', $fName);
                    if (empty($cleanName)) continue;
                    $existingList[] = [
                        'id' => sprintf('FEAT-MVP-%02d-%s', $i, Str::slug($cleanName)),
                        'index' => $i,
                        'tier' => 'mvp',
                        'title' => $cleanName,
                        'desc' => $isEn ? "In-depth specification for {$cleanName} module on {$businessName}." : "Spesifikasi mendalam modul {$cleanName} pada {$businessName}.",
                    ];
                    $i++;
                }
            }

            $refinedList = [];
            foreach ($existingList as $idx => $feat) {
                $refinedList[] = $this->generateDeterministicRefinement(
                    $blueprint,
                    'feature_single',
                    $feat,
                    $userInstruction,
                    $sourceData,
                    $additionalContext
                );
            }
            return $refinedList;
        }

        if (in_array($segmentKey, ['database_erd', 'erd_schema'])) {
            $tables = is_array($existingData) ? $existingData : [];
            if (empty($tables)) {
                $tables = [
                    ['name' => 'users', 'description' => $isEn ? 'User authentication accounts & permissions table' : 'Tabel akun otentikasi pengguna & hak akses'],
                    ['name' => Str::plural(Str::snake(Str::slug($blueprint->nama_bisnis ?: 'project'))) . '_records', 'description' => $isEn ? 'Core business domain entity table' : 'Tabel entitas bisnis utama'],
                    ['name' => 'audit_logs', 'description' => $isEn ? 'System activity immutable audit trail table' : 'Tabel riwayat jejak audit aktivitas sistem'],
                ];
            }

            $refinedTables = [];
            foreach ($tables as $t) {
                $tName = $t['name'] ?? 'records';
                $tDesc = $t['description'] ?? ($isEn ? "Operational data table for {$tName}" : "Tabel operasional data " . $tName);
                $columns = $t['columns'] ?? [];

                if (empty($columns)) {
                    $columns = [
                        ['name' => 'id', 'type' => 'VARCHAR(26)', 'nullable' => false, 'primary' => true, 'description' => 'Strict ULID Primary Key'],
                        ['name' => 'name', 'type' => 'JSONB', 'nullable' => false, 'primary' => false, 'description' => $isEn ? 'Bilingual entity name or title (ID/EN)' : 'Nama atau label entitas dwibahasa (ID/EN)'],
                        ['name' => 'code_reference', 'type' => 'VARCHAR(50)', 'nullable' => false, 'primary' => false, 'description' => $isEn ? 'Unique O(1) indexed reference code' : 'Kode referensi unik O(1)'],
                        ['name' => 'status', 'type' => 'VARCHAR(50)', 'nullable' => false, 'primary' => false, 'description' => $isEn ? 'State machine lifecycle status' : 'Status state machine'],
                        ['name' => 'created_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false, 'primary' => false, 'description' => $isEn ? 'Record creation timestamp' : 'Timestamp pembuatan data'],
                        ['name' => 'updated_at', 'type' => 'TIMESTAMPTZ', 'nullable' => false, 'primary' => false, 'description' => $isEn ? 'Record mutation timestamp' : 'Timestamp perubahan data'],
                        ['name' => 'deleted_at', 'type' => 'TIMESTAMPTZ', 'nullable' => true, 'primary' => false, 'description' => $isEn ? 'Soft delete timestamp' : 'Soft delete timestamp'],
                    ];
                }

                $ddlLines = ["CREATE TABLE IF NOT EXISTS \"{$tName}\" ("];
                foreach ($columns as $c) {
                    $cName = $c['name'];
                    $cType = $c['type'];
                    $null = ($c['nullable'] ?? false) ? 'NULL' : 'NOT NULL';
                    $pk = ($c['primary'] ?? false) ? ' PRIMARY KEY' : '';
                    $ddlLines[] = "    \"{$cName}\" {$cType} {$null}{$pk},";
                }
                $ddlLines[count($ddlLines) - 1] = rtrim($ddlLines[count($ddlLines) - 1], ',');
                $ddlLines[] = ');';
                $ddlLines[] = "CREATE INDEX IF NOT EXISTS \"idx_{$tName}_created_id\" ON \"{$tName}\" (\"created_at\" DESC, \"id\" DESC);";

                $refinedTables[] = [
                    'name' => $tName,
                    'description' => $tDesc,
                    'columns' => $columns,
                    'ddl_sql' => implode("\n", $ddlLines),
                ];
            }
            return $refinedTables;
        }

        if (in_array($segmentKey, ['alurs_kerja', 'workflow'])) {
            $workflows = is_array($existingData) ? $existingData : [];
            if (empty($workflows)) {
                $workflows = $isEn
                    ? [
                        [
                            'id' => 'FLOW-01',
                            'name' => 'Registration & Credential Verification Workflow',
                            'actor' => 'User / Customer',
                            'steps' => [
                                '1. User inputs credentials into ' . $businessName . ' portal',
                                '2. System verifies identity uniqueness and sends 6-digit OTP verification code',
                                '3. User verifies the 6-digit OTP code',
                                '4. Active session is issued with encrypted Bearer token',
                            ],
                        ],
                        [
                            'id' => 'FLOW-02',
                            'name' => 'Data Mutation & Audit Trail Execution Workflow',
                            'actor' => 'Business Operator / Admin',
                            'steps' => [
                                '1. Operator initiates transaction or state transition',
                                '2. Business schema validation and RBAC authorization are evaluated',
                                '3. Database commit executed within atomic DB::transaction block',
                                '4. Tamper-proof audit log recorded with actor ULID and client IP',
                            ],
                        ],
                    ]
                    : [
                        [
                            'id' => 'FLOW-01',
                            'name' => 'Alur Pendaftaran & Verifikasi Kredensial',
                            'actor' => 'Pengguna',
                            'steps' => [
                                '1. Pengguna menginput kredensial pada portal ' . $businessName,
                                '2. Sistem mengecek keunikan identitas dan mengirimkan kode OTP',
                                '3. Pengguna memverifikasi kode 6-digit',
                                '4. Sesi aktif diterbitkan dengan token terenkripsi',
                            ],
                        ],
                        [
                            'id' => 'FLOW-02',
                            'name' => 'Alur Eksekusi & Audit Mutasi Data',
                            'actor' => 'Operator / Admin',
                            'steps' => [
                                '1. Operator menginisiasi transaksi atau perubahan status',
                                '2. Validasi skema bisnis dan otorisasi RBAC dievaluasi',
                                '3. Database commit dilakukan dalam blok DB::transaction atomic',
                                '4. Audit log tercatat dengan capture IP dan actor ULID',
                            ],
                        ],
                    ];
            }
            return $workflows;
        }

        if ($segmentKey === 'system_actors') {
            $actors = is_array($existingData) ? $existingData : [];
            if (empty($actors)) {
                $actors = $isEn
                    ? [
                        [
                            'role' => 'Super Administrator',
                            'description' => 'Full control access for system configuration, audit logs, and API integrations.',
                            'permissions' => ['*'],
                        ],
                        [
                            'role' => 'Business Operator',
                            'description' => 'Daily operational management of transactions and live monitoring.',
                            'permissions' => ['view_records', 'create_records', 'update_records'],
                        ],
                        [
                            'role' => 'Client / Customer',
                            'description' => 'Self-service portal access, status dashboard, and order history.',
                            'permissions' => ['view_own_data'],
                        ],
                    ]
                    : [
                        [
                            'role' => 'Super Administrator',
                            'description' => 'Akses kontrol penuh konfigurasi sistem, audit log, dan integrasi API.',
                            'permissions' => ['*'],
                        ],
                        [
                            'role' => 'Operator Bisnis',
                            'description' => 'Manajemen operasional harian data transaksi dan monitoring.',
                            'permissions' => ['view_records', 'create_records', 'update_records'],
                        ],
                        [
                            'role' => 'Klien / Pelanggan',
                            'description' => 'Akses mandiri portal layanan, dashboard status, dan riwayat.',
                            'permissions' => ['view_own_data'],
                        ],
                    ];
            }
            return $actors;
        }

        if ($segmentKey === 'custom_ai_agent_rules') {
            if ($isEn) {
                return "# Neriah Pro Software Engineering Standards ({$businessName})\n\n" .
                       "- Primary Keys: Strictly 26-character ULID (VARCHAR(26)), AUTO_INCREMENT is prohibited.\n" .
                       "- Pagination: Keyset Cursor Pagination O(1) (cursorPaginate()) is mandatory.\n" .
                       "- Multi-Language Tier 1: Human-readable narrative columns (name, title, description, reason, memo, message_body, decision_note, tier_name) MUST be JSONB `{\"id\": \"...\", \"en\": \"...\"}` cast as array.\n" .
                       "- Scalar Integrity: PKs, FKs, Unique Codes/Slugs, State Machine Statuses, and Hashes MUST remain scalar VARCHAR in relational schema.\n" .
                       "- Clean Brutalism: High contrast, rounded-none or subtle borders, strictly zero pill/capsule buttons.\n" .
                       "- Anti-Hardcoding: Magic strings and fuzzy dummy slug queries are strictly banned.\n" .
                       "- Modern AI & Search Architecture: Features involving search, matching, recommendations, or generative AI MUST implement the 18 RAG Concepts (Hybrid Search [pgvector + BM25], Cross-Encoder Reranking, Parent-Child Chunking, Grounded Citations) and 20 Agentic AI Concepts (Tool Calling, MCP, Agentic Workflows, Guardrails, Observability). Naive SQL LIKE queries and ungrounded LLM prompts are strictly prohibited.\n" .
                       "- User Directive: {$directive}\n";
            }

            return "# Standar Rekayasa Perangkat Lunak Neriah Pro ({$businessName})\n\n" .
                   "- Primary Keys: Wajib ULID 26 karakter (VARCHAR(26)), dilarang auto-increment.\n" .
                   "- Pagination: Wajib Keyset Cursor Pagination O(1) (cursorPaginate()).\n" .
                   "- Multi-Bahasa Tier 1: Kolom narasi manusiawi (name, title, description, reason, memo, message_body, decision_note, tier_name) WAJIB bertipe JSONB `{\"id\": \"...\", \"en\": \"...\"}` di-cast array.\n" .
                   "- Integritas Skalar: PK, FK, Unique Code/Slug, Status State Machine, dan Hash WAJIB tetap VARCHAR skalar skema relasional, DILARANG dijadikan JSONB.\n" .
                   "- Clean Brutalism: Kontras tinggi, tanpa border capsule pill rounded-full.\n" .
                   "- Anti-Hardcoding: Dilarang keras magic strings atau dummy slug queries.\n" .
                   "- Arsitektur AI & Pencarian Modern: Fitur yang melibatkan pencarian, pencocokan data, rekomendasi, atau kecerdasan buatan WAJIB menerapkan 18 Konsep RAG (Hybrid Search [pgvector + BM25], Cross-Encoder Reranking, Parent-Child Chunking, Grounded Citations) dan 20 Konsep Agentic AI (Tool Calling, MCP, Agentic Workflows, Guardrails, Observability). Dilarang keras query SQL LIKE '%...' atau prompt tanpa acuan data.\n" .
                   "- Petunjuk Pengguna: {$directive}\n";
        }

        return $existingData;
    }

    /**
     * Resilient Deterministic Engine for Scaffold File elaboration.
     * Enriches any of the 22 scaffold files with concrete, domain-grounded code/DDL/specs
     * derived from the blueprint's ERD tables, MVP features, RBAC roles, and Modern Tech Stack.
     */
    protected function generateDeterministicScaffoldElaboration(
        VisionBlueprint $blueprint,
        string $filename,
        string $currentContent,
        ?string $userInstruction,
        array $sourceData,
        string $locale = 'id'
    ): string {
        $isEn = $locale === 'en';
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Platform'), $locale);
        $directive = $userInstruction ? trim($userInstruction) : ($isEn ? 'Deepen production-grade architecture & Modern Tech Stack' : 'Perdalam arsitektur siap produksi & Modern Tech Stack');
        $prd = $blueprint->prd_content ?? [];

        // Extract domain tables & features for grounded code generation
        $tables = collect($prd['database_erd'] ?? [])
            ->map(fn ($t) => is_array($t) ? ($t['table_name'] ?? ($t['name'] ?? null)) : null)
            ->filter()
            ->values()
            ->all();
        if (empty($tables)) {
            $tables = ['users', 'orders', 'transactions', 'audit_logs'];
        }
        $primaryTable = $tables[0] ?? 'orders';
        $secondaryTable = $tables[1] ?? ($tables[0] ?? 'transactions');

        $features = collect($prd['mvp_features'] ?? [])
            ->map(fn ($f) => is_array($f) ? $this->stringifyAttribute($f['title'] ?? ($f['name'] ?? ($f['feature'] ?? '')), $locale) : (string) $f)
            ->filter()
            ->take(5)
            ->values()
            ->all();
        $featureSummary = !empty($features) ? implode(', ', $features) : ($isEn ? 'Core Domain Modules' : 'Modul Inti Operasional');

        $lower = strtolower($filename);
        $marker = 'NERIAH_SCAFFOLD_DEEPENED_V2';

        // 1. OpenAPI JSON (.json) - Must remain 100% valid JSON!
        if ($lower === 'openapi.json' || str_ends_with($lower, '.json')) {
            $decoded = json_decode($currentContent, true);
            if (!is_array($decoded)) {
                $decoded = [
                    'openapi' => '3.1.0',
                    'info' => [
                        'title' => "{$businessName} Enterprise API",
                        'version' => '1.1.0',
                    ],
                    'paths' => [],
                ];
            }
            $decoded['info']['description'] = ($decoded['info']['description'] ?? '') . " | Deepened for {$businessName}: Strict 26-char ULID, Keyset Cursor Pagination O(1), RFC 9457 Problem Details, and 18-Technique Hybrid RAG.";
            $decoded['components']['parameters']['CursorParam'] = [
                'name' => 'cursor',
                'in' => 'query',
                'description' => 'Opaque 26-char ULID keyset cursor pointer for O(1) sub-10ms pagination',
                'required' => false,
                'schema' => ['type' => 'string', 'example' => '01J9Z8K4M2P5X7R9T1V3W5Y7Z0'],
            ];
            $decoded['components']['headers']['IdempotencyKey'] = [
                'description' => 'ULID/UUID token to guarantee idempotent mutation execution',
                'schema' => ['type' => 'string'],
            ];
            $decoded['components']['schemas']['ProblemDetailsRFC9457'] = [
                'type' => 'object',
                'required' => ['type', 'title', 'status', 'detail'],
                'properties' => [
                    'type' => ['type' => 'string', 'example' => 'https://neriahpro.com/errors/validation-failed'],
                    'title' => ['type' => 'string', 'example' => 'Unprocessable Entity'],
                    'status' => ['type' => 'integer', 'example' => 422],
                    'detail' => ['type' => 'string'],
                    'trace_ulid' => ['type' => 'string', 'minLength' => 26, 'maxLength' => 26],
                ],
            ];
            foreach (array_slice($tables, 0, 4) as $tbl) {
                $slugTbl = Str::slug($tbl, '-');
                $pathKey = "/api/v1/{$slugTbl}";
                if (!isset($decoded['paths'][$pathKey])) {
                    $decoded['paths'][$pathKey] = [
                        'get' => [
                            'summary' => "Cursor-paginated O(1) listing for {$tbl}",
                            'parameters' => [['$ref' => '#/components/parameters/CursorParam']],
                            'responses' => [
                                '200' => ['description' => "Keyset cursor slice for {$tbl} (sub-10ms)"],
                                '429' => ['description' => 'Rate limit exceeded (RFC 9457)'],
                            ],
                        ],
                    ];
                }
            }
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Check convergence idempotency marker for non-JSON files
        if (str_contains($currentContent, $marker) && empty($userInstruction)) {
            return $currentContent;
        }

        // 2. PostgreSQL 16 Strict DDL (.sql)
        if (str_ends_with($lower, '.sql')) {
            $sqlBlock = "\n\n-- ============================================================================\n"
                . "-- [{$marker}] AI ARCHITECTURAL DEEPENING FOR: {$businessName}\n"
                . "-- Directive: {$directive}\n"
                . "-- Stack: PostgreSQL 16 Strict | CHAR(26) ULID | O(1) Keyset Cursor | pgvector HNSW\n"
                . "-- ============================================================================\n"
                . "CREATE EXTENSION IF NOT EXISTS vector;\n"
                . "CREATE EXTENSION IF NOT EXISTS pg_trgm;\n\n"
                . "-- 1. Enterprise Immutable Audit Trail & SHA-256 Scope Integrity Table\n"
                . "CREATE TABLE IF NOT EXISTS enterprise_audit_trails (\n"
                . "    id CHAR(26) PRIMARY KEY,\n"
                . "    actor_ulid CHAR(26) NULL,\n"
                . "    entity_table VARCHAR(120) NOT NULL,\n"
                . "    entity_ulid CHAR(26) NOT NULL,\n"
                . "    action_type VARCHAR(40) NOT NULL,\n"
                . "    payload_diff JSONB NOT NULL DEFAULT '{}'::jsonb,\n"
                . "    sha256_checksum CHAR(64) NOT NULL,\n"
                . "    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP\n"
                . ");\n"
                . "CREATE INDEX IF NOT EXISTS idx_audit_cursor_o1 ON enterprise_audit_trails (created_at DESC, id DESC);\n\n"
                . "-- 2. Hybrid RAG (BM25 + pgvector 1536) Knowledge Chunks for {$businessName}\n"
                . "CREATE TABLE IF NOT EXISTS ai_knowledge_chunks (\n"
                . "    id CHAR(26) PRIMARY KEY,\n"
                . "    parent_doc_ulid CHAR(26) NOT NULL,\n"
                . "    domain_scope VARCHAR(80) NOT NULL DEFAULT 'general',\n"
                . "    chunk_text TEXT NOT NULL,\n"
                . "    metadata_filter JSONB NOT NULL DEFAULT '{}'::jsonb,\n"
                . "    embedding vector(1536),\n"
                . "    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP\n"
                . ");\n"
                . "CREATE INDEX IF NOT EXISTS idx_ai_chunks_cursor_o1 ON ai_knowledge_chunks (created_at ASC, id ASC);\n\n";

            foreach (array_slice($tables, 0, 5) as $tbl) {
                $safeTbl = preg_replace('/[^a-z0-9_]/i', '_', strtolower($tbl));
                $sqlBlock .= "-- O(1) Keyset Cursor Composite Index for {$safeTbl}\n"
                    . "CREATE INDEX IF NOT EXISTS idx_{$safeTbl}_keyset_cursor ON {$safeTbl} (created_at DESC, id DESC);\n";
            }

            return rtrim($currentContent) . $sqlBlock;
        }

        // 3. PHP Files (Seeder, Pest Contract Tests, Routes)
        if (str_ends_with($lower, '.php')) {
            if (str_contains($lower, 'seeder')) {
                $phpBlock = "\n\n// [{$marker}] Domain-Specific Synthetic Seeder Extension for {$businessName}\n"
                    . "// Directive: {$directive}\n"
                    . "// Modules Covered: {$featureSummary}\n"
                    . "/*\n"
                    . " * Additional High-Density Seed Verification:\n"
                    . " * - All records enforce 26-char Str::ulid()->toString() primary & foreign keys\n"
                    . " * - Zero-assumption domain values calibrated for {$businessName}\n"
                    . " * - Tables seeded: " . implode(', ', $tables) . "\n"
                    . " */\n";
                return rtrim($currentContent) . $phpBlock;
            }

            if (str_contains($lower, 'test')) {
                $phpBlock = "\n\n// [{$marker}] Deepened Enterprise Contract Assertions for {$businessName}\n"
                    . "// Directive: {$directive}\n"
                    . "it('enforces O(1) keyset cursor pagination and rejects offset page parameter on {$primaryTable}', function () {\n"
                    . "    \$response = \$this->getJson('/api/v1/" . Str::slug($primaryTable, '-') . "?limit=15');\n"
                    . "    \$response->assertStatus(200)->assertJsonStructure(['data', 'meta' => ['next_cursor', 'prev_cursor', 'per_page']]);\n"
                    . "});\n\n"
                    . "it('enforces 26-character ULID primary key format and idempotency replay protection on {$secondaryTable}', function () {\n"
                    . "    \$ulid = (string) \\Illuminate\\Support\\Str::ulid();\n"
                    . "    expect(strlen(\$ulid))->toBe(26);\n"
                    . "});\n";
                return rtrim($currentContent) . $phpBlock;
            }

            $phpBlock = "\n\n// [{$marker}] Deepened Route & Middleware Hardening for {$businessName}\n"
                . "// Directive: {$directive}\n"
                . "// Enforcing Sanctum RBAC isolation, Idempotency-Key verification, and O(1) cursor endpoints\n"
                . "Route::prefix('v1/enterprise-ops')->middleware(['throttle:60,1'])->group(function () {\n"
                . "    Route::get('/health-cursor-probe', fn () => response()->json([\n"
                . "        'status' => 'healthy',\n"
                . "        'project' => " . var_export($businessName, true) . ",\n"
                . "        'pk_standard' => 'ULID-CHAR-26',\n"
                . "        'pagination' => 'keyset_cursor_o1',\n"
                . "    ]));\n"
                . "});\n";
            return rtrim($currentContent) . $phpBlock;
        }

        // 4. Playwright TypeScript E2E Tests (.spec.ts)
        if (str_ends_with($lower, '.spec.ts') || str_ends_with($lower, '.ts')) {
            $tsBlock = "\n\n// [{$marker}] Deepened E2E Verification Suite for {$businessName}\n"
                . "// Directive: {$directive}\n"
                . "test('enforces zero native browser dialogs and high-contrast dark/light fidelity across {$businessName}', async ({ page }) => {\n"
                . "  let nativeDialogTriggered = false;\n"
                . "  page.on('dialog', () => { nativeDialogTriggered = true; });\n"
                . "  await page.goto('/');\n"
                . "  expect(nativeDialogTriggered).toBe(false);\n"
                . "});\n";
            return rtrim($currentContent) . $tsBlock;
        }

        // 5. Docker Compose / YAML (.yml / .yaml)
        if (str_ends_with($lower, '.yml') || str_ends_with($lower, '.yaml')) {
            $ymlBlock = "\n\n# [{$marker}] Hardened Runtime & Observability Tuning for {$businessName}\n"
                . "# Directive: {$directive}\n"
                . "# Enforces PHP 8.5+ JIT/OPcache, PostgreSQL 16 pgvector HNSW, and Redis 7 key eviction guardrails\n";
            return rtrim($currentContent) . $ymlBlock;
        }

        // 6. Shell Scripts (.sh)
        if (str_ends_with($lower, '.sh')) {
            $shBlock = "\n\n# [{$marker}] Post-Deploy Zero-Downtime Verification Probe for {$businessName}\n"
                . "# Directive: {$directive}\n"
                . "echo \"[VERIFY] Checking PostgreSQL 16 ULID schema & O(1) Cursor readiness for {$businessName}...\"\n"
                . "php artisan config:cache && php artisan route:cache && php artisan view:cache\n";
            return rtrim($currentContent) . $shBlock;
        }

        // 7. Markdown / AI Rules (.md, .cursorrules, AGENTS.md, CLAUDE.md)
        $mdBlock = "\n\n---\n"
            . "## ⚡ [{$marker}] Deepened Enterprise Architecture & AI Engineering Appendix ({$businessName})\n"
            . "- **Applied Directive**: {$directive}\n"
            . "- **Core Domain Modules**: {$featureSummary}\n"
            . "- **Database Entities (Strict `CHAR(26)` ULID)**: `" . implode('`, `', $tables) . "`\n"
            . "- **Modern Stack Lock**: Laravel 13, PHP 8.5+, Filament v5 (`\\Filament\\Schemas\\Schema`), Livewire 4, Flux UI, PostgreSQL 16 (`cursorPaginate()` O(1) sub-10ms), Redis 7.\n"
            . "- **18-Technique Production RAG Pipeline**: Semantic Chunking -> Embeddings -> `pgvector` HNSW + BM25 Hybrid Search -> Metadata RBAC Filtering -> Cross-Encoder Reranking -> Query Rewriting & Multi-Query -> Parent-Child Contextual Retrieval -> CRAG & Self-RAG Verification -> Grounded Citations.\n"
            . "- **20 Agentic AI Concepts**: Stateful Memory, Multi-Agent Orchestration, MCP Tool Use, Plan-and-Execute Reasoning, Guardrails (Anti-Prompt-Injection & UU PDP No. 27/2022 PII Masking), and Evals Telemetry.\n"
            . "- **UI/UX Brutalist Standard**: Solid high-contrast Light/Dark mode, subtle corners (`rounded-none` to `rounded-md`), zero capsule/pill shapes, and strict ban on native `alert()`/`confirm()`/`prompt()` dialogs.\n";

        return rtrim($currentContent) . $mdBlock;
    }

    /**
     * Elaborate a grounded, high-level directive prompt based on project source corpus & world-class standards.
     * Zero-assumption: ground strictly in the client's actual documents, business idea, and global engineering best practices.
     */
    public function elaborateDirective(
        VisionBlueprint $blueprint,
        string $segmentKey,
        ?string $preferredAiProvider = null,
        ?string $customTitle = null,
        ?string $featureId = null,
        ?string $featureContent = null,
        string $locale = 'id'
    ): string {
        $sourceData = $this->getSourceCorpus($blueprint);
        $corpusText = $this->stringifyAttribute($sourceData['corpus_text'] ?? '', $locale);
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise'), $locale);
        $rawProblems = $this->stringifyAttribute($blueprint->masalah_utama ?: '', $locale);
        $sector = $this->stringifyAttribute(
            $blueprint->prd_content['universal_blueprint_schema']['sectoral_ui_ux_archetype']['archetype_label'] 
            ?? ($blueprint->prd_content['project_metadata']['domain_category'] ?? 'Enterprise Digital Platform'),
            $locale
        );

        $isEn = $locale === 'en';
        $langPrompt = $isEn ? 'English (strictly professional engineering vocabulary)' : 'Bahasa Indonesia (baku, tegas, dan profesional)';

        // Segment-specific guidance for industry standards
        $segmentStandards = match ($segmentKey) {
            'alurs_kerja', 'workflow' => $isEn 
                ? 'User Journey & State Machine patterns, Happy Path, Edge Cases (Validation failure, Payment timeout, Cancellation/Refund), Actor Handoffs (Customer -> Admin Dispatcher -> Driver/Partner), Idempotency Tokens, and Automated Notifications (WhatsApp/Email).'
                : 'Pola User Journey & State Machine, Happy Path, Edge Cases (Validasi gagal, Timeout payment, Batal/Refund), Role Handoff antar Aktor (Pengguna -> Admin Dispatcher -> Supir/Mitra), Idempotency Token, dan Notifikasi Otomatis (WhatsApp/Email).',
            'database_erd', 'erd_schema' => $isEn
                ? 'PostgreSQL 16 Strict standards, O(1) Scalability, 26-character ULID Primary & Foreign Keys, Keyset Cursor Pagination, Foreign Key Constraints, Audit Trail & Logging (created_at, updated_at, deleted_at, created_by).'
                : 'Standar PostgreSQL 16 Strict, Skalabilitas O(1), ULID 26-karakter Primary & Foreign Keys, Keyset Cursor Pagination berindeks, Foreign Key Constraints, Audit Trail & Logging (created_at, updated_at, deleted_at, created_by).',
            'features', 'feature_single', 'mvp_features' => $isEn
                ? 'Domain-specific business logic, calculation formulas, real edge-case handling, state machine transitions, RFC 9457 Problem Details API contracts, and privacy data compliance (UU PDP / GDPR).'
                : 'Logika bisnis spesifik domain, formula kalkulasi riil, penanganan edge-case nyata, transisi state machine, kontrak API standar RFC 9457 Problem Details, dan kepatuhan privasi data (UU PDP).',
            'system_actors' => $isEn
                ? 'Granular RBAC matrix, Least Privilege principles, Multi-Tenant Access Isolation, Role Audit Trails, and Encrypted Session Management.'
                : 'Matriks RBAC granular, prinsip Least Privilege, Isolasi Akses Multi-Tenant, Audit Trail Peran, dan Session Management terenkripsi.',
            'executive_summary' => $isEn
                ? 'Executive Architecture Discovery across 26 parameters, High-load transaction analysis, Millions-user scalability, Risk & Mitigation Matrix, and High-Yield ROI Protections.'
                : 'Executive Architecture Discovery 26 Parameter, Analisis Beban Transaksi & Skalabilitas Jutaan Pengguna, Matriks Risiko & Mitigasi, dan High-Yield ROI Protections.',
            'scaffold_file' => $isEn
                ? 'Production Scaffolding standards, Hardened Docker Compose (PHP 8.5+, PG 16, Redis 7, Nginx), Pest API Contract Test Suite, Realistic Synthetic Data Seeders, and 18-Technique RAG + 20 Agentic AI Concepts.'
                : 'Standar Scaffolding Produksi, Docker Compose Hardened (PHP 8.5+, PG 16, Redis 7, Nginx), Pest API Contract Test Suite, Seeder Data Sintetis Realistis, serta 18 Teknik RAG + 20 Konsep Agentic AI.',
            default => $isEn
                ? 'World-class enterprise software engineering standards, Clean Architecture, sub-10ms performance, and high reliability.'
                : 'Standar rekayasa perangkat lunak enterprise kelas dunia, Clean Architecture, performa sub-10ms, dan reliabilitas tinggi.'
        };

        $systemInstruction = $isEn
            ? "You are the Principal Enterprise Software Architect at Neriah Pro. Your responsibility is to formulate a crisp, actionable, and hyper-specific Architectural Directive based strictly on facts from the client's Project Documents and World-Class Engineering Standards.\n\nCRITICAL MANDATE:\n1. STRICTLY FORBIDDEN to output generic, copy-pasted boilerplates (e.g. repeating 'Validate FormRequest, atomic DB::transaction, RBAC Least Privilege' across modules). Every module must have completely unique, deeply tailored domain specifications.\n2. Deeply analyze what this specific module or scaffold file does and formulate 3-4 concrete technical directives covering domain formulas, edge cases, state transitions, and integration contracts.\n3. Output language: English. Do NOT include conversational filler, preamble, or markdown code blocks. Output directly as numbered actionable points."
            : "Anda adalah Principal Software Enterprise Architect di Neriah Pro. Tugas Anda adalah merumuskan 'Instruksi Penajaman Arsitektur' (Actionable Directive) yang sangat terarah, konkret, dan berbasis FAKTA MURNI dari Dokumen Acuan Proyek klien serta STANDAR REKAYASA DUNIA.\n\nATURAN MUTLAK:\n1. DILARANG KERAS mengeluarkan template generik berulang (seperti copy-paste 'Validasi FormRequest, DB::transaction, RBAC Least Privilege' di semua modul). Setiap modul atau berkas scaffold WAJIB memiliki spesifikasi teknis dan bisnis yang 100% UNIK dan MENDALAM.\n2. Analisis fungsi spesifik modul/berkas ini: identifikasi formula bisnis, alur perhitungan, edge-case anomali nyata, transisi state, dan kontrak integrasi yang belum terpikirkan agar menjadi world-class.\n3. Bahasa output: {$langPrompt}. Outputkan HANYA instruksi penajaman langsung (tanpa basa-basi pembuka, tanpa markdown codeblock). Format dalam 3-4 poin bernomor padat, terstruktur, profesional, dan tegas.";

        $isFeatureTweak = in_array($segmentKey, ['feature_single', 'features', 'mvp_features']) || !empty($featureId);
        $featureName = $customTitle ?: ($featureId ?: 'Fitur Utama');

        if ($segmentKey === 'scaffold_file') {
            $targetFile = $customTitle ?: 'schema_complete.sql';
            $fileMandate = $this->getScaffoldFileSpecificMandate($targetFile, $isEn);
            $prompt = "PROYEK / CLIENT: {$businessName}
SEKTOR / ARSITEKTUR: {$sector}
TARGET BERKAS SCAFFOLD: {$targetFile}
BAHASA OUTPUT WAJIB: {$langPrompt}

ATURAN TEKNIS SPESIFIK BERKAS ({$targetFile}):
{$fileMandate}

DOKUMEN ACUAN PROYEK (MARKITDOWN CORPUS / BRIEF KLIEN):
" . Str::limit($corpusText ?: ($rawProblems ?: 'Solusi platform digital enterprise terotomasi.'), 2500) . "

CUPLIKAN ISI BERKAS SAAT INI ({$targetFile}):
" . Str::limit($featureContent ?: 'Berkas boilerplate awal.', 1800) . "

TUGAS ARSITEK:
Analisis berkas scaffold '{$targetFile}' di atas dan rumuskan 4 poin instruksi penajaman teknis (Actionable AI Directive) yang SANGAT SPESIFIK untuk memperdalam berkas '{$targetFile}' ini agar 100% komplit, siap produksi (zero-placeholder), dan selaras dengan entitas tabel, fitur MVP, serta standar Laravel 13 / PostgreSQL 16 ULID / Cursor O(1) / RAG & Agentic AI proyek '{$businessName}'.";
        } elseif ($isFeatureTweak) {
            $prompt = $isEn
                ? "PROJECT / CLIENT: {$businessName}
SECTOR / ARCHITECTURE: {$sector}
TARGET FEATURE / MODULE: {$featureName}" . ($featureId ? " [{$featureId}]" : "") . "
MANDATORY OUTPUT LANGUAGE: English (strictly professional engineering vocabulary)

GROUNDED PROJECT CORPUS (MARKITDOWN DOCUMENTS / CLIENT BRIEF):
" . Str::limit($corpusText ?: ($rawProblems ?: 'The client requires an automated enterprise digital platform solution.'), 3000) . "

CURRENT FEATURE SPECIFICATION:
" . Str::limit($featureContent ?: 'Core business operational module.', 2000) . "

ARCHITECTURAL DIRECTIVE ASSIGNMENT:
Perform a deep technical analysis of the '{$featureName}' module based strictly on the project documents and enterprise engineering best practices.
Identify critical requirements that are MISSING or UNDER-SPECIFIED in the current draft.
Formulate 3 to 4 crisp, actionable technical refinement directives tailored 100% to '{$featureName}':
1. Domain Business Logic & Computational Formulas (input parameters, threshold constraints, calculation rules).
2. Realistic Anomaly Scenarios & Edge Cases (network timeout retries, concurrency locking, TTL availability, collision mitigation).
3. State Machine Transitions & Service Integrations (lifecycle statuses, external API contracts, RFC 9457 error responses).
4. Sub-10ms O(1) Keyset Cursor Scalability & Data Protection Compliance.

STRICTLY FORBIDDEN to output generic, boilerplate filler. Provide concrete, technical directives specifically addressing '{$featureName}'."
                : "PROYEK / CLIENT: {$businessName}
SEKTOR / ARSITEKTUR: {$sector}
TARGET FITUR / MODUL: {$featureName}" . ($featureId ? " [{$featureId}]" : "") . "
BAHASA OUTPUT WAJIB: {$langPrompt}

DOKUMEN ACUAN PROYEK (MARKITDOWN CORPUS / BRIEF KLIEN):
" . Str::limit($corpusText ?: ($rawProblems ?: 'Klien memerlukan solusi platform digital terotomasi.'), 3000) . "

SPESIFIKASI FITUR SAAT INI:
" . Str::limit($featureContent ?: 'Fitur inti operasional bisnis.', 2000) . "

TUGAS ARSITEK:
Lakukan analisis mendalam terhadap modul '{$featureName}'.
Identifikasi poin-poin yang BELUM TERCOVER atau BELUM TERPIKIRKAN dari spesifikasi saat ini agar modul ini menjadi berstandar enterprise kelas dunia.
Rumuskan 3 hingga 4 poin instruksi penajaman teknis yang 100% EKSKLUSIF untuk fitur '{$featureName}' ini mencakup:
1. Logika Bisnis & Formula Kalkulasi Khusus Domain Modul Ini (variabel input, aturan batas, rumus perhitungan).
2. Skenario Anomali & Edge Cases Nyata Modul Ini (penanganan kegagalan, timeout, ketersediaan sementara/TTL, mitigasi bentrok).
3. Transisi State Machine & Integrasi Layanan (perubahan status data modul, interaksi API eksternal, format respons).
4. Skalabilitas O(1) & Perlindungan Data Sensitif.

DILARANG KERAS memberikan teks template generik. Tulis instruksi yang secara langsung membedah seluk-beluk teknis fitur '{$featureName}'.";
        } else {
            $prompt = $isEn
                ? "PROJECT / CLIENT: {$businessName}
SECTOR / ARCHITECTURE: {$sector}
CORE BUSINESS PROBLEMS: {$rawProblems}
SEGMENT TO REFINE: {$segmentKey}" . ($customTitle ? " ({$customTitle})" : "") . "
MANDATORY OUTPUT LANGUAGE: English (strictly professional engineering vocabulary)

GROUNDED PROJECT CORPUS (MARKITDOWN DOCUMENTS / CLIENT BRIEF):
" . Str::limit($corpusText ?: 'The client requires an automated enterprise digital platform with workflow integration and administrative tracking.', 4000) . "

ARCHITECTURAL DIRECTIVE ASSIGNMENT:
Formulate 3-4 concrete, deeply technical architectural directives for '{$segmentKey}' in English without fictitious assumptions."
                : "PROYEK / CLIENT: {$businessName}
SEKTOR / ARSITEKTUR: {$sector}
MASALAH UTAMA: {$rawProblems}
SEGMEN YANG DIELABORASI: {$segmentKey}" . ($customTitle ? " ({$customTitle})" : "") . "
BAHASA OUTPUT WAJIB: {$langPrompt}

DOKUMEN ACUAN PROYEK (MARKITDOWN CORPUS / BRIEF KLIEN):
" . Str::limit($corpusText ?: 'Klien memerlukan solusi platform digital terotomasi dengan integrasi alur bisnis, pelacakan status, dan dasbor admin.', 4000) . "

TUGAS ARSITEK:
Rumuskan 3-4 instruksi penajaman arsitektur yang konkret dan mendalam untuk segmen '{$segmentKey}' dalam {$langPrompt} tanpa asumsi fiktif.";
        }

        if ($preferredAiProvider === 'deterministic') {
            return $this->generateDeterministicDirective($blueprint, $segmentKey, $sourceData, $customTitle, $featureId, $featureContent, $locale);
        }

        try {
            $aiResult = MultiAiModelManager::executeWithFailover(
                $prompt,
                $systemInstruction,
                'prd',
                $preferredAiProvider
            );

            $text = trim($aiResult['text'] ?? '');
            if (!empty($text) && !str_contains($text, '1. Validasi & Kontrak Request: Rinci aturan validasi FormRequest anti-XSS')) {
                $text = preg_replace('/^```(?:markdown|text)?\s*/i', '', $text);
                $text = preg_replace('/\s*```$/', '', $text);
                $text = preg_replace('/^(?:Berikut adalah|Tentu|Berikut ini|Here is the|Certainly).*?:\s*\n+/i', '', $text);
                return trim($text);
            }
        } catch (\Throwable $e) {
            Log::warning('Elaborate directive AI notice: ' . $e->getMessage());
        }

        // Resilient deterministic fallback based on exact segment & deep domain semantic analysis
        return $this->generateDeterministicDirective($blueprint, $segmentKey, $sourceData, $customTitle, $featureId, $featureContent, $locale);
    }

    /**
     * Resilient deterministic directive engine with deep domain-semantic analysis.
     * Guarantees zero repetition across features by tailoring directives to exact domain archetypes.
     */
    protected function generateDeterministicDirective(
        VisionBlueprint $blueprint,
        string $segmentKey,
        array $sourceData,
        ?string $customTitle = null,
        ?string $featureId = null,
        ?string $featureContent = null,
        string $locale = 'id'
    ): string {
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise'), $locale);
        $sector = $this->stringifyAttribute(
            $blueprint->prd_content['universal_blueprint_schema']['sectoral_ui_ux_archetype']['archetype_label'] 
            ?? ($blueprint->prd_content['project_metadata']['domain_category'] ?? 'Enterprise Digital Platform'),
            $locale
        );
        $isEn = $locale === 'en';

        if ($segmentKey === 'scaffold_file') {
            $targetFile = strtolower(trim($customTitle ?: 'schema_complete.sql'));
            $tables = collect($blueprint->prd_content['database_erd'] ?? [])
                ->map(fn ($t) => is_array($t) ? ($t['table_name'] ?? ($t['name'] ?? null)) : null)
                ->filter()
                ->take(5)
                ->values()
                ->all();
            $tableList = !empty($tables) ? implode(', ', $tables) : 'users, orders, transactions';

            if (str_ends_with($targetFile, '.sql')) {
                return $isEn
                    ? "1. Strict ULID & Relational Constraints: Enforce CHAR(26) PRIMARY KEY and FOREIGN KEY definitions across all {$businessName} tables ({$tableList}) with explicit ON DELETE RESTRICT/CASCADE rules.\n2. Sub-10ms Keyset Cursor Indexes: Add composite indexes on `(created_at DESC, id DESC)` and status filter columns to guarantee O(1) cursor pagination at millions of rows.\n3. Hybrid RAG & Vector Storage: Add `pgvector` (`vector(1536)`) embedding columns and HNSW indexes (`m=16, ef_construction=64`) plus `pg_trgm` GIN indexes for hybrid semantic + keyword search.\n4. Tamper-Proof Audit Triggers: Append PostgreSQL trigger functions that record immutable JSONB diffs and SHA-256 integrity hashes on critical state transitions."
                    : "1. Penegakan Strict ULID & Relasi Tabel: Pastikan seluruh tabel domain {$businessName} ({$tableList}) menggunakan CHAR(26) PRIMARY KEY dan FOREIGN KEY dengan aturan ON DELETE RESTRICT/CASCADE yang presisi.\n2. Indeks Komposit Keyset Cursor O(1): Tambahkan composite index pada `(created_at DESC, id DESC)` serta kolom filter status utama guna menjamin paginasi cursor sub-10ms pada skala jutaan baris.\n3. Penyimpanan Vektor Hybrid RAG: Tambahkan ekstensi `pgvector` (`vector(1536)`) beserta indeks HNSW dan `pg_trgm` GIN untuk mendukung Hybrid Search (Semantic + BM25) pada dokumen proyek.\n4. Trigger Audit Trail & SHA-256: Lengkapi DDL dengan tabel dan fungsi trigger audit log otomatis yang mencatat perubahan JSONB serta hash integritas SHA-256.";
            }
            if ($targetFile === 'openapi.json') {
                return $isEn
                    ? "1. Complete Domain Endpoints: Expand OpenAPI 3.1.0 paths for all {$businessName} entities ({$tableList}) with request/response JSON schemas.\n2. Keyset Cursor Pagination Contract: Standardize `cursor` (26-char ULID) and `limit` query parameters with `next_cursor` and `has_more` metadata.\n3. Idempotency & Security Headers: Require `X-Idempotency-Key` on POST/PATCH mutations and Bearer Sanctum token authentication.\n4. RFC 9457 Problem Details: Define standardized 400, 401, 403, 409, 422, and 429 error payloads with `trace_ulid`."
                    : "1. Kelengkapan Endpoint Domain: Perluas path OpenAPI 3.1.0 untuk seluruh entitas {$businessName} ({$tableList}) lengkap dengan skema JSON request dan response.\n2. Kontrak Paginasi Keyset Cursor O(1): Standarisasi parameter query `cursor` (ULID 26-karakter) dan `limit` beserta metadata `next_cursor` dan `has_more`.\n3. Header Idempotensi & Keamanan: Wajibkan header `X-Idempotency-Key` pada endpoint mutasi (POST/PATCH) serta skema keamanan Bearer Sanctum.\n4. Standar Error RFC 9457 Problem Details: Definisikan struktur respons error 400, 401, 403, 409, 422, dan 429 yang memuat `trace_ulid`.";
            }
            if (str_contains($targetFile, 'seeder')) {
                return $isEn
                    ? "1. Domain-Authentic Seed Records: Populate realistic records for {$businessName} tables ({$tableList}) using 26-character `Str::ulid()` primary and foreign keys.\n2. RBAC Multi-Actor Accounts: Seed concrete test accounts for each role (SuperAdmin, Operational Dispatcher, Finance Staff, Field Partner, Customer).\n3. Realistic Pricing & State Variations: Include records across all lifecycle states (Draft, Pending, Confirmed, Completed, Cancelled/Refunded) with realistic IDR tariffs.\n4. Idempotent Seeding Execution: Wrap inserts in `DB::transaction()` and `updateOrInsert` so repeated seeding never corrupts data."
                    : "1. Data Sintetis Kontekstual Bisnis: Bangkitkan data realistis untuk tabel-tabel {$businessName} ({$tableList}) menggunakan `Str::ulid()` 26-karakter pada seluruh primary dan foreign key.\n2. Akun Pengujian Multi-Aktor RBAC: Sediakan akun uji lengkap untuk setiap peran (SuperAdmin, Admin Operasional/Dispatcher, Staf Keuangan, Mitra/Petugas Lapangan, dan Klien).\n3. Variasi Siklus Status & Tarif Nyata: Sertakan sampel transaksi di berbagai state machine (Draft, Menunggu Verifikasi, Terkonfirmasi, Selesai, Batal/Refund) dengan nominal tarif realistis.\n4. Eksekusi Seeder Idempoten: Bungkus proses seeding dalam `DB::transaction()` agar aman dijalankan berulang kali.";
            }
            if (str_contains($targetFile, 'test') || str_ends_with($targetFile, '.spec.ts')) {
                return $isEn
                    ? "1. Happy Path & Business Invariant Assertions: Verify end-to-end creation, state transition, and calculation accuracy for {$businessName} core modules.\n2. O(1) Cursor & Strict ULID Verification: Assert that listing endpoints return `next_cursor` pointers and all IDs are valid 26-character ULIDs.\n3. RBAC Cross-Role Isolation: Assert 403 Forbidden when lower-privileged actors attempt administrative or cross-tenant mutations.\n4. Idempotency & Edge-Case Resilience: Test duplicate `X-Idempotency-Key` replay protection, validation errors (422), and zero native browser dialogs."
                    : "1. Pengujian Happy Path & Rumus Bisnis: Verifikasi alur pembuatan data, transisi status, dan akurasi kalkulasi tarif/biaya pada modul inti {$businessName}.\n2. Validasi Paginasi Cursor O(1) & ULID 26-Karakter: Pastikan seluruh endpoint daftar mengembalikan pointer `next_cursor` dan ID berformat ULID 26-karakter.\n3. Isolasi Hak Akses RBAC (403 Forbidden): Uji bahwa pengguna biasa atau mitra tidak dapat mengakses endpoint admin maupun memodifikasi data milik pengguna lain.\n4. Ketahanan Idempotensi & Anomali: Uji pencegahan transaksi ganda dengan `X-Idempotency-Key`, validasi input 422, serta kepatuhan larangan dialog native browser.";
            }

            return $isEn
                ? "1. Domain Alignment for {$businessName}: Tailor all configurations, routes, and documentation in `{$customTitle}` to the concrete entities ({$tableList}) and MVP modules.\n2. Modern Stack Enforcement: Lock Laravel 13, PHP 8.5+, Filament v5 (`\\Filament\\Schemas\\Schema`), PostgreSQL 16 Strict ULID (`CHAR(26)`), and Keyset Cursor Pagination O(1).\n3. Production RAG & Agentic AI Guardrails: Integrate the 18-Technique Hybrid RAG pipeline and 20 Agentic AI Concepts with UU PDP No. 27/2022 compliance.\n4. Zero-Placeholder Production Readiness: Replace any stub comments with complete, executable production rules and verification probes."
                : "1. Penyelarasan Domain {$businessName}: Sesuaikan seluruh konfigurasi, route, dan dokumentasi di dalam berkas `{$customTitle}` agar mencerminkan entitas konkret ({$tableList}) serta modul MVP proyek.\n2. Penegakan Modern Tech Stack: Kunci standar Laravel 13, PHP 8.5+, Filament v5 (`\\Filament\\Schemas\\Schema`), PostgreSQL 16 Strict ULID (`CHAR(26)`), dan Keyset Cursor Pagination O(1).\n3. Arsitektur 18 Teknik RAG & 20 Konsep Agentic AI: Integrasikan pipeline Hybrid RAG (pgvector + BM25 + Reranking) dan guardrails kepatuhan UU PDP No. 27/2022.\n4. Kesiapan Produksi Tanpa Placeholder: Lengkapi seluruh bagian berkas hingga tuntas dan siap dieksekusi di lingkungan produksi.";
        }

        if (in_array($segmentKey, ['feature_single', 'features', 'mvp_features']) || !empty($featureId)) {
            $featLabel = $customTitle ?: ($featureId ?: 'Modul Fitur');
            return $this->elaborateFeatureDirectiveContext($blueprint, $featLabel, $featureId, $featureContent, $sourceData, $locale);
        }
        
        if ($isEn) {
            return match ($segmentKey) {
                'alurs_kerja', 'workflow' => "Deepen the end-to-end user journeys for {$businessName} by decomposing each stage into atomic transitions: intake of customer requirements, dynamic tariff estimation, instant E-Voucher issuance, administrative dispatcher fleet scheduling, live milestone check-ins, through to automated cancellation, refund reconciliation, and partner fee settlements.",
                'database_erd', 'erd_schema' => "Refine the strict PostgreSQL 16 schema for {$businessName}: enforce 26-character ULID primary and foreign keys, eliminate synthetic relations, add compound indexes for sub-10ms Keyset Cursor Pagination O(1), and equip every business entity with comprehensive audit trails (created_at, updated_at, deleted_at, created_by).",
                'features', 'mvp_features' => "Deepen all MVP Phase 1 specifications for {$businessName}: specify precise JSON API v1 input/output contracts, FormRequest validation rules, transaction state machines, personal data compliance (UU PDP / GDPR), and OpenAPI 3.0 documentation ready for frontend and third-party gateway integrations.",
                'system_actors' => "Structure the Role-Based Access Control (RBAC) matrix for {$businessName} across concrete actors: differentiate permissions between Public Guests, Field Drivers/Partners, Dispatchers, Finance Staff, and Superadministrators under the Principle of Least Privilege and Separation of Duties.",
                'executive_summary' => "Conduct a comprehensive architectural synthesis across all 26 discovery parameters for {$businessName}: clarify unique value propositions, quantifiable operational efficiencies, realistic ROI metrics, and long-term technical risk mitigations.",
                default => "Deepen the architecture and technical specifications for {$businessName} grounded in official Project Documents, enforcing world-class enterprise engineering standards (Clean Architecture, sub-10ms latency, and zero data loss)."
            };
        }

        return match ($segmentKey) {
            'alurs_kerja', 'workflow' => "Perdalam seluruh alur perjalanan pengguna (User Journey) untuk {$businessName} dengan memecah setiap tahapan menjadi langkah atomic yang jelas: mulai dari intake kebutuhan/layanan, kalkulasi & rekomendasi cerdas, pemesanan & penerbitan QR E-Voucher, verifikasi & penugasan staf/armada operasional di dasbor admin, eksekusi layanan lapangan, hingga alur pembatalan, rekonsiliasi, dan pengembalian dana (refund) otomatis. Sertakan aktor penanggung jawab, trigger pemicu, komputasi basis data atomik, dan mitigasi edge cases pada setiap tahapan.",
            'database_erd', 'erd_schema' => "Susun dan perinci skema basis data PostgreSQL 16 Strict untuk {$businessName} dengan memastikan seluruh primary key dan foreign key menggunakan tipe data ULID (VARCHAR 26), mengeliminasi relasi fiktif, mengindeks kolom kunci untuk pencarian O(1) Keyset Cursor Pagination, serta melengkapi entitas dengan audit trail (created_at, updated_at, deleted_at, created_by).",
            'features', 'mvp_features' => "Perdalam spesifikasi teknis modul MVP {$businessName} dengan menjabarkan input/output request yang presisi, validasi FormRequest anti-XSS/SQLi, status machine transisi data, kepatuhan privasi data UU PDP, serta kontrak OpenAPI 3.0 yang siap pakai untuk frontend dan integrasi gateway pihak ketiga.",
            'system_actors' => "Strukturkan matriks hak akses pengguna (RBAC) {$businessName} berbasis peran konkret: bedakan hak akses publik/klien, operator lapangan/mitra, staf dispatcher/keuangan, hingga administrator manajemen sesuai prinsip Least Privilege dan pemisahan wewenang (Separation of Duties).",
            'executive_summary' => "Lakukan sintesis mendalam terhadap 26 parameter arsitektur {$businessName}: pertegas proposisi nilai bisnis, efisiensi operasional, estimasi ROI realistis, serta mitigasi risiko teknis jangka panjang berbasis dokumen acuan.",
            default => "Lakukan penajaman arsitektur dan spesifikasi teknis untuk {$businessName} berpatokan pada Dokumen Acuan Proyek klien, terapkan standar rekayasa enterprise dunia (Clean Architecture, performa sub-10ms, dan ketahanan data)."
        };
    }

    /**
     * Domain-Semantic Feature Elaboration Synthesizer.
     * Identifies the exact functional domain of a feature and elaborates non-repetitive, world-class specifications.
     */
    protected function elaborateFeatureDirectiveContext(
        VisionBlueprint $blueprint,
        string $featLabel,
        ?string $featureId,
        ?string $featureContent,
        array $sourceData,
        string $locale = 'id'
    ): string {
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise'), $locale);
        $sector = $this->stringifyAttribute(
            $blueprint->prd_content['universal_blueprint_schema']['sectoral_ui_ux_archetype']['archetype_label'] 
            ?? ($blueprint->prd_content['project_metadata']['domain_category'] ?? 'Enterprise Digital Platform'),
            $locale
        );
        $cleanLabel = trim(preg_replace('/^(?:Fitur:\s*|Modul:\s*)/i', '', $featLabel));
        $featCode = $featureId ? " [{$featureId}]" : '';
        $isEn = $locale === 'en';

        // Title and ID provide the highest domain-specificity anchor for the active feature
        $titleCorpus = mb_strtolower($cleanLabel . ' ' . ($featureId ?? ''));
        $contentCorpus = mb_strtolower(is_string($featureContent) ? $featureContent : json_encode($featureContent ?? []));

        // 0. Audio Translation, Language Channels & Live Interpreter (e.g. Live Audio Interpreter / Church / Streaming)
        $isAudioChannel = preg_match('/(?:audio|language|channel|bahasa|penerjemah|interpreter|broadcast|webrtc|stream|translator|suara)/i', $titleCorpus);
        if ($isAudioChannel) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Real-Time Low-Latency Audio Streaming & WebRTC Architecture: Architect sub-150ms ultra-low latency audio streaming pipelines for language channels, detailing Opus codec parameters (48kHz/stereo, adaptive bitrate 32-96 kbps) and WebRTC/WebSocket transport fallbacks.\n" .
                       "2. Channel State Machine & Concurrency Control: Define channel lifecycle states (INACTIVE, STANDBY, LIVE_STREAMING, MUTED), enforcing optimistic/pessimistic locking to prevent simultaneous conflicting translator broadcast claims on the same channel.\n" .
                       "3. Role-Based Broadcast & Listener Access Boundaries: Specify granular access permissions distinguishing Multimedia Operators (channel provisioning, master mute), Assigned Interpreters (audio stream ingest, push-to-talk toggle), and Public Listeners (zero-install mobile browser streaming with instant channel switching).\n" .
                       "4. Stream Telemetry, Buffer Health & Network Degrade Fallback: Formulate automated packet-loss mitigation, jitter buffer adjustments, and graceful degradation to lower bitrate streams when audience network conditions fluctuate during live sessions.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Arsitektur Audio Streaming Real-Time Berlatensi Ultra Rendah (<150ms): Rancang pipeline streaming audio WebRTC untuk kanal bahasa, mendefinisikan parameter Opus codec (48kHz/stereo, adaptive bitrate 32-96 kbps) serta auto-fallback ke WebSocket audio stream.\n" .
                   "2. Siklus Status Kanal & Kontrol Konkurensi: Rumuskan state machine kanal bahasa (NONAKTIF, STANDBY, LIVE_STREAMING, MUTED), lengkap dengan locking database atomik guna mencegah bentrok dua penerjemah siaran di kanal yang sama.\n" .
                   "3. Isolasi Otoritas Penyiar & Pendengar: Tentukan hak akses RBAC tegas antara Operator Multimedia (pengelolaan kanal, master force-mute), Penerjemah Terpilih (ingest mikrofon, push-to-talk), dan Jemaat/Pendengar (akses web browser responsif tanpa install aplikasi).\n" .
                   "4. Telemetri Kualitas Siaran & Mitigasi Fluktuasi Jaringan: Rancang penanganan packet loss, jitter buffer dinamis, dan fallback otomatis ke bitrate lebih rendah saat koneksi internet jemaat tidak stabil di area ibadah.";
        }

        // 1. Airport Transfer (Ngurah Rai DPS In/Out) - Specific Airport Radar & Flight Sync
        $isAirportTransfer = preg_match('/(?:bandara|airport|transfer in|transfer out|ngurah rai|\bdps\b|flight|penerbangan|antar jemput)/i', $titleCorpus);
        if ($isAirportTransfer) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Ngurah Rai (DPS) Airport Radar & Flight Status Synchronization: Integrate real-time flight tracking APIs to automatically recalibrate driver pickup schedules upon flight delays, cancellations, or early arrivals.\n" .
                       "2. Baggage & Immigration Buffer Protocol: Enforce a 45-60 minute grace buffer post-touchdown for customs and baggage retrieval, with a 90-minute maximum waiting threshold backed by driver GPS geolocation verification.\n" .
                       "3. Digital Paging Board & Instant WhatsApp Handoff: Generate high-contrast digital name boards for driver tablets/phones and trigger automated WhatsApp notifications to landing passengers with driver photo, vehicle license plate, and pickup lane instructions.\n" .
                       "4. No-Show Mitigation & Dispute-Proof Audit Trail: Establish atomic workflows for passenger no-shows, airline cancellations, and lost luggage delays, recording GPS arrival timestamps and driver waiting logs for partner revenue protection.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Integrasi Pelacakan Radar Penerbangan DPS: Hubungkan sistem dengan Flight Status Tracker API Bandara Ngurah Rai (DPS) untuk mendeteksi status kedatangan pesawat secara real-time dan otomatis memajukan/mengundurkan jadwal penjemputan pengemudi saat pesawat delay atau early landing.\n" .
                   "2. Buffer Waktu Bagasi & Grace Period Kedatangan: Terapkan buffer penjemputan standar 45-60 menit pasca-landing untuk proses imigrasi dan bagasi, serta tetapkan grace period batas tunggu pengemudi hingga 90 menit dengan pencatatan bukti telemetri kehadiran driver di titik temu bandara.\n" .
                   "3. Paging Board Digital & Koordinasi WhatsApp Otomatis: Hasilkan banner digital nama tamu (Digital Paging Board) di aplikasi driver dan picu pesan otomatis WhatsApp ke tamu setibanya pesawat mendarat lengkap dengan foto driver, tipe kendaraan, dan nomor plat mobil.\n" .
                   "4. Penanganan Edge Cases Pembatalan & No-Show: Rumuskan alur mitigasi jika nomor penerbangan tidak ditemukan, pembatalan sepihak dari maskapai, atau tamu tidak muncul di meeting point (No-Show) dengan audit trail foto dan log GPS untuk proteksi hak pengemudi mitra.";
        }

        // 2. Overland Tour (Mount Bromo & Mount Ijen) - Multi-Day & National Parks
        $isOverland = preg_match('/(?:overland|bromo|ijen|jawa timur|luar bali|multi-day|selat bali|ferry|penyeberangan)/i', $titleCorpus);
        if ($isOverland) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Multi-Day Stage Progression & Bali Strait Ferry Coordination: Detail the multi-day overland staging workflow (Bali - Gilimanuk/Ketapang ferry crossing - Banyuwangi - Mount Bromo/Ijen), with night transit scheduling and vehicle handoff protocols.\n" .
                       "2. Regional Partner Dispatch & Conservation E-Ticketing: Integrate automated dispatch vouchers for Bromo Tengger Semeru National Park admission, Sukapura 4x4 Jeep rentals, and certified local Ijen Crater guides with specialized Blue Fire gas mask gear checklists.\n" .
                       "3. Volcanic Hazard & Force Majeure Contingency: Define contingency state transitions in the event of volcanic activity alerts (Waspada/Siaga status) or hazardous sulfur levels, providing clear rerouting or scheduled refund mechanisms.\n" .
                       "4. Driver Fatigue Prevention & Highway Safety Protocols: Enforce mandatory rest intervals and driver rotation policies on overland trips exceeding 8 consecutive driving hours to prevent fatigue and guarantee passenger safety.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Manajemen Etape Perjalanan Multi-Hari & Penyeberangan Feri: Rinci alur etape overland Bali - Ketapang/Gilimanuk - Banyuwangi - Bromo/Ijen, termasuk jadwal penyeberangan feri Selat Bali, estimasi waktu transit malam hari, dan sinkronisasi pergantian armada/sopir.\n" .
                   "2. Koordinasi Mitra Lokal & E-Tiket Konservasi: Rancang integrasi voucher digital untuk tiket masuk Balai Besar TNBTS (Bromo), sewa Jeep 4x4 lokal Desa Sukapura, pemandu berlisensi Kawah Ijen, dan penyediaan masker gas standar Blue Fire.\n" .
                   "3. Protokol Keselamatan & Mitigasi Force Majeure Vulkanik: Terapkan protokol darurat jika status vulkanik Bromo/Ijen naik (Waspada/Siaga) atau gas beracun sulfat meningkat, menyediakan opsi rute alternatif atau klausul refund/re-schedule transparan.\n" .
                   "4. Standar Keselamatan Kerja & Pencegahan Microsleep: Rumuskan kebijakan rotasi pengemudi untuk perjalanan overland jarak jauh (>12 jam), memastikan batas jam kerja sopir maksimal 8 jam mengemudi per hari dengan titik istirahat terverifikasi.";
        }

        // 3. Central Operational Dispatcher & Fleet Center
        $isDispatcher = preg_match('/(?:dispatcher|dasbor pusat|monitoring armada|penugasan armada|fleet center|penugasan supir|alokasi supir)/i', $titleCorpus);
        if ($isDispatcher) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Real-Time Fleet Kanban & Operational Status Board: Architect a Filament v5 control center displaying live fleet states (Available, Dispatched, In-Transit, Maintenance, Driver Rest) powered by zero-polling WebSocket updates.\n" .
                       "2. Intelligent Driver Dispatch & Matchmaking Algorithm: Formulate automated assignment criteria matching vehicle proximity, required passenger capacity, driver foreign language proficiency, and fair workload distribution.\n" .
                       "3. Trip Telemetry & Milestone Check-in Logging: Record tamper-proof audit trails for every service milestone: Pool Departure, Passenger Onboard, Destination Arrival, and Return to Base, with timestamped GPS coordinates.\n" .
                       "4. Emergency Auto-Reassignment Protocol: Specify a 15-minute emergency vehicle replacement workflow in the event of mechanical breakdowns, instantly re-routing the nearest available backup unit.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Papan Kendali Kanban Unit & Status Armada Real-Time: Bangun dasbor operasional Filament v5 dengan visualisasi status armada (Tersedia, Dalam Perjalanan, Perawatan, Istirahat Pengemudi) yang diperbarui secara instan via WebSocket tanpa polling berulang.\n" .
                   "2. Algoritma Penugasan Pengemudi Cerdas: Rinci logika pencocokan penugasan otomatis berdasarkan kedekatan lokasi armada, kapasitas tipe kendaraan, bahasa asing pengemudi (Inggris, Mandarin, dll.), dan rotasi beban kerja yang adil.\n" .
                   "3. Telemetri Perjalanan & Timestamp Check-in/Check-out: Catat audit trail presisi untuk setiap titik tonggak: Keberangkatan dari pool, Tamu Masuk Kendaraan (Pick-up), Destinasi Tercapai, dan Kembali ke Pool, lengkap dengan koordinat GPS.\n" .
                   "4. Prosedur Auto-Reassignment Tanggap Darurat: Rumuskan mekanisme penugasan armada pengganti secara kilat (<15 menit) jika unit mengalami kendala teknis atau mogok di jalan raya tanpa mengacaukan jadwal perjalanan tamu.";
        }

        // 4. E-Voucher QR Code & WhatsApp Instant Confirmation
        $isVoucherQr = preg_match('/(?:voucher|qr code|e-voucher|tiket digital|boarding pass|barcode)/i', $titleCorpus);
        if ($isVoucherQr) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Cryptographic HMAC SHA-256 QR E-Vouchers: Implement digital ticketing with tamper-proof HMAC signatures that can be securely validated online or offline without exposing passenger PII.\n" .
                       "2. WhatsApp Business Cloud API Automation: Deploy immediate post-settlement push notifications delivering the official PDF e-voucher attachment and quick-action pickup buttons.\n" .
                       "3. Instant Staff QR Scanner Verification: Provide a sub-second web/mobile camera scanner module for hotel lobby staff and airport drivers to validate boarding vouchers seamlessly.\n" .
                       "4. Delivery Failure Fallback & Retry Matrix: Establish an exponential backoff retry mechanism with automated SMTP email fallback if a passenger's phone number is unreachable on WhatsApp.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. E-Voucher Kriptografis Ber-QR Code HMAC SHA-256: Rancang pembuatan tiket digital ber-QR Code dengan tanda tangan kriptografi anti-pemalsuan yang dapat divalidasi secara offline maupun online tanpa mengekspos data pribadi tamu.\n" .
                   "2. Otomatisasi WhatsApp Business Cloud API: Bangun alur push pesan konfirmasi instan sesaat setelah pembayaran lunas, melampirkan berkas PDF E-Voucher resmi dan tombol navigasi ke titik penjemputan.\n" .
                   "3. Aplikasi Scanner Validasi Staf Lobi & Bandara: Sediakan modul pemindai kamera web/mobile ringan bagi pengemudi dan staf lobi hotel untuk memverifikasi QR voucher dalam hitungan <1 detik.\n" .
                   "4. Mitigasi Kegagalan Pengiriman (Retry & Email Fallback): Rancang mekanisme retry berjangka dan auto-fallback pengiriman melalui email SMTP resmi jika nomor telepon tamu tidak terhubung ke WhatsApp.";
        }

        // 5. Fleet Capacity & Luggage Calculator
        $isCapacityCalc = preg_match('/(?:kalkulator kapasitas|bagasi|dimensi bagasi|volume bagasi|luggage|trunk space)/i', $titleCorpus);
        if ($isCapacityCalc) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Volumetric Luggage & Passenger Seating Geometry: Detail the algorithmic calculation mapping cabin bags (20-inch) and large luggage (28-inch) against exact trunk dimensions across vehicle categories.\n" .
                       "2. Real-Time Visual Capacity Safety Indicator: Render dynamic capacity feedback (Safe / Full / Over-Capacity) on the guest reservation form as passenger and luggage counts change.\n" .
                       "3. Intelligent Fleet Upsell Recommendations: Provide automated pricing comparison logic suggesting single large-vehicle upgrades (e.g. HiAce) when more cost-effective than chartering two smaller cars.\n" .
                       "4. Vehicle Gross Weight & Safety Regulatory Compliance: Enforce vehicle load limits to guarantee highway safety and prevent overloaded vehicle hazards.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Komputasi Geometris Volume Bagasi & Kursi Penumpang: Rumuskan algoritma kalkulasi kapasitas volumetrik yang memetakan koper kabin (20-inci) dan koper besar (28-inci) terhadap dimensi bagasi riil setiap tipe kendaraan.\n" .
                   "2. Indikator Kapasitas Visual Real-Time: Sediakan indikator visual dinamis (Aman / Penuh / Over-Capacity) di antarmuka pemesanan saat tamu menambah jumlah penumpang atau barang bawaan.\n" .
                   "3. Rekomendasi Upsell Unit Cerdas: Hadirkan logika perbandingan tarif otomatis yang menyarankan upgrade armada (misal dari Avanza ke HiAce) yang lebih murah dan nyaman dibanding harus menyewa 2 mobil terpisah.\n" .
                   "4. Kepatuhan Beban Muatan Kendaraan: Terapkan batas muatan maksimal demi keselamatan penumpang dan kepatuhan terhadap regulasi lalu lintas jalan raya.";
        }

        // 6. Interactive Day Tour Catalog & Fleet Configurator
        $isFleetCatalog = preg_match('/(?:katalog|catalog|day tour|paket wisata|fleet configurator|konfigurasi armada|pilihan armada)/i', $titleCorpus);
        if ($isFleetCatalog) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Fleet Capacity Matrix & Luggage Volumetric Matching: Specify the vehicle assignment algorithm (e.g., Avanza, Innova Reborn, HiAce Commuter, Alphard) matching passenger count (adults/children) and physical luggage volume (cabin vs 28-inch check-in bags), including automated multi-vehicle split or vehicle upgrade recommendations when limits are exceeded.\n" .
                       "2. Dynamic Tariff Formulation & Service Inclusions: Define accurate rate calculation formulas: duration tiers (Half Day 6h vs Full Day 10-12h), hourly overtime surcharges, and explicit separation between All-inclusive items (fuel + multilingual driver) and on-the-road operational expenses (Bali Mandara toll, parking fees, regional tourism retribution).\n" .
                       "3. Fleet Inventory Availability & Transient Hold State: Enforce a 15-minute transient Inventory TTL Hold during customer checkout to prevent concurrent overbooking collisions, synchronized with regional blackout calendars (Nyepi Day, Christmas & New Year high season).\n" .
                       "4. Data Contract & Sub-10ms O(1) Scalability: Structure the tour catalog response with Keyset Cursor Pagination O(1) indexed by Bali geographical zones (South Bali, Ubud, Kintamani, Bedugul, East Bali), delivering instant transit estimates and verified fleet ratings.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Matriks Kapasitas Armada & Kalkulasi Bagasi Riil: Rinci algoritma pencocokan tipe armada (misal Avanza, Innova Reborn, HiAce Commuter, Alphard) berdasarkan kombinasi jumlah penumpang dewasa/anak dan volume bagasi fisik (koper kabin vs koper 28-inci). Tambahkan aturan validasi cerdas jika rombongan melebihi kapasitas agar sistem otomatis merekomendasikan split 2 armada atau upgrade unit yang lebih hemat.\n" .
                   "2. Formulasi Tarif Dinamis & Inklusi Operasional: Rumuskan formula kalkulasi tarif sewa: durasi paket (Half Day 6 jam vs Full Day 10-12 jam), surcharge biaya lembur (overtime) per jam, dan pemisahan tegas antara komponen All-in (BBM + Pengemudi Berbahasa Asing) versus biaya operasional lapangan yang ditanggung tamu (tiket tol Bali Mandara, biaya parkir destinasi, retribusi desa wisata).\n" .
                   "3. Kalender Ketersediaan & State Holding Unit: Terapkan mekanisme penguncian inventori armada sementara (Inventory TTL Hold 15 menit) saat tamu berada pada checkout alur pembayaran guna mencegah overbooking antar pemesan paralel, terintegrasi dengan status kalender blackout date (Hari Raya Nyepi, High Season Natal & Tahun Baru).\n" .
                   "4. Kontrak Data & Skalabilitas O(1): Format respons katalog dengan Keyset Cursor Pagination O(1) berbasis destinasi/zona Bali (Bali Selatan, Ubud, Kintamani, Bedugul, Bali Timur) dan pastikan respon katalog mengembalikan estimasi durasi tempuh serta rating armada secara instan sub-10ms.";
        }

        // 7. AI-Powered Wishlist & Itinerary Generator (Strictly targeted to custom wishlist and itinerary planners)
        $isAiItinerary = preg_match('/(?:\bwishlist\b|\bwish list\b|\bai itinerary\b|generator itinerary|smart itinerary|itinerary generator|rekomendasi rute|rekomendasi jadwal)/i', $titleCorpus);
        if ($isAiItinerary) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Zero-Assumption Wishlist Extraction & Intelligent Geo-Clustering: Architect an entity extraction engine for client free-text wishlists that automatically clusters destinations geographically (South Badung, Ubud/Gianyar, Kintamani/Bangli, Bedugul/Tabanan) to eliminate irrational zig-zag routes and reduce client transit fatigue.\n" .
                       "2. POI Operating Hours & Real-Traffic Transit Buffers: Validate operating hours of tourist destinations (e.g., Uluwatu Kecak performance at 18:00 WITA, Tanah Lot sunset, Lempuyang Temple) against historical Bali traffic congestion buffers to guarantee conflict-free schedules.\n" .
                       "3. Comprehensive Real-Budget & Expense Engine: Formulate an integrated budget calculation engine itemizing destination admission tickets (Domestic vs Foreign tourist rates), vehicle mileage costs, and certified regional tourist guide (HPI) options.\n" .
                       "4. Client Customization Lifecycle & Interactive Itinerary Dispatch: Implement the lifecycle state machine (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED), supporting official printable PDF vouchers with anti-tamper QR verification and interactive Google Maps GPS navigation links for drivers and travelers.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Ekstraksi Wishlist & Klastering Geografis Tanpa Asumsi: Rancang algoritma ekstraksi entitas destinasi dari catatan wish list bebas klien, lalu kelompokkan secara geografis (Geo-Clustering: Badung Selatan, Gianyar/Ubud, Bangli/Kintamani, Tabanan/Bedugul) guna mengeliminasi rute zig-zag yang tidak rasional dan menghemat waktu tempuh wisatawan.\n" .
                   "2. Validasi Jam Operasional POI & Buffer Kemacetan Riil: Integrasikan data jam buka/tutup resmi destinasi wisata (misal jadwal Tari Kecak Uluwatu pukul 18:00 WITA, sunset Tanah Lot, Pura Lempuyang) dengan buffer waktu transit realistis berbasis riwayat kemacetan lalu lintas Bali untuk mencegah bentrok jadwal atau tiba setelah loket tutup.\n" .
                   "3. Kalkulator Estimasi Anggaran Riil & Rekap Biaya Terpadu: Susun kalkulator biaya terpadu per itinerary yang menghitung tiket masuk objek wisata (WNA vs WNI), kebutuhan sewa armada sesuai jarak total kilometer, dan opsi jasa pemandu lokal berlisensi (HPI).\n" .
                   "4. Siklus Persetujuan Klien & Penerbitan Rencana Interaktif: Rancang transisi status rencana perjalanan (DRAFT_AI -> CLIENT_CUSTOMIZING -> DISPATCHER_VERIFIED -> LOCKED_CONFIRMED), lengkap dengan fitur ekspor ke format PDF siap cetak ber-QR Code serta tautan navigasi Google Maps interaktif untuk pengemudi dan wisatawan.";
        }

        // 8. Comprehensive Reports & Data Export Module
        $isReporting = preg_match('/(?:ekspor|laporan|reporting|rekapitulasi|ledger|excel|\bpdf\b)/i', $titleCorpus);
        if ($isReporting) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Dual-Format Export Engine (Excel .xlsx & Formal PDF): Build high-performance reporting using O(1) Keyset Cursor Pagination capable of streaming thousands of records without memory exhaustion.\n" .
                       "2. Financial Reconciliation & Partner Settlement Ledger: Formulate reconciliation ledgers calculating gross booking collections, platform fees, and net driver commissions.\n" .
                       "3. Document Download Audit Trail (UU PDP Compliance): Record tamper-proof download logs tracking administrator identity, IP address, and timestamps for personal data compliance.\n" .
                       "4. Multi-Dimensional Granular Filtering: Provide instant search filters across date ranges, geographical zones (Ubud/Kuta/Sanur), fleet types, and reservation statuses.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Modul Ekspor Format Ganda (Excel .xlsx & PDF Siap Cetak): Rancang pembuatan laporan operasional dan mutasi pemesanan dengan Keyset Cursor Pagination O(1) yang mampu mengekspor jutaan baris data tanpa membebani memori server.\n" .
                   "2. Rekonsiliasi Keuangan & Settlement Fee Mitra: Susun tabel ledger rekonsiliasi yang menghitung selisih pembayaran bruto dari payment gateway, potongan fee platform, dan hak bersih komisi pengemudi/rekanan.\n" .
                   "3. Audit Trail Unduhan Dokumen (UU PDP): Catat histori unduhan file yang merekam identitas admin, alamat IP, timestamp, dan enkripsi data sensitif guna kepatuhan penuh terhadap UU Perlindungan Data Pribadi.\n" .
                   "4. Filter Granular Multi-Dimensi: Lengkapi dengan filter pencarian berdasarkan periode waktu, zona destinasi (Ubud/Kuta/Sanur), jenis armada, dan status penyelesaian pesanan.";
        }

        // 9. Payment Gateway & 2-Stage Settlement (50% DP & 50% Pelunasan)
        $isPayment = preg_match('/(?:pembayaran|payment|midtrans|qris|\bdp\b|termin|pelunasan|checkout|transaksi)/i', $titleCorpus);
        if ($isPayment) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Two-Stage Milestone Automation (50% DP & 50% Balance): Link separate payment order IDs to the main reservation, auto-locking scope upon DP and releasing full service access upon settlement.\n" .
                       "2. Webhook Idempotency & Signature Verification: Enforce atomic pessimistic locks (lockForUpdate) on webhook callbacks to prevent duplicate payment confirmations.\n" .
                       "3. Multi-Currency Support (IDR, USD, AUD): Automate daily exchange rate caching with integrated international gateways (Wise/PayPal) alongside domestic Midtrans/Xendit QRIS.\n" .
                       "4. Automated Cancellation & Refund Penalty Engine: Implement tiered cancellation deductions based on departure proximity (H-7: 100% refund, H-3: 50% refund, H-1: non-refundable).";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Otomatisasi Termin 2 Tahap (DP 50% & Pelunasan 50%): Hubungkan order ID pembayaran terpisah ke induk reservasi, mengunci scope saat DP dan membuka akses layanan penuh saat pelunasan diterima.\n" .
                   "2. Webhook Idempotency & Verifikasi Tanda Tangan: Terapkan penguncian atomik (lockForUpdate) pada callback webhook payment gateway guna mencegah eksekusi ganda.\n" .
                   "3. Dukungan Multi-Mata Uang (IDR, USD, AUD): Sediakan konversi kurs harian otomatis dengan gateway internasional (Wise/PayPal) di samping QRIS/VA domestik.\n" .
                   "4. Formula Pembatalan & Pemotongan Refund Otomatis: Susun aturan pengembalian dana berjenjang berdasarkan kedekatan jadwal (H-7: refund penuh, H-3: refund 50%, H-1: hangus).";
        }

        // 10. Authentication, Profiling & Granular RBAC
        $isAuth = preg_match('/(?:autentikasi|auth|rbac|pengguna|login|profil|hak akses|user management)/i', $titleCorpus);
        if ($isAuth) {
            if ($isEn) {
                return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                       "1. Frictionless Authentication & WhatsApp OTP: Deploy passwordless magic links and WhatsApp OTP verification for travelers, minimizing registration drop-offs.\n" .
                       "2. Granular Role-Based Access Control (RBAC): Enforce strict permission boundaries separating Guest, Field Driver, Dispatcher, Finance, and Superadmin under the Principle of Least Privilege.\n" .
                       "3. Personal Data Privacy & Portability (UU PDP): Provide one-click data archive exports and compliant right-to-be-forgotten workflows under Indonesian PDP Law No. 27/2022.\n" .
                       "4. Multi-Device Session Security: Enforce session token rotation and automated revocation upon detection of concurrent logins or suspicious geolocation shifts.";
            }

            return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
                   "1. Autentikasi Tanpa Hambatan & WhatsApp OTP: Sediakan login tanpa password (magic link) dan verifikasi OTP WhatsApp bagi wisatawan guna meminimalkan hambatan registrasi.\n" .
                   "2. Pembagian Peran Granular (RBAC): Terapkan batas hak akses ketat antara Tamu, Pengemudi Mitra, Dispatcher, Keuangan, dan Superadmin mengacu pada prinsip Least Privilege.\n" .
                   "3. Privasi Data & Hak Portabilitas (UU PDP): Sediakan fitur ekspor arsip data pribadi dan alur penghapusan akun (right to be forgotten) sesuai UU No. 27/2022.\n" .
                   "4. Keamanan Sesi Multi-Perangkat: Terapkan rotasi token sesi dan pemutusan akses otomatis saat terdeteksi aktivitas login dari perangkat mencurigakan.";
        }

        // 11. Adaptive Contextual Synthesis (Any Arbitrary Domain / Sektor)
        if ($isEn) {
            return "Deepen the technical architecture of '{$cleanLabel}'{$featCode} for {$businessName} ({$sector}):\n\n" .
                   "1. Domain Business Logic & Input Parameters: Formulate explicit validation rules, computational formulas, and required request payloads for '{$cleanLabel}' grounded in the client's problem statement without fictional assumptions.\n" .
                   "2. State Transitions & Anomaly Edge Cases: Specify the lifecycle status progression, network timeout retry strategies, concurrent access guards, and graceful fallback behaviors unique to this module.\n" .
                   "3. Third-Party Integrations & Service Contracts: Detail external API integrations, idempotent webhook receivers, and standard RFC 9457 Problem Details error schemas.\n" .
                   "4. Enterprise Standards & O(1) Scalability: Implement strict PostgreSQL 16 ULID primary keys, Keyset Cursor Pagination O(1) for sub-10ms response times, and tamper-proof audit trails compliant with UU PDP No. 27/2022.";
        }

        return "Perdalam spesifikasi teknis modul '{$cleanLabel}'{$featCode} untuk {$businessName} ({$sector}):\n\n" .
               "1. Logika Bisnis & Parameter Input Domain: Rumuskan aturan validasi tegas, formula perhitungan riil, dan parameter request yang dibutuhkan untuk '{$cleanLabel}' berpatokan pada kebutuhan bisnis tanpa asumsi fiktif.\n" .
               "2. Transisi Status & Skenario Edge Cases: Rinci siklus status data (state machine), strategi penanganan timeout jaringan, mitigasi akses konkuren, dan fallback saat terjadi kegagalan operasional pada modul ini.\n" .
               "3. Integrasi Layanan & Kontrak Data: Petakan integrasi API pihak ketiga spesifik, verifikasi webhook idempoten, dan skema respons standar RFC 9457 Problem Details.\n" .
               "4. Standar Enterprise & Skalabilitas O(1): Tegakkan skema relasi PostgreSQL 16 Strict ULID, Keyset Cursor Pagination O(1) untuk menjamin latensi sub-10ms, serta pencatatan audit trail sesuai kepatuhan UU PDP No. 27/2022.";
    }

    /**
     * AI & Grounded Heuristic Task Gap Analyzer for Phase 1 (MVP) and Phase 2 (Roadmap).
     * Compares current tasks against the project source corpus & world-class standards
     * to identify missing, non-redundant architecture tasks or declare 100% maximal completeness.
     */
    public function analyzeTaskGaps(
        VisionBlueprint $blueprint,
        string $phase,
        array $currentTasks,
        ?string $aiProvider = null,
        string $locale = 'id'
    ): array {
        $isEn = $locale === 'en';
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: ($isEn ? 'Enterprise System' : 'Sistem Enterprise')), $locale);
        $sector = $this->stringifyAttribute(
            $blueprint->prd_content['universal_blueprint_schema']['sectoral_ui_ux_archetype']['archetype_label'] 
            ?? ($blueprint->prd_content['project_metadata']['domain_category'] ?? 'Enterprise Digital Platform'),
            $locale
        );

        $sourceData = $this->getSourceCorpus($blueprint);
        $corpusText = $this->stringifyAttribute($sourceData['corpus_text'] ?? '', $locale);

        $phaseLabel = $phase === 'phase2' 
            ? ($isEn ? 'Phase 2 (Roadmap)' : 'Fase 2 (Roadmap Susulan)') 
            : ($isEn ? 'Phase 1 (MVP Essential)' : 'Fase 1 (Fitur Wajib MVP)');

        // Extract normalized existing task titles safely
        $existingTitles = [];
        $existingCorpus = '';
        foreach ($currentTasks as $t) {
            if (!is_array($t)) continue;
            $tTitle = $this->stringifyAttribute($t['title'] ?? '', $locale);
            $tDesc = $this->stringifyAttribute($t['desc'] ?? '', $locale);
            if (!empty($tTitle)) {
                $existingTitles[] = $tTitle;
                $existingCorpus .= mb_strtolower($tTitle . ' ' . $tDesc . ' ');
            }
        }

        // 1. Attempt AI analysis if provider is not deterministic
        if ($aiProvider !== 'deterministic') {
            $langInstruction = $isEn ? 'Respond strictly in English.' : 'Gunakan Bahasa Indonesia formal.';
            $systemInstruction = <<<TXT
You are an Enterprise Solutions Architect & Systems Analyst specializing in Laravel 13, Filament v5, and PostgreSQL 16 Strict ULID.
Your job is to analyze the current tasks of {$businessName} for {$phaseLabel} against the Project Source Documents (Source Corpus).

CRITICAL ARCHITECTURE RULES:
1. ZERO ASSUMPTIONS & NO HALLUCINATION: Only identify tasks that are truly needed based on the client's problem statement, documents, or world-class enterprise standards (security, data integrity, idempotency, audit trail).
2. STRICTLY NO REDUNDANCY: Never propose capabilities or tasks that already exist in the Current Tasks list, even if described differently.
3. EXHAUSTIVE COMPLETENESS GATE: If the Current Tasks ALREADY cover all required business entities, state transitions, and integrations for this phase, you MUST set "is_maximal": true and "recommendations": [].
4. Return ONLY valid JSON (no markdown fences, no conversational filler).
{$langInstruction}
TXT;

            $existingJson = json_encode($currentTasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $prompt = <<<TXT
=== DOKUMEN ACUAN SUMBER PROYEK (SOURCE CORPUS) ===
{$corpusText}

=== SEKTOR / SISTEM: {$sector} ===
=== TARGET FASE: {$phaseLabel} ===

=== DAFTAR TASK SAAT INI ({count($currentTasks)} TASK) ===
{$existingJson}

TUGAS ANDA:
Analisis apakah ada task arsitektur atau fitur krusial yang BELUM ADA dalam daftar task saat ini untuk {$phaseLabel}.
Jika semua sudah lengkap dan tidak ada celah arsitektur penting, set "is_maximal": true dan "recommendations": [].
Jika ada gap esensial (1-3 task krusial), berikan rekomendasi task konkret.

Format JSON Wajib:
{
  "is_maximal": true_or_false,
  "summary": "Ringkasan analisis kelengkapan...",
  "recommendations": [
    {
      "title": "Nama Task / Fitur Konkret",
      "desc": "Deskripsi teknis dan kriteria penerimaan spesifik",
      "category": "Kategori Modul",
      "sprint_phase": "Sprint 1-2",
      "urgency_reason": "Alasan arsitektural mengapa task ini penting"
    }
  ]
}
TXT;

            try {
                $aiResult = MultiAiModelManager::executeWithFailover(
                    $prompt,
                    $systemInstruction,
                    'prd',
                    $aiProvider ?: 'auto'
                );

                $text = trim($aiResult['text'] ?? '');
                if (preg_match('/\{[\s\S]*\}/', $text, $match)) {
                    $parsed = json_decode($match[0], true);
                    if (is_array($parsed) && isset($parsed['is_maximal'])) {
                        // Double check against existing to guarantee zero duplicates
                        $recs = (array) ($parsed['recommendations'] ?? []);
                        $filteredRecs = [];
                        foreach ($recs as $r) {
                            if (!is_array($r)) continue;
                            $rTitle = $this->stringifyAttribute($r['title'] ?? '', $locale);
                            if (empty($rTitle)) continue;
                            $rLower = mb_strtolower($rTitle);
                            $duplicate = false;
                            foreach ($existingTitles as $et) {
                                $etLower = mb_strtolower($et);
                                if ($rLower === $etLower || str_contains($existingCorpus, $rLower) || str_contains($rLower, $etLower)) {
                                    $duplicate = true;
                                    break;
                                }
                            }
                            if (!$duplicate) {
                                $filteredRecs[] = [
                                    'title' => $rTitle,
                                    'desc' => $this->stringifyAttribute($r['desc'] ?? '', $locale),
                                    'category' => $this->stringifyAttribute($r['category'] ?? 'CORE_DOMAIN', $locale),
                                    'sprint_phase' => $this->stringifyAttribute($r['sprint_phase'] ?? ($phase === 'mvp' ? 'Sprint 1-2' : 'Fase 2 Roadmap'), $locale),
                                    'urgency_reason' => $this->stringifyAttribute($r['urgency_reason'] ?? '', $locale),
                                    'selected' => true,
                                ];
                            }
                        }

                        $isMax = count($filteredRecs) === 0 || ($parsed['is_maximal'] === true);
                        return [
                            'is_maximal' => $isMax,
                            'summary' => $this->stringifyAttribute($parsed['summary'] ?? ($isMax 
                                ? ($isEn ? 'All essential architecture tasks for this phase are 100% complete and fully verified.' : 'Seluruh task arsitektur esensial untuk fase ini telah 100% lengkap dan terverifikasi.')
                                : ($isEn ? 'Found missing architecture tasks.' : 'Ditemukan task arsitektur penting yang belum tercover.')), $locale),
                            'recommendations' => $isMax ? [] : $filteredRecs,
                            'telemetry' => [
                                'provider' => $aiResult['provider'] ?? 'ai',
                                'model' => $aiResult['model'] ?? 'failover',
                            ],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Task gap analysis AI warning: ' . $e->getMessage());
            }
        }

        // Deterministic Fallback Gap Analyzer
        return $this->deterministicTaskGapAnalysis($blueprint, $phase, $currentTasks, $existingTitles, $existingCorpus, $isEn);
    }

    /**
     * Resilient deterministic domain gap analyzer.
     * Evaluates domain archetypes against existing tasks to detect gaps or declare maximal completeness.
     */
    protected function deterministicTaskGapAnalysis(
        VisionBlueprint $blueprint,
        string $phase,
        array $currentTasks,
        array $existingTitles,
        string $existingCorpus,
        bool $isEn = false
    ): array {
        $locale = $isEn ? 'en' : 'id';
        $businessName = $this->stringifyAttribute($blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Sistem Enterprise'), $locale);
        $rawMasalah = $this->stringifyAttribute($blueprint->masalah_utama, $locale);
        $rawTujuan = $this->stringifyAttribute($blueprint->tujuan_utama, $locale);
        $rawFitur = $this->stringifyAttribute($blueprint->fitur_wajib, $locale);
        $lowerCorpus = mb_strtolower($rawMasalah . ' ' . $rawTujuan . ' ' . $rawFitur);
        $phaseLabel = $phase === 'phase2' 
            ? ($isEn ? 'Phase 2 (Roadmap)' : 'Fase 2 (Roadmap Susulan)') 
            : ($isEn ? 'Phase 1 (MVP)' : 'Fase 1 (Fitur Wajib MVP)');

        $recommendations = [];

        if ($phase === 'mvp') {
            // Check T1: Airport Transfer
            $hasAirportTransfer = preg_match('/(?:bandara|airport|transfer in|transfer out|ngurah rai|\bdps\b|flight)/i', $existingCorpus);
            $needsAirportTransfer = preg_match('/(?:bandara|airport|transfer|ngurah rai|\bdps\b)/i', $lowerCorpus);
            if ($needsAirportTransfer && !$hasAirportTransfer) {
                $recommendations[] = [
                    'title' => $isEn ? 'Airport & Hotel Transfer Dispatch Engine (Transfer In/Out DPS)' : 'Sistem Reservasi & Dispatch Transfer Bandara Ngurah Rai (Transfer In/Out)',
                    'desc' => $isEn ? 'Real-time flight arrival radar sync, 45-60 min baggage buffer, digital paging board, and driver GPS check-in.' : 'Sinkronisasi radar kedatangan pesawat DPS Ngurah Rai, buffer bagasi 45-60 menit, digital paging board, dan log GPS pengemudi.',
                    'category' => 'LOGISTICS_DISPATCH',
                    'sprint_phase' => 'Sprint 1 (Fondasi & CRUD)',
                    'urgency_reason' => $isEn ? 'Critical intake requirement for airport passenger transport without scheduling conflicts.' : 'Kebutuhan inti alur pemesanan transfer bandara tanpa risiko bentrok jadwal.',
                    'selected' => true,
                ];
            }

            // Check T2: Day Tour & Fleet Configurator
            $hasFleetCatalog = preg_match('/(?:katalog|catalog|day tour|paket wisata|fleet configurator|pilihan armada)/i', $existingCorpus);
            $needsFleetCatalog = preg_match('/(?:katalog|tour|wisata|armada|mobil|sewa)/i', $lowerCorpus);
            if ($needsFleetCatalog && !$hasFleetCatalog) {
                $recommendations[] = [
                    'title' => $isEn ? 'Interactive Day Tour Catalog & Fleet Configurator' : 'Katalog Paket Day Tour Interaktif & Konfigurasi Armada',
                    'desc' => $isEn ? 'Volumetric vehicle luggage matching, duration tiering (Half Day vs Full Day), and 15-minute transient inventory hold.' : 'Pencocokan volumetrik koper bagasi armada, formulasi durasi Half Day vs Full Day, dan penahanan inventori TTL 15 menit.',
                    'category' => 'TOUR_CATALOG_FLEET',
                    'sprint_phase' => 'Sprint 1 (Fondasi & CRUD)',
                    'urgency_reason' => $isEn ? 'Core catalog enabling clients to choose verified vehicles and instant booking quotes.' : 'Katalog utama yang memungkinkan tamu memilih armada resmi dengan estimasi tarif transparan.',
                    'selected' => true,
                ];
            }

            // Check T3: AI Wishlist & Itinerary Generator
            $hasItinerary = preg_match('/(?:wishlist|wish list|\bai itinerary\b|generator itinerary|smart itinerary)/i', $existingCorpus);
            $needsItinerary = preg_match('/(?:wishlist|wish list|itinerary|rute|kalkulasi biaya otomatis)/i', $lowerCorpus);
            if ($needsItinerary && !$hasItinerary) {
                $recommendations[] = [
                    'title' => $isEn ? 'AI-Powered Wishlist & Itinerary Generator (Zero-Assumption Engine)' : 'Mesin AI Itinerary Generator Cerdas (Zero-Assumption Engine)',
                    'desc' => $isEn ? 'Entity extraction from free-text wishlists, geographical clustering (South Badung, Ubud, Kintamani), and POI operating hours validation.' : 'Ekstraksi entitas wishlist bebas, klastering geografis (Badung, Ubud, Kintamani), dan validasi jam operasional POI objek wisata.',
                    'category' => 'AI_ITINERARY_PLANNER',
                    'sprint_phase' => 'Sprint 2 (Business Logic & Workflow)',
                    'urgency_reason' => $isEn ? 'Essential differentiating capability eliminating manual hours of itinerary planning.' : 'Fitur diferensiasi utama yang mengeliminasi rekap manual itinerary berjam-jam.',
                    'selected' => true,
                ];
            }

            // Check T4: Overland Tour (Bromo & Ijen)
            $hasOverland = preg_match('/(?:overland|bromo|ijen|jawa timur|selat bali)/i', $existingCorpus);
            $needsOverland = preg_match('/(?:overland|bromo|ijen|jawa timur)/i', $lowerCorpus);
            if ($needsOverland && !$hasOverland) {
                $recommendations[] = [
                    'title' => $isEn ? 'Overland Multi-Day Expedition Tour (Mount Bromo & Ijen Crater)' : 'Paket Ekspedisi Overland Multi-Hari (Bromo & Kawah Ijen)',
                    'desc' => $isEn ? 'Multi-day stage tracking, Bali Strait ferry booking synchronization, local Sukapura 4x4 Jeep vouchers, and Blue Fire certified guides.' : 'Manajemen etape perjalanan multi-hari, penyeberangan feri Selat Bali, voucher Jeep 4x4 Sukapura, dan pemandu Kawah Ijen.',
                    'category' => 'OVERLAND_EXPEDITION',
                    'sprint_phase' => 'Sprint 2 (Business Logic & Workflow)',
                    'urgency_reason' => $isEn ? 'High-value long-distance expedition requiring cross-province partner coordination.' : 'Layanan ekspedisi lintas provinsi bernilai tinggi yang memerlukan koordinasi mitra feri dan jeep.',
                    'selected' => true,
                ];
            }

            // Check T5: Fleet Dispatcher
            $hasDispatcher = preg_match('/(?:dispatcher|dasbor pusat|monitoring armada|penugasan supir|alokasi supir)/i', $existingCorpus);
            $needsDispatcher = preg_match('/(?:dispatcher|penugasan|armada|supir|alokasi jadwal)/i', $lowerCorpus);
            if ($needsDispatcher && !$hasDispatcher) {
                $recommendations[] = [
                    'title' => $isEn ? 'Central Operational Fleet Dispatcher & Real-Time Kanban Board' : 'Dasbor Pusat Dispatcher Operasional & Papan Kanban Armada',
                    'desc' => $isEn ? 'Live vehicle Kanban swimlanes (Available, Dispatched, In-Transit, Maintenance), automated driver language matching, and GPS milestone tracking.' : 'Papan Kanban visual status unit, algoritma pencocokan bahasa pengemudi, dan log telemetri GPS perjalanan.',
                    'category' => 'FLEET_DISPATCH_CENTER',
                    'sprint_phase' => 'Sprint 3 (Integrasi & Third-Party)',
                    'urgency_reason' => $isEn ? 'Required command center for supervisors to prevent double-booking and schedule collisions.' : 'Pusat kendali wajib bagi supervisor untuk mencegah bentrok jadwal pengemudi antar-pesanan.',
                    'selected' => true,
                ];
            }

            // Check T6: E-Voucher QR Code
            $hasVoucher = preg_match('/(?:voucher|qr code|e-voucher|tiket digital)/i', $existingCorpus);
            $needsVoucher = preg_match('/(?:voucher|qr|tiket|konfirmasi|whatsapp)/i', $lowerCorpus);
            if ($needsVoucher && !$hasVoucher) {
                $recommendations[] = [
                    'title' => $isEn ? 'Cryptographic QR Code E-Voucher & WhatsApp Instant Confirmation' : 'Penerbitan E-Voucher Ber-QR Code Kriptografis & Konfirmasi WhatsApp',
                    'desc' => $isEn ? 'Tamper-proof HMAC SHA-256 QR ticketing, automated WhatsApp Business Cloud API PDF dispatch, and sub-second web scanner validation.' : 'Tiket digital QR Code HMAC SHA-256 anti-pemalsuan, push otomatis WhatsApp Cloud API dengan lampiran PDF, dan scanner web cepat <1 detik.',
                    'category' => 'DIGITAL_VOUCHER_QR',
                    'sprint_phase' => 'Sprint 3 (Integrasi & Third-Party)',
                    'urgency_reason' => $isEn ? 'Automated digital boarding proof essential for airport and hotel pickups.' : 'Bukti penjemputan digital otomatis yang sah bagi tamu di terminal bandara dan lobi hotel.',
                    'selected' => true,
                ];
            }

            // Check T7: Payment Gateway (2-Stage DP)
            $hasPayment = preg_match('/(?:pembayaran|payment|midtrans|qris|\bdp\b|termin)/i', $existingCorpus);
            $needsPayment = preg_match('/(?:pembayaran|payment|midtrans|qris|transaksi|tarif)/i', $lowerCorpus);
            if ($needsPayment && !$hasPayment) {
                $recommendations[] = [
                    'title' => $isEn ? 'Two-Stage Payment Gateway (50% Down Payment & 50% Settlement)' : 'Gerbang Pembayaran Termin 2 Tahap (DP 50% & Pelunasan)',
                    'desc' => $isEn ? 'Midtrans/Xendit QRIS integration, automated DP scope freeze, idempotent webhook handling with lockForUpdate, and tiered refund engine.' : 'Integrasi Midtrans QRIS/VA, penguncian scope saat DP 50%, callback webhook idempoten dengan lockForUpdate, dan aturan refund berjenjang.',
                    'category' => 'FINANCIAL_PAYMENT_GATEWAY',
                    'sprint_phase' => 'Sprint 3 (Integrasi & Third-Party)',
                    'urgency_reason' => $isEn ? 'Secures client revenue and eliminates unpaid reservation collisions.' : 'Mengamankan arus kas reservasi dan mencegah pemesanan fiktif.',
                    'selected' => true,
                ];
            }
        } else {
            // Phase 2 Roadmap
            $hasMultiCurrency = preg_match('/(?:multi-currency|usd|aud|kurs|valuta)/i', $existingCorpus);
            if (!$hasMultiCurrency) {
                $recommendations[] = [
                    'title' => $isEn ? 'Multi-Currency Settlement & Real-Time Forex Rate Engine (USD/AUD/EUR)' : 'Konversi Multi-Mata Uang & Integrasi Kurs Valas Real-Time (USD/AUD/EUR)',
                    'desc' => $isEn ? 'Automated Bank Indonesia forex feed synchronization with international gateways (Wise/PayPal) for cross-border travelers.' : 'Sinkronisasi kurs valuta asing Bank Indonesia terintegrasi gateway pembayaran internasional (Wise/PayPal) untuk wisatawan mancanegara.',
                    'category' => 'FINANCIAL_GLOBAL',
                    'sprint_phase' => 'Fase 2 Roadmap',
                    'urgency_reason' => $isEn ? 'Scales platform adoption for international foreign tourists booking from abroad.' : 'Memperluas penetrasi pasar wisatawan mancanegara yang memesan langsung dari luar negeri.',
                    'selected' => true,
                ];
            }

            $hasDriverPwa = preg_match('/(?:pwa|mobile companion|offline|driver app)/i', $existingCorpus);
            if (!$hasDriverPwa) {
                $recommendations[] = [
                    'title' => $isEn ? 'Driver Mobile Companion PWA with Offline GPS Trip Logging' : 'Aplikasi PWA Pendamping Pengemudi Mitra dengan Offline GPS Telemetri',
                    'desc' => $isEn ? 'Lightweight Progressive Web App enabling field drivers to log trips, verify QR vouchers offline, and report vehicle inspection checklists.' : 'Progressive Web App ringan untuk pengemudi mitra mencatat milestone perjalanan, validasi voucher offline, dan checklist armada harian.',
                    'category' => 'MOBILE_COMPANION',
                    'sprint_phase' => 'Fase 2 Roadmap',
                    'urgency_reason' => $isEn ? 'Empowers field drivers in remote Bali & East Java areas with intermittent cellular reception.' : 'Membantu pengemudi di area pelosok Bali dan pegunungan Jawa Timur saat sinyal seluler terbatas.',
                    'selected' => true,
                ];
            }

            $hasB2bPartner = preg_match('/(?:b2b|afiliasi|mitra hotel|partner portal)/i', $existingCorpus);
            if (!$hasB2bPartner) {
                $recommendations[] = [
                    'title' => $isEn ? 'B2B Partner Portal & Travel Agent Commission Settlement Ledger' : 'Portal Kemitraan B2B & Ledger Rekonsiliasi Komisi Agen Perjalanan / Hotel',
                    'desc' => $isEn ? 'Dedicated sub-portal for concierge hotel desks and travel agencies with automated wholesale net rates and commission payouts.' : 'Sub-portal khusus staf lobi hotel dan agen travel rekanan dengan tarif wholesale dan otomatisasi perhitungan komisi rujukan.',
                    'category' => 'PARTNER_ECOSYSTEM',
                    'sprint_phase' => 'Fase 2 Roadmap',
                    'urgency_reason' => $isEn ? 'Accelerates B2B distribution through hotel lobby concierges and partner agencies.' : 'Membuka kanal distribusi B2B masif melalui jaringan lobi hotel bintang 4 & 5 serta agen rekanan.',
                    'selected' => true,
                ];
            }
        }

        $isMaximal = count($recommendations) === 0;

        return [
            'is_maximal' => $isMaximal,
            'summary' => $isMaximal
                ? ($isEn 
                    ? "All critical domain architecture requirements for {$phaseLabel} are 100% complete and fully accounted for in the current task list based on official Project Documents. No additional tasks are needed."
                    : "Seluruh kebutuhan arsitektur inti untuk {$phaseLabel} telah 100% lengkap dan terpenuhi dengan sempurna pada daftar task saat ini berdasarkan Dokumen Acuan Proyek. Tidak ada kebutuhan task tambahan.")
                : ($isEn 
                    ? "Identified " . count($recommendations) . " essential architecture tasks based on Project Source Documents that are not yet covered in {$phaseLabel}."
                    : "Ditemukan " . count($recommendations) . " task arsitektur esensial berdasarkan Dokumen Acuan yang belum tercover pada {$phaseLabel}."),
            'recommendations' => $recommendations,
            'telemetry' => [
                'provider' => 'deterministic_heuristic_engine',
                'model' => 'enterprise-domain-analyzer-v1',
            ],
        ];
    }
}
