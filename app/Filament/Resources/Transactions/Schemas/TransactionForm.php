<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Transaction Details')
                    ->schema([
                        \Filament\Schemas\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        \Filament\Schemas\Components\Select::make('product_id')
                            ->relationship('product', 'slug')
                            ->searchable()
                            ->preload(),
                        \Filament\Schemas\Components\TextInput::make('midtrans_transaction_id')
                            ->maxLength(255),
                        \Filament\Schemas\Components\TextInput::make('midtrans_order_id')
                            ->maxLength(255),
                        \Filament\Schemas\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'settlement' => 'Settlement',
                                'expire' => 'Expire',
                                'cancel' => 'Cancel',
                                'deny' => 'Deny',
                                'refund' => 'Refund',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),
                    
                \Filament\Schemas\Components\Section::make('Financial Details')
                    ->schema([
                        \Filament\Schemas\Components\TextInput::make('total_idr')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        \Filament\Schemas\Components\TextInput::make('original_currency')
                            ->default('IDR')
                            ->maxLength(255),
                        \Filament\Schemas\Components\TextInput::make('original_amount')
                            ->numeric(),
                        \Filament\Schemas\Components\TextInput::make('exchange_rate')
                            ->numeric(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Customer Details')
                    ->schema([
                        \Filament\Schemas\Components\KeyValue::make('customer_details')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
