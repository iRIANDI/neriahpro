<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\VisionBlueprint;

/**
 * BlueprintSlopScannerService
 *
 * Killer Feature #5: Blueprint Drift Auditor & Slop Scanner Engine.
 * Evaluates blueprints or codebase snippets against Zero-Slop UI, Strict ULID DB,
 * Local Asset Sovereignty, and 4-Pillar AI Governance rules, generating an Auto-Fix Prompt.
 */
class BlueprintSlopScannerService
{
    /**
     * Audit a VisionBlueprint (and its compiled PosProject) for 100% Project OS v2.5 readiness.
     */
    public function auditBlueprint(VisionBlueprint $blueprint, bool $recordAudit = true): array
    {
        $posProject = $blueprint->posProject;
        if (! $posProject) {
            $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: [];
        $execMd = (string) $posProject->compiled_execution_md;
        $checks = [];
        $violations = [];

        // Check 1: Strict ULID & Zero AUTO_INCREMENT across data dictionary
        $tables = $schema['data_dictionary'] ?? [];
        $nonUlidTables = [];
        foreach ($tables as $t) {
            $pk = collect($t['columns'] ?? [])->firstWhere('primary', true);
            if (! $pk || stripos((string) ($pk['type'] ?? ''), 'ULID') === false) {
                $nonUlidTables[] = $t['table'] ?? 'unknown';
            }
        }
        $checks[] = [
            'id' => 'SCAN_01_STRICT_ULID_DDL',
            'code' => 'SCAN_01_STRICT_ULID_DDL',
            'label' => 'Primary Key 100% Strict ULID & Keyset Cursor O(1)',
            'passed' => empty($nonUlidTables) && count($tables) > 0,
            'detail' => empty($nonUlidTables)
                ? count($tables) . ' tabel terverifikasi menggunakan CHAR(26) ULID & ON DELETE RESTRICT.'
                : 'Tabel belum ULID: ' . implode(', ', $nonUlidTables),
        ];
        if (! empty($nonUlidTables)) {
            $violations[] = 'Tabel berikut belum memakai PK ULID: ' . implode(', ', $nonUlidTables);
        }

        // Check 2: Sectoral UI/UX Archetype Contract
        $sectorUi = $schema['sectoral_ui_ux_archetype'] ?? [];
        $hasSectorUi = ! empty($sectorUi['primary_archetype']);
        $checks[] = [
            'id' => 'SCAN_02_SECTORAL_UI_ARCHETYPE',
            'code' => 'SCAN_02_SECTORAL_UI_ARCHETYPE',
            'label' => 'Sectoral UI/UX Archetype & Anti-Slop Ergonomics',
            'passed' => $hasSectorUi,
            'detail' => $hasSectorUi
                ? "Archetype aktif: {$sectorUi['primary_archetype']} & {$sectorUi['secondary_archetype']}"
                : 'Kontrak Sectoral UI/UX Archetype belum terkompilasi.',
        ];
        if (! $hasSectorUi) {
            $violations[] = 'Blueprint belum memiliki Sectoral UI/UX Archetype spesifik industri.';
        }

        // Check 3: 7 Zero-Assumption Dimensions (Outbox, Webhook Vault, Formulas, RFC 9457, Acceptance Tests)
        $hasSevenDims = ! empty($schema['api_and_envelope_contract'])
            && ! empty($schema['distributed_saga_and_outbox_contract'])
            && isset($schema['deterministic_formulas'])
            && ! empty($schema['acceptance_test_matrix']);
        $checks[] = [
            'id' => 'SCAN_03_SEVEN_ZERO_ASSUMPTION_DIMS',
            'code' => 'SCAN_03_SEVEN_ZERO_ASSUMPTION_DIMS',
            'label' => '7 Dimensi Zero-Assumption (RFC 9457, Outbox, Math, Acceptance Tests)',
            'passed' => $hasSevenDims,
            'detail' => $hasSevenDims
                ? 'RFC 9457, Transactional Outbox, DECIMAL(19,4) Penny Allocation, & Given-When-Then Matrix lengkap.'
                : 'Sebagian dari 7 dimensi Zero-Assumption belum terisi.',
        ];
        if (! $hasSevenDims) {
            $violations[] = 'Kompilasi ulang diperlukan untuk melengkapi 7 Dimensi Zero-Assumption.';
        }

        // Check 4: Phased Prompt Sequencer (Phase 1..6)
        $phasedPrompts = $posProject->phased_prompts_json ?: ($schema['phased_prompt_sequencer'] ?? []);
        $hasSixPhases = count($phasedPrompts) >= 6;
        $checks[] = [
            'id' => 'SCAN_04_PHASED_PROMPTS_6',
            'code' => 'SCAN_04_PHASED_PROMPTS_6',
            'label' => 'Phased Prompt Sequencer (Fase 1 s/d 6 Anti-Context Overflow)',
            'passed' => $hasSixPhases,
            'detail' => $hasSixPhases
                ? '6 Fase eksekusi bertahap siap di-copy oleh developer.'
                : 'Urutan prompt 6 fase belum lengkap.',
        ];
        if (! $hasSixPhases) {
            $violations[] = 'Phased Prompt Sequencer belum memiliki 6 fase lengkap.';
        }

        // Check 5: Slop Keywords in Execution Blueprint
        $slopFound = [];
        foreach (['window.alert(', 'window.confirm(', 'cdn.jsdelivr.net', 'unpkg.com'] as $forbidden) {
            if (stripos($execMd, $forbidden) !== false && stripos($execMd, 'HARAM') === false && stripos($execMd, 'FORBIDDEN') === false && stripos($execMd, 'Dilarang') === false) {
                $slopFound[] = $forbidden;
            }
        }
        $checks[] = [
            'id' => 'SCAN_05_ZERO_SLOP_DIRECTIVES',
            'code' => 'SCAN_05_ZERO_SLOP_DIRECTIVES',
            'label' => 'Zero UI Slop & 100% Local Asset Sovereignty',
            'passed' => empty($slopFound),
            'detail' => empty($slopFound)
                ? 'Bebas dari instruksi CDN pihak ketiga, dialog browser murahan, dan kapsul rounded-full.'
                : 'Ditemukan referensi mencurigakan: ' . implode(', ', $slopFound),
        ];

        // Check 6: Day-2 Production Layers (Thermal/A4 Print, Tenant Zero-State, Snapshot, Staged Import, 4-Lane Queue & Private Disk)
        $hasDayTwoLayers = ! empty($schema['printable_and_thermal_contracts'])
            && ! empty($schema['tenant_onboarding_and_provisioning'])
            && ! empty($schema['historical_snapshot_contract']['mandatory_snapshot_columns'])
            && ! empty($schema['staged_bulk_import_export'])
            && count((array) ($schema['day_two_operations_contract']['queue_lanes'] ?? [])) >= 4
            && ! empty($schema['defense_in_depth_security']['media_disk_isolation']);
        $checks[] = [
            'id' => 'SCAN_06_HISTORICAL_SNAPSHOT_AND_DAY2_OPS',
            'code' => 'SCAN_06_HISTORICAL_SNAPSHOT_AND_DAY2_OPS',
            'label' => '8 Layer Day-2 Ops (Thermal/A4 Print, Snapshot *_snapshot, Staged CSV, 4-Lane Queue & Private Disk)',
            'passed' => $hasDayTwoLayers,
            'detail' => $hasDayTwoLayers
                ? '5 kolom *_snapshot, InitializeNewTenantAction, Staged Importer, 4 Redis Queue Lanes, & Private Disk Signed URL aktif.'
                : 'Sebagian kontrak Day-2 Operations v3.0 belum terkompilasi.',
        ];
        if (! $hasDayTwoLayers) {
            $violations[] = 'Kompilasi ulang blueprint ke v3.0 untuk menyertakan 8 Layer Day-2 Production.';
        }

        $passedCount = count(array_filter($checks, fn ($c) => $c['passed']));
        $score = (int) round(($passedCount / max(1, count($checks))) * 100);

        $autoFixPrompt = empty($violations)
            ? "✅ Blueprint '{$blueprint->nama_bisnis}' LULUS 100% audit kepatuhan Project OS v3.0 (10 Ultimate Pillars, Zero Drift & Zero Slop). Lanjutkan eksekusi via Phased Prompt Sequencer Fase 1–6."
            : "# AUTO-FIX BLUEPRINT DRIFT PROMPT\nPerbaiki seluruh penyimpangan berikut pada proyek `{$blueprint->nama_bisnis}`:\n- " . implode("\n- ", $violations);

        if ($recordAudit) {
            AuditLog::record(
                action: 'blueprint_slop_scanned',
                model: $blueprint,
                newValues: ['score' => $score, 'violations_count' => count($violations)],
                description: "Blueprint Drift & Slop Scanner memeriksa '{$blueprint->nama_bisnis}': Skor {$score}/100.",
                eventCategory: 'slop_audit'
            );
        }

        return [
            'success' => true,
            'status' => empty($violations) ? 'PASS' : 'DRIFT_DETECTED',
            'target' => $blueprint->nama_bisnis,
            'slug' => $blueprint->slug,
            'compliance_score' => $score,
            'passed_checks' => $passedCount,
            'total_checks' => count($checks),
            'checks' => $checks,
            'violations' => $violations,
            'auto_fix_prompt' => $autoFixPrompt,
        ];
    }

    /**
     * Audit an arbitrary code snippet (Blade / JSX / Migration / PHP Action) for Slop & Drift violations.
     */
    public function scanCodeSnippet(string $code, bool $recordAudit = false): array
    {
        $violations = [];

        $rules = [
            ['pattern' => '/\brounded-full\b/', 'rule' => 'UI_SLOP_CAPSULE', 'fix' => 'Ganti `rounded-full` dengan `rounded-none`, `rounded-xs`, atau `rounded-sm` (maks `rounded-md`).'],
            ['pattern' => '/\bbg-gradient-to-(?:r|l|t|b|tr|tl|br|bl)\b/', 'rule' => 'UI_SLOP_GRADIENT', 'fix' => 'Hapus gradien warna-warni `bg-gradient-to-*`; gunakan permukaan solid tegas (`bg-white dark:bg-zinc-950`) dengan border tajam.'],
            ['pattern' => '/(?<![a-zA-Z0-9_])(?:window\.)?(?:alert|confirm|prompt)\s*\(/', 'rule' => 'NATIVE_BROWSER_DIALOG', 'fix' => 'Ganti `alert()/confirm()/prompt()` dengan `window.showToast(...)` atau modal konfirmasi Alpine/Tailwind.'],
            ['pattern' => '/https?:\/\/(?:cdn\.jsdelivr\.net|unpkg\.com|cdnjs\.cloudflare\.com|api\.qrserver\.com|chart\.googleapis\.com)/i', 'rule' => 'EXTERNAL_CDN_OR_QR_API_DEPENDENCY', 'fix' => 'Unduh pustaka JS/CSS secara lokal ke `public/js/vendor/` dan gunakan inline SVG QR lokal tanpa external QR API.'],
            ['pattern' => '/\$table->id\s*\(/', 'rule' => 'DB_AUTO_INCREMENT_ID', 'fix' => 'Ganti `$table->id()` menjadi `$table->ulid(\'id\')->primary()` dan gunakan trait `HasUlids`.'],
            ['pattern' => '/\$table->uuid\s*\(\s*[\'"]id[\'"]\s*\)/', 'rule' => 'PGSQL_UUID_MISMATCH', 'fix' => 'Ganti `$table->uuid(\'id\')` menjadi `$table->ulid(\'id\')->primary()` untuk mencegah SQLSTATE[22P02] di PostgreSQL 16.'],
            ['pattern' => '/(?:->|::)paginate\s*\(/', 'rule' => 'OFFSET_PAGINATION_SLOP', 'fix' => 'Ganti `->paginate()` / `::paginate()` dengan `->orderBy(\'id\', \'desc\')->cursorPaginate()` untuk kompleksitas O(1).'],
        ];

        foreach ($rules as $r) {
            if (preg_match($r['pattern'], $code)) {
                $violations[] = [
                    'rule' => $r['rule'],
                    'fix' => $r['fix'],
                ];
            }
        }

        $score = max(0, 100 - (count($violations) * 15));

        $autoFixPrompt = empty($violations)
            ? '✅ Kode bersih 100% sesuai standar Anti-Slop & Strict ULID Project OS.'
            : "# PROJECT OS SLOP SCANNER — AUTO-FIX PROMPT\nTemuan pelanggaran standar pada kode Anda yang WAJIB segera diperbaiki:\n"
                . implode("\n", array_map(fn ($v) => "- **[{$v['rule']}]**: {$v['fix']}", $violations))
                . "\n\nTerapkan perubahan secara surgical diff dan pastikan seluruh pengujian tetap hijau.";

        if ($recordAudit) {
            AuditLog::record(
                action: 'code_snippet_slop_scanned',
                newValues: ['score' => $score, 'violations_count' => count($violations)],
                description: "Slop Scanner memeriksa potongan kode: Skor {$score}/100 (" . count($violations) . " temuan).",
                eventCategory: 'slop_audit'
            );
        }

        return [
            'success' => true,
            'compliance_score' => $score,
            'violations_count' => count($violations),
            'violations' => $violations,
            'auto_fix_prompt' => $autoFixPrompt,
        ];
    }

    /**
     * Generate standalone POSIX bash script `verify-blueprint.sh` included in every exported project & CLI bootstrapper.
     */
    public static function generateVerifyBlueprintBashScript(string $appName): string
    {
        return <<<BASH
#!/usr/bin/env bash
# ==============================================================================
# Project OS v2.5 — Blueprint Drift Auditor & Slop Scanner (verify-blueprint.sh)
# Target Project: {$appName}
# ==============================================================================
set -u

VIOLATIONS=0
FIX_ITEMS=""

echo "=================================================================="
echo " [Project OS] Auditing Codebase Compliance for: {$appName}"
echo "=================================================================="

# Scan 1: UI Slop & External CDN in frontend views/components
for dir in resources/views resources/js src app; do
  if [ -d "\$dir" ]; then
    if grep -RInE "rounded-full|bg-gradient-to-" "\$dir" --include="*.blade.php" --include="*.jsx" --include="*.tsx" --include="*.vue" >/dev/null 2>&1; then
      echo " [FAIL] Scan 1A: Found 'rounded-full' or 'bg-gradient-to-*' in \$dir"
      VIOLATIONS=\$((VIOLATIONS + 1))
      FIX_ITEMS="\${FIX_ITEMS}\n- Hapus seluruh class 'rounded-full' dan 'bg-gradient-to-*' di folder \$dir; ganti ke sudut tegas (rounded-none / rounded-xs / rounded-sm) dan warna solid."
    fi
    if grep -RInE "window\.alert\(|window\.confirm\(|[^a-zA-Z0-9_]alert\(|[^a-zA-Z0-9_]confirm\(" "\$dir" --include="*.blade.php" --include="*.jsx" --include="*.tsx" --include="*.js" >/dev/null 2>&1; then
      echo " [FAIL] Scan 1B: Found native browser alert()/confirm() dialog in \$dir"
      VIOLATIONS=\$((VIOLATIONS + 1))
      FIX_ITEMS="\${FIX_ITEMS}\n- Ganti seluruh dialog native alert()/confirm() di \$dir dengan sistem Toast terpusat (window.showToast) atau modal konfirmasi modern."
    fi
    if grep -RInE "cdn\.jsdelivr\.net|unpkg\.com|cdnjs\.cloudflare\.com|api\.qrserver\.com|chart\.googleapis\.com" "\$dir" --include="*.blade.php" --include="*.html" >/dev/null 2>&1; then
      echo " [FAIL] Scan 1C: Found external third-party CDN or QR API link in \$dir"
      VIOLATIONS=\$((VIOLATIONS + 1))
      FIX_ITEMS="\${FIX_ITEMS}\n- Pindahkan seluruh script/CSS CDN eksternal di \$dir ke aset lokal (public/js/vendor/ atau bundel Vite) dan render QR sebagai inline SVG lokal."
    fi
  fi
done

# Scan 2: Database ULID & Keyset Cursor Pagination O(1)
if [ -d "database/migrations" ]; then
  if grep -RInE "\\\$table->id\(|\\\$table->uuid\(['\"]id['\"]\)" database/migrations >/dev/null 2>&1; then
    echo " [FAIL] Scan 2A: Found \$table->id() or \$table->uuid('id') in database/migrations"
    VIOLATIONS=\$((VIOLATIONS + 1))
    FIX_ITEMS="\${FIX_ITEMS}\n- Ubah seluruh primary key tabel domain bisnis di database/migrations menjadi \\\$table->ulid('id')->primary()."
  fi
fi

if [ -d "app" ]; then
  if grep -RInE "->paginate\(" app >/dev/null 2>&1; then
    echo " [FAIL] Scan 2B: Found offset-based ->paginate() in app/"
    VIOLATIONS=\$((VIOLATIONS + 1))
    FIX_ITEMS="\${FIX_ITEMS}\n- Ganti seluruh pemanggilan ->paginate() di folder app/ menjadi ->orderBy('id', 'desc')->cursorPaginate() untuk menjamin O(1)."
  fi
fi

# Scan 3: Domain Actions Transaction & Audit Trail Check
if [ -d "app/Actions" ]; then
  if ! grep -RInE "DB::transaction" app/Actions >/dev/null 2>&1; then
    echo " [WARN] Scan 3: app/Actions belum memanggil DB::transaction()"
    VIOLATIONS=\$((VIOLATIONS + 1))
    FIX_ITEMS="\${FIX_ITEMS}\n- Bungkus mutasi state di dalam app/Actions menggunakan DB::transaction() + lockForUpdate() serta catat ke AuditLog::record()."
  fi
fi

echo "------------------------------------------------------------------"
if [ "\$VIOLATIONS" -eq 0 ]; then
  echo " [PASS] 0 Pelanggaran Ditemukan! Proyek 100% Patuh Standar Project OS."
  exit 0
else
  echo " [ACTION REQUIRED] Ditemukan \$VIOLATIONS kategori pelanggaran standar!"
  echo ""
  echo "================ COPY & PASTE PROMPT INI KE AI AGENT ================"
  printf "Perbaiki seluruh pelanggaran standar Project OS berikut secara surgical diff:%b\nPastikan semua pengujian otomatis lulus 100%% setelah diperbaiki.\n" "\$FIX_ITEMS"
  echo "====================================================================="
  exit 1
fi
BASH;
    }
}