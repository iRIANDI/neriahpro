<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Support\PosSectorUiArchetypeCatalog;

/**
 * BrownfieldMigrationAuditorService
 *
 * Killer Feature #4: Reverse-Engineer / Brownfield Schema & Migration Importer.
 * Audits legacy SQL DDL, Laravel Migrations, or Prisma schemas against Project OS v2.5
 * standards and produces a Zero-Data-Loss Refactoring Blueprint + AI Refactor Prompt.
 */
class BrownfieldMigrationAuditorService
{
    public function auditSchema(string $rawSchema, ?string $projectHint = null, bool $recordAudit = true): array
    {
        $trimmed = trim($rawSchema);
        if ($trimmed === '') {
            return [
                'success' => false,
                'error' => 'Input schema/migrasi tidak boleh kosong.',
            ];
        }

        $tables = $this->extractTableNames($trimmed);
        $violations = [];

        // Scan 1: Primary Key AUTO_INCREMENT / SERIAL / $table->id()
        if (preg_match('/\b(AUTO_INCREMENT|BIGSERIAL|SMALLSERIAL|SERIAL)\b|\$table->(?:big)?[Ii]ncrements\s*\(|\$table->id\s*\(|@default\s*\(\s*autoincrement\s*\(\s*\)\s*\)/i', $trimmed)) {
            $violations[] = [
                'code' => 'V01_AUTO_INCREMENT_PK',
                'severity' => 'CRITICAL',
                'layer' => 'DATABASE_PK',
                'title' => 'Terdeteksi Primary Key AUTO_INCREMENT / SERIAL / $table->id()',
                'impact' => 'Rawan enumerasi IDOR, bottleneck insert terdistribusi, dan gagal merge multi-region.',
                'remediation' => 'Gunakan Dual-Column Shadow Backfill: tambahkan kolom ulid CHAR(26), isi ULID terurut waktu untuk data lama, migrasikan foreign key ke foreignUlid(), lalu promosikan menjadi PRIMARY KEY.',
            ];
        }

        // Scan 2: PostgreSQL UUID mismatch for HasUlids
        if (preg_match('/\$table->uuid\s*\(\s*[\'"]id[\'"]\s*\)/i', $trimmed)) {
            $violations[] = [
                'code' => 'V02_UUID_ULID_MISMATCH',
                'severity' => 'CRITICAL',
                'layer' => 'POSTGRESQL_STRICT',
                'title' => 'Terdeteksi $table->uuid(\'id\') yang Menolak ULID 26-Karakter di PostgreSQL',
                'impact' => 'Memicu fatal error SQLSTATE[22P02] Invalid text representation pada PostgreSQL 16.',
                'remediation' => 'Ubah menjadi $table->ulid(\'id\')->primary() (CHAR(26)/VARCHAR(26)) dan pasang trait HasUlids.',
            ];
        }

        // Scan 3: Blind ON DELETE CASCADE
        if (preg_match('/ON\s+DELETE\s+CASCADE|->cascadeOnDelete\s*\(/i', $trimmed)) {
            $violations[] = [
                'code' => 'V03_BLIND_CASCADE_DELETE',
                'severity' => 'HIGH',
                'layer' => 'DATA_LIFECYCLE',
                'title' => 'Terdeteksi ON DELETE CASCADE / cascadeOnDelete() Berbahaya',
                'impact' => 'Penghapusan akun/master dapat memusnahkan histori transaksi finansial & jejak audit UU PDP / pajak.',
                'remediation' => 'Ganti seluruh FK transaksional menjadi ON DELETE RESTRICT dan terapkan kebijakan IMMUTABLE_LEDGER atau ANONYMIZE_PII ([REDACTED_USER_<ULID>]).',
            ];
        }

        // Scan 4: Float/Double financial columns
        if (preg_match('/\b(FLOAT|DOUBLE\s+PRECISION|REAL)\b|\$table->(?:float|double)\s*\(/i', $trimmed)) {
            $violations[] = [
                'code' => 'V04_FLOAT_PRECISION_DRIFT',
                'severity' => 'CRITICAL',
                'layer' => 'FINANCIAL_MATH',
                'title' => 'Terdeteksi Tipe Data FLOAT / DOUBLE untuk Nilai Numerik / Finansial',
                'impact' => 'Menyebabkan selisih pembulatan IEEE-754 (penny drift) pada saldo buku besar, pajak PPN, dan stok.',
                'remediation' => 'Konversi menggunakan ALTER TABLE ... TYPE NUMERIC(19,4) dengan aturan pembulatan HALF_UP dan Largest-Remainder Penny Allocation.',
            ];
        }

        // Scan 5: Missing Multi-Tenant Scope
        if (! preg_match('/\b(organization_id|tenant_id|workspace_id|company_id)\b/i', $trimmed)) {
            $violations[] = [
                'code' => 'V05_MISSING_TENANT_ISOLATION',
                'severity' => 'HIGH',
                'layer' => 'MULTI_TENANCY',
                'title' => 'Belum Ada Kolom Isolasi Multi-Tenant (organization_id)',
                'impact' => 'Sulit di-scale menjadi B2B SaaS multi-cabang/multi-organisasi dan berisiko kebocoran data lintas tenant.',
                'remediation' => 'Tambahkan foreignUlid(\'organization_id\')->index() beserta Global Tenant Scope pada tabel domain bisnis.',
            ];
        }

        // Scan 6: Missing Concurrency / Idempotency Guards
        if (! preg_match('/\b(idempotency_key|lockForUpdate|FOR\s+UPDATE|EXCLUDE\s+USING\s+gist)\b/i', $trimmed)) {
            $violations[] = [
                'code' => 'V06_NO_CONCURRENCY_IDEMPOTENCY',
                'severity' => 'HIGH',
                'layer' => 'CONCURRENCY',
                'title' => 'Tidak Ditemukan Idempotency Key atau Pessimistic Lock (FOR UPDATE)',
                'impact' => 'Rentan double-spending, stok minus saat flash-sale, dan double-booking jadwal.',
                'remediation' => 'Tambahkan kolom UNIQUE idempotency_key pada tabel mutasi/order dan bungkus perubahan state di dalam DB::transaction() + lockForUpdate().',
            ];
        }

        // Scan 7: Missing Audit Log, Outbox, and Webhook Vault tables
        $lowerTables = array_map('strtolower', $tables);
        $missingInfraTables = [];
        foreach (['audit_logs', 'outbox_messages', 'webhook_inbound_logs'] as $reqTbl) {
            if (! in_array($reqTbl, $lowerTables, true) && ! stripos($trimmed, $reqTbl)) {
                $missingInfraTables[] = $reqTbl;
            }
        }
        if (! empty($missingInfraTables)) {
            $violations[] = [
                'code' => 'V07_MISSING_RELIABILITY_TABLES',
                'severity' => 'MEDIUM',
                'layer' => 'RESILIENCE_AUDIT',
                'title' => 'Tabel Reliabilitas & Jejak Audit Belum Lengkap (' . implode(', ', $missingInfraTables) . ')',
                'impact' => 'Tidak ada jaminan pengiriman event terdistribusi (Transactional Outbox), verifikasi replay webhook HMAC-SHA256, atau jejak forensik.',
                'remediation' => 'Sertakan migrasi standar Project OS untuk tabel ' . implode(', ', $missingInfraTables) . ' dengan PK ULID.',
            ];
        }

        // Scan 8: Offset Pagination
        if (preg_match('/->paginate\s*\(|\bOFFSET\s+\d+/i', $trimmed)) {
            $violations[] = [
                'code' => 'V08_OFFSET_PAGINATION_O_N',
                'severity' => 'MEDIUM',
                'layer' => 'SCALABILITY_O1',
                'title' => 'Terdeteksi Offset Pagination (->paginate() / OFFSET) Berkompleksitas O(N)',
                'impact' => 'Query melambat drastis saat tabel melampaui ratusan ribu hingga jutaan baris.',
                'remediation' => 'Ganti seluruh ->paginate() dengan ->orderBy(\'id\', \'desc\')->cursorPaginate() berkompleksitas O(1).',
            ];
        }

        // Resolve closest Sector & Primitives from schema corpus
        $corpus = trim(($projectHint ?? '') . "\n" . implode(' ', $tables) . "\n" . mb_substr($trimmed, 0, 4000));
        $resolved = ProjectOsBlueprintCompilerService::resolveSectorAndPrimitives($corpus);
        /** @var \App\Models\PosSector $sector */
        $sector = $resolved['sector'];
        $primitives = $resolved['primitives'];
        $uiArchetype = PosSectorUiArchetypeCatalog::forSector((string) $sector->code);

        $penalty = 0;
        foreach ($violations as $v) {
            $penalty += match ($v['severity']) {
                'CRITICAL' => 20,
                'HIGH' => 12,
                default => 7,
            };
        }
        $score = max(10, 100 - $penalty);
        $grade = $score >= 85 ? 'ENTERPRISE_READY' : ($score >= 55 ? 'NEEDS_REFACTOR' : 'CRITICAL_BROWNFIELD_DEBT');

        $migrationSteps = [
            'Tahap 1 (Shadow ULID Column — Zero Downtime): Tambahkan kolom `ulid_id CHAR(26) NULL` pada tabel eksisting (' . (empty($tables) ? 'seluruh tabel domain' : implode(', ', array_slice($tables, 0, 6))) . ') dan backfill menggunakan ULID berbasis timestamp `created_at` agar urutan kronologis terjaga.',
            'Tahap 2 (Dual-Write & Foreign Key Mapping): Tambahkan kolom `*_ulid CHAR(26)` pada tabel relasi anak, sinkronkan via JOIN batch per 5.000 baris, lalu pasang `ON DELETE RESTRICT` (hapus seluruh `ON DELETE CASCADE`).',
            'Tahap 3 (Precision & Governance Upgrade): Konversi seluruh kolom nominal/kuantitas ke `DECIMAL(19,4)`, tambahkan `organization_id` untuk multi-tenancy, serta buat tabel `audit_logs`, `outbox_messages`, dan `webhook_inbound_logs`.',
            'Tahap 4 (Cutover Primary Key & UI Archetype): Promosikan `ulid_id` menjadi `id` PRIMARY KEY, aktifkan Keyset Cursor Pagination `O(1)`, dan bangun ulang antarmuka operasional mengikuti archetype `' . ($uiArchetype['primary_archetype'] ?? 'POS_SPLIT_TERMINAL') . '`.',
        ];

        $refactorPrompt = $this->buildAiRefactorPrompt($sector->code, $sector->localizedName('id'), $uiArchetype, $primitives->pluck('code')->values()->all(), $tables, $violations, $migrationSteps);

        $result = [
            'success' => true,
            'readiness_score' => $score,
            'grade' => $grade,
            'detected_tables' => $tables,
            'detected_tables_count' => count($tables),
            'violations_count' => count($violations),
            'violations' => $violations,
            'recommended_sector' => [
                'code' => $sector->code,
                'name' => $sector->localizedName('id'),
                'primary_ui_archetype' => $uiArchetype['primary_archetype'] ?? '',
                'secondary_ui_archetype' => $uiArchetype['secondary_archetype'] ?? '',
            ],
            'recommended_primitives' => $primitives->map(fn ($p) => [
                'code' => $p->code,
                'name' => is_array($p->name) ? ($p->name['id'] ?? $p->code) : (string) $p->name,
            ])->values()->all(),
            'zero_data_loss_migration_plan' => $migrationSteps,
            'ai_refactor_prompt' => $refactorPrompt,
        ];

        if ($recordAudit) {
            AuditLog::record(
                action: 'brownfield_schema_audited',
                newValues: [
                    'tables_count' => count($tables),
                    'violations_count' => count($violations),
                    'readiness_score' => $score,
                    'recommended_sector' => $sector->code,
                ],
                description: "Brownfield Schema Importer memeriksa " . count($tables) . " tabel (Skor: {$score}/100, Sektor: {$sector->code}).",
                eventCategory: 'brownfield_import'
            );
        }

        return $result;
    }

