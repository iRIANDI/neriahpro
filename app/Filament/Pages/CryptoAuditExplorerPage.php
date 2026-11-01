<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Actions\Audit\ExportFilteredAuditExcelAction;
use App\Models\AuditLog;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CryptoAuditExplorerPage extends Page
{
    protected static ?string $slug = 'crypto-audit-explorer';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string | \UnitEnum | null $navigationGroup = 'System & Security';

    protected static ?string $navigationLabel = 'Cryptographic Audit Explorer';

    protected static ?string $title = 'Cryptographic Audit Explorer & 3-Sheet Exporter';

    protected static ?int $navigationSort = 14;

    protected string $view = 'filament.pages.crypto-audit-explorer-page';

    // Ultimate Enterprise Table State
    public string $viewMode = 'list'; // list | grid
    public string $density = 'normal'; // compact | normal | relaxed
    public string $search = '';
    public ?string $cursor = null;
    public string $sortBy = 'id';
    public string $sortDirection = 'desc'; // asc | desc | none
    public array $selectedIds = [];
    public ?string $inspectedLogId = null;

    // 8 Filter Dimensions + 12 Sector Presets
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sectorCode = 'ALL_12_SECTORS';
    public string $eventCategory = '';
    public string $executionChannel = '';
    public string $actorType = '';
    public string $actionName = '';
    public string $targetType = '';
    public array $activePresetKeywords = [];

    // Column Visibility Selector
    public array $visibleColumns = [
        'created_at' => true,
        'sector_channel' => true,
        'action_category' => true,
        'actor' => true,
        'entity' => true,
        'hash_chain' => true,
    ];

    public array $chainVerificationSummary = [];

    public function mount(): void
    {
        $this->chainVerificationSummary = AuditLog::verifyChainSummary(250);
    }

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

        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'developer', 'investor'])
            || $user->can('verify_crypto_audit_chain')
            || $user->can('view_audit_logs');
    }

    public function getHeading(): string | Htmlable
    {
        return app()->getLocale() === 'en'
            ? 'Cryptographic Audit Log Explorer (SHA-256 Chain & 3-Sheet .xlsx Exporter)'
            : 'Cryptographic Audit Log Explorer (Rantai SHA-256 & Ekspor 3-Sheet .xlsx)';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return app()->getLocale() === 'en'
            ? 'PRIMITIVE_CRYPTO_AUDIT_EXPLORER v9.0 — 8 Filter Dimensions, 12 Sector Statutory Presets (PSAK 109, ISAK 35, PP 93/2010, ISO 27001, SOC2, RME Kemenkes, UU PDP), O(1) Keyset Cursor & Live Tamper Detection.'
            : 'PRIMITIVE_CRYPTO_AUDIT_EXPLORER v9.0 — 8 Dimensi Filter, 12 Preset Audit Industri (PSAK 109, ISAK 35, PP 93/2010, ISO 27001, SOC2, RME Kemenkes, UU PDP), Cursor O(1) & Deteksi Manipulasi DB Real-Time.';
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['list', 'grid'], true)) {
            $this->viewMode = $mode;
        }
    }

    public function setDensity(string $density): void
    {
        if (in_array($density, ['compact', 'normal', 'relaxed'], true)) {
            $this->density = $density;
        }
    }

    public function toggleColumnVisibility(string $colKey): void
    {
        if (array_key_exists($colKey, $this->visibleColumns)) {
            $this->visibleColumns[$colKey] = ! $this->visibleColumns[$colKey];
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
            $this->sortBy = 'id';
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

    public function applySectorPreset(string $code): void
    {
        $presets = PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets(app()->getLocale());
        if ($code === 'ALL_12_SECTORS' || ! isset($presets[$code])) {
            $this->sectorCode = 'ALL_12_SECTORS';
            $this->activePresetKeywords = [];
            $this->cursor = null;

            return;
        }

        $this->sectorCode = $code;
        $this->activePresetKeywords = (array) ($presets[$code]['preset_keywords'] ?? []);
        $this->cursor = null;
    }

    public function clearAllFilters(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->sectorCode = 'ALL_12_SECTORS';
        $this->eventCategory = '';
        $this->executionChannel = '';
        $this->actorType = '';
        $this->actionName = '';
        $this->targetType = '';
        $this->activePresetKeywords = [];
        $this->search = '';
        $this->cursor = null;
        $this->selectedIds = [];
    }

    public function nextPage(?string $nextCursor): void
    {
        $this->cursor = $nextCursor;
    }

    public function prevPage(?string $prevCursor): void
    {
        $this->cursor = $prevCursor;
    }

    public function toggleSelectRow(string $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
            $this->selectedIds = array_values(array_unique($this->selectedIds));
        }
    }

    public function selectCurrentPageRows(array $ids): void
    {
        $cleanIds = array_values(array_filter(array_map('strval', $ids)));
        $allSelected = count($cleanIds) > 0 && count(array_intersect($cleanIds, $this->selectedIds)) === count($cleanIds);

        if ($allSelected) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $cleanIds));
        } else {
            $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $cleanIds)));
        }
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function inspectRow(?string $id): void
    {
        $this->inspectedLogId = $id;
    }

    public function runLiveChainVerification(): void
    {
        $this->chainVerificationSummary = AuditLog::verifyChainSummary(500);
        $isIntact = ($this->chainVerificationSummary['status'] ?? '') === 'VERIFIED_INTACT';

        Notification::make()
            ->color($isIntact ? 'success' : 'danger')
            ->title($isIntact ? 'SHA-256 Hash Chain: VERIFIED INTACT ✅' : '🚨 TAMPER ALERT: Rantai Hash Rusak!')
            ->body(sprintf(
                'Memeriksa %s baris berantai SHA-256 (%s terverifikasi utuh, %s manipulasi terdeteksi).',
                number_format((int) ($this->chainVerificationSummary['checked_rows'] ?? 0)),
                number_format((int) ($this->chainVerificationSummary['verified_chained_rows'] ?? 0)),
                number_format((int) ($this->chainVerificationSummary['tampered_rows_count'] ?? 0))
            ))
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function activeFilterPayload(): array
    {
        return [
            'selected_ids' => ! empty($this->selectedIds) ? $this->selectedIds : null,
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
            'sector_code' => $this->sectorCode !== 'ALL_12_SECTORS' ? $this->sectorCode : null,
            'event_category' => $this->eventCategory ?: null,
            'execution_channel' => $this->executionChannel ?: null,
            'actor_type' => $this->actorType ?: null,
            'action_name' => $this->actionName ?: null,
            'target_type' => $this->targetType ?: null,
            'preset_keywords' => ! empty($this->activePresetKeywords) ? $this->activePresetKeywords : null,
            'search' => $this->search ?: null,
        ];
    }

    public function exportThreeSheetExcel(ExportFilteredAuditExcelAction $exporter): ?BinaryFileResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('super_admin') && ! $user->can('export_3sheet_audit_xlsx') && ! $user->can('export_audit_logs'))) {
            Notification::make()->danger()->title('Akses Ekspor Ditolak RBAC')->send();

            return null;
        }

        AuditLog::record(
            action: 'crypto_audit_xlsx_3sheet_exported',
            newValues: ['sector_code' => $this->sectorCode, 'selected_count' => count($this->selectedIds)],
            description: 'Ekspor Buku Kerja Audit Kriptografi 3-Sheet (.xlsx) dijalankan dari CryptoAuditExplorerPage.',
            eventCategory: 'security',
            sectorCode: $this->sectorCode !== 'ALL_12_SECTORS' ? $this->sectorCode : null,
            primitiveCode: 'PRIMITIVE_CRYPTO_AUDIT_EXPLORER',
            executionChannel: 'WEB_UI'
        );

        return $exporter->exportThreeSheetXlsx($this->activeFilterPayload(), 'NERIAHPRO_CORE');
    }

    public function exportStreamingCsv(ExportFilteredAuditExcelAction $exporter): ?StreamedResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('super_admin') && ! $user->can('export_audit_logs') && ! $user->can('export_3sheet_audit_xlsx'))) {
            Notification::make()->danger()->title('Akses Ekspor Ditolak RBAC')->send();

            return null;
        }

        AuditLog::record(
            action: 'crypto_audit_csv_stream_exported',
            newValues: ['sector_code' => $this->sectorCode, 'selected_count' => count($this->selectedIds)],
            description: 'Ekspor CSV Streaming O(1) bersegel SHA-256 dijalankan dari CryptoAuditExplorerPage.',
            eventCategory: 'security',
            sectorCode: $this->sectorCode !== 'ALL_12_SECTORS' ? $this->sectorCode : null,
            primitiveCode: 'PRIMITIVE_CRYPTO_AUDIT_EXPLORER',
            executionChannel: 'WEB_UI'
        );

        return $exporter->exportStreamingCsv($this->activeFilterPayload(), 'NERIAHPRO_CORE');
    }

    /**
     * @return array<string, mixed>
     */
    public function getExplorerDataProperty(): array
    {
        $exporter = app(ExportFilteredAuditExcelAction::class);
        $filters = $this->activeFilterPayload();
        unset($filters['selected_ids']); // Keep full table visible while checkboxes select rows

        $query = $exporter->buildFilteredQuery($filters);
        // Re-order according to user's Tri-State Sorting with deterministic ULID pointer fallback
        $query->reorder();
        $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        if (in_array($this->sortBy, ['action', 'event_category', 'actor_name'], true) && $this->sortDirection !== 'none') {
            $query->orderBy($this->sortBy, $dir)->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', $dir);
        }

        $paginated = $query->cursorPaginate(20, ['*'], 'cursor', $this->cursor);
        $inspected = $this->inspectedLogId ? AuditLog::query()->find($this->inspectedLogId) : null;

        return [
            'rows' => $paginated->items(),
            'next_cursor' => $paginated->nextCursor()?->encode(),
            'prev_cursor' => $paginated->previousCursor()?->encode(),
            'presets' => PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets(app()->getLocale()),
            'inspected' => $inspected,
        ];
    }
}

