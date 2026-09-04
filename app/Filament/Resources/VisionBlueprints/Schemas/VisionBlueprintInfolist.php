<?php

namespace App\Filament\Resources\VisionBlueprints\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class VisionBlueprintInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Proyek & Status Publikasi')
                    ->schema([
                        TextEntry::make('nama_bisnis')
                            ->label('Nama Bisnis / Proyek')
                            ->weight('bold'),
                        TextEntry::make('client_name')
                            ->label('PIC Klien'),
                        TextEntry::make('email')
                            ->label('Email Kontak'),
                        TextEntry::make('phone')
                            ->label('WhatsApp')
                            ->placeholder('-'),
                        TextEntry::make('project_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Prospecting' => 'gray',
                                'Contract Signed' => 'warning',
                                'In Progress' => 'info',
                                'Completed' => 'success',
                                default => 'primary',
                            }),
                        IconEntry::make('is_published')
                            ->label('Status Akses Publik')
                            ->boolean()
                            ->trueIcon('heroicon-o-globe-alt')
                            ->falseIcon('heroicon-o-lock-closed')
                            ->trueColor('success')
                            ->falseColor('warning'),
                        TextEntry::make('slug')
                            ->label('Tautan Dokumen PRD')
                            ->url(fn ($record) => $record->public_url)
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn ($state) => url('/blueprint/' . $state))
                            ->color('primary')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Kuesioner Discovery Klien')
                    ->schema([
                        TextEntry::make('masalah_utama')
                            ->label('Masalah Utama')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('tujuan_utama')
                            ->label('Tolak Ukur Kesuksesan (KPI)')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('target_audiens')
                            ->label('Target Audiens')
                            ->placeholder('-'),
                        TextEntry::make('aktor_sistem')
                            ->label('Aktor Sistem / RBAC')
                            ->placeholder('-'),
                        TextEntry::make('fitur_wajib')
                            ->label('Fitur Wajib (Fase 1 - MVP)')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('fitur_tambahan')
                            ->label('Fitur Tambahan (Fase 2)')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('alur_kerja')
                            ->label('Alur Kerja Utama (User Flow)')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('kebutuhan_integrasi')
                            ->label('Kebutuhan Integrasi')
                            ->placeholder('-'),
                        TextEntry::make('referensi_desain')
                            ->label('Referensi Desain')
                            ->placeholder('-'),
                        TextEntry::make('kesiapan_aset')
                            ->label('Kesiapan Aset')
                            ->placeholder('-'),
                        TextEntry::make('target_waktu')
                            ->label('Target Waktu Rilis')
                            ->placeholder('-'),
                    ])->columns(2),
            ]);
    }
}
