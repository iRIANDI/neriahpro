<?php

namespace App\Filament\Resources\InterviewSessions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class InterviewSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sesi Simulasi Wawancara')
                    ->schema([
                        TextInput::make('target_company')
                            ->label('Perusahaan Target')
                            ->required(),

                        TextInput::make('job_title')
                            ->label('Posisi yang Dilamar')
                            ->required(),

                        TextInput::make('overall_score')
                            ->label('Skor Keseluruhan (0-100)')
                            ->numeric()
                            ->suffix('Poin'),

                        Textarea::make('job_description')
                            ->label('Deskripsi Pekerjaan Target')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('questions')
                            ->label('Daftar Pertanyaan AI (JSON)')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $state)
                            ->rows(8)
                            ->columnSpanFull(),

                        Textarea::make('evaluation')
                            ->label('Evaluasi Jawaban STAR (JSON)')
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $state)
                            ->rows(8)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }
}
