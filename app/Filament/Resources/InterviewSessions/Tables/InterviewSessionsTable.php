<?php

namespace App\Filament\Resources\InterviewSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterviewSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target_company')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('job_title')
                    ->label('Posisi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('overall_score')
                    ->label('Skor AI')
                    ->badge()
                    ->color(fn ($state) => $state >= 75 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state ? $state . '/100' : 'Belum Dinilai')
                    ->sortable(),

                TextColumn::make('questions')
                    ->label('Jml Pertanyaan')
                    ->badge()
                    ->color('info')
                    ->state(fn ($record) => is_array($record->questions) ? count($record->questions) . ' Soal' : '0 Soal'),

                TextColumn::make('created_at')
                    ->label('Tanggal Sesi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
