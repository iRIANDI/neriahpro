<?php

namespace App\Filament\Resources\VisionBlueprints\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class VisionBlueprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('VisionBlueprintTabs')
                    ->tabs([
                        Tabs\Tab::make('Status & Publikasi')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Section::make('Identitas Proyek & Kontrak')
                                    ->schema([
                                        TextInput::make('nama_bisnis')
                                            ->label('Nama Proyek / Bisnis')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('client_name')
                                            ->label('Nama PIC Klien')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Email Kontak')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label('WhatsApp / Telepon')
                                            ->tel()
                                            ->maxLength(50),
                                        Select::make('project_status')
                                            ->label('Status Proyek')
                                            ->options([
                                                'Prospecting' => 'Prospecting',
                                                'Contract Signed' => 'Contract Signed',
                                                'In Progress' => 'In Progress',
                                                'Completed' => 'Completed',
                                            ])
                                            ->required()
                                            ->default('Prospecting'),
                                        Toggle::make('is_published')
                                            ->label('Publikasikan PRD ke Klien (Public Access)')
                                            ->helperText('Jika Aktif, klien dapat melihat dokumen PRD & ERD lengkap di URL publik. Jika Nonaktif, dokumen terkunci dalam mode Private / Draft.')
                                            ->default(true),
                                        TextInput::make('slug')
                                            ->label('Public URL Slug')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Otomatis di-generate untuk tautan publik.'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Kuesioner Discovery')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                                Section::make('Blok A: Konteks Bisnis & Tujuan')
                                    ->schema([
                                        Textarea::make('masalah_utama')
                                            ->label('Masalah Utama yang Ingin Diselesaikan')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('tujuan_utama')
                                            ->label('Tolak Ukur Kesuksesan (KPI)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Blok B: Pengguna & Hak Akses')
                                    ->schema([
                                        TextInput::make('target_audiens')
                                            ->label('Target Audiens / Pengunjung Utama')
                                            ->columnSpanFull(),
                                        Textarea::make('aktor_sistem')
                                            ->label('Aktor Sistem / Pengguna yang Login (RBAC)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Blok C: Fungsionalitas & Alur Kerja')
                                    ->schema([
                                        Textarea::make('fitur_wajib')
                                            ->label('Fitur Wajib (Fase 1 - MVP Peluncuran)')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('fitur_tambahan')
                                            ->label('Fitur Tambahan (Fase 2 - Roadmap)')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('alur_kerja')
                                            ->label('Alur Kerja Utama (User Flow)')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Blok D: Integrasi & Kesiapan Aset')
                                    ->schema([
                                        TextInput::make('kebutuhan_integrasi')
                                            ->label('Kebutuhan Integrasi Pihak Ketiga (API/Gateway)'),
                                        TextInput::make('referensi_desain')
                                            ->label('Referensi Desain / Inspirasi Website'),
                                        Select::make('kesiapan_aset')
                                            ->label('Kesiapan Aset (Logo, Konten)')
                                            ->options([
                                                'Belum Siap Sama Sekali' => 'Belum Siap Sama Sekali',
                                                'Sedang Disiapkan' => 'Sedang Disiapkan Tim Internal',
                                                'Sudah Siap Lengkap' => 'Sudah Siap Lengkap',
                                            ])
                                            ->default('Sedang Disiapkan'),
                                        TextInput::make('target_waktu')
                                            ->label('Target Rilis / Deadline'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Ultimate PRD Engine (Adjustable)')
                            ->icon('heroicon-m-cpu-chip')
                            ->schema([
                                Section::make('Penyesuaian Dokumen PRD & Skema ERD')
                                    ->description('Data di bawah ini disintesis secara otomatis dari input klien. Anda dapat mengedit, menambah tabel database ERD, atau mengubah poin-poin PRD sesuai kesepakatan teknis.')
                                    ->schema([
                                        Textarea::make('prd_content')
                                            ->label('Struktur PRD & Schema Database (JSON)')
                                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $state)
                                            ->mutateDehydratedStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                                            ->rows(25)
                                            ->columnSpanFull()
                                            ->helperText('Simpan perubahan untuk mengunci revisi PRD.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
