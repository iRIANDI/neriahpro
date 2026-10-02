<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identifikasi Produk')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Slug / Identifier')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])->columns(2),

                Section::make('Konten Multi-Bahasa (JSON)')
                    ->schema([
                        Tabs::make('Language Selector')
                            ->tabs([
                                Tabs\Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-m-language')
                                    ->badge('ID')
                                    ->schema([
                                        TextInput::make('name.id')
                                            ->label('Nama Layanan / Produk (ID)')
                                            ->placeholder('Contoh: Enterprise Rapid Monolith')
                                            ->required()
                                            ->maxLength(255),
                                        \App\Support\FilamentRichEditor::make('description.id', 'products')
                                            ->label('Deskripsi Layanan (ID)')
                                            ->columnSpanFull(),
                                    ]),

                                Tabs\Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Service / Product Name (EN)')
                                            ->placeholder('e.g. Enterprise Rapid Monolith')
                                            ->required()
                                            ->maxLength(255),
                                        \App\Support\FilamentRichEditor::make('description.en', 'products')
                                            ->label('Service Description (EN)')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Fitur Unggulan Layanan')
                    ->schema([
                        \Filament\Forms\Components\TagsInput::make('features')
                            ->label('Daftar Fitur')
                            ->placeholder('Ketik fitur lalu tekan Enter...')
                            ->helperText('Contoh: Backend Laravel 13, Paginasi Keyset O(1), Dasbor Admin Filament')
                            ->columnSpanFull(),
                    ]),

                Section::make('Harga & Pembayaran (Midtrans Ready)')
                    ->schema([
                        TextInput::make('price_idr')
                            ->label('Harga (IDR)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        TextInput::make('price_usd')
                            ->label('Harga Ekivalen (USD)')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),
            ]);
    }
}
