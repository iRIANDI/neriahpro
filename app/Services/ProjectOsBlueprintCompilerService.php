<?php

namespace App\Services;

use App\Models\PosPrimitive;
use App\Models\PosProject;
use App\Models\PosRulePack;
use App\Models\PosSector;
use App\Models\VisionBlueprint;
use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProjectOsBlueprintCompilerService
{
    public const SCHEMA_URI = 'https://neriahpro.com/schemas/project-os-v1.json';

    /**
     * Check safely whether pos_sectors table is ready in DB.
     */
    protected static function hasMatrixTables(): bool
    {
        try {
            return Schema::hasTable('pos_sectors') && Schema::hasTable('pos_primitives');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Build in-memory PosSector & PosPrimitive collections when DB migration hasn't run yet.
     */
    protected static function buildInMemoryMatrix(): array
    {
        $primitives = collect(ProjectOsMatrixCatalog::primitives())->map(function (array $p): PosPrimitive {
            $m = new PosPrimitive($p);
            $m->id = $m->newUniqueId();

            return $m;
        })->keyBy('code');

        $sectors = collect(ProjectOsMatrixCatalog::sectors())->map(function (array $s) use ($primitives): PosSector {
            $mandatory = $s['mandatory_primitives'] ?? [];
            $optional = $s['optional_primitives'] ?? [];
            unset($s['mandatory_primitives'], $s['optional_primitives']);

            $sector = new PosSector($s);
            $sector->id = $sector->newUniqueId();

            $attached = collect();
            $order = 1;
            foreach ($mandatory as $code) {
                if ($primitives->has($code)) {
                    $clone = clone $primitives->get($code);
                    $clone->setRelation('pivot', new \App\Models\PosSectorPrimitive(['is_mandatory' => true, 'sort_order' => $order++]));
                    $attached->push($clone);
                }
            }
            foreach ($optional as $code) {
                if ($primitives->has($code)) {
                    $clone = clone $primitives->get($code);
                    $clone->setRelation('pivot', new \App\Models\PosSectorPrimitive(['is_mandatory' => false, 'sort_order' => $order++]));
                    $attached->push($clone);
                }
            }
            $sector->setRelation('primitives', $attached);

            return $sector;
        });

        return ['sectors' => $sectors, 'primitives' => $primitives];
    }

    /**
     * Retrieve O(1) cached 12-Sector + 12-Primitive + Rule Pack catalog.
     */
    public static function getCachedCatalog(): array
    {
        ProjectOsMatrixCatalog::ensureSeeded();

        if (! self::hasMatrixTables()) {
            $mem = self::buildInMemoryMatrix();

            return [
                'sectors' => $mem['sectors']->map(fn (PosSector $s) => [
                    'id' => $s->id,
                    'code' => $s->code,
                    'sector_number' => $s->sector_number,
                    'name' => $s->name,
                    'description' => $s->description,
                    'domain_terminology' => $s->domain_terminology,
                    'blueprint_invariants' => $s->blueprint_invariants,
                    'default_compliance_standards' => $s->default_compliance_standards,
                    'detection_keywords' => $s->detection_keywords ?? [],
                    'mandatory_primitives' => $s->primitives->filter(fn ($p) => $p->pivot?->is_mandatory)->pluck('code')->values()->all(),
                    'optional_primitives' => $s->primitives->reject(fn ($p) => $p->pivot?->is_mandatory)->pluck('code')->values()->all(),
                ])->values()->all(),
                'primitives' => $mem['primitives']->values()->map(fn (PosPrimitive $p) => [
                    'id' => $p->id,
                    'code' => $p->code,
                    'name' => $p->name,
                    'category' => $p->category,
                    'description' => $p->description,
                    'table_names' => array_values(array_map(fn ($t) => $t['table'] ?? '', $p->schema_blueprints ?? [])),
                    'state_machine_entities' => array_values(array_map(fn ($sm) => $sm['entity'] ?? '', $p->state_machines ?? [])),
                    'detection_keywords' => $p->detection_keywords ?? [],
                ])->all(),
                'rule_packs' => ProjectOsMatrixCatalog::rulePacks(),
            ];
        }

        return Cache::rememberForever(PosSector::CACHE_KEY, function (): array {
            $sectors = PosSector::query()
                ->where('is_active', true)
                ->with('primitives')
                ->orderBy('sector_number')
                ->get()
                ->map(function (PosSector $s): array {
                    $mandatory = [];
                    $optional = [];
                    foreach ($s->primitives as $p) {
                        if ($p->pivot?->is_mandatory) {
                            $mandatory[] = $p->code;
                        } else {
                            $optional[] = $p->code;
                        }
                    }

                    return [
                        'id' => $s->id,
                        'code' => $s->code,
                        'sector_number' => $s->sector_number,
                        'name' => $s->name,
                        'description' => $s->description,
                        'domain_terminology' => $s->domain_terminology,
                        'blueprint_invariants' => $s->blueprint_invariants,
                        'default_compliance_standards' => $s->default_compliance_standards,
                        'detection_keywords' => $s->detection_keywords ?? [],
                        'mandatory_primitives' => $mandatory,
                        'optional_primitives' => $optional,
                    ];
                })
                ->values()
                ->all();

            $primitives = PosPrimitive::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get()
                ->map(fn (PosPrimitive $p): array => [
                    'id' => $p->id,
                    'code' => $p->code,
                    'name' => $p->name,
                    'category' => $p->category,
                    'description' => $p->description,
                    'table_names' => array_values(array_map(fn ($t) => $t['table'] ?? '', $p->schema_blueprints ?? [])),
                    'state_machine_entities' => array_values(array_map(fn ($sm) => $sm['entity'] ?? '', $p->state_machines ?? [])),
                    'detection_keywords' => $p->detection_keywords ?? [],
                ])
                ->values()
                ->all();

            $rulePacks = PosRulePack::query()
                ->orderBy('layer')
                ->orderBy('slug')
                ->get()
                ->map(fn (PosRulePack $r): array => [
                    'id' => $r->id,
                    'slug' => $r->slug,
                    'name' => $r->name,
                    'layer' => $r->layer,
                    'enforcement_level' => $r->enforcement_level,
                    'is_global_default' => $r->is_global_default,
                ])
                ->values()
                ->all();

            return [
                'sectors' => $sectors,
                'primitives' => $primitives,
                'rule_packs' => $rulePacks,
            ];
        });
    }

    /**
     * Deterministically resolve Primary Sector + Cross-Sector Composable Primitives from text corpus & explicit selections.
     */
    public static function resolveSectorAndPrimitives(
        string $corpus,
        ?string $explicitSectorCode = null,
        array $explicitPrimitiveCodes = []
    ): array {
        ProjectOsMatrixCatalog::ensureSeeded();

        $normalized = mb_strtolower($corpus);
        if (self::hasMatrixTables()) {
            $sectors = PosSector::query()->where('is_active', true)->with('primitives')->orderBy('sector_number')->get();
            $allPrimitives = PosPrimitive::query()->where('is_active', true)->get()->keyBy('code');
        } else {
            $mem = self::buildInMemoryMatrix();
            $sectors = $mem['sectors'];
            $allPrimitives = $mem['primitives'];
        }

        if ($sectors->isEmpty()) {
            return [
                'sector' => null,
                'primitives' => collect(),
                'architecture_archetype' => 'SECTOR_CANONICAL',
                'sector_scores' => [],
            ];
        }

        // 1. Score all 12 sectors (use word-boundary match for short acronyms <= 4 chars to avoid false hits like 'erp' inside 'terpadu')
        $matchesKeyword = static function (string $haystack, string $kwLower): bool {
            if ($kwLower === '') {
                return false;
            }
            if (mb_strlen($kwLower) <= 4 && ! str_contains($kwLower, ' ')) {
                return (bool) preg_match('/\b' . preg_quote($kwLower, '/') . '\b/u', $haystack);
            }

            return str_contains($haystack, $kwLower);
        };

        $scores = [];
        foreach ($sectors as $sector) {
            $score = 0;
            foreach (($sector->detection_keywords ?? []) as $kw) {
                $kwLower = mb_strtolower(trim((string) $kw));
                if ($matchesKeyword($normalized, $kwLower)) {
                    $score += str_contains($kwLower, ' ') ? 3 : 2;
                }
            }
            $scores[$sector->code] = $score;
        }

        // 2. Pick primary sector (explicit override wins if valid)
        $primarySector = null;
        if (! empty($explicitSectorCode)) {
            $primarySector = $sectors->firstWhere('code', $explicitSectorCode);
        }

        if (! $primarySector) {
            arsort($scores);
            $topCode = array_key_first($scores);
            $topScore = $scores[$topCode] ?? 0;

            // Default fallback to Sector 8 (Professional Services / B2B SaaS Workspace) or Sector 1 if zero keywords hit
            $primarySector = $topScore > 0
                ? $sectors->firstWhere('code', $topCode)
                : ($sectors->firstWhere('code', 'SECTOR_08_PROFESSIONAL_PSA') ?? $sectors->first());
        }

        // 3. Collect mandatory primitives of primary sector
        $selectedCodes = [];
        $mandatoryCodes = [];
        foreach ($primarySector->primitives as $prim) {
            if ($prim->pivot?->is_mandatory) {
                $selectedCodes[$prim->code] = true;
                $mandatoryCodes[$prim->code] = true;
            }
        }

        // 4. Add explicitly requested primitives from UI / API
        foreach ($explicitPrimitiveCodes as $pCode) {
            $pCode = trim((string) $pCode);
            if ($pCode !== '' && $allPrimitives->has($pCode)) {
                $selectedCodes[$pCode] = true;
            }
        }

        // 5. Auto-detect cross-sector primitives from corpus for Composite Business Models
        // (e.g. Badminton Club = Sector 2 Space Booking + Subscription/Coaching + POS Catalog/Inventory + Double-Entry Ledger)
        foreach ($allPrimitives as $code => $primitive) {
            if (isset($selectedCodes[$code])) {
                continue;
            }
            $hitCount = 0;
            foreach (($primitive->detection_keywords ?? []) as $kw) {
                $kwLower = mb_strtolower(trim((string) $kw));
                if ($matchesKeyword($normalized, $kwLower)) {
                    $hitCount += str_contains($kwLower, ' ') ? 2 : 1;
                }
            }
            if ($hitCount >= 2) {
                $selectedCodes[$code] = true;
            }
        }

        // Dependency integrity: Inventory Ledger or BoM requires Catalog SKU; Fund Disbursement requires Double-Entry Ledger
        if ((isset($selectedCodes['PRIMITIVE_INVENTORY_LEDGER']) || isset($selectedCodes['PRIMITIVE_BOM_WORK_ORDER'])) && $allPrimitives->has('PRIMITIVE_CATALOG_SKU')) {
            $selectedCodes['PRIMITIVE_CATALOG_SKU'] = true;
        }
        if (isset($selectedCodes['PRIMITIVE_FUND_DISBURSEMENT_IMPACT']) && $allPrimitives->has('PRIMITIVE_DOUBLE_ENTRY_LEDGER')) {
            $selectedCodes['PRIMITIVE_DOUBLE_ENTRY_LEDGER'] = true;
        }

        // Always include Double-Entry Ledger when payment / billing / invoice / Midtrans / revenue flows are mentioned
        if (! isset($selectedCodes['PRIMITIVE_DOUBLE_ENTRY_LEDGER']) && $allPrimitives->has('PRIMITIVE_DOUBLE_ENTRY_LEDGER')) {
            foreach (['bayar', 'payment', 'midtrans', 'invoice', 'tagihan', 'omzet', 'pendapatan', 'kasir', 'pos', 'transaksi'] as $finKw) {
                if (str_contains($normalized, $finKw)) {
                    $selectedCodes['PRIMITIVE_DOUBLE_ENTRY_LEDGER'] = true;
                    break;
                }
            }
        }

        $resolvedPrimitives = collect(array_keys($selectedCodes))
            ->map(fn (string $code) => $allPrimitives->get($code))
            ->filter()
            ->values();

        $nonMandatoryInjected = array_diff(array_keys($selectedCodes), array_keys($mandatoryCodes));
        $archetype = count($nonMandatoryInjected) > 0 ? 'COMPOSITE' : 'SECTOR_CANONICAL';

        return [
            'sector' => $primarySector,
            'primitives' => $resolvedPrimitives,
            'architecture_archetype' => $archetype,
            'sector_scores' => $scores,
        ];
    }

    /**
     * Compile the deterministic Project OS Universal Blueprint Schema v1.0 JSON & persist PosProject.
     */
    public static function compileUniversalSchema(?VisionBlueprint $blueprint = null, array $options = []): array
    {
        $meta = is_array($blueprint?->user_metadata) ? $blueprint->user_metadata : [];
        $explicitSector = $options['primary_sector']
            ?? ($meta['composable_matrix']['primary_sector_code'] ?? null)
            ?? ($meta['primary_sector'] ?? null);

        $explicitPrimitives = $options['selected_primitives']
            ?? ($meta['composable_matrix']['injected_primitives'] ?? [])
            ?? ($meta['selected_primitives'] ?? []);

        if (! is_array($explicitPrimitives)) {
            $explicitPrimitives = [];
        }

        $corpus = implode(' ', array_filter([
            $options['corpus'] ?? '',
            $blueprint?->nama_bisnis ?? '',
            $blueprint?->masalah_utama ?? '',
            $blueprint?->tujuan_utama ?? '',
            $blueprint?->target_audiens ?? '',
            $blueprint?->aktor_sistem ?? '',
            is_array($blueprint?->fitur_wajib) ? implode(' ', $blueprint->fitur_wajib) : (string) ($blueprint?->fitur_wajib ?? ''),
            is_array($blueprint?->fitur_tambahan) ? implode(' ', $blueprint->fitur_tambahan) : (string) ($blueprint?->fitur_tambahan ?? ''),
            $blueprint?->alur_kerja ?? '',
            $blueprint?->kebutuhan_integrasi ?? '',
        ]));

        $resolved = self::resolveSectorAndPrimitives($corpus, $explicitSector, $explicitPrimitives);
        /** @var PosSector|null $sector */
        $sector = $resolved['sector'];
        /** @var \Illuminate\Support\Collection<int, PosPrimitive> $primitives */
        $primitives = $resolved['primitives'];

        $appName = trim((string) ($options['app_name'] ?? $blueprint?->nama_bisnis ?? 'Target Enterprise Application'));
        $stackPreset = (string) ($options['stack_preset'] ?? 'LARAVEL13_FILAMENT5_LIVEWIRE4_FLUX_PGSQL16_RAILPACK');

        // Build Data Dictionary: Base Multi-Tenant Anchor + All Primitive Tables + Custom Blueprint Entities
        $dataDictionary = [
            [
                'table' => 'organizations',
                'tenant_scoped' => false,
                'soft_deletes' => false,
                'source_module' => 'CORE_TENANCY_ANCHOR',
                'columns' => [
                    ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                    ['name' => 'name', 'type' => 'VARCHAR(160)', 'nullable' => false],
                    ['name' => 'slug', 'type' => 'VARCHAR(160)', 'nullable' => false],
                    ['name' => 'timezone', 'type' => 'VARCHAR(64)', 'nullable' => false],
                    ['name' => 'default_currency', 'type' => 'CHAR(3)', 'nullable' => false],
                    ['name' => 'is_active', 'type' => 'BOOLEAN', 'nullable' => false],
                ],
                'unique_constraints' => [
                    ['name' => 'organizations_slug_unique', 'rule' => 'UNIQUE (slug)'],
                ],
            ],
        ];

        $seenTables = ['organizations' => true, 'users' => true];
        $concurrencyLocks = [];
        $stateMachines = [];
        $seenEntities = [];
        $permissions = [];
        $seenActions = [];
        $edgeCasePlaybooks = [];
        $geminiTools = [];

        foreach ($primitives as $primitive) {
            foreach (($primitive->schema_blueprints ?? []) as $tblDef) {
                $tName = $tblDef['table'] ?? '';
                if ($tName === '' || isset($seenTables[$tName])) {
                    continue;
                }
                $seenTables[$tName] = true;
                $tblDef['source_module'] = $primitive->code;
                $dataDictionary[] = $tblDef;
            }

            foreach (($primitive->concurrency_contracts ?? []) as $lock) {
                $lock['source_module'] = $primitive->code;
                $concurrencyLocks[] = $lock;
            }

            foreach (($primitive->state_machines ?? []) as $sm) {
                $entity = $sm['entity'] ?? '';
                if ($entity === '' || isset($seenEntities[$entity])) {
                    continue;
                }
                $seenEntities[$entity] = true;
                $sm['source_module'] = $primitive->code;
                $stateMachines[] = $sm;
            }

            foreach (($primitive->rbac_permissions ?? []) as $perm) {
                $act = $perm['action'] ?? '';
                if ($act === '' || isset($seenActions[$act])) {
                    continue;
                }
                $seenActions[$act] = true;
                $permissions[] = $perm;
            }

            foreach (($primitive->edge_case_playbooks ?? []) as $edge) {
                $edge['source_module'] = $primitive->code;
                $edgeCasePlaybooks[] = $edge;
            }

            foreach (($primitive->gemini_tool_declarations ?? []) as $tool) {
                $geminiTools[] = $tool;
            }
        }

        // Synthesize Custom Domain Entities from user's unique features if not already covered by primitive tables
        $customEntities = $options['custom_entities'] ?? self::extractCustomDomainTables($blueprint, $seenTables);
        foreach ($customEntities as $customTbl) {
            $tName = $customTbl['table'] ?? '';
            if ($tName !== '' && ! isset($seenTables[$tName])) {
                $seenTables[$tName] = true;
                $dataDictionary[] = $customTbl;
            }
        }

        $hasTables = self::hasMatrixTables();
        $rulePacks = $hasTables
            ? PosRulePack::query()->where('is_global_default', true)->orderBy('layer')->get()
            : collect(ProjectOsMatrixCatalog::rulePacks())->map(function (array $r): PosRulePack {
                $m = new PosRulePack($r);
                $m->id = $m->newUniqueId();

                return $m;
            });

        $schema = [
            '$schema' => self::SCHEMA_URI,
            'schema_version' => '1.0.0',
            'project_metadata' => [
                'name' => $appName,
                'blueprint_id' => $blueprint?->id,
                'architecture_archetype' => $resolved['architecture_archetype'],
                'primary_sector' => $sector?->code ?? 'SECTOR_08_PROFESSIONAL_PSA',
                'primary_sector_name' => $sector?->name ?? ['id' => 'Jasa Profesional & SaaS', 'en' => 'Professional Services & SaaS'],
                'domain_terminology' => $sector?->domain_terminology ?? [],
                'compliance_standards' => $sector?->default_compliance_standards ?? ['UU PDP No. 27/2022', 'OWASP ASVS L2'],
                'injected_primitives' => $primitives->pluck('code')->values()->all(),
                'active_rule_packs' => $rulePacks->pluck('slug')->values()->all(),
                'tenancy_model' => 'MULTI_TENANT_SHARED_DB_COLUMN_SCOPED',
                'tenant_identifier' => 'organization_id',
                'stack_preset' => $stackPreset,
            ],
            'tech_stack_contract' => [
                'backend' => 'Laravel 13+ / PHP 8.5+',
                'frontend_admin' => 'FilamentPHP v5 + Livewire v4',
                'frontend_public' => 'Livewire v4 + Flux UI + Tailwind CSS v4 + Alpine.js (100% Local Asset Bundle)',
                'database' => 'PostgreSQL 16+ (Strict ULID Mode, pgvector 768-dim HNSW)',
                'deployment' => 'Railpack (railpack.json) + FrankenPHP Caddyfile + Zero-Downtime 6-Scenario deploy.sh',
                'Forbidden_Assumptions' => [
                    'DO NOT use floating-point (float/double) for money or stock quantities; ALWAYS use DECIMAL(19,4) or BIGINT minor units.',
                    'DO NOT update balances or stock directly with static UPDATE queries; ALWAYS append to journal_lines or inventory_movements inside DB::transaction() with lockForUpdate().',
                    'DO NOT write raw business state transitions inside Controllers or Livewire components; ALWAYS encapsulate inside dedicated invokable Action classes wrapped in DB::transaction().',
                    'DO NOT use AUTO_INCREMENT ($table->id()) or $table->uuid(\'id\') for ULID models in PostgreSQL; ALWAYS use $table->ulid(\'id\')->primary() + HasUlids + foreignUlid().',
                    'DO NOT use OFFSET-based ->paginate(); ALWAYS use O(1) keyset ->cursorPaginate() ordered deterministically by id.',
                    'DO NOT generate AI-Slop UI (rounded-full capsule buttons, cheesy purple-pink gradients, thick shadow-2xl, hover:scale-105, native alert()/confirm(), or external CDN scripts).',
                ],
            ],
            'domain_invariants' => [
                'sector_invariants' => $sector?->blueprint_invariants ?? [],
                'currency_handling' => [
                    'type' => 'DECIMAL(19,4)',
                    'rounding_mode' => 'HALF_UP',
                    'default_currency' => 'IDR',
                ],
                'timezone_strategy' => [
                    'storage' => 'UTC',
                    'display' => 'Tenant-configured (organizations.timezone, e.g. Asia/Jakarta or Asia/Makassar)',
                ],
                'concurrency_locks' => $concurrencyLocks,
            ],
            'data_dictionary' => $dataDictionary,
            'state_machines' => $stateMachines,
            'rbac_matrix' => [
                'roles' => ['SUPER_ADMIN', 'TENANT_OWNER', 'OPERATIONAL_STAFF', 'FINANCE_AUDITOR', 'CUSTOMER'],
                'permissions' => $permissions,
            ],
            'edge_case_playbooks' => $edgeCasePlaybooks,
            'gemini_native_tools' => $geminiTools,
        ];

        // Persist or synchronize PosProject record in database when sector & table exist
        if ($hasTables && $sector && $sector->exists && Schema::hasTable('pos_projects') && ($blueprint?->exists || ! empty($options['persist_project']))) {
            $executionMd = self::renderExecutionBlueprintMarkdown($schema);
            $agentsMd = self::renderUnifiedAgentsMarkdown($schema);
            $graphJson = self::renderInitialGraphifyJson($schema);

            $lookup = $blueprint?->id
                ? ['vision_blueprint_id' => $blueprint->id]
                : ['app_name' => $appName, 'primary_sector_id' => $sector->id];

            PosProject::query()->updateOrCreate(
                $lookup,
                [
                    'user_id' => $options['user_id'] ?? auth()->id(),
                    'vision_blueprint_id' => $blueprint?->id,
                    'primary_sector_id' => $sector->id,
                    'app_name' => $appName,
                    'architecture_archetype' => $resolved['architecture_archetype'],
                    'tenancy_model' => 'MULTI_TENANT_SHARED_DB_COLUMN_SCOPED',
                    'tenant_identifier' => 'organization_id',
                    'stack_preset' => $stackPreset,
                    'selected_primitive_ids' => $primitives->pluck('id')->values()->all(),
                    'selected_rule_pack_ids' => $rulePacks->pluck('id')->values()->all(),
                    'custom_entities' => $customEntities,
                    'compiled_schema_json' => json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'compiled_execution_md' => $executionMd,
                    'compiled_agents_md' => $agentsMd,
                    'compiled_graph_json' => $graphJson,
                ]
            );
        }

        return $schema;
    }

    /**
     * Extract clean ULID-compliant custom entity blueprints from user feature lines without overlapping core primitive tables.
     */
    protected static function extractCustomDomainTables(?VisionBlueprint $blueprint, array $seenTables): array
    {
        if (! $blueprint || empty($blueprint->fitur_wajib)) {
            return [];
        }

        $rawFeatures = is_array($blueprint->fitur_wajib)
            ? $blueprint->fitur_wajib
            : preg_split('/[\r\n,;]+/', (string) $blueprint->fitur_wajib);

        $featureLines = array_values(array_filter(array_map('trim', $rawFeatures ?: [])));
        if (empty($featureLines)) {
            return [];
        }

        $stopWords = ['modul', 'sistem', 'fitur', 'manajemen', 'kelola', 'otomatis', 'integrasi', 'dashboard', 'portal', 'dan', 'untuk', 'dengan', 'data', 'laporan'];
        $customTables = [];

        foreach (array_slice($featureLines, 0, 4) as $idx => $featureLine) {
            $clean = preg_replace('/[^a-zA-Z0-9\s]/', ' ', strtolower((string) $featureLine));
            $words = array_values(array_filter(
                explode(' ', (string) $clean),
                fn ($w) => strlen($w) >= 4 && ! in_array($w, $stopWords, true)
            ));

            if (empty($words)) {
                continue;
            }

            $baseSlug = Str::snake(implode('_', array_slice($words, 0, 2)));
            $tableName = Str::plural($baseSlug);
            if ($tableName === '' || isset($seenTables[$tableName])) {
                continue;
            }

            $seenTables[$tableName] = true;
            $customTables[] = [
                'table' => $tableName,
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'CUSTOM_DOMAIN_EXTENSION_' . ($idx + 1),
                'feature_origin' => (string) $featureLine,
                'columns' => [
                    ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                    ['name' => 'created_by_user_id', 'type' => 'ULID', 'foreign' => 'users.id', 'nullable' => true],
                    ['name' => 'reference_code', 'type' => 'VARCHAR(48)', 'nullable' => false],
                    ['name' => 'title', 'type' => 'JSONB', 'nullable' => false],
                    ['name' => 'amount_value', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                    ['name' => 'attributes_json', 'type' => 'JSONB', 'nullable' => true],
                    ['name' => 'status', 'type' => 'VARCHAR(32)', 'nullable' => false, 'index' => true],
                ],
                'unique_constraints' => [
                    ['name' => "{$tableName}_org_ref_unique", 'rule' => 'UNIQUE (organization_id, reference_code)'],
                ],
            ];
        }

        return $customTables;
    }

    /**
     * Render Master Deterministic AI Execution Prompt (PROJECT_OS_EXECUTION_BLUEPRINT.md) with <ZERO_ASSUMPTION_DIRECTIVES>.
     */
    public static function renderExecutionBlueprintMarkdown(array $schema): string
    {
        $meta = $schema['project_metadata'] ?? [];
        $sectorCode = $meta['primary_sector'] ?? 'SECTOR_08_PROFESSIONAL_PSA';
        $sectorLabel = $meta['primary_sector_name']['id'] ?? $sectorCode;
        $primitives = implode(', ', $meta['injected_primitives'] ?? []);
        $compliance = implode(' | ', $meta['compliance_standards'] ?? []);

        $md = "# PROJECT OS EXECUTION BLUEPRINT\n";
        $md .= "> Generated by NeriahPro.com — Deterministic Software Specification (`{$meta['name']}`)\n\n";
        $md .= "<ZERO_ASSUMPTION_DIRECTIVES>\n";
        $md .= "1. STRICT COMPLIANCE: You are a deterministic execution engine. Do NOT invent columns, states, roles, or workflows outside this document and `.agents/BLUEPRINT_SCHEMA.json`.\n";
        $md .= "2. MISSING REQUIREMENT PROTOCOL: If an edge case or relation is not explicitly defined in the Data Dictionary or State Machine below, STOP and ask the developer before generating code.\n";
        $md .= "3. ATOMICITY: Every state transition listed under [3. Deterministic State Machines] MUST be implemented as a dedicated invokable Action class wrapped in `DB::transaction()`.\n";
        $md .= "4. LOCKING: Every query modifying `inventory_batches`, `inventory_movements`, `allocatable_resources`, `bookings`, `accounts`, `journal_entries`, `event_seats`, or `fund_programs` MUST acquire a row-level lock (`lockForUpdate()`) on the parent resource first.\n";
        $md .= "5. ZERO HARDCODE & STRICT ULID: Every business table MUST use `->ulid('id')->primary()` (`HasUlids`) and `DECIMAL(19,4)` for money/quantities. NEVER use `float`, `->id()`, `->uuid('id')`, `->paginate()`, or hardcoded entity names.\n";
        $md .= "</ZERO_ASSUMPTION_DIRECTIVES>\n\n";

        $md .= "## 1. Domain & Architecture Context\n";
        $md .= "- **Application Name:** `{$meta['name']}`\n";
        $md .= "- **Architecture Archetype:** `{$meta['architecture_archetype']}`\n";
        $md .= "- **Primary Functional Sector:** `{$sectorCode}` — {$sectorLabel}\n";
        $md .= "- **Active Core Primitives:** `{$primitives}`\n";
        $md .= "- **Compliance & Regulatory Baseline:** {$compliance}\n";
        $md .= "- **Multi-Tenancy Scope:** `{$meta['tenancy_model']}` — All tenant-owned tables MUST enforce `BelongsToTenant` global scope on `{$meta['tenant_identifier']}`.\n";
        $md .= "- **Target Stack Contract:** Laravel 13+ (PHP 8.5+) | Filament v5 | Livewire v4 | Flux UI | PostgreSQL 16 (`pgvector` 768-dim) | Railpack FrankenPHP.\n\n";

        if (! empty($schema['domain_invariants']['sector_invariants'])) {
            $md .= "### Sector Mandatory Invariants\n";
            foreach ($schema['domain_invariants']['sector_invariants'] as $inv) {
                $md .= "- {$inv}\n";
            }
            $md .= "\n";
        }

        $md .= "## 2. Exact Database Schema & Constraints\n";
        foreach (($schema['data_dictionary'] ?? []) as $table) {
            $scopeTag = ! empty($table['tenant_scoped']) ? 'Tenant-Scoped (`organization_id`)' : 'Global Anchor';
            $srcTag = $table['source_module'] ?? 'CORE';
            $md .= "### Table: `{$table['table']}` — *{$scopeTag} | Module: `{$srcTag}`*\n";
            $md .= "| Column | Exact Type | Modifiers / Foreign Key / Enum |\n";
            $md .= "| :--- | :--- | :--- |\n";
            foreach (($table['columns'] ?? []) as $col) {
                $mods = [];
                if (! empty($col['primary'])) {
                    $mods[] = 'PRIMARY KEY (26-char ULID)';
                }
                if (! empty($col['foreign'])) {
                    $mods[] = "FK -> `{$col['foreign']}`";
                }
                if (! empty($col['enum_ref'])) {
                    $mods[] = "Enum: `{$col['enum_ref']}`";
                }
                if (! empty($col['index'])) {
                    $mods[] = 'INDEXED';
                }
                if (isset($col['nullable'])) {
                    $mods[] = $col['nullable'] ? 'NULLABLE' : 'NOT NULL';
                }
                $md .= "| `{$col['name']}` | `{$col['type']}` | " . implode(', ', $mods) . " |\n";
            }
            if (! empty($table['unique_constraints'])) {
                foreach ($table['unique_constraints'] as $uc) {
                    $md .= "- **Constraint `{$uc['name']}`:** `{$uc['rule']}`\n";
                }
            }
            $md .= "\n";
        }

        $md .= "## 3. Deterministic State Machines & Concurrency Locks\n";
        foreach (($schema['state_machines'] ?? []) as $sm) {
            $statesStr = implode('` -> `', $sm['states'] ?? []);
            $md .= "### Entity: `{$sm['entity']}` (`{$sm['enum_class']}` | Initial: `{$sm['initial_state']}`)\n";
            $md .= "- **Valid States:** `{$statesStr}`\n\n";
            $md .= "| From State(s) | To State | Dedicated Action Class | Mandatory Guards | Atomic Side Effects |\n";
            $md .= "| :--- | :--- | :--- | :--- | :--- |\n";
            foreach (($sm['transitions'] ?? []) as $tr) {
                $from = implode(', ', $tr['from'] ?? []);
                $guards = implode(', ', $tr['guards'] ?? []);
                $effects = implode(', ', $tr['side_effects'] ?? []);
                $md .= "| `{$from}` | `{$tr['to']}` | `{$tr['action']}` | {$guards} | {$effects} |\n";
            }
            $md .= "\n";
        }

        if (! empty($schema['domain_invariants']['concurrency_locks'])) {
            $md .= "### Pessimistic Concurrency Lock Contracts\n";
            $md .= "| Operation | Locking Mechanism | Timeout | Deterministic Fallback |\n";
            $md .= "| :--- | :--- | :--- | :--- |\n";
            foreach ($schema['domain_invariants']['concurrency_locks'] as $lk) {
                $md .= "| {$lk['operation']} | `{$lk['mechanism']}` | {$lk['timeout_seconds']}s | {$lk['fallback_action']} |\n";
            }
            $md .= "\n";
        }

        $md .= "## 4. Role-Based Access Control (RBAC) & Policies\n";
        $md .= "- **Canonical Roles:** `" . implode('`, `', $schema['rbac_matrix']['roles'] ?? []) . "`\n\n";
        $md .= "| Action Permission | Allowed Roles | Requires Reason | SHA-256 Audit Logged |\n";
        $md .= "| :--- | :--- | :--- | :--- |\n";
        foreach (($schema['rbac_matrix']['permissions'] ?? []) as $perm) {
            $roles = implode(', ', $perm['allowed'] ?? []);
            $reqReason = ! empty($perm['requires_reason']) ? 'YES' : 'NO';
            $audited = ! empty($perm['audit_logged']) ? 'YES' : 'STANDARD';
            $md .= "| `{$perm['action']}` | `{$roles}` | {$reqReason} | {$audited} |\n";
        }
        $md .= "\n";

        $md .= "## 5. Edge-Case & Conflict Resolution Playbook\n";
        foreach (($schema['edge_case_playbooks'] ?? []) as $idx => $play) {
            $num = $idx + 1;
            $md .= "{$num}. **Scenario (`{$play['source_module']}`):** {$play['scenario']}\n";
            $md .= "   - **Deterministic Resolution:** {$play['resolution_rule']}\n";
        }
        $md .= "\n";

        $md .= "## 6. Step-by-Step Implementation Order for AI (6-Stage ECC Loop)\n";
        $md .= "1. **Enums First:** Generate PHP 8.5 Backed Enums (`app/Enums/*Status.php`) for every State Machine listed in Section 3.\n";
        $md .= "2. **ULID Migrations & Models:** Generate PostgreSQL 16 migrations (`->ulid('id')->primary()`, `->foreignUlid()`, `DECIMAL(19,4)`) and Eloquent Models (`HasUlids`, `BelongsToTenant`).\n";
        $md .= "3. **Atomic Domain Actions:** Generate Invokable Action classes (`app/Actions/*Action.php`) enforcing Guards, `DB::transaction()`, and `lockForUpdate()`.\n";
        $md .= "4. **Pest TDD Verification First:** Generate Feature Tests (`tests/Feature/*ContractTest.php`) proving every State Guard, Pessimistic Lock, Tenant Isolation, and Edge-Case Playbook BEFORE building UI.\n";
        $md .= "5. **Zero-Slop Filament v5 & Flux UI:** Bind Filament Resources (`Width::Full`, 12-col Grid, `TranslatableTabs`, `cursorPaginate()`) and Livewire Flux components exclusively to Action classes, strictly adhering to `.agents/UI_UX_CONTRACT.md`.\n";
        $md .= "6. **Graphify Sync & Railpack Deploy:** Update `.agents/graphify/graph.json`, run full Pest verification, and deploy via `./deploy.sh`.\n";

        return $md;
    }

    /**
     * BAGIAN 2: Master Zero-Slop UI/UX Design System Contract (UI_UX_CONTRACT.md).
     */
    public static function renderUiUxContractMarkdown(): string
    {
        return <<<'MD'
# ZERO-SLOP UI/UX DESIGN SYSTEM CONTRACT (AESTHETIC TIER: LINEAR / VERCEL / STRIPE)

<UI_ANTI_SLOP_DIRECTIVES>
Anda dilarang keras menghasilkan desain "AI Slop" (tampilan template murahan, penuh gradien warna pelangi, sudut membulat ekstrem seperti kapsul/pil, teks pudar berasio kontras rendah, atau popup browser natif). Ikuti parameter matematis di bawah ini secara mutlak:
</UI_ANTI_SLOP_DIRECTIVES>

## 1. Border Radius & Geometry (Strict Subtle Corners & Anti-Capsule Rule)
- **HARAM `rounded-full`, `rounded-2xl`, & `rounded-3xl` pada Tombol, Input, Badge, dan Card**:
  - Dilarang keras membuat tombol berbentuk kapsul (`rounded-full`) atau kartu bergelembung besar.
  - **Tombol & Input Field**: WAJIB menggunakan sudut tipis presisi `rounded-xs` (2px), `rounded-sm` (4px), atau maksimal `rounded-md` (6px) dengan tinggi terukur (`h-8`, `h-9`, atau `h-10`, `px-3.5`, `text-xs`/`text-sm font-medium`).
  - **Container Card / Panel / Modal**: WAJIB menggunakan `rounded-sm` (4px) atau maksimal `rounded-md` (6px) bergaya teknikal tajam (`rounded-none` diizinkan untuk grid brutalist presisi).
  - **Badge / Status Chip**: Gunakan `rounded-xs` atau `rounded-sm` (`px-2 py-0.5 text-xs font-bold border`), BUKAN kapsul bulat penuh.

## 2. Borders, Hairlines & Shadows (Crisp 1px Structural Precision)
- **HARAM Shadow Tebal & Mengambang (`shadow-lg`, `shadow-xl`, `shadow-2xl`) pada Kartu**:
  - Elevasi halaman dibangun melalui kontras permukaan solid dan *hairline border* 1px yang tajam.
  - **Standar Card & Panel**: `bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800` (tanpa shadow mengambang).
  - **Hover Micro-Effects**: `hover:scale-105` adalah HARAM. Gunakan transisi border & latar halus yang menjaga kontras teks 100%: `transition-colors duration-150 hover:border-zinc-400 dark:hover:border-zinc-600 hover:bg-zinc-50/90 dark:hover:bg-zinc-800/80`.

## 3. Color Palette, High-Contrast Legibility & Ban on Cheesy Gradients
- **HARAM Gradien AI Generik**:
  - Dilarang keras menggunakan `bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500` atau teks gradien `bg-clip-text text-transparent`.
- **Arsitektur Permukaan Solid & Rasio Kontras WCAG AA/AAA (Anti-Tenggelam)**:
  - **Background Canvas Utama**: `bg-zinc-50 dark:bg-zinc-950`
  - **Permukaan Kartu / Tabel**: `bg-white dark:bg-zinc-900`
  - **Tipografi Primer**: `text-zinc-950 dark:text-zinc-50 tracking-tight`
  - **Tipografi Sekunder**: `text-zinc-700 dark:text-zinc-300` (Dilarang font pastel pudar seperti `text-emerald-500` atau `text-zinc-400` di atas kartu putih).
  - **Badge Semantik Berkontras Tinggi**:
    - *Success*: `bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700`
    - *Warning*: `bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700`
    - *Danger*: `bg-rose-100 text-rose-950 border border-rose-400 dark:bg-rose-950/80 dark:text-rose-200 dark:border-rose-700`

## 4. Typography, Thousand Separators & `tabular-nums` Rigor
- **Wajib `tabular-nums` & Pemisah Ribuan untuk Semua Angka**:
  - Setiap nominal uang (>= 1.000 wajib diformat pemisah ribuan `.` ID / `,` EN), persentase, kode ULID, kuantitas stok, dan tanggal WAJIB memakai class `tabular-nums`.
- **Hierarchy Ukuran Font Ketat (Anti-Giant Text)**:
  - Judul Halaman Dashboard: `text-xl sm:text-2xl font-bold tracking-tight text-zinc-950 dark:text-white`.
  - Label Form & Header Tabel: `text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider`.

## 5. Layout, Viewport, Enterprise Table & Form Density
- **Full-Width Filament & Dashboard Containers**:
  - Setiap halaman Filament (`ListRecords`, `CreateRecord`, `EditRecord`, `ViewRecord`, `ManageRecords`, `Page`) WAJIB mendeklarasikan `getMaxContentWidth(): Width` yang mengembalikan `Width::Full`.
- **Asymmetrical 12-Column Grid**:
  - Gunakan `Grid::make(['default' => 1, 'xl' => 12])` dengan proporsi `xl:col-span-8` (kerja utama) dan `xl:col-span-4` (panel status/metadata), serta pasangan input pendek dalam `sm:grid-cols-2`.
- **Enterprise Table & List Standard**:
  - Wajib `cursorPaginate()` O(1), sticky floating header, freeze kolom kiri/kanan pada tabel lebar, tombol hapus cepat (`X`) pada kolom search, serta switcher Grid/List View & Row Density.

## 6. Interactive Modals, Toasts & Zero Native Popups
- **HARAM `window.alert()`, `alert()`, `confirm()`, `prompt()`**:
  - Wajib menggunakan sistem Toast terpusat (`window.showToast(...)`) dan Modal Alpine.js/Flux/Filament dengan `backdrop-blur-xs`, penjelasan dampak aksi, serta `wire:loading.attr="disabled"`.

## 7. Multi-Language Input Architecture (In-Place `TranslatableTabs`)
- **HARAM Global Locale Dropdown (`LocaleSwitcher`) di Pojok Atas Halaman**:
  - Semua field dwibahasa JSON (`id`/`en`) wajib memakai `TranslatableTabs` langsung pada input terkait dengan tombol terjemah 1-klik dan auto-sync saat simpan (`HasTranslatableTabs`).
MD;
    }

    /**
     * BAGIAN 3: Master Zero-Slop Engineering, PostgreSQL 16 & Security Rules (ENGINEERING_RULES.md).
     */
    public static function renderEngineeringRulesMarkdown(): string
    {
        return <<<'MD'
# ZERO-SLOP ENGINEERING, DATABASE & INFRASTRUCTURE RULES

## 1. Database Architecture & Strict PostgreSQL 16 Standards
1. **ULID Primary Keys Everywhere**:
   - Semua tabel domain bisnis WAJIB menggunakan `$table->ulid('id')->primary()` dan trait `HasUlids` pada Model Eloquent.
   - **HARAM `AUTO_INCREMENT` (`$table->id()`)** pada tabel bisnis.
   - **HARAM `$table->uuid('id')` untuk Model `HasUlids` di PostgreSQL**: PostgreSQL menolak string ULID 26-karakter pada kolom `uuid` (`SQLSTATE[22P02]`). Gunakan selalu `->ulid('id')` dan `->foreignUlid('...')`.
   - **Pengecualian Tabel Vendor**: Tabel internal framework/plugin (`jobs`, `failed_jobs`, `cache`, `sessions`, `activity_log`) mempertahankan struktur bawaan vendor agar 100% aman saat upgrade.
2. **O(1) Keyset Cursor Pagination Only**:
   - **HARAM `->paginate()` (OFFSET-based)** karena kompleksitas O(N) melumpuhkan database pada jutaan baris. Wajib gunakan `->cursorPaginate()` dengan fallback deterministik `->orderBy('id', 'desc')`.
3. **Strict PostgreSQL SQL Syntax (No MySQL-isms)**:
   - **HARAM String Kosong `""` pada Kolom JSON/JSONB**: Selalu kirim JSON valid (`'{}'`, `'[]'`) atau `NULL`.
   - **HARAM Fungsi Spesifik MySQL**: Dilarang menggunakan `YEAR(col)`, `MONTH(col)`, `JSON_UNQUOTE()`, `MATCH() AGAINST()`, atau `SET FOREIGN_KEY_CHECKS=0`. Gunakan `EXTRACT(YEAR FROM col)::integer`, `col->>'key'`, dan `Schema::disableForeignKeyConstraints()`.
   - **Self-Referencing Foreign Keys**: Untuk relasi ke tabel yang sama (`parent_id`), definisikan `foreignUlid` pada blok `Schema::table()` TERPISAH setelah `Schema::create()` selesai.
4. **Financial, Inventory & Concrete Relational Integrity (Zero Hardcode)**:
   - **HARAM `FLOAT` atau `DOUBLE` untuk Uang & Kuantitas**: Gunakan `DECIMAL(19,4)` (pembulatan `HALF_UP`) atau `BIGINT`.
   - **Immutable Ledger**: Saldo akun (`accounts`) atau stok gudang (`inventory_batches`) DILARANG di-`UPDATE` sembarangan tanpa baris mutasi (`journal_lines` / `inventory_movements`) di dalam `DB::transaction()` dengan `lockForUpdate()`.
   - **HARAM Hardcoded Magic Strings**: Dilarang mencari status bisnis dengan `LIKE '%DEMO%'` atau pencocokan slug/nama perusahaan statis. Wajib gunakan foreign key ULID konkret dan kolom enum status eksplisit.

## 2. Frontend Asset Sovereignty & Livewire 4 Reactivity Rules
1. **HARAM CDN Eksternal (100% Local Asset Sovereignty)**:
   - Dilarang memuat JS, CSS, font, icon, atau QR dari CDN luar (`cdn.tailwindcss.com`, `unpkg.com`, `cdnjs`, `jsdelivr`). Semua wajib dibundle lokal via `@vite` atau `public/js/vendor/`.
2. **Alpine.js Quote Escaping**:
   - Di dalam atribut HTML `x-data="..."`, **HARAM** menggunakan kutip mentah yang merusak parser DOM; gunakan `@js(...)`, `&quot;`, atau `Alpine.data(...)`.
3. **Livewire 4 & FilePond DOM Diffing Protection**:
   - Pada form dengan `FileUpload` / uploader media, **HARAM** memasang `->live(onBlur: true)` pada field teks yang menghancurkan instance FilePond saat upload berlangsung.
4. **Spatie Translatable Cast Protection**:
   - **HARAM** mendaftarkan atribut yang sudah ada di `$translatable` ke dalam `$casts = ['title' => 'array']` untuk mencegah *double-serialization crash*.

## 3. Agentic AI (Local MarkItDown + 3-Tier OCR + pgvector RAG + Native Tool Calling)
1. **Zero-Token Local Document Ingestion & 3-Tier OCR**:
   - Dokumen (`.pdf`, `.docx`, `.xlsx`, `.pptx`, `.csv`, `.json`, `.html`) wajib dikonversi secara lokal ke `.md` tanpa membakar token AI sebelum masuk konteks/vektor.
   - Gambar wajib melewati pipeline 3-Tier OCR (Tesseract Lokal -> 1-Shot Vision OCR Cache `.md` -> Neutral Metadata Fallback) agar base64 gambar tidak dikirim berulang kali di multi-turn chat.
2. **Separation of Read (RAG) and Write (Native Tool Calling)**:
   - Pembacaan pengetahuan menggunakan PostgreSQL `pgvector` (768-dimensi HNSW `<=>` + Reciprocal Rank Fusion).
   - Penulisan database WAJIB melalui `functionDeclarations` resmi dengan `Validator::make()`, Idempotency Guard 120 detik, `DB::transaction()`, dan rantai hash `SHA-256` pada `audit_logs`.
MD;
    }

    /**
     * BAGIAN 4: Ponytail + affaan-m/ECC Agentic Harness, Lifecycle & Instincts (AGENT_HARNESS_ECC.md).
     */
    public static function renderAgentHarnessEccMarkdown(): string
    {
        return <<<'MD'
# PROJECT OS AGENT HARNESS & EXECUTION PROTOCOL (PONYTAIL + ECC ENGINE)

## 1. The Ponytail + ECC 4-Rung Decision Ladder (Token & Context Optimization)
Untuk menghemat >50% konsumsi token dan mencegah *context pollution*:
- **Rung 1 (Framework Native First)**: Maksimalkan fitur bawaan Laravel 13, Filament v5, Livewire 4, Flux UI, dan Alpine.js sebelum membuat abstraksi baru.
- **Rung 2 (Graphify O(1) Lookup First)**: DILARANG melakukan *blind directory scanning*. Baca `.agents/graphify/graph.json` dan `.agents/BLUEPRINT_SCHEMA.json` terlebih dahulu, lalu baca baris spesifik yang relevan.
- **Rung 3 (Declarative Over Procedural)**: Utamakan Backed Enum, Schema Contract, Model Casts, dan Database Constraints daripada percabangan `if-else` prosedural.
- **Rung 4 (Surgical Diff Only)**: Terapkan perubahan kode secara presisi pada baris terdampak tanpa menulis ulang seluruh file.

## 2. The 6-Stage ECC Execution Loop (Mandatory Lifecycle per Task)
Setiap pengembangan fitur WAJIB melewati 6 tahapan deterministik ini:
1. **PLAN**: Periksa entitas di `.agents/graphify/graph.json` dan kontrak di `.agents/BLUEPRINT_SCHEMA.json`. Jika ada kebutuhan bisnis di luar spesifikasi, BERHENTI dan TANYA terlebih dahulu.
2. **TEST (TDD First)**: Tulis pengujian Pest PHP untuk memverifikasi *State Machine Guards*, *Pessimistic Concurrency Locks (`lockForUpdate()`)*, *Edge-Case Playbooks*, dan *Tenant Isolation* sebelum menyentuh UI.
3. **IMPLEMENT**: Bangun Migration (ULID + `DECIMAL(19,4)`), Model (`HasUlids`, `BelongsToTenant`), Enum, dan Invokable Action Class (`DB::transaction()`), lalu hubungkan ke Filament v5 / Flux UI.
4. **REVIEW (Self-Audit Against Anti-Slop Checklist)**:
   - Pastikan tidak ada `$table->id()`, `$table->uuid('id')` untuk ULID, `float`, atau `->paginate()`.
   - Pastikan tidak ada `rounded-full`, gradien warna-warni, font kontras pudar, `alert()`, atau CDN eksternal.
5. **VERIFY (4-Pillar Synchronization)**:
   - Sinkronkan: **(1) AI RAG & Native Tool Registry**, **(2) Smart Guide Admin**, **(3) RBAC Policies**, dan **(4) SHA-256 Chained Audit Log**.
6. **REMEMBER & DEPLOY**:
   - Perbarui `.agents/graphify/graph.json` setiap menambah Model/Action/Endpoint baru.
   - Commit & push ke repositori Git, lalu jalankan skenario `./deploy.sh [1-6]` yang sesuai.

## 3. Built-in Agent "Instincts" (Auto-Triggered by File Context)
- **Saat menyentuh `database/migrations/*.php`**:
  - *Instinct*: Wajib `ulid('id')->primary()`, `foreignUlid()`, `DECIMAL(19,4)`, larangan sintaks MySQL di PostgreSQL, dan pemisahan `Schema::table` untuk *self-referencing FK*.
- **Saat menyentuh `app/Actions/**/*.php`**:
  - *Instinct*: Wajib `DB::transaction()`, akuisisi `lockForUpdate()` pada parent resource dengan urutan ULID konsisten (anti-deadlock), validasi Guard Enum sebelum mutasi, dan pencatatan jejak audit.
- **Saat menyentuh `app/Filament/**/*.php`**:
  - *Instinct*: Wajib signature `\Filament\Schemas\Schema $form`, `Width::Full`, `Grid::make(['default' => 1, 'xl' => 12])`, `TranslatableTabs` in-place (tanpa `LocaleSwitcher` global), dan proteksi FilePond dari `->live(onBlur: true)`.
- **Saat menyentuh `resources/views/**/*.blade.php` atau `resources/js/**/*.jsx`**:
  - *Instinct*: Wajib `tabular-nums` & pemisah ribuan pada angka, sudut tipis maksimal `rounded-md` (haram `rounded-full`), hairline border 1px kontras tinggi dark/light, tanpa `alert()`, dan 100% aset lokal tanpa CDN.
MD;
    }

    /**
     * Render unified AGENTS.md / CLAUDE.md combining Zero-Assumption Directives, Sector Context, ECC Harness, UI/UX, and Engineering Rules.
     */
    public static function renderUnifiedAgentsMarkdown(array $schema): string
    {
        $meta = $schema['project_metadata'] ?? [];
        $primitives = implode(', ', $meta['injected_primitives'] ?? []);

        return "# AGENTS.md & CLAUDE.md — {$meta['name']} (Project OS Zero-Assumption Edition)\n\n"
            . "> **Primary Sector:** `{$meta['primary_sector']}` | **Archetype:** `{$meta['architecture_archetype']}` | **Stack Preset:** `{$meta['stack_preset']}`\n"
            . "> **Injected Primitives:** `{$primitives}`\n"
            . "> **Canonical Machine Contract:** Always read `.agents/BLUEPRINT_SCHEMA.json` and `PROJECT_OS_EXECUTION_BLUEPRINT.md` before writing any code.\n\n"
            . "---\n\n"
            . self::renderAgentHarnessEccMarkdown() . "\n\n---\n\n"
            . self::renderEngineeringRulesMarkdown() . "\n\n---\n\n"
            . self::renderUiUxContractMarkdown() . "\n";
    }

    /**
     * Render initial Graphify knowledge graph seed (.agents/graphify/graph.json).
     */
    public static function renderInitialGraphifyJson(array $schema): string
    {
        $meta = $schema['project_metadata'] ?? [];
        $nodes = [
            [
                'id' => 'project:' . Str::slug($meta['name'] ?? 'app'),
                'type' => 'ProjectRoot',
                'label' => $meta['name'] ?? 'Target Application',
                'sector' => $meta['primary_sector'] ?? '',
                'archetype' => $meta['architecture_archetype'] ?? 'COMPOSITE',
            ],
        ];
        $edges = [];

        foreach (($schema['data_dictionary'] ?? []) as $tbl) {
            $tableName = $tbl['table'] ?? '';
            if ($tableName === '') {
                continue;
            }
            $modelClass = 'App\\Models\\' . Str::studly(Str::singular($tableName));
            $nodes[] = [
                'id' => "table:{$tableName}",
                'type' => 'DatabaseTable',
                'label' => $tableName,
                'primary_key' => 'ULID',
                'tenant_scoped' => (bool) ($tbl['tenant_scoped'] ?? false),
                'module' => $tbl['source_module'] ?? 'CORE',
            ];
            $nodes[] = [
                'id' => "model:{$modelClass}",
                'type' => 'EloquentModel',
                'label' => $modelClass,
                'path' => 'app/Models/' . Str::studly(Str::singular($tableName)) . '.php',
            ];
            $edges[] = [
                'from' => "model:{$modelClass}",
                'to' => "table:{$tableName}",
                'relation' => 'PERSISTS_TO_ULID_TABLE',
            ];
        }

        foreach (($schema['state_machines'] ?? []) as $sm) {
            $enumClass = $sm['enum_class'] ?? ('App\\Enums\\' . ($sm['entity'] ?? 'Entity') . 'Status');
            $nodes[] = [
                'id' => "enum:{$enumClass}",
                'type' => 'StateMachineEnum',
                'label' => $enumClass,
                'states' => $sm['states'] ?? [],
            ];

            foreach (($sm['transitions'] ?? []) as $tr) {
                $actionName = trim(explode('(', (string) ($tr['action'] ?? 'ExecuteTransitionAction'))[0]);
                $nodes[] = [
                    'id' => "action:App\\Actions\\{$actionName}",
                    'type' => 'DomainAction',
                    'label' => "App\\Actions\\{$actionName}",
                    'path' => "app/Actions/{$actionName}.php",
                    'guards' => $tr['guards'] ?? [],
                ];
                $edges[] = [
                    'from' => "action:App\\Actions\\{$actionName}",
                    'to' => "enum:{$enumClass}",
                    'relation' => "TRANSITIONS_TO_{$tr['to']}",
                ];
            }
        }

        return (string) json_encode([
            'graphify_version' => '1.0.0',
            'generated_by' => 'NeriahPro Project OS Composable Engine',
            'primary_sector' => $meta['primary_sector'] ?? '',
            'injected_primitives' => $meta['injected_primitives'] ?? [],
            'nodes' => $nodes,
            'edges' => $edges,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
