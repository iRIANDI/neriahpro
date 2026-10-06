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
                    ->color(fn (?string $state): string => match ($state) {
                        'Prospecting', 'Draft' => 'gray',
                        'Contract Created' => 'warning',
                        'Awaiting DP Payment', 'Contract Signed' => 'info',
                        'Active Sprint', 'In Development (DP Paid)', 'In Progress' => 'success',
                        'In Development (Free Grant)' => 'purple',
                        'Completed' => 'teal',
                        default => 'primary',
                    })
                    ->icon(fn (?string $state): ?string => match ($state) {
                        'Prospecting', 'Draft' => 'heroicon-o-document-text',
                        'Contract Created' => 'heroicon-o-pencil-square',
                        'Awaiting DP Payment' => 'heroicon-o-credit-card',
                        'Contract Signed' => 'heroicon-o-document-check',
                        'Active Sprint', 'In Development (DP Paid)', 'In Progress' => 'heroicon-o-bolt',
                        'In Development (Free Grant)' => 'heroicon-o-gift',
                        'Completed' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-clock',
                    })
                    ->searchable()
                    ->sortable(),

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
                    ->label('Status Proyek')
                    ->options([
                        'Prospecting' => 'Prospecting (Draft PRD)',
                        'Contract Created' => 'Contract Created (Menunggu TTD Klien)',
                        'Awaiting DP Payment' => 'Awaiting DP Payment (Menunggu DP 50%)',
                        'Active Sprint' => 'Active Sprint (Dalam Pengerjaan)',
                        'In Development (DP Paid)' => 'In Development (DP Paid)',
                        'In Development (Free Grant)' => 'In Development (Free Grant)',
                        'Completed' => 'Completed (Selesai)',
                    ]),
                \Filament\Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Hanya Publik')
                    ->falseLabel('Hanya Privat'),
            ])
            ->recordActions([
                // 1. Universal PRD Inspection Actions
                Action::make('view_prd')
                    ->label('Buka PRD')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn ($record) => $record->public_url)
                    ->openUrlInNewTab(),

                Action::make('download_pdf')
                    ->label('Cetak PRD')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn ($record) => route('blueprint.download-pdf', $record->slug))
                    ->openUrlInNewTab(),

                // 2. Draft / Prospecting: AI Synthesis & Contract Creation
                Action::make('regenerate_prd')
                    ->label('Sintesis PRD Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn ($record) => empty($record->getContractDocument()) && in_array($record->project_status, ['Prospecting', 'Draft', null, '']))
                    ->requiresConfirmation()
                    ->modalHeading('Sintesis Ulang Ultimate PRD & Arsitektur')
                    ->modalDescription('Proses ini akan mengkalibrasi ulang ruang lingkup, model data ERD, dan breakdown biaya menggunakan AI Orchestrator.')
                    ->action(function ($record) {
                        $record->generateAndSavePrd();
                        Notification::make()
                            ->title('Ultimate PRD & ERD Berhasil Disintesis Ulang')
                            ->success()
                            ->send();
                    }),

                Action::make('convert_to_contract')
                    ->label('Ikat Kontrak Digital')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->visible(fn ($record) => empty($record->getContractDocument()) && in_array($record->project_status, ['Prospecting', 'Draft', null, '']))
                    ->requiresConfirmation()
                    ->modalHeading('Ikat Blueprint Menjadi Kontrak Digital Resmi')
                    ->modalDescription('Sistem akan mengunci ruang lingkup PRD ini (Scope Locked), menyusun klausul kontrak hukum, dan menyiapkan tautan pembayaran DP 50% via Midtrans.')
                    ->action(function ($record) {
                        $contract = $record->convertToDigitalContract();
                        Notification::make()
                            ->title('Kontrak Digital Berhasil Diterbitkan!')
                            ->body('Dokumen kontrak telah dibuat dengan tanda tangan digital sah dan tagihan DP Midtrans.')
                            ->success()
                            ->send();
                    }),

                // 3. Contract Stage: View Contract PDF & Client Signing Page
                Action::make('view_contract')
                    ->label('Lihat Kontrak')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => $record->getContractDocument() !== null)
                    ->url(fn ($record) => ($contract = $record->getContractDocument()) ? route('document.preview', $contract->id) : null)
                    ->openUrlInNewTab(),

                Action::make('sign_contract')
                    ->label('Form TTD Klien')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => ($contract = $record->getContractDocument()) && $contract->status !== 'signed')
                    ->url(fn ($record) => ($contract = $record->getContractDocument()) ? route('document.sign', $contract->id) : null)
                    ->openUrlInNewTab(),

                // 4. Payment Stage: Awaiting DP Confirmation & Midtrans Invoice Link
                Action::make('confirm_dp')
                    ->label('Konfirmasi DP')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record->project_status === 'Awaiting DP Payment')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penerimaan Pembayaran DP 50%')
                    ->modalDescription('Apakah pembayaran DP 50% untuk proyek ini telah tervalidasi? Sistem akan mengubah status menjadi Active Sprint, mengunci scope (Scope Locked), dan mengaktifkan sandbox staging.')
                    ->action(function ($record) {
                        $record->update(['project_status' => 'Active Sprint']);
                        $stagingUrl = $record->provisionStagingUrl();
                        if ($contract = $record->getContractDocument()) {
                            $contract->update(['status' => 'signed', 'scope_locked' => true]);
                        }
                        Notification::make()
                            ->title('DP 50% Terkonfirmasi - Active Sprint Dimulai!')
                            ->body("Status proyek diubah ke Active Sprint dan URL Staging disiapkan: {$stagingUrl}")
                            ->success()
                            ->send();
                    }),

                Action::make('view_dp_invoice')
                    ->label('Tagihan DP')
                    ->icon('heroicon-o-credit-card')
                    ->color('info')
                    ->visible(fn ($record) => $record->project_status === 'Awaiting DP Payment' && !empty($record->getContractDocument()?->midtrans_payment_url))
                    ->url(fn ($record) => $record->getContractDocument()?->midtrans_payment_url)
                    ->openUrlInNewTab(),

                // 5. Active Development Stage: Access Sandbox Staging & Handover
                Action::make('view_staging')
                    ->label('Akses Staging')
                    ->icon('heroicon-o-server-stack')
                    ->color('primary')
                    ->visible(fn ($record) => !empty($record->staging_url) && in_array($record->project_status, ['Active Sprint', 'In Development (DP Paid)', 'In Development (Free Grant)', 'In Progress', 'Completed']))
                    ->url(fn ($record) => $record->staging_url)
                    ->openUrlInNewTab(),

                Action::make('mark_completed')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('teal')
                    ->visible(fn ($record) => in_array($record->project_status, ['Active Sprint', 'In Development (DP Paid)', 'In Development (Free Grant)', 'In Progress']))
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Proyek Selesai & Serah Terima')
                    ->modalDescription('Apakah seluruh sprint dan proses serah terima proyek ini telah tuntas 100%?')
                    ->action(function ($record) {
                        $record->update(['project_status' => 'Completed']);
                        Notification::make()
                            ->title('Proyek Berhasil Diselesaikan!')
                            ->body('Status proyek telah diperbarui menjadi Completed.')
                            ->success()
                            ->send();
                    }),

                // 6. Access Control & Management Actions
                Action::make('toggle_publish')
                    ->label(fn ($record) => $record->is_published ? 'Set Privat' : 'Set Publik')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-lock-closed' : 'heroicon-o-globe-alt')
                    ->color(fn ($record) => $record->is_published ? 'warning' : 'gray')
                    ->action(function ($record) {
                        $record->update(['is_published' => !$record->is_published]);
                        Notification::make()
                            ->title($record->is_published ? 'PRD Sekarang Publik' : 'PRD Sekarang Privat')
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
