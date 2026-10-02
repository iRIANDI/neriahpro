<?php

namespace App\Filament\Resources\BlueprintVouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class BlueprintVouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Voucher')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode voucher berhasil disalin!'),

                TextColumn::make('description')
                    ->label('Peruntukan')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('discount_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free_bypass' => 'success',
                        'percent' => 'info',
                        'fixed' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free_bypass' => '100% Free Bypass',
                        'percent' => 'Persentase (%)',
                        'fixed' => 'Potongan Tetap',
                        default => $state,
                    }),

                TextColumn::make('discount_value')
                    ->label('Nilai')
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percent' || $record->discount_type === 'free_bypass'
                        ? number_format($record->discount_value, 0) . '%'
                        : 'Rp ' . number_format($record->discount_value, 0, ',', '.')
                    ),

                TextColumn::make('used_count')
                    ->label('Terpakai / Kuota')
                    ->formatStateUsing(fn ($record) => $record->used_count . ' / ' . ($record->max_uses ?? '∞')),

                IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Selamanya')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
