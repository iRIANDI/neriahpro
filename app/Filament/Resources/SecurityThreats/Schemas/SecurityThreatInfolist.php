<?php

namespace App\Filament\Resources\SecurityThreats\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SecurityThreatInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Forensik Percobaan Serangan')
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label('Alamat IP Penyerang')
                            ->weight('bold')
                            ->copyable(),

                        TextEntry::make('threat_type')
                            ->label('Kategori Ancaman')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'rce_system_call' => 'RCE (System Call Execution)',
                                'php_object_deserialization' => 'Unsafe Deserialization',
                                'python_dataset_rce' => 'Dataset Loader Exploit',
                                'php_info_probe' => 'PHP Recon / Info Leak',
                                'critical_path_traversal' => 'Path Traversal',
                                default => ucfirst(str_replace('_', ' ', $state)),
                            })
                            ->color('danger'),

                        TextEntry::make('endpoint')
                            ->label('Target Endpoint URL')
                            ->copyable(),

                        TextEntry::make('http_method')
                            ->label('Metode HTTP')
                            ->badge(),

                        IconEntry::make('is_blocked')
                            ->label('Status Pemblokiran')
                            ->boolean()
                            ->trueIcon('heroicon-o-no-symbol')
                            ->falseIcon('heroicon-o-check-circle')
                            ->trueColor('danger')
                            ->falseColor('success'),

                        TextEntry::make('created_at')
                            ->label('Waktu Deteksi')
                            ->dateTime('d M Y H:i:s')
                            ->description(fn ($record) => $record->created_at->diffForHumans()),

                        TextEntry::make('matched_pattern')
                            ->label('Pola Injeksi yang Cocok')
                            ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                            ->badge()
                            ->color('danger')
                            ->columnSpanFull(),

                        TextEntry::make('user_agent')
                            ->label('User Agent Client')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Sampel Muatan Payload yang Dicegat')
                    ->schema([
                        TextEntry::make('payload_sample')
                            ->label('Raw Payload Dump')
                            ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
