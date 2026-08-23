<?php

namespace App\Filament\Resources\VisionBlueprints\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VisionBlueprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Otomatis di-generate saat data dibuat'),
                TextInput::make('client_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                \Filament\Forms\Components\CheckboxList::make('service_options')
                    ->options([
                        'Web Development' => 'Web Development',
                        'SEO' => 'SEO',
                        'Social Media Management' => 'Social Media Management',
                        'App Development' => 'App Development',
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('project_status')
                    ->options([
                        'Prospecting' => 'Prospecting',
                        'Contract Signed' => 'Contract Signed',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                    ])
                    ->required()
                    ->default('Prospecting'),
            ]);
    }
}