    protected function extractTableNames(string $raw): array
    {
        $names = [];

        if (preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:["`\[]?\w+["`\]]?\.)?["`\[]?([a-zA-Z0-9_]+)["`\]]?/i', $raw, $m1)) {
            foreach ($m1[1] as $t) {
                $names[] = $t;
            }
        }

        if (preg_match_all('/Schema::create\s*\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/i', $raw, $m2)) {
            foreach ($m2[1] as $t) {
                $names[] = $t;
            }
        }

        if (preg_match_all('/^\s*model\s+([A-Za-z0-9_]+)\s*\{/m', $raw, $m3)) {
            foreach ($m3[1] as $t) {
                $names[] = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $t)) . 's';
            }
        }

        return array_values(array_unique(array_filter($names)));
    }

    protected function buildAiRefactorPrompt(
        string $sectorCode,
        string $sectorLabel,
        array $uiArchetype,
        array $primitiveCodes,
        array $tables,
        array $violations,
        array $steps
    ): string {
        $md = "# PROJECT OS BROWNFIELD ZERO-DATA-LOSS REFACTORING DIRECTIVE\n\n";
        $md .= "Anda adalah Principal Software Architect. Tugas Anda merombak skema legacy proyek ini ke standar **Project OS v2.5** tanpa kehilangan satu baris data pun.\n\n";
        $md .= "- **Target Sektor Fungsional:** `{$sectorCode}` ({$sectorLabel})\n";
        $md .= "- **UI/UX Screen Archetype Wajib:** `{$uiArchetype['primary_archetype']}` & `{$uiArchetype['secondary_archetype']}`\n";
        $md .= "- **Core Primitives Aktif:** `" . implode('`, `', $primitiveCodes) . "`\n";
        $md .= "- **Tabel Eksisting Terdeteksi:** " . (empty($tables) ? '*(Lihat skema terlampir)*' : '`' . implode('`, `', $tables) . '`') . "\n\n";

        $md .= "## 1. Pelanggaran Arsitektur yang Wajib Dihapus\n";
        foreach ($violations as $idx => $v) {
            $num = $idx + 1;
            $md .= "{$num}. **[{$v['severity']}] `{$v['code']}` — {$v['title']}**\n";
            $md .= "   - *Solusi Wajib:* {$v['remediation']}\n";
        }

        $md .= "\n## 2. Urutan Migrasi Zero-Data-Loss\n";
        foreach ($steps as $s) {
            $md .= "- {$s}\n";
        }

        $md .= "\n## 3. Aturan Mutlak Anti-Slop\n";
        $md .= "- DILARANG menggunakan `\$table->id()`, `\$table->uuid('id')`, `ON DELETE CASCADE`, `->paginate()`, `rounded-full`, `bg-gradient-to-*`, atau `window.alert()`.\n";
        $md .= "- WAJIB menulis Pest test untuk memverifikasi integritas migrasi sebelum mengubah kode UI.\n";

        return $md;
    }
}

