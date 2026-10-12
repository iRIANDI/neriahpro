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
                Textarea::make('title')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
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
