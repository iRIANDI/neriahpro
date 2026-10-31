<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Tables\Enums\PaginationMode;

class AuditLogResource extends Resource
{
    use \App\Filament\Traits\AuditableByMidtransReviewer;

    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';
    protected static string | \UnitEnum | null $navigationGroup = 'System & Security';
    protected static ?string $navigationLabel = 'Jejak Audit (Audit Trail)';
    protected static ?string $modelLabel = 'Jejak Audit';
    protected static ?string $pluralModelLabel = 'Jejak Audit (Audit Trail)';
    protected static ?int $navigationSort = 15;
    protected static ?string $recordTitleAttribute = 'action';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Aktivitas')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('created_at')
                            ->label('Waktu Kejadian')
                            ->disabled(),
                        TextInput::make('event_category')
                            ->label('Kategori Domain Event')
                            ->disabled(),
                        TextInput::make('action')
                            ->label('Aksi / Operasi')
                            ->disabled(),
                        TextInput::make('integrity_hash')
                            ->label('Stempel Integritas SHA-256')
                            ->disabled(),
                        TextInput::make('actor_name')
                            ->label('Pelaku / Eksekutor')
                            ->disabled(),
                        TextInput::make('actor_type')
                            ->label('Tipe Pelaku')
                            ->disabled(),
                        TextInput::make('description')
                            ->label('Deskripsi Aktivitas')
                            ->columnSpanFull()
                            ->disabled(),
                        TextInput::make('model_type')
                            ->label('Model Entitas')
                            ->disabled(),
                        TextInput::make('model_id')
                            ->label('ID Rekaman')
                            ->disabled(),
                        TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),
                        TextInput::make('context_url')
                            ->label('URL Konteks')
                            ->disabled(),
                        KeyValue::make('old_values')
                            ->label('Nilai Sebelum Diubah')
                            ->disabled(),
                        KeyValue::make('new_values')
                            ->label('Nilai Sesudah Diubah')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationMode(PaginationMode::Cursor)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('event_category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?: 'system'))
                    ->color(fn (?string $state): string => match ($state) {
                        'project_os', 'cli_bootstrap', 'day2_ops', 'delta_evolution' => 'success',
                        'ai_agent', 'blueprint_doctor', 'mcp_bridge', 'env_vault' => 'info',
                        'brownfield_import', 'slop_audit' => 'warning',
                        'security', 'rbac', 'redteam_security' => 'danger',
                        'billing' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created', 'create', 'cli_bootstrap_pulled', 'delta_cr_created', 'mcp_phase_verified', 'env_vault_checked' => 'success',
                        'updated', 'update', 'blueprint_slop_scanned', 'brownfield_schema_audited', 'blueprint_doctor_diagnosed', 'mcp_config_exported', 'mcp_drift_verified' => 'info',
                        'deleted', 'force_deleted', 'rbac_tool_denied', 'mcp_auth_denied' => 'danger',
                        'login' => 'primary',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('actor_name')
                    ->label('Pelaku')
                    ->searchable()
                    ->description(fn ($record) => $record->actor_type ? strtoupper($record->actor_type) : null),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(56)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('model_type')
                    ->label('Entitas')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '-')
                    ->searchable(),
                TextColumn::make('integrity_hash')
                    ->label('SHA-256')
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (?string $state): string => $state ? substr($state, 0, 12) . '…' : 'LEGACY')
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->copyable()
                    ->copyMessage('SHA-256 Integrity Hash disalin'),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->fontFamily('mono')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('event_category')
                    ->label('Kategori Domain')
                    ->options([
                        'project_os' => 'Project OS & Blueprint',
                        'delta_evolution' => 'Delta Evolution (v1.0 -> v1.1)',
                        'mcp_bridge' => 'Live MCP Server Bridge (IDE)',
                        'redteam_security' => 'Phase 7 Red-Team & Tenant Shield',
                        'env_vault' => 'Environment Vault (.env.blueprint)',
                        'blueprint_doctor' => 'Blueprint Doctor Pre-Flight',
                        'day2_ops' => 'Day-2 Ops, Print & Staged Import',
                        'ai_agent' => 'AI Copilot & Native Tools',
                        'brownfield_import' => 'Brownfield SQL Importer',
                        'slop_audit' => 'Slop & Drift Scanner',
                        'cli_bootstrap' => 'CLI Bootstrapper (/init)',
                        'billing' => 'Billing & Midtrans',
                        'rbac' => 'RBAC & Permissions',
                        'security' => 'Security & Threat Shield',
                        'system' => 'System General',
                    ]),
                SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'created' => 'Created',
                        'create' => 'Create (AI/System)',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
                        'delta_cr_created' => 'Delta Change Request Created',
                        'mcp_phase_verified' => 'MCP Phase Verified (Green)',
                        'mcp_config_exported' => 'MCP .mcp.json Exported',
                        'env_vault_checked' => 'Environment Vault Checked',
                        'cli_bootstrap_pulled' => 'CLI Bootstrap Pulled',
                        'brownfield_schema_audited' => 'Brownfield Schema Audited',
                        'blueprint_slop_scanned' => 'Blueprint Slop Scanned',
                        'rbac_tool_denied' => 'RBAC Tool Denied',
                    ]),
                SelectFilter::make('actor_type')
                    ->label('Tipe Pelaku')
                    ->options([
                        'admin' => 'Admin',
                        'client' => 'Client',
                        'ai_agent' => 'AI Agent',
                        'system' => 'System',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'view' => ViewAuditLog::route('/{record}'),
        ];
    }
}

