<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi Midtrans')
                    ->schema([
                        TextEntry::make('midtrans_order_id')
                            ->label('Order ID')
                            ->copyable()
                            ->weight('bold'),

                        TextEntry::make('midtrans_transaction_id')
                            ->label('Midtrans Transaction ID')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status Transaksi')
                            ->badge()
                            ->colors([
                                'success' => fn ($state): bool => in_array($state, ['settlement', 'capture', 'success']),
                                'warning' => 'pending',
                                'danger' => fn ($state): bool => in_array($state, ['expire', 'cancel', 'deny', 'failed']),
                                'info' => 'refund',
                            ]),

                        TextEntry::make('created_at')
                            ->label('Waktu Dibuat')
                            ->dateTime('d M Y H:i:s'),
                    ])->columns(2),

                Section::make('Informasi Finansial & Pembayaran')
                    ->schema([
                        TextEntry::make('total_idr')
                            ->label('Total (IDR)')
                            ->money('IDR', locale: 'id')
                            ->weight('bold'),

                        TextEntry::make('original_currency')
                            ->label('Mata Uang Asal'),

                        TextEntry::make('original_amount')
                            ->label('Nominal Asal'),

                        TextEntry::make('exchange_rate')
                            ->label('Kurs Konversi')
                            ->placeholder('-'),
                    ])->columns(2),

                Section::make('Detail Klien & Pelanggan')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nama Akun')
                            ->placeholder('-'),

                        TextEntry::make('user.email')
                            ->label('Email Akun')
                            ->placeholder('-'),

                        TextEntry::make('customer_details')
                            ->label('Customer Metadata')
                            ->state(fn ($record) => is_array($record->customer_details) ? json_encode($record->customer_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '-')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
