<?php

namespace App\Filament\Resources\Resumes\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;

class ResumeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama & Kandidat')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Resume')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('target_role')
                            ->label('Target Posisi / Profesi')
                            ->placeholder('e.g. Senior Backend Engineer')
                            ->maxLength(255),

                        Select::make('template')
                            ->label('Pilihan Desain Template')
                            ->options([
                                'modern_minimalist' => 'Modern Minimalist',
                                'executive_clean' => 'Executive Clean',
                                'creative_ats' => 'Creative ATS-Optimized',
                                'tech_dark' => 'Tech Dark Executive',
                            ])
                            ->default('modern_minimalist')
                            ->required(),

                        Select::make('font_family')
                            ->label('Tipografi Huruf')
                            ->options([
                                'Inter' => 'Inter (Modern Sans)',
                                'Roboto' => 'Roboto (Clean Geometric)',
                                'Lato' => 'Lato (Warm & Professional)',
                                'Merriweather' => 'Merriweather (Classic Serif)',
                            ])
                            ->default('Inter')
                            ->required(),

                        TextInput::make('primary_color')
                            ->label('Warna Aksen Utama')
                            ->default('#4f46e5'),

                        Toggle::make('is_public')
                            ->label('Publikasikan Link Online')
                            ->default(true)
                            ->helperText('Jika aktif, tautan publik /cv/{slug} dapat diakses oleh rekruter.'),

                        TextInput::make('ats_score')
                            ->label('Skor ATS AI (0-100)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix('Poin'),
                    ])->columns(2),

                Section::make('Konten JSON Terstruktur')
                    ->collapsed()
                    ->schema([
                        Textarea::make('content')
                            ->label('Data CV (JSON)')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $state)
                            ->mutateDehydratedStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                            ->rows(20)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
