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
                                'all' => 'Semua Klien Terdaftar (Leads, Blueprint & CV Pro)',
                                'onboarding_clients' => 'Klien Leads Onboarding Saja',
                                'blueprint_clients' => 'Klien Project OS Blueprint',
                                'cv_users' => 'Pengguna & Kandidat CV Pro',
                            ])
                            ->default('all')
                            ->required()
                            ->helperText('Sistem akan otomatis menghapus duplikasi alamat email saat pengiriman.'),

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
