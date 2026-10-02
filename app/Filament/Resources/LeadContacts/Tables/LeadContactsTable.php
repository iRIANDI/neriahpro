<?php

namespace App\Filament\Resources\LeadContacts\Tables;

use App\Mail\PromotionalCampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Models\LeadContact;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class LeadContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('company_name')
                    ->label('Perusahaan / Entitas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('job_title')
                    ->label('Jabatan')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lead' => 'info',
                        'prospect' => 'warning',
                        'client' => 'success',
                        'partner' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lead' => 'Lead Baru',
                        'prospect' => 'Prospek Aktif',
                        'client' => 'Klien Tetap',
                        'partner' => 'Partner Bisnis',
                        'archived' => 'Diarsipkan',
                        default => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'lead' => 'Lead Baru',
                        'prospect' => 'Prospek Aktif',
                        'client' => 'Klien Tetap',
                        'partner' => 'Partner Bisnis',
                        'archived' => 'Diarsipkan',
                    ]),
            ])
            ->actions([
                // Send Direct Custom Email Action
                Action::make('sendDirectEmail')
                    ->label('Kirim Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form([
                        TextInput::make('sender_name')
                            ->label('Nama Pengirim (Display Name)')
                            ->default('Yoseph Iriandi - Neriah Pro')
                            ->required(),

                        TextInput::make('sender_email')
                            ->label('Email Pengirim (Sender Address)')
                            ->default('yoseph@neriahpro.com')
                            ->email()
                            ->required()
                            ->helperText('Bisa menggunakan email apapun pada domain neriahpro.com (e.g. yoseph@neriahpro.com, hello@neriahpro.com).'),

                        TextInput::make('reply_to_email')
                            ->label('Email Tujuan Balasan (Reply-To Header)')
                            ->default('yoseph.iriandi.tambunan@gmail.com')
                            ->email()
                            ->required()
                            ->helperText('PENTING: Balasan dari penerima akan otomatis masuk ke Gmail pribadi ini.'),

                        TextInput::make('subject')
                            ->label('Subjek Email')
                            ->required()
                            ->placeholder('e.g. Kolaborasi & Solusi Arsitektur Digital untuk {company}'),

                        Textarea::make('content_html')
                            ->label('Isi Pesan Surat / Penawaran')
                            ->rows(6)
                            ->required()
                            ->placeholder("Halo {name},\n\nKami tertarik untuk berdiskusi terkait inovasi arsitektur perangkat lunak bersama {company}...\n\nSalam hormat,\nYoseph Iriandi")
                            ->helperText('Merge tag otomatis: {name}, {company}, {email}, {cta_url}.'),

                        TextInput::make('cta_label')
                            ->label('Teks Tombol Aksi (CTA)')
                            ->default('Jadwalkan Konsultasi')
                            ->maxLength(100),

                        TextInput::make('cta_url')
                            ->label('URL Tombol Aksi')
                            ->default(url('/'))
                            ->url(),
                    ])
                    ->action(function (LeadContact $record, array $data): void {
                        try {
                            $company = $record->company_name ?: 'Perusahaan Anda';
                            $subject = str_replace(
                                ['{name}', '{company}'],
                                [$record->name, $company],
                                $data['subject']
                            );

                            $content = str_replace(
                                ['{company}'],
                                [$company],
                                $data['content_html']
                            );

                            // Create an audit campaign entry
                            $campaign = EmailCampaign::create([
                                'created_by_user_id' => auth()->id(),
                                'title' => 'Direct Mail to: ' . $record->name . ' (' . ($record->company_name ?: $record->email) . ')',
                                'subject' => $subject,
                                'sender_name' => $data['sender_name'],
                                'sender_email' => $data['sender_email'],
                                'reply_to_email' => $data['reply_to_email'],
                                'reply_to_name' => $data['sender_name'],
                                'target_audience' => 'manual_recipient',
                                'custom_recipient_email' => $record->email,
                                'custom_recipient_name' => $record->name,
                                'custom_company_name' => $record->company_name,
                                'content_html' => $content,
                                'cta_label' => $data['cta_label'] ?? 'Lihat Detail',
                                'cta_url' => $data['cta_url'] ?? url('/'),
                                'status' => 'sending',
                                'total_recipients' => 1,
                            ]);

                            Mail::to($record->email)->send(
                                new PromotionalCampaignMail($campaign, $record->name, $record->email)
                            );

                            $campaign->update([
                                'status' => 'sent',
                                'sent_count' => 1,
                                'sent_at' => now(),
                            ]);

                            EmailCampaignLog::create([
                                'email_campaign_id' => $campaign->id,
                                'recipient_email' => $record->email,
                                'recipient_name' => $record->name,
                                'status' => 'sent',
                                'sent_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Email Berhasil Terkirim!')
                                ->body("Email resmi telah dikirim ke {$record->email} dari {$data['sender_email']}. Balasan akan masuk ke {$data['reply_to_email']}.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal Mengirim Email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

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
