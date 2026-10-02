<?php

namespace App\Filament\Resources\BlueprintVouchers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;

class BlueprintVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kode Promo / Voucher Pelayanan')
                    ->description('Voucher ini dapat digunakan klien pada tahap pembayaran Project OS PRD.')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Voucher')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->extraAlpineAttributes(['x-on:input' => '$wire.set(\'data.code\', $el.value.toUpperCase(), false)'])
                            ->placeholder('CONTOH: PELAYANAN-KASIH'),

                        TextInput::make('description')
                            ->label('Deskripsi / Peruntukan')
                            ->placeholder('e.g. Program Pelayanan Gratis Aplikasi Komunitas & UMKM')
                            ->maxLength(255),

                        Select::make('discount_type')
                            ->label('Tipe Potongan / Bypass')
                            ->options([
                                'free_bypass' => '100% Free Bypass (Pelayanan Kasih / Rp 0)',
                                'percent' => 'Persentase Diskon (%)',
                                'fixed' => 'Potongan Tetap (Rp)',
                            ])
                            ->required()
                            ->default('free_bypass')
                            ->reactive(),

                        TextInput::make('discount_value')
                            ->label('Nilai Potongan')
                            ->numeric()
                            ->default(100)
                            ->required()
                            ->helperText('Untuk Free Bypass isi 100. Untuk persen isi 1-100, untuk nominal isi angka rupiah.'),

                        TextInput::make('max_uses')
                            ->label('Batas Maksimum Penggunaan (Kuota)')
                            ->numeric()
                            ->nullable()
                            ->placeholder('Kosongkan jika kuota tidak terbatas')
                            ->helperText('Contoh: 10 klien pertama.'),

                        TextInput::make('used_count')
                            ->label('Jumlah Sudah Digunakan')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),

                        DateTimePicker::make('expires_at')
                            ->label('Tanggal & Waktu Kedaluwarsa')
                            ->nullable()
                            ->helperText('Kosongkan jika voucher berlaku selamanya.'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk mematikan voucher secara instan.'),
                    ])
                    ->columns(2),
            ]);
    }
}
