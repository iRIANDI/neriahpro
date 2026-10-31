<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\PosEnterpriseGuaranteeCatalog;
use Illuminate\Support\Str;

/**
 * Project OS v6.0 — TopologicalPromptChunker & Exact Symbol Lock Generator.
 *
 * Implements:
 * - JAMINAN 3: Exact Symbol Lock (`SYMBOL_MANIFEST.json`) preventing cross-part signature drift
 * - JAMINAN 4: DAG Topological Sort (Kahn's Algorithm) on `foreignUlid` relations + Token-Safe Sub-Chunking (max 5–6 entities/part)
 * - JAMINAN 5: Hard Compiler Exit-Gate (`<MANDATORY_VERIFICATION_GATE>`) with Exit Code 0 handshake at the end of every Part
 */
class TopologicalPromptChunker
{
    public const DEFAULT_MAX_ENTITIES_PER_PART = 5;

    /**
     * Sort all tables in `data_dictionary` using Kahn's Topological Sort Algorithm based on `foreignUlid` dependencies.
     *
     * @param  array<int, array<string, mixed>>  $dataDictionary
     * @return array<string, mixed>
     */
    public static function buildTopologicalTableLayers(
        array $dataDictionary,
        int $maxEntitiesPerPart = self::DEFAULT_MAX_ENTITIES_PER_PART
    ): array {
        $maxEntitiesPerPart = max(3, min(6, $maxEntitiesPerPart));

        $tableMap = [];
        foreach ($dataDictionary as $tbl) {
            $tName = trim((string) ($tbl['table'] ?? ''));
            if ($tName !== '' && ! isset($tableMap[$tName])) {
                $tableMap[$tName] = $tbl;
            }
        }

        // Ensure core anchor hints if `users` is referenced even when managed by framework default auth migration
        $knownTables = array_keys($tableMap);
        $knownLookup = array_flip($knownTables);

        $dependsOn = [];
        $dependents = [];
        $edges = [];

        foreach ($knownTables as $tName) {
            $dependsOn[$tName] = [];
            $dependents[$tName] = [];
        }

        foreach ($tableMap as $tName => $tbl) {
            foreach (($tbl['columns'] ?? []) as $col) {
                if (! is_array($col)) {
                    continue;
                }
                $cName = (string) ($col['name'] ?? '');
                $cType = (string) ($col['type'] ?? '');
                $refRaw = (string) ($col['references'] ?? '');

                $targetTable = null;
                if ($refRaw !== '') {
                    $targetTable = explode('.', $refRaw)[0];
                } elseif (str_contains($cType, 'foreign') && str_ends_with($cName, '_id')) {
                    $guessedPlural = Str::plural(substr($cName, 0, -3));
                    if (isset($knownLookup[$guessedPlural])) {
                        $targetTable = $guessedPlural;
                    }
                }

                // Ignore self-references (e.g. parent_id -> same table) or references outside data_dictionary
                if ($targetTable && $targetTable !== $tName && isset($knownLookup[$targetTable])) {
                    if (! in_array($targetTable, $dependsOn[$tName], true)) {
                        $dependsOn[$tName][] = $targetTable;
                        $dependents[$targetTable][] = $tName;
                        $edges[] = [
                            'from_table' => $tName,
                            'depends_on' => $targetTable,
                            'via_column' => $cName,
                        ];
                    }
                }
            }
        }

        // Kahn's Algorithm by Discrete Topological Depth Layers (Layer 0 -> Layer 1 -> Layer 2 -> Layer 3+)
        $inDegree = [];
        foreach ($knownTables as $tName) {
            $inDegree[$tName] = count($dependsOn[$tName]);
        }

        $remaining = $knownLookup;
        $layers = [];
        $tableLayerIndex = [];
        $orderedTables = [];
        $layerNum = 0;

        while (! empty($remaining)) {
            $currentWave = [];
            foreach (array_keys($remaining) as $candidate) {
                if (($inDegree[$candidate] ?? 0) === 0) {
                    $currentWave[] = $candidate;
                }
            }

            // Deterministic cycle-breaker if mutual foreign keys ever appear
            if (empty($currentWave)) {
                $minDeg = null;
                $picked = null;
                foreach (array_keys($remaining) as $candidate) {
                    $deg = $inDegree[$candidate] ?? 0;
                    if ($minDeg === null || $deg < $minDeg) {
                        $minDeg = $deg;
                        $picked = $candidate;
                    }
                }
                if ($picked !== null) {
                    $currentWave[] = $picked;
                }
            }

            // Keep `organizations` first in Layer 0, otherwise preserve canonical declaration stability
            usort($currentWave, static function (string $a, string $b) use ($knownLookup): int {
                if ($a === 'organizations') {
                    return -1;
                }
                if ($b === 'organizations') {
                    return 1;
                }

                return ($knownLookup[$a] ?? 0) <=> ($knownLookup[$b] ?? 0);
            });

            $layers[$layerNum] = [];
            foreach ($currentWave as $resolvedTable) {
                unset($remaining[$resolvedTable]);
                $layers[$layerNum][] = $resolvedTable;
                $tableLayerIndex[$resolvedTable] = $layerNum;
                $orderedTables[] = $tableMap[$resolvedTable];

                foreach ($dependents[$resolvedTable] ?? [] as $child) {
                    if (isset($remaining[$child])) {
                        $inDegree[$child] = max(0, ($inDegree[$child] ?? 1) - 1);
                    }
                }
            }

            $layerNum++;
        }

        // Partition ordered tables into Token-Safe Sub-Chunks of max 5–6 entities per Part
        $schemaSubChunks = [];
        $rawChunks = array_chunk($orderedTables, $maxEntitiesPerPart);
        foreach ($rawChunks as $idx => $chunkTables) {
            $subNum = $idx + 1;
            $chunkLayerNums = array_values(array_unique(array_map(
                fn ($t) => $tableLayerIndex[$t['table']] ?? 0,
                $chunkTables
            )));
            sort($chunkLayerNums);
            $layerLabel = count($chunkLayerNums) === 1
                ? 'Layer ' . $chunkLayerNums[0]
                : 'Layer ' . $chunkLayerNums[0] . '–' . end($chunkLayerNums);

            $schemaSubChunks[] = [
                'part_code' => "2.{$subNum}",
                'layer_label' => $layerLabel,
                'layers_covered' => $chunkLayerNums,
                'entities_count' => count($chunkTables),
                'table_names' => array_values(array_map(fn ($t) => (string) $t['table'], $chunkTables)),
                'tables' => $chunkTables,
            ];
        }

        return [
            'algorithm' => 'KAHN_DAG_TOPOLOGICAL_SORT',
            'max_entities_per_part' => $maxEntitiesPerPart,
            'total_tables' => count($orderedTables),
            'total_layers' => count($layers),
            'layers' => $layers,
            'table_layer_map' => $tableLayerIndex,
            'dependency_edges' => $edges,
            'ordered_tables' => $orderedTables,
            'schema_sub_chunks' => $schemaSubChunks,
        ];
    }

