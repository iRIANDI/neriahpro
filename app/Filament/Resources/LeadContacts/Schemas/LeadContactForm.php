<?php

namespace App\Filament\Resources\LeadContacts\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kontak & Entitas')
                    ->description('Data profil calon klien, partner, atau penerima surat resmi.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('e.g. Budi Santoso')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('e.g. budi.santoso@perusahaan.co.id')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('company_name')
                            ->label('Nama Perusahaan / Entitas')
                            ->placeholder('e.g. PT Solusi Digital Semesta')
                            ->maxLength(255),

                        TextInput::make('job_title')
                            ->label('Jabatan / Posisi')
                            ->placeholder('e.g. Chief Technology Officer (CTO)')
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->placeholder('e.g. 081234567890')
                            ->tel()
                            ->maxLength(50),

                        Select::make('status')
                            ->label('Status Hubungan')
                            ->options([
                                'lead' => 'Lead Baru',
                                'prospect' => 'Prospek Aktif',
                                'client' => 'Klien Tetap',
                                'partner' => 'Partner Bisnis',
                                'archived' => 'Diarsipkan',
                            ])
                            ->default('lead')
                            ->required(),
                    ])->columns(2),

                Section::make('Metadata & Catatan Tambahan (Kustom)')
                    ->description('Simpan atribut kustom seperti industri, sumber referensi, budget perkiraan, dsb.')
                    ->schema([
                        KeyValue::make('metadata')
                            ->label('Metadata Kustom (Key-Value)')
                            ->keyLabel('Kunci (Key)')
                            ->valueLabel('Nilai (Value)')
                            ->helperText('Contoh: industri => Fintech, tier => Enterprise, sumber => LinkedIn.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
