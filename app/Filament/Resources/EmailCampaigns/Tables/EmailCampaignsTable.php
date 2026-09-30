<?php

namespace App\Filament\Resources\EmailCampaigns\Tables;

use App\Mail\PromotionalCampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class EmailCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nama Kampanye')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (EmailCampaign $record): string => 'Subjek: ' . $record->subject),

                TextColumn::make('target_audience')
                    ->label('Target Audiens')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => 'Semua Klien',
                        'onboarding_clients' => 'Leads Onboarding',
                        'blueprint_clients' => 'Project OS Blueprint',
                        'cv_users' => 'CV Pro Users',
                        default => ucfirst($state),
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                TextColumn::make('sent_count')
                    ->label('Pengiriman')
                    ->state(fn (EmailCampaign $record): string => $record->sent_count . ' / ' . $record->total_recipients . ' terkirim')
                    ->description(fn (EmailCampaign $record): ?string => $record->failed_count > 0 ? "{$record->failed_count} gagal" : null)
                    ->color(fn (EmailCampaign $record): string => $record->failed_count > 0 ? 'warning' : 'gray'),

                TextColumn::make('sent_at')
                    ->label('Waktu Terkirim')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum dikirim')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'scheduled' => 'Terjadwal',
                    ]),
                SelectFilter::make('target_audience')
                    ->label('Filter Audiens')
                    ->options([
                        'all' => 'Semua Klien',
                        'onboarding_clients' => 'Leads Onboarding',
                        'blueprint_clients' => 'Project OS Blueprint',
                        'cv_users' => 'CV Pro Users',
                    ]),
            ])
            ->actions([
                // 1. Test Send Action
                Action::make('sendTest')
                    ->label('Kirim Tes')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->form([
                        TextInput::make('test_email')
                            ->label('Alamat Email Penerima Uji Coba')
                            ->email()
                            ->required()
                            ->default(auth()->user()?->email ?: 'yoseph.iriandi.tambunan@gmail.com')
                            ->helperText('Email promosi akan dikirimkan ke alamat ini sebagai simulasi tampilan.'),
                    ])
                    ->action(function (EmailCampaign $record, array $data): void {
                        try {
                            Mail::to($data['test_email'])->send(
                                new PromotionalCampaignMail($record, 'Tester Neriah Pro', $data['test_email'])
                            );

                            Notification::make()
                                ->title('Email Uji Coba Berhasil Dikirim!')
                                ->body("Email simulasi telah dikirim ke {$data['test_email']}.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal Mengirim Email Uji Coba')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // 2. Dispatch Campaign Now Action
                Action::make('dispatchCampaign')
                    ->label('Kirim Kampanye')
                    ->icon('heroicon-o-megaphone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengiriman Massal Email Promosi')
                    ->modalDescription(function (EmailCampaign $record): string {
                        $recipients = $record->resolveRecipients();
                        return "Apakah Anda yakin ingin mengirim kampanye ini ke {$recipients->count()} klien dalam segmen '{$record->target_audience}'? Tindakan ini tidak dapat dibatalkan.";
                    })
                    ->modalSubmitActionLabel('Ya, Luncurkan Kampanye Sekarang')
                    ->action(function (EmailCampaign $record): void {
                        $recipients = $record->resolveRecipients();

                        if ($recipients->isEmpty()) {
                            Notification::make()
                                ->title('Tidak Ada Penerima')
                                ->body('Tidak ditemukan alamat email valid pada segmen audiens yang dipilih.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $record->update([
                            'status' => 'sending',
                            'total_recipients' => $recipients->count(),
                        ]);

                        $sentCount = 0;
                        $failedCount = 0;

                        foreach ($recipients as $recipient) {
                            try {
                                Mail::to($recipient['email'])->send(
                                    new PromotionalCampaignMail($record, $recipient['name'], $recipient['email'])
                                );

                                EmailCampaignLog::create([
                                    'email_campaign_id' => $record->id,
                                    'recipient_email' => $recipient['email'],
                                    'recipient_name' => $recipient['name'],
                                    'status' => 'sent',
                                ]);

                                $sentCount++;
                            } catch (\Throwable $e) {
                                EmailCampaignLog::create([
                                    'email_campaign_id' => $record->id,
                                    'recipient_email' => $recipient['email'],
                                    'recipient_name' => $recipient['name'],
                                    'status' => 'failed',
                                    'error_message' => $e->getMessage(),
                                ]);

                                $failedCount++;
                            }
                        }

                        $record->update([
                            'status' => $sentCount > 0 ? 'sent' : 'failed',
                            'sent_count' => $sentCount,
                            'failed_count' => $failedCount,
                            'sent_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Kampanye Promosi Selesai Dikirim!')
                            ->body("Berhasil mengirim {$sentCount} email. ({$failedCount} gagal).")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (EmailCampaign $record): bool => in_array($record->status, ['draft', 'failed'])),

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
