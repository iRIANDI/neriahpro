<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name.id')
                            ->label('Name (Indonesian)')
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        RichEditor::make('description.en')
                            ->label('Description (English)')
                            ->columnSpanFull(),
                        RichEditor::make('description.id')
                            ->label('Description (Indonesian)')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pricing & Status')
                    ->schema([
                        TextInput::make('price_idr')
                            ->label('Price (IDR)')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('price_usd')
                            ->label('Price (USD)')
                            ->numeric(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
