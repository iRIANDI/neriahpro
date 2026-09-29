<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk & Layanan')
                    ->state(fn ($record) => is_array($record->name) ? ($record->name['id'] ?? $record->name['en'] ?? '-') : $record->name)
                    ->description(fn ($record) => is_array($record->name) && isset($record->name['en']) ? 'EN: ' . $record->name['en'] : null)
                    ->searchable(query: fn ($query, $search) => $query->whereRaw("LOWER(name::text) LIKE ?", ['%' . strtolower($search) . '%']))
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('price_idr')
                    ->label('Harga (IDR)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('price_usd')
                    ->label('Harga (USD)')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('features')
                    ->label('Fitur')
                    ->badge()
                    ->state(fn ($record) => is_array($record->features) ? count($record->features) . ' Fitur' : '-')
                    ->color('info')
                    ->tooltip(fn ($record) => is_array($record->features) ? implode("\n• ", array_merge([''], $record->features)) : null)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Nonaktif')
                    ->native(false),
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
