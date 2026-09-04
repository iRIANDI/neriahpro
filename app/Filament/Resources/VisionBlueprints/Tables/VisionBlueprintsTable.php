<?php

namespace App\Filament\Resources\VisionBlueprints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class VisionBlueprintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bisnis')
                    ->label('Nama Bisnis / Proyek')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => 'PIC: ' . ($record->client_name ?: '-')),

                TextColumn::make('email')
                    ->label('Email Kontak')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Public?')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('project_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Prospecting' => 'gray',
                        'Contract Signed' => 'warning',
                        'In Progress' => 'info',
                        'Completed' => 'success',
                        default => 'primary',
                    })
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Tautan PRD')
                    ->copyable()
                    ->copyMessage('Link PRD berhasil disalin')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn (string $state): string => url('/blueprint/' . $state))
                    ->color('primary')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('project_status')
                    ->options([
                        'Prospecting' => 'Prospecting',
                        'Contract Signed' => 'Contract Signed',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                    ]),
                \Filament\Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Hanya Publik')
                    ->falseLabel('Hanya Privat'),
            ])
            ->recordActions([
                Action::make('view_prd')
                    ->label('Buka PRD')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn ($record) => $record->public_url)
                    ->openUrlInNewTab(),

                Action::make('toggle_publish')
                    ->label(fn ($record) => $record->is_published ? 'Set Privat' : 'Set Publik')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-lock-closed' : 'heroicon-o-globe-alt')
                    ->color(fn ($record) => $record->is_published ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_published' => !$record->is_published]);
                        Notification::make()
                            ->title($record->is_published ? 'PRD Sekarang Publik' : 'PRD Sekarang Privat')
                            ->success()
                            ->send();
                    }),

                Action::make('regenerate_prd')
                    ->label('Sintesis PRD Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->generateAndSavePrd();
                        Notification::make()
                            ->title('Ultimate PRD & ERD Berhasil Disintesis Ulang')
                            ->success()
                            ->send();
                    }),

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
