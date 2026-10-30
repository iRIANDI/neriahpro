<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Support\RawJs;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('product_id')
                            ->relationship('product', 'slug')
                            ->searchable()
                            ->preload(),
                        TextInput::make('midtrans_transaction_id')
                            ->maxLength(255),
                        TextInput::make('midtrans_order_id')
                            ->maxLength(255),
                        Select::make('status')
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
                    
                Section::make('Financial Details')
                    ->schema([
                        TextInput::make('total_idr')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters([',', '.'])
                            ->numeric(),
                        TextInput::make('original_currency')
                            ->default('IDR')
                            ->maxLength(255),
                        TextInput::make('original_amount')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters([',', '.'])
                            ->numeric(),
                        TextInput::make('exchange_rate')
                            ->numeric(),
                    ])->columns(2),

                Section::make('Customer Details')
                    ->schema([
                        KeyValue::make('customer_details')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
