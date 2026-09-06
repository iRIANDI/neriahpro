<?php

namespace App\Filament\Resources\LegalPolicies\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;

class LegalPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identifikasi Kebijakan')
                    ->schema([
                        TextInput::make('type')
                            ->label('Tipe Dokumen / Slug')
                            ->placeholder('terms_and_conditions, privacy_policy, refund_policy')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])->columns(2),

                Section::make('Konten Kebijakan Hukum (JSON)')
                    ->schema([
                        Tabs::make('Language Selector')
                            ->tabs([
                                Tabs\Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-m-language')
                                    ->badge('ID')
                                    ->schema([
                                        TextInput::make('title.id')
                                            ->label('Judul Kebijakan (ID)')
                                            ->placeholder('Contoh: Syarat dan Ketentuan Layanan')
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('content.id')
                                            ->label('Isi Kebijakan Lengkap (ID)')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),

                                Tabs\Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        TextInput::make('title.en')
                                            ->label('Policy Title (EN)')
                                            ->placeholder('e.g. Terms and Conditions of Service')
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('content.en')
                                            ->label('Full Policy Content (EN)')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