    /**
     * JAMINAN 3: Generate Frozen Exact Symbol Manifest (`SYMBOL_MANIFEST.json`) synced with Graphify.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function generateSymbolManifest(array $schema): array
    {
        $meta = $schema['project_metadata'] ?? [];
        $appName = (string) ($meta['name'] ?? 'Enterprise Application');
        $sectorCode = (string) ($meta['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $isolationId = (string) ($meta['project_isolation_id'] ?? Str::slug($appName));

        $dag = $schema['topological_dag_plan']
            ?? self::buildTopologicalTableLayers((array) ($schema['data_dictionary'] ?? []));

        $stateByEntity = [];
        foreach (($schema['state_machines'] ?? []) as $sm) {
            $ent = trim((string) ($sm['entity'] ?? ''));
            if ($ent !== '') {
                $stateByEntity[strtolower($ent)] = $sm;
            }
        }

        $glueByTable = [];
        foreach (PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives() as $gp) {
            $glueByTable[$gp['table']] = $gp;
        }

        $entitiesManifest = [];
        $tableToEntityName = [];

        foreach (($dag['ordered_tables'] ?? []) as $tbl) {
            $tName = (string) ($tbl['table'] ?? '');
            if ($tName === '') {
                continue;
            }
            $entityStudly = Str::studly(Str::singular($tName));
            $tableToEntityName[$tName] = $entityStudly;
        }

        foreach (($dag['ordered_tables'] ?? []) as $tbl) {
            $tName = (string) ($tbl['table'] ?? '');
            if ($tName === '') {
                continue;
            }

            $entityStudly = $tableToEntityName[$tName];
            $modelFqcn = "App\\Models\\{$entityStudly}";
            $layerIdx = (int) ($dag['table_layer_map'][$tName] ?? 0);

            $exactColumns = [];
            $exactRelations = [];
            $hasStatusColumn = false;

            foreach (($tbl['columns'] ?? []) as $col) {
                if (! is_array($col)) {
                    continue;
                }
                $cName = (string) ($col['name'] ?? '');
                if ($cName === '' || $cName === 'timestamps') {
                    if ($cName === 'timestamps') {
                        $exactColumns[] = 'created_at';
                        $exactColumns[] = 'updated_at';
                    }
                    continue;
                }
                $exactColumns[] = $cName;
                if ($cName === 'status') {
                    $hasStatusColumn = true;
                }

                // Infer exact BelongsTo relation for foreignUlid columns
                $refRaw = (string) ($col['references'] ?? '');
                $targetTable = $refRaw !== '' ? explode('.', $refRaw)[0] : null;
                if (! $targetTable && str_ends_with($cName, '_id')) {
                    $candidatePlural = Str::plural(substr($cName, 0, -3));
                    if (isset($tableToEntityName[$candidatePlural])) {
                        $targetTable = $candidatePlural;
                    }
                }

                if ($targetTable && ($targetTable === 'users' || isset($tableToEntityName[$targetTable]))) {
                    $relMethod = Str::camel(str_ends_with($cName, '_id') ? substr($cName, 0, -3) : $cName);
                    $targetModel = $targetTable === 'users'
                        ? 'App\\Models\\User'
                        : ('App\\Models\\' . $tableToEntityName[$targetTable]);
                    $exactRelations[$relMethod] = "BelongsTo({$targetModel}::class, '{$cName}')";
                }
            }

            $matchedSm = $stateByEntity[strtolower($entityStudly)]
                ?? $stateByEntity[strtolower($tName)]
                ?? null;

            $statusEnumFqcn = null;
            if ($matchedSm && ! empty($matchedSm['enum_class'])) {
                $statusEnumFqcn = (string) $matchedSm['enum_class'];
            } elseif ($hasStatusColumn) {
                $statusEnumFqcn = "App\\Enums\\{$entityStudly}Status";
            }

            // Build exact deterministic bound actions (`__invoke` contract)
            $domainFolder = str_starts_with($tName, 'sys_') ? 'System' : $entityStudly;
            $dtoCreateFqcn = "App\\Data\\{$domainFolder}\\{$entityStudly}UpsertData";
            $boundActions = [];

            if (isset($glueByTable[$tName])) {
                $gp = $glueByTable[$tName];
                $boundActions['execute_glue_contract'] = [
                    'fqcn' => $gp['action_fqcn'],
                    'signature' => $gp['action_signature'],
                ];
            } else {
                $boundActions['create_or_lock'] = [
                    'fqcn' => "App\\Actions\\{$domainFolder}\\Create{$entityStudly}Action",
                    'dto_fqcn' => $dtoCreateFqcn,
                    'signature' => "public function __invoke({$dtoCreateFqcn} \$data, string \$actorUlid): {$modelFqcn}",
                ];

                if ($statusEnumFqcn !== null) {
                    $entVar = self::entityVar($entityStudly);
                    $boundActions['transition_status'] = [
                        'fqcn' => "App\\Actions\\{$domainFolder}\\Transition{$entityStudly}StatusAction",
                        'signature' => "public function __invoke(string \${$entVar}Ulid, {$statusEnumFqcn} \$nextStatus, string \$actorUlid, ?string \$reasonCode = null): {$modelFqcn}",
                    ];
                }
            }

            $entitiesManifest[$entityStudly] = [
                'model_fqcn' => $modelFqcn,
                'table' => $tName,
                'dag_layer' => $layerIdx,
                'source_module' => (string) ($tbl['source_module'] ?? 'DOMAIN_CORE'),
                'primary_key' => 'id (ULID string, 26 chars)',
                'status_enum_fqcn' => $statusEnumFqcn,
                'exact_columns' => array_values(array_unique($exactColumns)),
                'exact_relations' => $exactRelations,
                'bound_actions' => $boundActions,
            ];
        }

        return [
            'manifest_version' => '1.0.0',
            'project_isolation_id' => $isolationId,
            'project_name' => $appName,
            'primary_sector' => $sectorCode,
            'strict_naming_contract' => 'AI DILARANG KERAS mengubah nama class, method (__invoke), DTO, relasi, atau kolom di bawah ini. Seluruh Part 1.0 s/d Part 7.0 WAJIB mematuhi kontrak simbol eksak ini 100%:',
            'topological_migration_order' => array_values(array_map(
                fn ($t) => (string) $t['table'],
                $dag['ordered_tables'] ?? []
            )),
            'entities_count' => count($entitiesManifest),
            'entities' => $entitiesManifest,
        ];
    }

    /**
     * JAMINAN 5: Build the `<MANDATORY_VERIFICATION_GATE>` terminal handshake block for any Part.
     *
     * @param  array<int, string>  $extraChecks
     */
    public static function buildMandatoryVerificationGate(
        string $partCode,
        string $testFilterName,
        array $extraChecks = []
    ): string {
        $extraLines = '';
        foreach ($extraChecks as $idx => $chk) {
            $stepNo = 5 + $idx;
            $extraLines .= "\n{$stepNo}. Additional Gate Check:\n   {$chk}";
        }

        return <<<GATE
<MANDATORY_VERIFICATION_GATE>
DO NOT declare Part {$partCode} finished until you have executed ALL of the following verification commands in the local terminal and achieved EXIT CODE 0 for every single step:

1. Syntax & PostgreSQL Topological FK Check:
   `php artisan migrate --pretend` (Ensure zero SQL syntax errors and 100% valid foreignUlid dependency order)
2. Strict Type & Exact Symbol Lock Audit:
   `./vendor/bin/phpstan analyse --memory-limit=1G` (Ensure 100% exact match with `.agents/SYMBOL_MANIFEST.json` — zero undefined methods, columns, or handle() drift)
3. Automated Feature/Unit Test for Part {$partCode}:
   `php artisan test --filter={$testFilterName}`
4. Graphify Synchronization & Zero-Error Handshake Receipt:
   Update `.agents/graphify/graph.json` with the newly materialized classes/edges and print `[PART {$partCode} VERIFIED — ZERO ERRORS]` so the engineer knows it is 100% safe to proceed to the next Part.{$extraLines}

If ANY error or warning occurs in steps 1–4, you MUST diagnose and fix it autonomously right now before replying to the user.
</MANDATORY_VERIFICATION_GATE>
GATE;
    }

