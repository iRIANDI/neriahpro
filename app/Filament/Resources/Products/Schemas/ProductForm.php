<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Product Details')
                    ->schema([
                        \Filament\Schemas\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),
                        \Filament\Schemas\Components\TextInput::make('name.id')
                            ->label('Name (Indonesian)')
                            ->maxLength(255),
                        \Filament\Schemas\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        \Filament\Schemas\Components\RichEditor::make('description.en')
                            ->label('Description (English)')
                            ->columnSpanFull(),
                        \Filament\Schemas\Components\RichEditor::make('description.id')
                            ->label('Description (Indonesian)')
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Pricing & Status')
                    ->schema([
                        \Filament\Schemas\Components\TextInput::make('price_idr')
                            ->label('Price (IDR)')
                            ->required()
                            ->numeric()
                            ->default(0),
                        \Filament\Schemas\Components\TextInput::make('price_usd')
                            ->label('Price (USD)')
                            ->numeric(),
                        \Filament\Schemas\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
