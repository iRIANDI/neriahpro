<?php

namespace App\Filament\Resources\CmsGlobalSettings;

use App\Filament\Resources\CmsGlobalSettings\Pages\CreateCmsGlobalSetting;
use App\Filament\Resources\CmsGlobalSettings\Pages\EditCmsGlobalSetting;
use App\Filament\Resources\CmsGlobalSettings\Pages\ListCmsGlobalSettings;
use App\Models\CmsGlobalSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class CmsGlobalSettingResource extends Resource
{
    protected static ?string $model = CmsGlobalSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Group::make()->schema([
                        Section::make('Setting Configuration')
                            ->description('Define the global setting key and its corresponding configuration data.')
                            ->schema([
                                Select::make('key')
                                    ->label('Setting Key')
                                    ->options([
                                        'navigation_format' => 'Navigation Format',
                                        'footer_format' => 'Footer Format',
                                        'site_identity' => 'Site Identity',
                                        'social_links' => 'Social Links',
                                        'contact_info' => 'Contact Information',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Select the predefined setting key.'),
                                
                                KeyValue::make('value')
                                    ->label('Configuration Data')
                                    ->keyLabel('Property Name')
                                    ->valueLabel('Property Value')
                                    ->helperText('Define the configuration data for this setting key. e.g. "style" => "minimalist", "layout" => "center".')
                                    ->required(),
                            ]),
                    ])->columnSpan(['sm' => 3, 'md' => 2]),

                    Group::make()->schema([
                        Section::make('Information')
                            ->description('About Global Settings')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('help')
                                    ->content('Global settings control site-wide features. Choose a Setting Key and define its properties in the Configuration Data table.')
                                    ->hiddenLabel(),
                            ]),
                    ])->columnSpan(['sm' => 3, 'md' => 1]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->searchable()->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCmsGlobalSettings::route('/'),
            'create' => CreateCmsGlobalSetting::route('/create'),
            'edit' => EditCmsGlobalSetting::route('/{record}/edit'),
        ];
    }
}
