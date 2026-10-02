<?php

namespace App\Filament\Resources\EmailCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pengirim & Routing Balasan (Reply-To)')
                    ->description('Tentukan nama & email pengirim kustom serta email tujuan ketika penerima membalas.')
                    ->schema([
                        TextInput::make('sender_name')
                            ->label('Nama Pengirim (Display Name)')
                            ->placeholder('e.g. Yoseph Iriandi / Neriah Pro')
                            ->default('Yoseph Iriandi - Neriah Pro')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('sender_email')
                            ->label('Email Pengirim (Sender Address)')
                            ->placeholder('e.g. yoseph@neriahpro.com')
                            ->default('yoseph@neriahpro.com')
                            ->email()
                            ->required()
                            ->helperText('Bebas menggunakan nama alamat email apapun pada domain resmi neriahpro.com.'),

                        TextInput::make('reply_to_email')
                            ->label('Email Tujuan Balasan (Reply-To Header)')
                            ->placeholder('e.g. yoseph.iriandi.tambunan@gmail.com')
                            ->default('yoseph.iriandi.tambunan@gmail.com')
                            ->email()
                            ->required()
                            ->helperText('PENTING: Ketika penerima menekan "Reply/Balas", email akan langsung terkirim ke Gmail pribadi ini.'),

                        TextInput::make('reply_to_name')
                            ->label('Nama Penerima Balasan')
                            ->default('Yoseph Iriandi')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Informasi Kampanye Promosi')
                    ->description('Tentukan judul internal, audiens target, dan subjek email penawaran.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Kampanye')
                            ->placeholder('e.g. Promo Eksklusif Q4 Arsitektur Digital & CV Pro')
                            ->required()
                            ->maxLength(255),

                        Select::make('target_audience')
                            ->label('Target Segmen Klien')
                            ->options([
                                'all' => 'Semua Klien Terdaftar (CRM Leads, Blueprint & CV Pro)',
                                'lead_contacts' => 'Database Kontak CRM / Leads Perusahaan',
                                'manual_recipient' => 'Kirim ke 1 Penerima Spesifik (Kustom)',
                                'onboarding_clients' => 'Klien Leads Onboarding Saja',
                                'blueprint_clients' => 'Klien Project OS Blueprint',
                                'cv_users' => 'Pengguna & Kandidat CV Pro',
                            ])
                            ->default('all')
                            ->live()
                            ->required()
                            ->helperText('Pilih audiens tujuan atau pilih 1 penerima manual untuk pengiriman langsung.'),

                        TextInput::make('custom_recipient_email')
                            ->label('Email Penerima Kustom')
                            ->placeholder('calon.klien@perusahaan.com')
                            ->email()
                            ->visible(fn ($get) => $get('target_audience') === 'manual_recipient')
                            ->required(fn ($get) => $get('target_audience') === 'manual_recipient'),

                        TextInput::make('custom_recipient_name')
                            ->label('Nama Penerima Kustom')
                            ->placeholder('Bpk. Budi Santoso')
                            ->visible(fn ($get) => $get('target_audience') === 'manual_recipient'),

                        TextInput::make('custom_company_name')
                            ->label('Nama Perusahaan Penerima')
                            ->placeholder('PT Teknologi Nusantara Maju')
                            ->visible(fn ($get) => $get('target_audience') === 'manual_recipient'),

                        TextInput::make('subject')
                            ->label('Subjek Email')
                            ->placeholder('e.g. Akselerasikan Bisnis Anda: Diskon 25% Layanan Arsitektur Perangkat Lunak')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('preview_text')
                            ->label('Teks Pratinjau (Preheader)')
                            ->placeholder('Teks singkat yang muncul sebelum email dibuka')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Isi Pesan Promosi & Call-to-Action')
                    ->description('Gunakan tag personalisasi {name} atau {nama} untuk menyebut nama klien secara otomatis.')
                    ->schema([
                        Textarea::make('content_html')
                            ->label('Isi Surat / Penawaran Promosi')
                            ->rows(8)
                            ->required()
                            ->placeholder("Kami memiliki penawaran spesial untuk kebutuhan digitalisasi perusahaan Anda...\n\nGunakan kode promo NERIAHPRO2026 untuk mendapatkan potongan harga.")
                            ->helperText('Tersedia merge tag: {name}, {email}, {cta_url}. Format paragraf akan otomatis dikonversi rapi ke email.'),

                        TextInput::make('cta_label')
                            ->label('Teks Tombol Aksi (CTA)')
                            ->default('Klaim Promo Sekarang')
                            ->maxLength(100),

                        TextInput::make('cta_url')
                            ->label('Tautan Tujuan Tombol (URL)')
                            ->default(url('/'))
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Jadwal & Status Pengiriman')
                    ->schema([
                        Select::make('status')
                            ->label('Status Kampanye')
                            ->options([
                                'draft' => 'Draft (Belum Dikirim)',
                                'scheduled' => 'Terjadwal',
                                'sending' => 'Sedang Mengirim',
                                'sent' => 'Terkirim Selesai',
                                'failed' => 'Gagal',
                            ])
                            ->default('draft')
                            ->disabled()
                            ->dehydrated(),

                        DateTimePicker::make('scheduled_at')
                            ->label('Jadwalkan Pengiriman Otomatis')
                            ->helperText('Opsional: Tentukan waktu otomatis pengiriman email.'),
                    ])->columns(2),
            ]);
    }
}
