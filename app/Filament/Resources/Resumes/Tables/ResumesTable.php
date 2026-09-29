<?php

namespace App\Filament\Resources\Resumes\Tables;

use App\Services\CvPro\CvAiService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResumesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Resume')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => 'Posisi: ' . ($record->target_role ?: 'Profesional')),

                TextColumn::make('ats_score')
                    ->label('Skor ATS AI')
                    ->badge()
                    ->color(fn ($record): string => $record->ats_badge_color)
                    ->state(fn ($record): string => $record->ats_score . '/100 • ' . $record->ats_label)
                    ->sortable(),

                TextColumn::make('template')
                    ->label('Template')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'modern_minimalist' => 'Modern Minimalist',
                        'executive_clean' => 'Executive Clean',
                        'creative_ats' => 'Creative ATS',
                        'tech_dark' => 'Tech Dark',
                        default => ucfirst($state),
                    }),

                IconColumn::make('is_public')
                    ->label('Publik?')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('template')
                    ->options([
                        'modern_minimalist' => 'Modern Minimalist',
                        'executive_clean' => 'Executive Clean',
                        'creative_ats' => 'Creative ATS',
                        'tech_dark' => 'Tech Dark',
                    ]),
            ])
            ->recordActions([
                Action::make('view_online')
                    ->label('Buka CV')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn ($record) => $record->public_url)
                    ->openUrlInNewTab(),

                Action::make('re_audit')
                    ->label('Audit Ulang ATS')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->action(function ($record) {
                        $audit = CvAiService::lintResume($record->content ?? [], 'id');
                        $record->update([
                            'ats_score' => $audit['overall_score'],
                            'ats_feedback' => $audit,
                        ]);

                        Notification::make()
                            ->title('Audit ATS Selesai!')
                            ->body("Skor baru: {$audit['overall_score']}/100 ({$record->ats_label})")
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
