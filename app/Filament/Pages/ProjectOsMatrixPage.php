<?php

namespace App\Filament\Pages;

use App\Models\PosPrimitive;
use App\Models\PosProject;
use App\Models\PosRulePack;
use App\Models\PosSector;
use App\Models\PosStackPreset;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Support\PosStackPresetCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectOsMatrixPage extends Page
{
    protected static ?string $slug = 'project-os-matrix';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-plus';
    protected static string | \UnitEnum | null $navigationGroup = 'Project OS // Core';
    protected static ?string $navigationLabel = '12-Sector Blueprint Matrix';
    protected static ?string $title = '12-Sector & Composable Core Primitives Matrix';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.project-os-matrix-page';

    public string $activeTab = 'sectors'; // sectors | primitives | stacks | rules | projects | day2 | lifecycle | lab
    public string $viewMode = 'grid'; // grid | list
    public string $density = 'normal'; // compact | normal | relaxed
    public string $search = '';
    public ?string $cursor = null;
    public string $sortBy = 'default';
    public string $sortDirection = 'asc'; // asc | desc | none
    public array $selectedIds = [];
    public string $decisionPreviewSector = 'SECTOR_01_COMMERCE_OMNICHANNEL';

    // Killer #4, #5 & Layer 8 Blueprint Doctor Interactive Lab State
    public string $brownfieldSql = '';
    public string $brownfieldHint = '';
    public array $brownfieldResult = [];
    public string $slopSnippet = '';
    public array $slopResult = [];
    public string $doctorSectorCode = 'SECTOR_03_HEALTHCARE_CLINICAL';
    public string $doctorStackCode = 'STACK_M1_LARAVEL_MONOLITH';
    public string $doctorWorkflow = 'Klinik poli umum, rekam medis dokter, dan penebusan resep obat apotek FEFO dengan jurnal kas.';
    public int $doctorTimelineDays = 30;
    public array $doctorSelectedPrimitives = ['PRIMITIVE_CLINICAL_EMR_AUDIT'];
    public array $doctorResult = [];

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Strict Midtrans Reviewer & Client Isolation per GRAPH_REPORT.md
        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->can('view_project_os_matrix')
            || $user->hasAnyRole(['super_admin', 'developer', 'investor'])
            || in_array(strtolower($user->email ?? ''), [
                'yoseph.iriandi.tambunan@gmail.com',
                'iriandi@neriahpro.com',
                'admin@neriahpro.com',
                'yoseph.iriandi@gmail.com',
            ], true);
    }

    public function getHeading(): string | Htmlable
    {
        return '12-Sector Industrial UI/UX, Core Primitives & Zero-to-Production Matrix';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Project OS v4.0 Ultimate: 12 Sectoral UI/UX Archetypes, 12 Core Primitives, 9 Stack Adapters (M1–S2), 8 Layer Day-2 Ops, 5 Day-30+ Lifecycle Engines (Delta v1.1, Live MCP Bridge, Decision Matrix match(), Phase 7 Red-Team, .env Vault) & 3-Pane Studio.';
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['sectors', 'primitives', 'stacks', 'rules', 'projects', 'day2', 'lifecycle', 'lab'], true)) {
            $this->activeTab = $tab;
            $this->cursor = null;
            $this->selectedIds = [];
        }
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['grid', 'list'], true)) {
            $this->viewMode = $mode;
        }
    }

    public function setDensity(string $density): void
    {
        if (in_array($density, ['compact', 'normal', 'relaxed'], true)) {
            $this->density = $density;
        }
    }

    public function cycleSort(string $column): void
    {
        if ($this->sortBy !== $column) {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        } elseif ($this->sortDirection === 'asc') {
            $this->sortDirection = 'desc';
        } elseif ($this->sortDirection === 'desc') {
            $this->sortDirection = 'none';
            $this->sortBy = 'default';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->cursor = null;
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->cursor = null;
    }

    public function updatedSearch(): void
    {
        $this->cursor = null;
    }

    public function nextPage(?string $nextCursor): void
    {
        $this->cursor = $nextCursor;
    }

    public function prevPage(?string $prevCursor): void
    {
        $this->cursor = $prevCursor;
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function syncMatrixSeeder(): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin') && ! $user->can('manage_project_os_matrix')) {
            Notification::make()
                ->danger()
                ->title('Akses Ditolak RBAC')
                ->body('Hanya Super Admin atau pemilik izin manage_project_os_matrix yang dapat menyinkronkan seeder matriks.')
                ->send();
            return;
        }

        try {
            if (Schema::hasTable('pos_sectors')) {
                app(\Database\Seeders\ProjectOsMatrixSeeder::class)->run();
                if (Schema::hasTable('pos_stack_presets')) {
                    app(\Database\Seeders\PosStackPresetSeeder::class)->run();
                }
                Cache::forget(PosSector::CACHE_KEY);
                Notification::make()
                    ->success()
                    ->title('Matriks 12-Sektor UI/UX & 9 Stack Presets Tersinkronisasi')
                    ->body('Seluruh 12 Sectoral UI/UX Archetypes & Realistic Seeders, 12 Core Primitives, 9 Stack Presets (M1–S2), dan 6 Rule Packs berhasil diperbarui.')
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Mode In-Memory Aktif')
                    ->body('Tabel pos_sectors belum dimigrasi. Jalankan ./deploy.sh 2 untuk mempersistensikan matriks ke database PostgreSQL 16.')
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Sinkronisasi')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function exportJson(): StreamedResponse
    {
        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $payload = match ($this->activeTab) {
            'sectors' => $catalog['sectors'] ?? [],
            'primitives' => $catalog['primitives'] ?? [],
            'stacks' => $catalog['stack_presets'] ?? [],
            'rules' => $catalog['rule_packs'] ?? [],
            default => $this->getProjectsSummaryList(),
        };

        if (! empty($this->selectedIds)) {
            $payload = array_values(array_filter($payload, fn ($row) => in_array((string) ($row['id'] ?? $row['code'] ?? $row['slug'] ?? ''), $this->selectedIds, true)));
        }

        $filename = 'project-os-' . $this->activeTab . '-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($payload): void {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function exportCsv(): StreamedResponse
    {
        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $rows = match ($this->activeTab) {
            'sectors' => array_map(fn ($s) => [
                'id' => $s['id'] ?? '',
                'sector_number' => $s['sector_number'] ?? '',
                'code' => $s['code'] ?? '',
                'name_id' => $s['name']['id'] ?? '',
                'mandatory_primitives' => implode('|', $s['mandatory_primitives'] ?? []),
                'compliance' => implode('|', $s['default_compliance_standards'] ?? []),
            ], $catalog['sectors'] ?? []),
            'primitives' => array_map(fn ($p) => [
                'id' => $p['id'] ?? '',
                'code' => $p['code'] ?? '',
                'category' => $p['category'] ?? '',
                'name_id' => $p['name']['id'] ?? '',
                'tables' => implode('|', $p['table_names'] ?? []),
                'state_machines' => implode('|', $p['state_machine_entities'] ?? []),
            ], $catalog['primitives'] ?? []),
            'stacks' => array_map(fn ($st) => [
                'id' => $st['id'] ?? $st['code'] ?? '',
                'short_code' => $st['short_code'] ?? '',
                'code' => $st['code'] ?? '',
                'topology' => $st['topology'] ?? '',
                'name_id' => $st['name']['id'] ?? '',
                'type_contract' => $st['inter_layer_type_contract']['mechanism'] ?? '',
                'team_scale' => $st['primary_language'] ?? '',
            ], $catalog['stack_presets'] ?? []),
            'rules' => array_map(fn ($r) => [
                'slug' => $r['slug'] ?? '',
                'layer' => $r['layer'] ?? '',
                'name_id' => $r['name']['id'] ?? '',
            ], $catalog['rule_packs'] ?? []),
            default => $this->getProjectsSummaryList(),
        };

        if (! empty($this->selectedIds)) {
            $rows = array_values(array_filter($rows, fn ($row) => in_array((string) ($row['id'] ?? $row['code'] ?? $row['slug'] ?? ''), $this->selectedIds, true)));
        }

        $filename = 'project-os-' . $this->activeTab . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            if (! empty($rows)) {
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $r) {
                    fputcsv($out, array_map(fn ($v) => is_array($v) ? json_encode($v) : (string) $v, $r));
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function getProjectsSummaryList(): array
    {
        try {
            if (! Schema::hasTable('pos_projects')) {
                return [];
            }

            return PosProject::query()
                ->with('primarySector')
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get()
                ->map(fn (PosProject $p) => [
                    'id' => $p->id,
                    'app_name' => $p->app_name,
                    'sector_code' => $p->primarySector?->code ?? '-',
                    'archetype' => $p->architecture_archetype,
                    'primitives_count' => count($p->selected_primitive_ids ?? []),
                    'updated_at' => $p->updated_at?->format('Y-m-d H:i') ?? '-',
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function getMatrixDataProperty(): array
    {
        ProjectOsMatrixCatalog::ensureSeeded();
        $q = mb_strtolower(trim($this->search));
        $hasDb = false;

        try {
            $hasDb = Schema::hasTable('pos_sectors') && Schema::hasTable('pos_primitives');
        } catch (\Throwable) {
            $hasDb = false;
        }

        $stats = [
            'sectors' => count(ProjectOsMatrixCatalog::sectors()),
            'primitives' => count(ProjectOsMatrixCatalog::primitives()),
            'stacks' => count(PosStackPresetCatalog::stackPresets()),
            'rules' => count(ProjectOsMatrixCatalog::rulePacks()),
            'projects' => 0,
        ];

        $hasStacksDb = false;
        if ($hasDb) {
            try {
                $stats['sectors'] = PosSector::query()->count();
                $stats['primitives'] = PosPrimitive::query()->count();
                $hasStacksDb = Schema::hasTable('pos_stack_presets');
                if ($hasStacksDb) {
                    $dbStackCount = PosStackPreset::query()->count();
                    if ($dbStackCount > 0) {
                        $stats['stacks'] = $dbStackCount;
                    }
                }
                $stats['rules'] = PosRulePack::query()->count();
                $stats['projects'] = Schema::hasTable('pos_projects') ? PosProject::query()->count() : 0;
            } catch (\Throwable) {
            }
        }

        // O(1) Keyset Cursor Pagination when DB tables are migrated
        if ($hasDb) {
            if ($this->activeTab === 'sectors') {
                $query = PosSector::query()->with('primitives');
                if ($q !== '') {
                    $query->where(function ($sub) use ($q): void {
                        $sub->whereRaw('LOWER(code) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(CAST(name AS CHAR)) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(CAST(description AS CHAR)) LIKE ?', ["%{$q}%"]);
                    });
                }
                if ($this->sortBy === 'code' && $this->sortDirection !== 'none') {
                    $query->orderBy('code', $this->sortDirection);
                } else {
                    $query->orderBy('sector_number', $this->sortDirection === 'desc' ? 'desc' : 'asc');
                }
                $query->orderBy('id', 'asc');
                $paginated = $query->cursorPaginate(12, ['*'], 'cursor', $this->cursor);

                return [
                    'stats' => $stats,
                    'items' => collect($paginated->items())->map(function (PosSector $s) {
                        $uiArch = $s->resolvedUiArchetypes();
                        $seeder = $s->resolvedSeederContract();
                        return [
                            'id' => $s->id,
                            'sector_number' => $s->sector_number,
                            'code' => $s->code,
                            'name' => $s->name['id'] ?? $s->code,
                            'name_en' => $s->name['en'] ?? '',
                            'description' => $s->description['id'] ?? '',
                            'mandatory_primitives' => $s->primitives->filter(fn ($p) => $p->pivot?->is_mandatory)->pluck('code')->values()->all(),
                            'optional_primitives' => $s->primitives->reject(fn ($p) => $p->pivot?->is_mandatory)->pluck('code')->values()->all(),
                            'invariants' => $s->blueprint_invariants ?? [],
                            'compliance' => $s->default_compliance_standards ?? [],
                            'primary_archetype' => $uiArch['primary_archetype'] ?? '',
                            'secondary_archetype' => $uiArch['secondary_archetype'] ?? '',
                            'ui_headline' => $uiArch['headline']['id'] ?? '',
                            'wireframe_panes' => $uiArch['wireframe_blueprint']['panes'] ?? [],
                            'keyboard_shortcuts' => $uiArch['keyboard_shortcuts'] ?? [],
                            'seeder_class' => $seeder['seeder_class'] ?? 'RealisticSectorSeeder',
                        ];
                    })->all(),
                    'next_cursor' => $paginated->nextCursor()?->encode(),
                    'prev_cursor' => $paginated->previousCursor()?->encode(),
                ];
            }

            if ($this->activeTab === 'primitives') {
                $query = PosPrimitive::query();
                if ($q !== '') {
                    $query->where(function ($sub) use ($q): void {
                        $sub->whereRaw('LOWER(code) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(category) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(CAST(name AS CHAR)) LIKE ?', ["%{$q}%"]);
                    });
                }
                if ($this->sortBy === 'category' && $this->sortDirection !== 'none') {
                    $query->orderBy('category', $this->sortDirection);
                } else {
                    $query->orderBy('code', $this->sortDirection === 'desc' ? 'desc' : 'asc');
                }
                $query->orderBy('id', 'asc');
                $paginated = $query->cursorPaginate(12, ['*'], 'cursor', $this->cursor);

                return [
                    'stats' => $stats,
                    'items' => collect($paginated->items())->map(fn (PosPrimitive $p) => [
                        'id' => $p->id,
                        'code' => $p->code,
                        'category' => $p->category,
                        'name' => $p->name['id'] ?? $p->code,
                        'description' => $p->description['id'] ?? '',
                        'tables' => array_values(array_map(fn ($t) => $t['table'] ?? '', $p->schema_blueprints ?? [])),
                        'state_machines' => array_values(array_map(fn ($sm) => ($sm['entity'] ?? '') . ' (' . count($sm['states'] ?? []) . ' states)', $p->state_machines ?? [])),
                        'locks_count' => count($p->concurrency_contracts ?? []),
                        'playbooks_count' => count($p->edge_case_playbooks ?? []),
                        'formulas_count' => count($p->deterministic_formulas ?? []),
                        'sagas_count' => count($p->saga_and_webhook_contracts ?? []),
                        'archetypes_count' => count($p->ui_screen_archetypes ?? []),
                        'acceptance_count' => count($p->acceptance_test_scenarios ?? []),
                    ])->all(),
                    'next_cursor' => $paginated->nextCursor()?->encode(),
                    'prev_cursor' => $paginated->previousCursor()?->encode(),
                ];
            }

            if ($this->activeTab === 'stacks' && $hasStacksDb && PosStackPreset::query()->exists()) {
                $query = PosStackPreset::query();
                if ($q !== '') {
                    $query->where(function ($sub) use ($q): void {
                        $sub->whereRaw('LOWER(code) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(short_code) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(topology) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(CAST(name AS CHAR)) LIKE ?', ["%{$q}%"]);
                    });
                }
                if ($this->sortBy === 'code' && $this->sortDirection !== 'none') {
                    $query->orderBy('code', $this->sortDirection);
                } else {
                    $query->orderBy('sort_order', $this->sortDirection === 'desc' ? 'desc' : 'asc');
                }
                $query->orderBy('id', 'asc');
                $paginated = $query->cursorPaginate(12, ['*'], 'cursor', $this->cursor);

                return [
                    'stats' => $stats,
                    'items' => collect($paginated->items())->map(fn (PosStackPreset $st) => [
                        'id' => $st->id,
                        'code' => $st->code,
                        'short_code' => $st->short_code,
                        'topology' => $st->topology,
                        'name' => $st->name['id'] ?? $st->code,
                        'description' => $st->best_use_cases['id'] ?? '',
                        'type_contract' => $st->inter_layer_type_contract['mechanism'] ?? '-',
                        'team_scale' => $st->primary_language ?? '-',
                        'is_default' => (bool) $st->is_default,
                        'stack_layers' => array_keys($st->layer_technologies ?? []),
                    ])->all(),
                    'next_cursor' => $paginated->nextCursor()?->encode(),
                    'prev_cursor' => $paginated->previousCursor()?->encode(),
                ];
            }

            if ($this->activeTab === 'rules') {
                $query = PosRulePack::query();
                if ($q !== '') {
                    $query->where(function ($sub) use ($q): void {
                        $sub->whereRaw('LOWER(slug) LIKE ?', ["%{$q}%"])
                            ->orWhereRaw('LOWER(layer) LIKE ?', ["%{$q}%"]);
                    });
                }
                $query->orderBy('layer', $this->sortDirection === 'desc' ? 'desc' : 'asc')->orderBy('id', 'asc');
                $paginated = $query->cursorPaginate(12, ['*'], 'cursor', $this->cursor);

                return [
                    'stats' => $stats,
                    'items' => collect($paginated->items())->map(fn (PosRulePack $r) => [
                        'id' => $r->id,
                        'slug' => $r->slug,
                        'layer' => $r->layer,
                        'name' => $r->name['id'] ?? $r->slug,
                        'directives' => $r->rules_payload['directives'] ?? [],
                    ])->all(),
                    'next_cursor' => $paginated->nextCursor()?->encode(),
                    'prev_cursor' => $paginated->previousCursor()?->encode(),
                ];
            }

            if ($this->activeTab === 'projects' && Schema::hasTable('pos_projects')) {
                $query = PosProject::query()->with(['primarySector', 'visionBlueprint']);
                if ($q !== '') {
                    $query->whereRaw('LOWER(app_name) LIKE ?', ["%{$q}%"]);
                }
                $query->orderBy('id', $this->sortDirection === 'asc' ? 'asc' : 'desc');
                $paginated = $query->cursorPaginate(12, ['*'], 'cursor', $this->cursor);

                return [
                    'stats' => $stats,
                    'items' => collect($paginated->items())->map(fn (PosProject $p) => [
                        'id' => $p->id,
                        'app_name' => $p->app_name,
                        'sector_code' => $p->primarySector?->code ?? '-',
                        'archetype' => $p->architecture_archetype,
                        'primitives_count' => count($p->selected_primitive_ids ?? []),
                        'blueprint_slug' => $p->visionBlueprint?->slug,
                        'cli_init_command' => 'curl -sL ' . url('/init/' . ($p->visionBlueprint?->id ?: $p->id)) . ' | bash',
                        'phases_count' => count($p->phased_prompts_json ?: []) ?: 7,
                        'updated_at' => $p->updated_at?->format('d M Y H:i') ?? '-',
                    ])->all(),
                    'next_cursor' => $paginated->nextCursor()?->encode(),
                    'prev_cursor' => $paginated->previousCursor()?->encode(),
                ];
            }
        }

        // Resilient In-Memory Fallback before migration runs
        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $rawItems = match ($this->activeTab) {
            'sectors' => array_map(function ($s) {
                $uiArch = $s['ui_ux_archetypes'] ?? \App\Support\PosSectorUiArchetypeCatalog::forSector($s['code']);
                $seeder = $s['realistic_seeder_contract'] ?? \App\Support\PosSectorUiArchetypeCatalog::seederForSector($s['code']);
                return [
                    'id' => $s['id'] ?? $s['code'],
                    'sector_number' => $s['sector_number'] ?? 0,
                    'code' => $s['code'],
                    'name' => $s['name']['id'] ?? $s['code'],
                    'name_en' => $s['name']['en'] ?? '',
                    'description' => $s['description']['id'] ?? '',
                    'mandatory_primitives' => $s['mandatory_primitives'] ?? [],
                    'optional_primitives' => $s['optional_primitives'] ?? [],
                    'invariants' => $s['blueprint_invariants'] ?? [],
                    'compliance' => $s['default_compliance_standards'] ?? [],
                    'primary_archetype' => $uiArch['primary_archetype'] ?? '',
                    'secondary_archetype' => $uiArch['secondary_archetype'] ?? '',
                    'ui_headline' => $uiArch['headline']['id'] ?? '',
                    'wireframe_panes' => $uiArch['wireframe_blueprint']['panes'] ?? [],
                    'keyboard_shortcuts' => $uiArch['keyboard_shortcuts'] ?? [],
                    'seeder_class' => $seeder['seeder_class'] ?? 'RealisticSectorSeeder',
                ];
            }, $catalog['sectors'] ?? []),
            'primitives' => array_map(fn ($p) => [
                'id' => $p['id'] ?? $p['code'],
                'code' => $p['code'],
                'category' => $p['category'] ?? '',
                'name' => $p['name']['id'] ?? $p['code'],
                'description' => $p['description']['id'] ?? '',
                'tables' => $p['table_names'] ?? [],
                'state_machines' => $p['state_machine_entities'] ?? [],
                'locks_count' => 1,
                'playbooks_count' => 2,
                'formulas_count' => $p['formulas_count'] ?? 1,
                'sagas_count' => $p['sagas_count'] ?? 1,
                'archetypes_count' => $p['ui_archetypes_count'] ?? 1,
                'acceptance_count' => $p['acceptance_tests_count'] ?? 2,
            ], $catalog['primitives'] ?? []),
            'stacks' => array_map(fn ($st) => [
                'id' => $st['id'] ?? $st['code'],
                'code' => $st['code'],
                'short_code' => $st['short_code'] ?? '',
                'topology' => $st['topology'] ?? 'MONOLITH',
                'name' => $st['name']['id'] ?? $st['code'],
                'description' => $st['best_use_cases']['id'] ?? '',
                'type_contract' => $st['inter_layer_type_contract']['mechanism'] ?? '-',
                'team_scale' => $st['primary_language'] ?? '-',
                'is_default' => (bool) ($st['is_default'] ?? false),
                'stack_layers' => array_keys($st['layer_technologies'] ?? []),
            ], $catalog['stack_presets'] ?? []),
            'rules' => array_map(fn ($r) => [
                'id' => $r['slug'],
                'slug' => $r['slug'],
                'layer' => $r['layer'],
                'name' => $r['name']['id'] ?? $r['slug'],
                'directives' => $r['rules_payload']['directives'] ?? [],
            ], $catalog['rule_packs'] ?? []),
            default => [],
        };

        if ($q !== '') {
            $rawItems = array_values(array_filter($rawItems, fn ($item) => str_contains(mb_strtolower(json_encode($item)), $q)));
        }

        return [
            'stats' => $stats,
            'items' => $rawItems,
            'next_cursor' => null,
            'prev_cursor' => null,
        ];
    }

    public function loadSampleBrownfieldSql(): void
    {
        $this->brownfieldHint = 'Sistem Kasir & Inventori Apotek Klinik';
        $this->brownfieldSql = <<<'SQL'
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    nik VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    total_price FLOAT DEFAULT 0,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);
SQL;
    }

    public function runBrownfieldAudit(): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasAnyRole(['super_admin', 'developer']) && ! $user->can('execute_brownfield_importer')) {
            Notification::make()->danger()->title('Akses Ditolak RBAC')->body('Akun Anda tidak memiliki izin execute_brownfield_importer.')->send();
            return;
        }

        if (trim($this->brownfieldSql) === '') {
            Notification::make()->warning()->title('Schema Kosong')->body('Tempelkan SQL DDL, migrasi Laravel, atau schema.prisma terlebih dahulu.')->send();
            return;
        }

        $this->brownfieldResult = app(\App\Services\BrownfieldMigrationAuditorService::class)
            ->auditSchema($this->brownfieldSql, $this->brownfieldHint ?: null, true);

        Notification::make()
            ->success()
            ->title('Audit Brownfield Selesai — Skor ' . ($this->brownfieldResult['readiness_score'] ?? 0) . '/100')
            ->body('Rencana Migrasi Shadow ULID 4 Tahap & AI Refactor Prompt siap disalin.')
            ->send();
    }

    public function runSlopCodeScan(): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasAnyRole(['super_admin', 'developer']) && ! $user->can('run_slop_drift_scanner')) {
            Notification::make()->danger()->title('Akses Ditolak RBAC')->body('Akun Anda tidak memiliki izin run_slop_drift_scanner.')->send();
            return;
        }

        if (trim($this->slopSnippet) === '') {
            Notification::make()->warning()->title('Kode Kosong')->body('Tempelkan potongan kode Blade/JSX/Migration untuk dipindai.')->send();
            return;
        }

        $this->slopResult = app(\App\Services\BlueprintSlopScannerService::class)
            ->scanCodeSnippet($this->slopSnippet, true);

        Notification::make()
            ->success()
            ->title('Slop Scanner Selesai — Skor ' . ($this->slopResult['compliance_score'] ?? 100) . '/100')
            ->body(($this->slopResult['violations_count'] ?? 0) . ' pelanggaran terdeteksi.')
            ->send();
    }

    public function runBlueprintDoctorLab(): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasAnyRole(['super_admin', 'developer', 'investor']) && ! $user->can('run_blueprint_doctor')) {
            Notification::make()->danger()->title('Akses Ditolak RBAC')->body('Akun Anda tidak memiliki izin run_blueprint_doctor.')->send();
            return;
        }

        $this->doctorResult = app(\App\Services\BlueprintDoctorService::class)->diagnose([
            'sector_code' => $this->doctorSectorCode,
            'primitive_codes' => $this->doctorSelectedPrimitives,
            'stack_preset_code' => $this->doctorStackCode,
            'workflow_text' => $this->doctorWorkflow,
            'timeline_days' => max(7, $this->doctorTimelineDays),
        ], true);

        Notification::make()
            ->success()
            ->title('Blueprint Doctor Selesai — Skor ' . ($this->doctorResult['readiness_score'] ?? 100) . '/100')
            ->body($this->doctorResult['summary_banner']['id'] ?? 'Audit kesiapan selesai.')
            ->send();
    }

    public function applyBlueprintDoctorAutoFix(): void
    {
        $fix = $this->doctorResult['one_click_auto_fix'] ?? [];
        if (! empty($fix['merged_primitive_codes'])) {
            $this->doctorSelectedPrimitives = array_values(array_unique($fix['merged_primitive_codes']));
        }
        if (! empty($fix['recommended_stack_preset_code'])) {
            $this->doctorStackCode = (string) $fix['recommended_stack_preset_code'];
        }
        $this->runBlueprintDoctorLab();
    }
}

