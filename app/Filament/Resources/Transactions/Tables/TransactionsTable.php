<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('midtrans_order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->midtrans_transaction_id ? 'Midtrans: ' . $record->midtrans_transaction_id : null),

                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email ?? '-'),

                TextColumn::make('product.slug')
                    ->label('Produk / Layanan')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => fn ($state): bool => in_array($state, ['settlement', 'capture', 'success']),
                        'warning' => 'pending',
                        'danger' => fn ($state): bool => in_array($state, ['expire', 'cancel', 'deny', 'failed']),
                        'info' => 'refund',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'settlement' => 'Settlement (Lunas)',
                        'capture' => 'Capture (Berhasil)',
                        'pending' => 'Pending (Menunggu)',
                        'expire' => 'Expired (Kadaluarsa)',
                        'cancel' => 'Dibatalkan',
                        'deny' => 'Ditolak',
                        'refund' => 'Refund',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('total_idr')
                    ->label('Total (IDR)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'settlement' => 'Settlement (Lunas)',
                        'pending' => 'Pending',
                        'expire' => 'Expired',
                        'cancel' => 'Cancelled',
                        'refund' => 'Refunded',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
