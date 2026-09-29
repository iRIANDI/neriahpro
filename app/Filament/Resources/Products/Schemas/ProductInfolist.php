<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk & Layanan')
                    ->schema([
                        TextEntry::make('name_id')
                            ->label('Nama Produk (ID)')
                            ->state(fn ($record) => is_array($record->name) ? ($record->name['id'] ?? '-') : $record->name)
                            ->weight('bold'),

                        TextEntry::make('name_en')
                            ->label('Product Name (EN)')
                            ->state(fn ($record) => is_array($record->name) ? ($record->name['en'] ?? '-') : '-'),

                        TextEntry::make('slug')
                            ->label('Slug / Identifier')
                            ->badge()
                            ->color('gray'),

                        IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        TextEntry::make('price_idr')
                            ->label('Harga (IDR)')
                            ->money('IDR', locale: 'id')
                            ->weight('bold'),

                        TextEntry::make('price_usd')
                            ->label('Harga Ekivalen (USD)')
                            ->money('USD'),
                    ])->columns(2),

                Section::make('Fitur Unggulan Layanan')
                    ->schema([
                        TextEntry::make('features')
                            ->label('Daftar Fitur')
                            ->bulleted()
                            ->columnSpanFull(),
                    ]),

                Section::make('Deskripsi Layanan')
                    ->schema([
                        TextEntry::make('description_id')
                            ->label('Deskripsi (ID)')
                            ->html()
                            ->state(fn ($record) => is_array($record->description) ? ($record->description['id'] ?? '-') : $record->description),

                        TextEntry::make('description_en')
                            ->label('Description (EN)')
                            ->html()
                            ->state(fn ($record) => is_array($record->description) ? ($record->description['en'] ?? '-') : '-'),
                    ])->columns(2),
            ]);
    }
}
