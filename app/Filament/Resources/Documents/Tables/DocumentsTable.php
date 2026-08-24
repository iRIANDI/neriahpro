<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('document_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => 'Kontrak',
                        'blueprint_approval' => 'Vision Blueprint',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'draft',
                        'warning' => 'pending_signature',
                        'success' => 'signed',
                    ]),
                TextColumn::make('signed_at')
                    ->label('Ditandatangani Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('preview_pdf')
                    ->label('Preview PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn ($record) => route('document.preview', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
