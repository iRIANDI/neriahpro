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
                        TextInput::make('action')
                            ->label('Aksi / Operasi')
                            ->disabled(),
                        TextInput::make('actor_name')
                            ->label('Pelaku / Eksekutor')
                            ->disabled(),
                        TextInput::make('actor_type')
                            ->label('Tipe Pelaku')
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted', 'force_deleted' => 'danger',
                        'login' => 'primary',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('actor_name')
                    ->label('Pelaku')
                    ->searchable()
                    ->description(fn ($record) => $record->actor_type ? strtoupper($record->actor_type) : null),
                TextColumn::make('model_type')
                    ->label('Entitas')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '-')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->fontFamily('mono')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
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

