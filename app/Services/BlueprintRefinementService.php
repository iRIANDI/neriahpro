<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use App\Services\Ai\MultiAiModelManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlueprintRefinementService
{
    /**
     * Retrieve the grounded source corpus for a blueprint.
     * Looks for synthesized_corpus.md, individual converted markdown files, or synthesizes from model attributes.
     */
    public function getSourceCorpus(VisionBlueprint $blueprint): array
    {
        $slug = $blueprint->slug ?: Str::slug($blueprint->nama_bisnis);
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
            $fallbackCorpus .= "Nama Bisnis: " . ($blueprint->nama_bisnis ?: 'N/A') . "\n";
            $fallbackCorpus .= "Klien: " . ($blueprint->client_name ?: 'N/A') . "\n";
            $fallbackCorpus .= "Target Waktu: " . ($blueprint->target_waktu ?: '30 Hari Kerja') . "\n\n";

            if ($blueprint->masalah_utama) {
                $fallbackCorpus .= "## 1. Masalah Utama & Latar Belakang\n" . $blueprint->masalah_utama . "\n\n";
            }
            if ($blueprint->tujuan_utama) {
                $fallbackCorpus .= "## 2. Tujuan Utama & Indikator Keberhasilan\n" . $blueprint->tujuan_utama . "\n\n";
            }
            if ($blueprint->target_audiens) {
                $fallbackCorpus .= "## 3. Target Audiens & Pengguna\n" . $blueprint->target_audiens . "\n\n";
            }
            if ($blueprint->aktor_sistem) {
                $fallbackCorpus .= "## 4. Pengguna & Hak Akses (Aktor / RBAC)\n" . $blueprint->aktor_sistem . "\n\n";
            }
            if ($blueprint->fitur_wajib) {
                $fallbackCorpus .= "## 5. Fitur Wajib (MVP Fase 1)\n" . $blueprint->fitur_wajib . "\n\n";
            }
            if ($blueprint->fitur_tambahan) {
                $fallbackCorpus .= "## 6. Fitur Tambahan (Fase 2 / Roadmap)\n" . $blueprint->fitur_tambahan . "\n\n";
            }
            if ($blueprint->alur_kerja) {
                $fallbackCorpus .= "## 7. Alur Kerja Sistem (Workflow & Business Logic)\n" . $blueprint->alur_kerja . "\n\n";
            }
            if ($blueprint->kebutuhan_integrasi) {
                $fallbackCorpus .= "## 8. Kebutuhan Integrasi API & Layanan Eksternal\n" . $blueprint->kebutuhan_integrasi . "\n\n";
            }
            if (!empty($meta['target_platform'])) {
                $fallbackCorpus .= "Target Platform: " . $meta['target_platform'] . "\n";
            }
            if (!empty($meta['skala_pengguna'])) {
                $fallbackCorpus .= "Skala Pengguna: " . $meta['skala_pengguna'] . "\n";
            }
            if (!empty($meta['kepatuhan_keamanan'])) {
                $fallbackCorpus .= "Kepatuhan Keamanan: " . $meta['kepatuhan_keamanan'] . "\n";
            }
            if (!empty($meta['out_of_scope'])) {
                $fallbackCorpus .= "Out of Scope: " . $meta['out_of_scope'] . "\n";
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

        // Execute via MultiAiModelManager with automatic failover
        $aiResult = MultiAiModelManager::executeWithFailover(
            $prompt,
            $systemInstruction,
            'discovery',
            $preferredAiProvider
        );

        $rawAiText = $aiResult['text'] ?? null;
        if (empty($rawAiText)) {
            throw new \RuntimeException(
                $aiResult['notification'] ?? 'Gagal menghubungi AI Model. Silakan coba kembali atau ganti provider.'
            );
        }

        // Clean JSON markdown fences
        $cleaned = trim($rawAiText);
        if (str_starts_with($cleaned, '```json')) {
            $cleaned = substr($cleaned, 7);
        } elseif (str_starts_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 3);
        }
        if (str_ends_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 0, -3);
        }
        $cleaned = trim($cleaned);

        $parsed = json_decode($cleaned, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
            Log::warning("AI Refinement returned non-JSON string for segment {$segmentKey}: " . substr($cleaned, 0, 200));
            // If the segment accepts raw text/markdown (like custom_ai_agent_rules or executive_summary), accept it
            if (in_array($segmentKey, ['custom_ai_agent_rules', 'executive_summary_text'])) {
                $parsed = $cleaned;
            } else {
                throw new \RuntimeException('AI mengembalikan format yang tidak valid (bukan JSON terstruktur). Silakan ulangi.');
            }
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

        // Save to database
        $blueprint->update(['prd_content' => $updatedPrd]);

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
            'message' => 'Segmen ' . strtoupper(str_replace('_', ' ', $segmentKey)) . ' berhasil diperdalam berdasarkan Dokumen Acuan.',
            'segment_key' => $segmentKey,
            'refined_data' => $parsed,
            'ai_telemetry' => [
                'provider' => $aiResult['provider'] ?? 'ai',
                'model' => $aiResult['model'] ?? 'default',
                'fallback_occurred' => $aiResult['fallback_occurred'] ?? false,
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
        $userPromptDirective = !empty($userInstruction) 
            ? "PETUNJUK PENAJAMAN DARI PENGGUNA:\n{$userInstruction}\n\n" 
            : "PETUNJUK: Perdalam secara otomatis dan komprehensif berdasarkan Dokumen Acuan di atas.\n\n";

        // Segment 1: FEATURES (Entire List or Single Feature)
        if ($segmentKey === 'feature_single') {
            $featureTitle = $existingData['title'] ?? 'Fitur';
            $systemInstruction = <<<TXT
Anda adalah Principal Software Architect di Neriah Pro.
Tugas Anda: Memperdalam SATU SPESIFIKASI FITUR TERTENTU berdasarkan DOKUMEN ACUAN PROYEK (MarkItDown Corpus / Spesifikasi Klien).
ATURAN KETAT:
1. Gali seluruh aturan bisnis, formula hitungan, edge cases, dan kondisi pengecualian yang tertulis dalam Dokumen Acuan untuk fitur ini.
2. Skenario Gherkin HARUS sangat spesifik dan realistis (Given/When/Then), minimal 3 skenario (Happy Path, Validation Error, dan Edge Case/Security Path).
3. Acceptance Criteria harus dapat diuji secara objektif (Pest PHP / Playwright).
4. Jangan gunakan kata-kata generik atau placeholder kosong.
5. Pertahankan ID Fitur, tier ('mvp'), dan index yang sama.
6. Kembalikan HANYA objek JSON valid (tanpa wrapper ```json).
TXT;

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
  "user_story": "Sebagai ..., saya ingin ... sehingga ...",
  "acceptance_criteria": ["...", "..."],
  "frontend": { "component": "...", "interactions": ["..."] },
  "backend": { "controller": "...", "service": "...", "validation_rules": { "field": "rules" } },
  "integration": { "third_party": "...", "webhook": "..." },
  "code_agent_directive": "Instruksi teknis langsung ke AI Coding Agent..."
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
    "acceptance_criteria": ["...", "..."],
    "frontend": { "component": "...", "interactions": ["..."] },
    "backend": { "controller": "...", "service": "...", "validation_rules": {} },
    "integration": { "third_party": "...", "webhook": "..." },
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
4. Gali seluruh entitas data dari dokumen acuan (tabel transaksi, riwayat audit, pivot matriks, approval tier, snapshot biaya).
5. Sertakan DDL SQL PostgreSQL yang valid dan siap dieksekusi.
6. Kembalikan HANYA array JSON valid dari objek-objek tabel.
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
3. Kembalikan HANYA array JSON valid.
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
    "action": "Aksi yang dilakukan...",
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

            foreach ($specs as $idx => $spec) {
                if (($featureId && (($spec['id'] ?? '') === $featureId || ($spec['feature_id'] ?? '') === $featureId)) ||
                    ($specIndex !== null && $idx === (int) $specIndex)) {
                    $specs[$idx] = array_merge($spec, is_array($parsedData) ? $parsedData : []);
                    $found = true;
                    break;
                }
            }

            if (!$found && is_array($parsedData)) {
                $specs[] = $parsedData;
            }

            $prd['engineering_specs']['mvp_specs'] = $specs;
            $prd['features']['mvp_phase1'] = $specs;

            // Re-render feature dependency diagram if available
            try {
                $actors = $prd['system_actors'] ?? [];
                $erdTables = $prd['erd_schema']['tables'] ?? [];
                $prd['virtual_charts']['feature_dependency_mermaid'] = PrdGeneratorService::generateFeatureDependencyMermaid($actors, $specs, $erdTables);
            } catch (\Throwable $e) {}

            return $prd;
        }

        if (in_array($segmentKey, ['features', 'feature_specifications', 'mvp_features'])) {
            $featuresList = is_array($parsedData) ? $parsedData : [];
            $prd['engineering_specs']['mvp_specs'] = $featuresList;
            $prd['features']['mvp_phase1'] = $featuresList;

            // Re-render feature dependency diagram
            try {
                $actors = $prd['system_actors'] ?? [];
                $erdTables = $prd['erd_schema']['tables'] ?? [];
                $prd['virtual_charts']['feature_dependency_mermaid'] = PrdGeneratorService::generateFeatureDependencyMermaid($actors, $featuresList, $erdTables);
            } catch (\Throwable $e) {}

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
}
