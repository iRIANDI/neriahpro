<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dokumen')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Dokumen')
                            ->required()
                            ->maxLength(255),
                        Select::make('document_type')
                            ->label('Tipe Dokumen')
                            ->options([
                                'contract' => 'Kontrak Kerja Sama',
                                'blueprint_approval' => 'Persetujuan Vision Blueprint',
                            ])
                            ->required()
                            ->default('contract'),
                        Select::make('status')
                            ->label('Status Dokumen')
                            ->options([
                                'draft' => 'Draft',
                                'pending_signature' => 'Menunggu Tanda Tangan',
                                'signed' => 'Ditandatangani',
                            ])
                            ->required()
                            ->default('draft'),
                    ])->columns(3),

                Section::make('Nilai Kontrak & Termin Pembayaran Midtrans')
                    ->schema([
                        TextInput::make('contract_amount')
                            ->label('Total Nilai Kontrak (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        TextInput::make('dp_amount')
                            ->label('Uang Muka / DP 50% (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Toggle::make('scope_locked')
                            ->label('Kunci Ruang Lingkup (Scope Locked)')
                            ->helperText('Jika aktif, seluruh fitur terkunci berdasarkan dokumen PRD yang disepakati.')
                            ->default(true),
                        TextInput::make('midtrans_order_id')
                            ->label('Midtrans Order ID')
                            ->maxLength(255),
                        TextInput::make('midtrans_payment_url')
                            ->label('Tautan Pembayaran Midtrans (Snap URL)')
                            ->url()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Informasi Penandatangan & Audit Trail')
                    ->schema([
                        TextInput::make('signer_name')
                            ->label('Nama Penandatangan')
                            ->maxLength(255),
                        TextInput::make('signer_email')
                            ->label('Email Penandatangan')
                            ->email()
                            ->maxLength(255),
                        Placeholder::make('signer_ip_address_display')
                            ->label('IP Address Penandatangan')
                            ->content(fn ($record) => $record?->signer_ip_address ?: '-'),
                        Placeholder::make('signed_at_display')
                            ->label('Waktu Ditandatangani (UTC)')
                            ->content(fn ($record) => $record?->signed_at ? $record->signed_at->format('d M Y H:i:s') : '-'),
                        Placeholder::make('document_hash_display')
                            ->label('SHA-256 Cryptographic Hash')
                            ->content(fn ($record) => $record?->document_hash ?: '-')
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Section::make('Tanda Tangan Digital')
                    ->schema([
                        SignaturePad::make('digital_signature_image')
                            ->label('Tanda Tangan')
                            ->dotSize(2.0)
                            ->lineMinWidth(1.0)
                            ->lineMaxWidth(2.5)
                            ->penColor('blue')
                            ->backgroundColor('rgba(0,0,0,0)')
                            ->clearable()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->status !== 'signed')
                            ->disabled(fn ($record) => $record?->status === 'signed'),
                    ])->columns(1),
            ]);
    }
}
