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

                Section::make('Konten Kebijakan Hukum Tier-1 (JSON ID & EN)')
                    ->schema([
                        \App\Filament\Forms\Components\TranslatableTabs::make(
                            'Legal Policy Bilingual Content',
                            [
                                TextInput::make('title.id')
                                    ->label('Judul Kebijakan (ID)')
                                    ->placeholder('Contoh: Syarat dan Ketentuan Layanan')
                                    ->required()
                                    ->maxLength(255),
                                \App\Support\FilamentRichEditor::make('content.id', 'policies')
                                    ->label('Isi Kebijakan Lengkap (ID)')
                                    ->required()
                                    ->columnSpanFull(),
                            ],
                            [
                                TextInput::make('title.en')
                                    ->label('Policy Title (EN)')
                                    ->placeholder('e.g. Terms and Conditions of Service')
                                    ->maxLength(255),
                                \App\Support\FilamentRichEditor::make('content.en', 'policies')
                                    ->label('Full Policy Content (EN)')
                                    ->columnSpanFull(),
                            ],
                            ['title', 'content']
                        ),
                    ]),
            ]);
    }
}
