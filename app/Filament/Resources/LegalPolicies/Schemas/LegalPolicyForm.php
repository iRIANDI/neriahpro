<?php

namespace App\Filament\Resources\LegalPolicies\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;

class LegalPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Policy Details')
                    ->schema([
                        TextInput::make('type')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->default(true),
                        
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title.id')
                            ->label('Title (Indonesian)')
                            ->maxLength(255),
                            
                        RichEditor::make('content.en')
                            ->label('Content (English)')
                            ->required()
                            ->columnSpanFull(),
                        RichEditor::make('content.id')
                            ->label('Content (Indonesian)')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
