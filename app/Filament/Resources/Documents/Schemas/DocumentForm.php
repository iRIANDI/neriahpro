<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
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
                                'contract' => 'Kontrak',
                                'blueprint_approval' => 'Persetujuan Vision Blueprint',
                            ])
                            ->required()
                            ->default('contract'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'pending_signature' => 'Menunggu Tanda Tangan',
                                'signed' => 'Ditandatangani',
                            ])
                            ->required()
                            ->default('draft'),
                    ])->columns(2),

                Section::make('Tanda Tangan Digital')
                    ->schema([
                        TextInput::make('signer_name')
                            ->label('Nama Penandatangan')
                            ->maxLength(255),
                        TextInput::make('signer_email')
                            ->label('Email Penandatangan')
                            ->email()
                            ->maxLength(255),
                        DateTimePicker::make('signed_at')
                            ->label('Waktu Ditandatangani')
                            ->disabled(),
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
                    ])->columns(2),
            ]);
    }
}
