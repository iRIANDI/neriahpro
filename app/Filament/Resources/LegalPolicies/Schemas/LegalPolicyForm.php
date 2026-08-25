<?php

namespace App\Filament\Resources\LegalPolicies\Schemas;

use Filament\Schemas\Schema;

class LegalPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Policy Details')
                    ->schema([
                        \Filament\Schemas\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        \Filament\Schemas\Components\Toggle::make('is_active')
                            ->default(true),
                        
                        \Filament\Schemas\Components\TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required()
                            ->maxLength(255),
                        \Filament\Schemas\Components\TextInput::make('title.id')
                            ->label('Title (Indonesian)')
                            ->maxLength(255),
                            
                        \Filament\Schemas\Components\RichEditor::make('content.en')
                            ->label('Content (English)')
                            ->required()
                            ->columnSpanFull(),
                        \Filament\Schemas\Components\RichEditor::make('content.id')
                            ->label('Content (Indonesian)')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
