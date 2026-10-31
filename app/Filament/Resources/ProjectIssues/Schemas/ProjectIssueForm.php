<?php

namespace App\Filament\Resources\ProjectIssues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectIssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vision_blueprint_id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('feature_id')
                    ->default(null),
                \App\Filament\Forms\Components\TranslatableTabs::make(
                    'Project Issue Bilingual Content',
                    [
                        TextInput::make('title.id')
                            ->label('Judul Temuan / Isu (ID)')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description.id')
                            ->label('Deskripsi Rinci Temuan (ID)')
                            ->required()
                            ->rows(3),
                    ],
                    [
                        TextInput::make('title.en')
                            ->label('Issue Title (EN)')
                            ->maxLength(255),
                        Textarea::make('description.en')
                            ->label('Detailed Issue Description (EN)')
                            ->rows(3),
                    ],
                    ['title', 'description']
                ),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
                TextInput::make('priority')
                    ->required()
                    ->default('medium'),
                TextInput::make('type')
                    ->required()
                    ->default('bug'),
                Toggle::make('is_approved_for_uat')
                    ->required(),
                Textarea::make('resolution_notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
