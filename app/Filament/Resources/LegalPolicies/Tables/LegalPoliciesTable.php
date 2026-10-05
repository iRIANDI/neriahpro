<?php

namespace App\Filament\Resources\LegalPolicies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LegalPoliciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kebijakan')
                    ->state(fn ($record) => is_array($record->title) ? ($record->title['id'] ?? $record->title['en'] ?? '-') : $record->title)
                    ->description(fn ($record) => is_array($record->title) && isset($record->title['en']) ? 'EN: ' . $record->title['en'] : null)
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("LOWER(title::text) LIKE ?", ['%' . strtolower($search) . '%'])),

                TextColumn::make('type')
                    ->label('Tipe Dokumen')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terms_and_conditions' => 'Syarat & Ketentuan',
                        'privacy_policy' => 'Kebijakan Privasi',
                        'refund_policy' => 'Kebijakan Pengembalian (Refund)',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->colors([
                        'primary' => 'terms_and_conditions',
                        'success' => 'privacy_policy',
                        'warning' => 'refund_policy',
                    ]),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Kebijakan')
                    ->options([
                        'terms_and_conditions' => 'Syarat & Ketentuan',
                        'privacy_policy' => 'Kebijakan Privasi',
                        'refund_policy' => 'Kebijakan Pengembalian (Refund)',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
