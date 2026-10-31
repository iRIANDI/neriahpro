<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Support\RawJs;

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

                Section::make('Konten Multi-Bahasa Tier-1 (JSON ID & EN)')
                    ->schema([
                        \App\Filament\Forms\Components\TranslatableTabs::make(
                            'Product Bilingual Content',
                            [
                                TextInput::make('name.id')
                                    ->label('Nama Layanan / Produk (ID)')
                                    ->placeholder('Contoh: Enterprise Rapid Monolith')
                                    ->required()
                                    ->maxLength(255),
                                \App\Support\FilamentRichEditor::make('description.id', 'products')
                                    ->label('Deskripsi Layanan (ID)')
                                    ->columnSpanFull(),
                            ],
                            [
                                TextInput::make('name.en')
                                    ->label('Service / Product Name (EN)')
                                    ->placeholder('e.g. Enterprise Rapid Monolith')
                                    ->maxLength(255),
                                \App\Support\FilamentRichEditor::make('description.en', 'products')
                                    ->label('Service Description (EN)')
                                    ->columnSpanFull(),
                            ],
                            ['name', 'description']
                        ),
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
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters([',', '.'])
                            ->numeric()
                            ->default(0),
                        TextInput::make('price_usd')
                            ->label('Harga Ekivalen (USD)')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),
            ]);
    }
}
