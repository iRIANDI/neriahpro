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

    public string $activeTab = 'sectors'; // sectors | primitives | rules | projects
    public string $viewMode = 'grid'; // grid | list
    public string $density = 'normal'; // compact | normal | relaxed
    public string $search = '';
    public ?string $cursor = null;
    public string $sortBy = 'default';
    public string $sortDirection = 'asc'; // asc | desc | none
    public array $selectedIds = [];

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

        return $user->hasAnyRole(['super_admin', 'investor'])
            || in_array(strtolower($user->email ?? ''), [
                'yoseph.iriandi.tambunan@gmail.com',
                'iriandi@neriahpro.com',
                'admin@neriahpro.com',
                'yoseph.iriandi@gmail.com',
            ], true);
    }

    public function getHeading(): string | Htmlable
    {
        return '12-Sector Composable Blueprint & Core Primitives Matrix';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Taksonomi 12 Sektor Fungsional, 12 Core Primitives (7 Dimensi Zero-Assumption), 9 Curated Tech Stack & Topology Presets (M1–S2), dan 6 Global Anti-Slop Rule Packs.';
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['sectors', 'primitives', 'stacks', 'rules', 'projects'], true)) {
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
        try {
            if (Schema::hasTable('pos_sectors')) {
                app(\Database\Seeders\ProjectOsMatrixSeeder::class)->run();
                if (Schema::hasTable('pos_stack_presets')) {
                    app(\Database\Seeders\PosStackPresetSeeder::class)->run();
                }
                Cache::forget(PosSector::CACHE_KEY);
                Notification::make()
                    ->success()
                    ->title('Matriks 12-Sektor & 9 Stack Presets Tersinkronisasi')
                    ->body('Seluruh 12 Sektor Fungsional, 12 Core Primitives (7 Dimensi Zero-Assumption), 9 Stack Presets (M1–S2), dan 6 Rule Packs berhasil diperbarui di database & cache O(1).')
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
                    'items' => collect($paginated->items())->map(fn (PosSector $s) => [
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
                    ])->all(),
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
            'sectors' => array_map(fn ($s) => [
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
            ], $catalog['sectors'] ?? []),
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
}

