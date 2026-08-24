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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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
                Grid::make(['default' => 1, 'sm' => 1, 'md' => 12])->schema([
                    Group::make()->schema([
                        Section::make('Configuration')
                            ->description('Define the global setting key and its configuration values.')
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
                                    ->helperText('Select the specific setting you want to manage.'),
                                
                                KeyValue::make('value')
                                    ->label('Properties Data')
                                    ->keyLabel('Property (e.g. style, align)')
                                    ->valueLabel('Value (e.g. dark, center)')
                                    ->helperText('Provide specific attributes for this setting.')
                                    ->required(),
                            ])->columns(['default' => 1, 'lg' => 2]), // Put them side-by-side on large screens
                    ])->columnSpan(['default' => 1, 'sm' => 1, 'md' => 8, 'lg' => 9]),

                    Group::make()->schema([
                        Section::make('Information')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('help')
                                    ->content('This setting affects the site-wide frontend appearance.')
                                    ->hiddenLabel(),
                            ]),
                    ])->columnSpan(['default' => 1, 'sm' => 1, 'md' => 4, 'lg' => 3]),
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
