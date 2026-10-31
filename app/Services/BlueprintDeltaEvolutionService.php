<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PosChangeRequest;
use App\Models\PosPrimitive;
use App\Models\PosProject;
use App\Models\VisionBlueprint;
use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Feature #1 (Day-30+ Lifecycle): Iterative Change Request & Delta Blueprint Generator (v1.0 -> v1.1).
 *
 * Produces a Surgical Delta Prompt containing ONLY additive strict-ULID migrations,
 * Graphify `graph.json` node/edge diffs, delta `GeminiToolRegistry` declarations,
 * and Zero-Regression Tests so existing v1.0 production code is never corrupted by AI slop.
 */
class BlueprintDeltaEvolutionService
{
    /**
     * Generate (and optionally persist) a Surgical Delta Change Request from `v1.x` -> `v1.(x+1)`.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createChangeRequest(
        ?VisionBlueprint $blueprint,
        ?PosProject $posProject,
        array $payload,
        bool $persist = true
    ): array {
        ProjectOsMatrixCatalog::ensureSeeded();

        if (! $posProject && $blueprint) {
            $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
            if (! $posProject) {
                $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
            }
        }

        $title = trim((string) ($payload['title'] ?? 'Penambahan Fitur Evolusi Operasional'));
        $summary = trim((string) ($payload['change_summary'] ?? $title));

        $currentVersion = (string) ($posProject?->current_version ?: 'v1.0');
        $nextVersion = $this->incrementMinorVersion($currentVersion);

        $existingSchema = [];
        if ($posProject && ! empty($posProject->compiled_schema_json)) {
            $decoded = is_array($posProject->compiled_schema_json)
                ? $posProject->compiled_schema_json
                : json_decode((string) $posProject->compiled_schema_json, true);
            if (is_array($decoded)) {
                $existingSchema = $decoded;
            }
        }

        $existingPrimitives = array_values(array_map(
            'strval',
            (array) ($existingSchema['project_metadata']['injected_primitives'] ?? [])
        ));
        $existingTableNames = array_values(array_map(
            fn ($t) => (string) ($t['table'] ?? ''),
            (array) ($existingSchema['data_dictionary'] ?? [])
        ));

        // 1. Resolve newly requested primitives (explicit + keyword auto-inference from change_summary)
        $requestedPrimitives = array_values(array_unique(array_filter(array_map(
            'strval',
            (array) ($payload['added_primitives'] ?? [])
        ))));

        $lowerSummary = mb_strtolower($title . ' ' . $summary);
        $keywordPrimitiveMap = [
            'PRIMITIVE_DOUBLE_ENTRY_LEDGER' => ['komisi', 'commission', 'jurnal', 'ledger', 'split-payment', 'split payment', 'bagi hasil', 'settlement'],
            'PRIMITIVE_PAYMENT_ORCHESTRATION' => ['split-payment', 'split payment', 'escrow', 'qris', 'midtrans', 'xendit', 'pembayaran'],
            'PRIMITIVE_INVENTORY_LEDGER' => ['stok', 'gudang', 'batch', 'fefo', 'resep', 'inventory', 'sku'],
            'PRIMITIVE_APPROVAL_WORKFLOW' => ['approval', 'otorisasi', 'berjenjang', 'maker-checker', 'persetujuan'],
            'PRIMITIVE_SUBSCRIPTION_RECURRING' => ['langganan', 'membership', 'recurring', 'kuota', 'retainer'],
            'PRIMITIVE_LOYALTY_TIER_ENGINE' => ['poin', 'loyalty', 'tier member', 'voucher', 'cashback'],
        ];

        foreach ($keywordPrimitiveMap as $primCode => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lowerSummary, $kw) && ! in_array($primCode, $existingPrimitives, true)) {
                    $requestedPrimitives[] = $primCode;
                    break;
                }
            }
        }
        $addedPrimitives = array_values(array_unique(array_diff($requestedPrimitives, $existingPrimitives)));

        // 2. Collect delta schema tables & tool declarations from newly added primitives + custom tables
        $deltaTables = [];
        $deltaTools = [];

        if (! empty($addedPrimitives) && Schema::hasTable('pos_primitives')) {
            $primModels = PosPrimitive::query()->whereIn('code', $addedPrimitives)->get();
            foreach ($primModels as $pm) {
                foreach (($pm->schema_blueprints ?? []) as $tbl) {
                    $tName = (string) ($tbl['table'] ?? '');
                    if ($tName !== '' && ! in_array($tName, $existingTableNames, true)) {
                        $deltaTables[] = $tbl;
                        $existingTableNames[] = $tName;
                    }
                }
                foreach (($pm->gemini_tool_declarations ?? []) as $tool) {
                    $deltaTools[] = $tool;
                }
            }
        }

        // Also synthesize a clean ULID custom delta table if user provided explicit table names or domain feature
        $rawCustomTables = (array) ($payload['added_tables'] ?? []);
        if (empty($rawCustomTables) && empty($deltaTables)) {
            $slugWords = array_values(array_filter(
                explode('_', Str::snake(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $title))),
                fn ($w) => strlen($w) >= 4 && ! in_array($w, ['tambah', 'sistem', 'fitur', 'modul', 'untuk', 'aplikasi'], true)
            ));
            $derivedTable = ! empty($slugWords)
                ? Str::plural(implode('_', array_slice($slugWords, 0, 2)))
                : 'feature_extension_ledgers';
            $rawCustomTables[] = $derivedTable;
        }

        foreach ($rawCustomTables as $customItem) {
            $tableName = is_array($customItem)
                ? Str::snake((string) ($customItem['table'] ?? 'custom_delta_records'))
                : Str::plural(Str::snake((string) $customItem));

            if ($tableName === '' || in_array($tableName, $existingTableNames, true)) {
                continue;
            }
            $existingTableNames[] = $tableName;
            $deltaTables[] = [
                'table' => $tableName,
                'deletion_policy' => 'IMMUTABLE_LEDGER',
                'columns' => [
                    ['name' => 'id', 'type' => 'ULID (CHAR 26)', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'FOREIGN_ULID -> organizations.id ON DELETE RESTRICT'],
                    ['name' => 'reference_code', 'type' => 'VARCHAR(64) UNIQUE'],
                    ['name' => 'amount_snapshot', 'type' => 'DECIMAL(19,4) DEFAULT 0.0000'],
                    ['name' => 'rule_snapshot', 'type' => 'JSONB'],
                    ['name' => 'status', 'type' => 'VARCHAR(32) INDEX'],
                ],
            ];
            $deltaTools[] = [
                'name' => 'manage_' . $tableName,
                'description' => "Mengelola dan mengaudit data {$tableName} secara atomik dengan isolasi organization_id & Keyset O(1).",
            ];
        }

        // 3. Decision rules delta
        $addedDecisionRules = array_values((array) ($payload['added_decision_rules'] ?? []));
        if (empty($addedDecisionRules)) {
            $addedDecisionRules[] = [
                'rule_id' => 'DEC-DELTA-' . strtoupper(str_replace('.', '', $nextVersion)),
                'priority' => 15,
                'condition_a' => 'change_request_scope == "' . Str::upper(Str::slug($title, '_')) . '"',
                'condition_b' => 'organization_active == true',
                'condition_c' => 'amount > 0.0000',
                'action_code' => 'EXECUTE_SURGICAL_DELTA_RULE',
                'exact_formula' => 'allocated_amount = ROUND_HALF_UP(gross_amount * tier_rate, 4) /* Penny Allocation Guaranteed */',
            ];
        }

        // 4. Additive Strict ULID SQL Migrations
        $sqlBlocks = [];
        foreach ($deltaTables as $dt) {
            $tName = $dt['table'];
            $sqlBlocks[] = <<<SQL
CREATE TABLE IF NOT EXISTS {$tName} (
    id CHAR(26) PRIMARY KEY,
    organization_id CHAR(26) NOT NULL REFERENCES organizations(id) ON DELETE RESTRICT,
    reference_code VARCHAR(64) NOT NULL,
    unit_price_snapshot DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
    counterparty_snapshot JSONB NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'DRAFT',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_{$tName}_org_cursor ON {$tName} (organization_id, id);
SQL;
        }
        $deltaSql = implode("\n\n", $sqlBlocks);

        // 5. Graphify `graph.json` Diff
        $graphDiff = [
            'from_version' => $currentVersion,
            'to_version' => $nextVersion,
            'added_primitives' => $addedPrimitives,
            'added_nodes' => array_map(fn ($t) => [
                'id' => 'table:' . $t['table'],
                'type' => 'ulid_table',
                'version_introduced' => $nextVersion,
            ], $deltaTables),
            'added_tools' => array_column($deltaTools, 'name'),
        ];

        // 6. Zero-Regression Test Scenarios
        $appName = (string) ($blueprint?->nama_bisnis ?? $posProject?->app_name ?? 'Enterprise Application');
        $regressionScenarios = [
            [
                'id' => 'REG-' . strtoupper(str_replace('.', '', $nextVersion)) . '-01',
                'title' => "Invarian {$currentVersion} Tetap Utuh Tanpa Regresi Schema atau State Machine",
                'given' => "Data transaksi produksi dari rilis {$currentVersion} sudah berjalan dengan snapshot harga historis terkunci",
                'when' => "Migrasi aditif {$nextVersion} ({$title}) dijalankan",
                'then' => 'Seluruh endpoint lama lulus 100%, tidak ada DROP COLUMN / perubahan tipe PK, dan laporan historis tetap identik hingga 4 desimal.',
            ],
            [
                'id' => 'REG-' . strtoupper(str_replace('.', '', $nextVersion)) . '-02',
                'title' => 'Isolasi Multi-Tenant & Concurrency Lock pada Modul Baru',
                'given' => "Dua request paralel mengeksekusi alur baru '{$title}' pada Tenant A, sementara Tenant B mencoba membaca ID tersebut",
                'when' => 'Action dieksekusi di dalam DB::transaction() + lockForUpdate()',
                'then' => 'Saldo/kuota akurat tanpa race condition, dan percobaan akses lintas tenant oleh Tenant B ditolak dengan HTTP 404.',
            ],
        ];

        // 7. Render Surgical Delta Prompt
        $addedPrimStr = empty($addedPrimitives) ? '(Custom Domain Extension)' : implode(', ', $addedPrimitives);
        $addedTableStr = implode(', ', array_column($deltaTables, 'table'));
        $graphDiffJson = json_encode($graphDiff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $surgicalPrompt = <<<PROMPT
<SURGICAL_DELTA_BLUEPRINT from="{$currentVersion}" to="{$nextVersion}">
# PROJECT OS v4.0 — SURGICAL CHANGE REQUEST PROMPT ({$currentVersion} -> {$nextVersion})
**Proyek:** `{$appName}`
**Judul Change Request:** {$title}
**Ringkasan Kebutuhan Baru:** {$summary}
**Modul Primitif Baru Disuntikkan:** `{$addedPrimStr}`
**Tabel ULID Baru (Additive Only):** `{$addedTableStr}`

## ATURAN ANTI-REGRESI & ZERO-SLOP (WAJIB DIPATUHI AI AGENT):
1. **DILARANG KERAS** menulis ulang, menghapus kolom, atau merombak tabel/Action dari versi `{$currentVersion}` yang sudah berjalan di produksi!
2. Gunakan prinsip **Ponytail Rung 4 (Surgical Diff)**: hanya buat migrasi `CREATE TABLE` / `ALTER TABLE ADD COLUMN` baru dengan Primary Key **26-char ULID (`->ulid('id')->primary()`)** dan `ON DELETE RESTRICT`.
3. Seluruh aturan bisnis bercabang wajib didaftarkan ke `App\Domains\Rules\DeterministicDecisionMatrix` (`match()` data-driven, **HARAM** nested `if-else`).
4. Perbarui `.agents/graphify/graph.json`, `GeminiToolRegistry`, `AuditLog::record()` (SHA-256), dan RBAC Policy sesuai diff di bawah ini.

## 1. MIGRASI ADITIF STRICT ULID (`{$nextVersion}`)
```sql
{$deltaSql}
```

## 2. DIFF `.agents/graphify/graph.json`
```json
{$graphDiffJson}
```

## 3. SKENARIO REGRESSION & ISOLATION TEST WAJIB LULUS (`tests/Feature/Delta{$nextVersion}RegressionTest.php`)
- **{$regressionScenarios[0]['id']}**: {$regressionScenarios[0]['then']}
- **{$regressionScenarios[1]['id']}**: {$regressionScenarios[1]['then']}
</SURGICAL_DELTA_BLUEPRINT>
PROMPT;

        $savedModel = null;
        if ($persist && Schema::hasTable('pos_change_requests')) {
            $savedModel = PosChangeRequest::query()->create([
                'pos_project_id' => $posProject?->exists ? $posProject->id : null,
                'vision_blueprint_id' => $blueprint?->exists ? $blueprint->id : null,
                'created_by' => Auth::id(),
                'from_version' => $currentVersion,
                'to_version' => $nextVersion,
                'title' => $title,
                'change_summary' => $summary,
                'added_primitives' => $addedPrimitives,
                'added_tables' => $deltaTables,
                'added_decision_rules' => $addedDecisionRules,
                'delta_migrations_sql' => $deltaSql,
                'delta_graph_diff' => $graphDiff,
                'delta_tool_declarations' => $deltaTools,
                'regression_test_scenarios' => $regressionScenarios,
                'surgical_delta_prompt' => $surgicalPrompt,
                'status' => 'READY',
            ]);

            if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'current_version')) {
                $posProject->forceFill(['current_version' => $nextVersion])->saveQuietly();
            }

            AuditLog::record(
                action: 'delta_change_request_generated',
                model: $savedModel,
                newValues: [
                    'from_version' => $currentVersion,
                    'to_version' => $nextVersion,
                    'title' => $title,
                    'added_primitives' => $addedPrimitives,
                    'added_tables' => array_column($deltaTables, 'table'),
                ],
                description: "Iterative Delta Blueprint ({$currentVersion} -> {$nextVersion}) dibuat untuk '{$appName}': {$title}.",
                eventCategory: 'delta_evolution'
            );
        }

        return [
            'success' => true,
            'id' => $savedModel?->id,
            'from_version' => $currentVersion,
            'to_version' => $nextVersion,
            'title' => $title,
            'change_summary' => $summary,
            'added_primitives' => $addedPrimitives,
            'added_tables' => $deltaTables,
            'added_decision_rules' => $addedDecisionRules,
            'delta_migrations_sql' => $deltaSql,
            'delta_graph_diff' => $graphDiff,
            'delta_tool_declarations' => $deltaTools,
            'regression_test_scenarios' => $regressionScenarios,
            'surgical_delta_prompt' => $surgicalPrompt,
        ];
    }

    protected function incrementMinorVersion(string $version): string
    {
        if (preg_match('/^v?(\d+)\.(\d+)/i', trim($version), $m)) {
            $major = (int) $m[1];
            $minor = ((int) $m[2]) + 1;

            return "v{$major}.{$minor}";
        }

        return 'v1.1';
    }
}