    /**
     * JAMINAN 4 + 3 + 5: Compile Token-Safe Sub-Chunked Prompts (`Part 1.0` -> `Part 2.1..2.N` -> `Part 3.1..3.M` -> `Part 4.0` -> `Part 5.1..5.5` -> `Part 6.0` -> `Part 7.0`).
     *
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>|null  $symbolManifest
     * @return array<int, array<string, mixed>>
     */
    public static function compileSubChunkedPrompts(
        array $schema,
        ?array $symbolManifest = null,
        int $maxEntitiesPerPart = self::DEFAULT_MAX_ENTITIES_PER_PART
    ): array {
        $meta = $schema['project_metadata'] ?? [];
        $appName = (string) ($meta['name'] ?? 'Enterprise Application');
        $sectorCode = (string) ($meta['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $stackCode = (string) ($meta['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        $isolationId = (string) ($meta['project_isolation_id'] ?? Str::slug($appName));

        $dag = $schema['topological_dag_plan']
            ?? self::buildTopologicalTableLayers((array) ($schema['data_dictionary'] ?? []), $maxEntitiesPerPart);
        $manifest = $symbolManifest ?? ($schema['symbol_manifest'] ?? self::generateSymbolManifest($schema));
        $menuMatrix = $schema['enterprise_menu_matrix']
            ?? PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector($sectorCode);

        $entitiesByTable = [];
        foreach (($manifest['entities'] ?? []) as $entName => $entSpec) {
            $entitiesByTable[$entSpec['table']] = array_merge(['entity' => $entName], $entSpec);
        }

        $parts = [];

        // =========================================================================
        // PART 1.0: FOUNDATION, ISOLATED NAMESPACE, SYMBOL_MANIFEST & 5 GLUE SETUP
        // =========================================================================
        $gate1 = self::buildMandatoryVerificationGate('1.0', 'Part1_0_FoundationAndHarnessTest');
        $parts[] = [
            'part_code' => '1.0',
            'major_phase' => 1,
            'filename' => '01.0-foundation-and-symbol-lock.md',
            'title' => 'Part 1.0: Foundation, Strict ULID Harness, SYMBOL_MANIFEST Lock & 5 Enterprise Glue Base',
            'dag_layer_badge' => 'Harness & Lock',
            'entities_count' => 1,
            'estimated_loc' => 650,
            'objective' => 'Menginisialisasi ruang kerja terisolasi, membekukan .agents/SYMBOL_MANIFEST.json, trait BelongsToOrganization, dan kerangka 5 Enterprise Glue.',
            'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART 1.0: FOUNDATION, STRICT ULID HARNESS & SYMBOL LOCK ({$appName})
> **Project Isolation ID:** `{$isolationId}` (100% Isolated Workspace — DILARANG mencampur artefak proyek lain)
> **Stack Topology:** `{$stackCode}` | **Primary Sector:** `{$sectorCode}`

## Kontrak Wajib Part 1.0:
1. Pastikan berkas `.agents/SYMBOL_MANIFEST.json`, `.agents/ENTERPRISE_MENU_MATRIX.md`, dan `.agents/graphify/graph.json` tersimpan utuh sebagai Single Source of Truth penamaan class, method `__invoke()`, DTO, dan kolom ULID.
2. Bangun base multi-tenant trait `App\Models\Concerns\BelongsToOrganization` (Global Scope `organization_id` anti-IDOR HTTP 404) serta `HasUlids` 26-karakter (`CHAR(26)` / `VARCHAR(26)` — DILARANG `uuid` native PostgreSQL atau `BIGSERIAL`).
3. Siapkan direktori `app/Actions`, `app/Data`, `app/Enums`, dan `app/Domains/Rules/DeterministicDecisionMatrix.php` berbasis PHP 8.4+ `match()`.
4. Buat tes fondasi `tests/Feature/Part1_0_FoundationAndHarnessTest.php` yang memverifikasi keberadaan `.agents/SYMBOL_MANIFEST.json` dan isolasi tenant.

{$gate1}
PROMPT,
        ];

        // =========================================================================
        // PARTS 2.1 .. 2.N: KAHN'S DAG TOPOLOGICAL DATABASE & MODEL SUB-CHUNKS
        // =========================================================================
        $schemaSubChunks = $dag['schema_sub_chunks'] ?? [];
        foreach ($schemaSubChunks as $idx => $chunk) {
            $partCode = (string) $chunk['part_code']; // e.g. 2.1, 2.2
            $subNum = $idx + 1;
            $layerLabel = (string) $chunk['layer_label'];
            $tableNamesList = implode(', ', $chunk['table_names']);

            $sliceManifest = [];
            foreach ($chunk['table_names'] as $tName) {
                if (isset($entitiesByTable[$tName])) {
                    $ent = $entitiesByTable[$tName];
                    $sliceManifest[$ent['entity']] = [
                        'model_fqcn' => $ent['model_fqcn'],
                        'table' => $ent['table'],
                        'dag_layer' => $ent['dag_layer'],
                        'status_enum_fqcn' => $ent['status_enum_fqcn'],
                        'exact_columns' => $ent['exact_columns'],
                        'exact_relations' => $ent['exact_relations'],
                    ];
                }
            }
            $sliceJson = json_encode($sliceManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $testName = "Part2_{$subNum}_SchemaAndModelTest";
            $gate2 = self::buildMandatoryVerificationGate($partCode, $testName);
            $chunkTablesCount = count($chunk['table_names']);

            $parts[] = [
                'part_code' => $partCode,
                'major_phase' => 2,
                'filename' => sprintf('02.%d-schema-dag-chunk-%d.md', $subNum, $subNum),
                'title' => "Part {$partCode}: Database Migrations, Models & Enums — {$layerLabel} ({$chunkTablesCount} Tabel)",
                'dag_layer_badge' => $layerLabel,
                'entities_count' => $chunkTablesCount,
                'estimated_loc' => $chunkTablesCount * 240,
                'objective' => "Membangun migrasi PostgreSQL 16 Strict ULID, Model Eloquent, dan Status Enum secara urut DAG untuk tabel: {$tableNamesList}.",
                'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART {$partCode}: TOPOLOGICAL DATABASE & MODELS ({$layerLabel} — {$appName})
> **Anti-Truncation Guarantee:** Part ini dibatasi hanya untuk **{$chunkTablesCount} entitas** yang sudah diurutkan dengan **Kahn's Topological Sort** sehingga seluruh `foreignUlid` dijamin merujuk pada tabel yang sudah dibuat di Layer sebelumnya!
> **Target Tables ({$layerLabel}):** `{$tableNamesList}`

## FROZEN SYMBOL MANIFEST SLICE (DILARANG MENGUBAH NAMA CLASS / KOLOM / RELASI):
```json
{$sliceJson}
```

## Instruksi Eksekusi Presisi:
1. Buat file migrasi Laravel menggunakan `\$table->ulid('id')->primary()`, `\$table->foreignUlid(...)`, dan `DECIMAL(19,4)` untuk seluruh nominal uang/stok (0 `FLOAT`).
2. Buat Model Eloquent (`HasUlids`, `BelongsToOrganization` bila memiliki `organization_id`) dan PHP Backed Enum untuk `status_enum_fqcn` persis sesuai JSON di atas.
3. Buat feature test `tests/Feature/{$testName}.php` yang memverifikasi pembuatan record ULID dan relasi Eloquent untuk ke-{$chunkTablesCount} tabel ini.

{$gate2}
PROMPT,
            ];
        }

        // =========================================================================
        // PARTS 3.1 .. 3.M: DOMAIN ACTIONS, DTOs, LOCKS & 5 ENTERPRISE GLUE GUARDS
        // =========================================================================
        $actionChunks = array_chunk(array_values($manifest['entities'] ?? []), $maxEntitiesPerPart);
        foreach ($actionChunks as $idx => $entGroup) {
            $subNum = $idx + 1;
            $partCode = "3.{$subNum}";
            $groupLetter = chr(64 + min(26, $subNum));

            $actionSlice = [];
            $entLabels = [];
            foreach ($entGroup as $entSpec) {
                $shortModel = class_basename((string) $entSpec['model_fqcn']);
                $entLabels[] = $shortModel;
                $actionSlice[$shortModel] = [
                    'model_fqcn' => $entSpec['model_fqcn'],
                    'table' => $entSpec['table'],
                    'bound_actions' => $entSpec['bound_actions'],
                ];
            }
            $entListStr = implode(', ', $entLabels);
            $actionSliceJson = json_encode($actionSlice, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $testName = "Part3_{$subNum}_DomainActionsTest";
            $gate3 = self::buildMandatoryVerificationGate($partCode, $testName);

            $parts[] = [
                'part_code' => $partCode,
                'major_phase' => 3,
                'filename' => sprintf('03.%d-domain-actions-group-%s.md', $subNum, strtolower($groupLetter)),
                'title' => "Part {$partCode}: Domain Actions, DTOs & Concurrency Locks — Group {$groupLetter} (" . count($entGroup) . ' Modul)',
                'dag_layer_badge' => "Actions Group {$groupLetter}",
                'entities_count' => count($entGroup),
                'estimated_loc' => count($entGroup) * 260,
                'objective' => "Mengimplementasikan Action `__invoke()`, DTO, `lockForUpdate()`, dan 5 Enterprise Glue Guards untuk: {$entListStr}.",
                'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART {$partCode}: ATOMIC DOMAIN ACTIONS & ENTERPRISE GLUE (GROUP {$groupLetter})
> **Target Entities:** `{$entListStr}`

## FROZEN ACTION & DTO SIGNATURE LOCK (WAJIB 100% IDENTIK DENGAN SYMBOL_MANIFEST.json):
```json
{$actionSliceJson}
```

## Instruksi Rekayasa Anti-Slop:
1. Buat seluruh class DTO (`readonly class`) dan Action dengan method **`__invoke(...)`** persis sesuai signature pada blok JSON di atas (DILARANG mengganti `__invoke` menjadi `handle` atau `execute`).
2. Bungkus seluruh mutasi finansial/kuota/stok dalam `DB::transaction()` + `lockForUpdate()`, patuhi `AssertFiscalPeriodOpenAction` (anti-backdate bulan tutup buku), `GenerateAtomicDocumentNumberAction` (penomoran atomik bebas `rand()`), serta `SubmitOrApproveMakerCheckerAction` untuk diskon > 15% / void / refund.
3. Buat pengujian konkurensi & invarian `tests/Feature/{$testName}.php`.

{$gate3}
PROMPT,
            ];
        }

        // =========================================================================
        // PART 4.0: AUTOMATED PEST VERIFICATION & REALISTIC SECTOR SEEDER GATE
        // =========================================================================
        $gate4 = self::buildMandatoryVerificationGate('4.0', 'DomainInvariantVerificationTest');
        $parts[] = [
            'part_code' => '4.0',
            'major_phase' => 4,
            'filename' => '04.0-pest-and-realistic-seeder-gate.md',
            'title' => 'Part 4.0: Automated Pest Invariant Gate & Realistic Sector Seeder (100% Green)',
            'dag_layer_badge' => 'QA & Seeder Gate',
            'entities_count' => count($dag['ordered_tables'] ?? []),
            'estimated_loc' => 800,
            'objective' => 'Menjalankan pengujian integrasi lintas modul (state machine, ledger balance == 0, fiscal lock, maker-checker) & RealisticSectorSeeder.',
            'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART 4.0: AUTOMATED PEST VERIFICATION & REALISTIC SEEDER ({$appName})
1. Lengkapi `database/seeders/RealisticSectorSeeder.php` mengikuti urutan `topological_migration_order` di `.agents/SYMBOL_MANIFEST.json` menggunakan terminologi asli industri `{$sectorCode}` (0 kata `lorem ipsum` / `foo bar`).
2. Buat `tests/Feature/DomainInvariantVerificationTest.php` yang menguji:
   - Keseimbangan Debit == Kredit (`DECIMAL(19,4)`),
   - Penolakan backdate pada `sys_accounting_periods` berstatus `CLOSED_FINAL`,
   - Penolakan `maker_id === checker_id` pada `sys_approval_requests`,
   - Penolakan transisi state ilegal & penomoran dokumen atomik berurutan.

{$gate4}
PROMPT,
        ];

        // =========================================================================
        // PARTS 5.1 .. 5.5: SECTORAL UI/UX SCREENS CHUNKED PER L1-L4 MENU DIVISION
        // =========================================================================
        $archetypePrimary = (string) ($schema['sectoral_ui_ux_archetype']['primary_archetype'] ?? 'POS_SPLIT_TERMINAL');
        $boundActionsSummary = [];
        foreach (array_slice(array_values($manifest['entities'] ?? []), 0, 14) as $entSpec) {
            foreach (($entSpec['bound_actions'] ?? []) as $actSpec) {
                $boundActionsSummary[] = $actSpec['fqcn'] . ' -> ' . $actSpec['signature'];
            }
        }
        $actionsRefBlock = implode("\n", array_map(fn ($line) => "- `{$line}`", array_slice($boundActionsSummary, 0, 12)));

        foreach (($menuMatrix['divisions'] ?? []) as $div) {
            $divNum = (int) ($div['division_number'] ?? 1);
            $partCode = "5.{$divNum}";
            $divTitle = (string) ($div['division_name']['id'] ?? "Divisi {$divNum}");
            $activeMenus = array_values(array_filter(
                (array) ($div['menus'] ?? []),
                fn ($m) => ! empty($m['included_in_v1'])
            ));

            if (empty($activeMenus)) {
                continue;
            }

            $menuBullets = implode("\n", array_map(
                fn ($m) => "- `[{$m['code']}]` **{$m['title']}** (`" . implode(', ', $m['l4_capabilities'] ?? []) . '`)',
                $activeMenus
            ));
            $testName = "Part5_{$divNum}_UiDivisionTest";
            $gate5 = self::buildMandatoryVerificationGate($partCode, $testName);

            $parts[] = [
                'part_code' => $partCode,
                'major_phase' => 5,
                'filename' => sprintf('05.%d-ui-division-%d.md', $divNum, $divNum),
                'title' => "Part {$partCode}: Sectoral Anti-Slop UI — {$divTitle} (" . count($activeMenus) . ' Layar)',
                'dag_layer_badge' => "UI Divisi {$divNum}/5",
                'entities_count' => count($activeMenus),
                'estimated_loc' => count($activeMenus) * 220,
                'objective' => "Membangun layar operasional anti-slop untuk {$divTitle} terhubung presisi ke Action di SYMBOL_MANIFEST.json.",
                'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART {$partCode}: SECTORAL UI/UX — {$divTitle} ({$appName})
> **Industrial UI Archetype:** `{$archetypePrimary}` | **Zero Cross-Part Drift:** Panggil Action persis sesuai `.agents/SYMBOL_MANIFEST.json`!

## Daftar Sub-Menu L1–L4 yang WAJIB Dibangun pada Part {$partCode}:
{$menuBullets}

## Referensi Cepat Bound Actions (`SYMBOL_MANIFEST.json`):
{$actionsRefBlock}

## Standar Mutlak Zero-Slop UI:
1. **HARAM** `rounded-full`, gradien warna-warni `bg-gradient-to-*`, font pudar di latar terang, CDN eksternal, dan `window.alert()`/`confirm()`.
2. **WAJIB** sudut tajam/tipis (`rounded-none` / `rounded-xs` / `rounded-sm`), kontras tinggi Dark & Light mode WCAG AA/AAA, Keyset Cursor Pagination `O(1)` (`cursorPaginate()`), sticky header, tombol X clear pada search, `tabular-nums` + pemisah ribuan, serta cetak Thermal 80mm / A4 SVG QR lokal bila relevan.
3. Buat feature test render & klik aksi `tests/Feature/{$testName}.php`.

{$gate5}
PROMPT,
            ];
        }

        // =========================================================================
        // PART 6.0: 4-PILLAR AI COPILOT, DAY-2 OPS & ZERO-DOWNTIME DEPLOY PACK
        // =========================================================================
        $gate6 = self::buildMandatoryVerificationGate('6.0', 'Part6_0_AiCopilotAndDay2OpsTest', [
            '`bash verify-blueprint.sh` (Ensure 0 anti-slop violations across database, UI, and security)',
        ]);
        $parts[] = [
            'part_code' => '6.0',
            'major_phase' => 6,
            'filename' => '06.0-ai-copilot-and-production-pack.md',
            'title' => 'Part 6.0: 4-Pillar AI Copilot (RAG+CAG+KAG+Tools), Outbox Replay & Production Pack',
            'dag_layer_badge' => 'AI & Day-2 Ops',
            'entities_count' => 4,
            'estimated_loc' => 850,
            'objective' => 'Menyinkronkan AI Copilot Tri-Hybrid pgvector, RBAC, Smart Guide, SHA-256 Audit Log, 4-Lane Queue, & deploy.sh.',
            'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART 6.0: 4-PILLAR AI COPILOT & PRODUCTION DEPLOYMENT ({$appName})
1. Sinkronkan **4 Pilar Tata Kelola Enterprise** (AI Copilot RAG `pgvector` 768-dim + CAG + KAG + Native Tools tervalidasi RBAC, Smart Guide Admin, RBAC Policies, dan SHA-256 `AuditLog`).
2. Hubungkan `sys_integration_logs` ke antarmuka 1-Click Webhook/Outbox Replay Admin dan siapkan health probes (`/healthz`, `/livez`, `/readyz`) + `deploy.sh`.
3. Buat feature test `tests/Feature/Part6_0_AiCopilotAndDay2OpsTest.php`.

{$gate6}
PROMPT,
        ];

        // =========================================================================
        // PART 7.0: SECURITY RED-TEAM, TENANT-LEAKAGE & ENTERPRISE GLUE AUDIT
        // =========================================================================
        $gate7 = self::buildMandatoryVerificationGate('7.0', 'TenantIsolationSecurityTest', [
            '`php artisan pos:check-env` (Validate .env vault completeness before production release)',
        ]);
        $parts[] = [
            'part_code' => '7.0',
            'major_phase' => 7,
            'filename' => '07.0-security-redteam-and-glue-audit.md',
            'title' => 'Part 7.0: Automated Security Red-Team, Tenant IDOR, Fiscal Lock & Maker-Checker Suite',
            'dag_layer_badge' => 'Red-Team Final Gate',
            'entities_count' => 6,
            'estimated_loc' => 750,
            'objective' => 'Menjalankan pengujian penetrasi otomatis (Cross-Tenant IDOR, Livewire Snapshot Tamper, Private Disk Signed URL, Prompt Injection, Fiscal Cut-Off & Maker-Checker).',
            'prompt' => <<<PROMPT
# PROJECT OS v6.0 — PART 7.0: AUTOMATED SECURITY RED-TEAM & FINAL RELEASE GATE ({$appName})
1. Lengkapi dan jalankan `tests/Feature/Security/TenantIsolationSecurityTest.php` mencakup:
   - **REDTEAM-01**: Cross-Tenant IDOR (`ORG-ALPHA` -> `ORG-BETA` wajib HTTP `404 Not Found`),
   - **REDTEAM-02**: Livewire `wire:snapshot` property tampering (`#[Locked]`),
   - **REDTEAM-03**: Private Disk (`sys_reason_evidences`) akses tanpa Signed URL 5 menit wajib HTTP `403`,
   - **REDTEAM-04**: AI Native Tool Prompt Injection oleh role non-admin wajib diblokir `guardMutation()`,
   - **REDTEAM-05**: Percobaan backdate ke bulan `CLOSED_FINAL` & self-approval `maker_id === checker_id` wajib ditolak 100%.
2. Jika terhubung ke `neriahpro-mcp`, panggil `neriahpro_report_completion(phase_number: 7)` setelah seluruh gate hijau.

{$gate7}
PROMPT,
        ];

        return $parts;
    }

    /**
     * Helper to convert StudlyCase entity name into clean camelCase variable prefix.
     */
    private static function entityVar(string $studly): string
    {
        return lcfirst($studly);
    }
}
