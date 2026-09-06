<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Perjanjian')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => 'PIC: ' . ($record->signer_name ?: '-') . ' (' . ($record->signer_email ?: '-') . ')'),

                TextColumn::make('status')
                    ->label('Status TTD')
                    ->badge()
                    ->colors([
                        'danger' => 'draft',
                        'warning' => 'pending_signature',
                        'success' => 'signed',
                    ]),

                TextColumn::make('e_meterai_status')
                    ->label('E-Meterai')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'stamped' => 'E-Meterai Sah',
                        'pending' => 'Menunggu Pembubuhan',
                        'failed' => 'Gagal',
                        default => 'Tanpa E-Meterai',
                    })
                    ->colors([
                        'success' => 'stamped',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'gray' => 'none',
                    ])
                    ->description(fn ($record) => $record->e_meterai_sn ?: null),

                IconColumn::make('scope_locked')
                    ->label('Scope Lock')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('contract_amount')
                    ->label('Nilai Kontrak')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('dp_amount')
                    ->label('DP 50% (Midtrans)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->description(fn ($record) => $record->midtrans_order_id ? 'Order: ' . $record->midtrans_order_id : null),

                TextColumn::make('signed_at')
                    ->label('Ditandatangani')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_signature' => 'Menunggu Tanda Tangan',
                        'signed' => 'Ditandatangani',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('e_meterai_status')
                    ->options([
                        'stamped' => 'E-Meterai Sah',
                        'pending' => 'Menunggu Pembubuhan',
                        'none' => 'Tanpa E-Meterai',
                    ]),
            ])
            ->recordActions([
                Action::make('sign_document')
                    ->label('Halaman TTD')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn ($record) => route('document.sign', $record))
                    ->openUrlInNewTab(),

                Action::make('midtrans_dp')
                    ->label('Bayar DP Midtrans')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->url(fn ($record) => $record->midtrans_payment_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->midtrans_payment_url)),

                Action::make('preview_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn ($record) => route('document.preview', $record))
                    ->openUrlInNewTab(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
