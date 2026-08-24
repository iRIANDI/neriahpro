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
use Filament\Schemas\Components\Tabs;
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
            ->columns(12)
            ->components([
                Group::make()->schema([
                    Tabs::make('Settings')
                        ->tabs([
                            Tabs\Tab::make('General Setup')
                                ->icon('heroicon-m-adjustments-horizontal')
                                ->schema([
                                    Select::make('key')
                                        ->label('Setting Category')
                                        ->options([
                                            'app_timezone' => 'Master Timezone (UTC Offset)',
                                            'site_identity' => 'Site Identity',
                                            'contact_info' => 'Contact Information',
                                            'social_links' => 'Social Links',
                                            'navigation_format' => 'Navigation Format',
                                            'footer_format' => 'Footer Format',
                                            'footer_links' => 'Footer Links (Legacy)',
                                            'schema_org_jsonld' => 'Schema.org JSON-LD (SEO)',
                                        ])
                                        ->searchable()
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->live()
                                        ->helperText('Select the specific setting you want to manage.'),
                                ]),

                            Tabs\Tab::make('Configuration Details')
                                ->icon('heroicon-m-cog')
                                ->schema([
                                    // Timezone Select
                                    Select::make('timezone_value')
                                        ->label('Application Timezone')
                                        ->options([
                                            'UTC' => 'UTC (Standard)',
                                            'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat (GMT+7)',
                                            'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah (GMT+8)',
                                            'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur (GMT+9)',
                                        ])
                                        ->searchable()
                                        ->visible(fn ($get) => $get('key') === 'app_timezone'),

                                    \Filament\Forms\Components\Builder::make('navigation_value')
                                        ->label('Navigation Configuration')
                                        ->blocks([
                                            \Filament\Forms\Components\Builder\Block::make('simple_nav')
                                                ->label('Simple Navigation')
                                                ->icon('heroicon-m-bars-3')
                                                ->schema([
                                                    \Filament\Forms\Components\Repeater::make('links')->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('url')->required(),
                                                    ])->columns(2)
                                                ]),
                                            \Filament\Forms\Components\Builder\Block::make('mega_menu_nav')
                                                ->label('Mega Menu Navigation')
                                                ->icon('heroicon-m-queue-list')
                                                ->schema([
                                                    \Filament\Forms\Components\Repeater::make('menus')->schema([
                                                        TextInput::make('title')->required(),
                                                        \Filament\Forms\Components\Repeater::make('links')->schema([
                                                            TextInput::make('label')->required(),
                                                            TextInput::make('url')->required(),
                                                        ])->columns(2)
                                                    ])
                                                ]),
                                        ])
                                        ->maxItems(1)
                                        ->visible(fn ($get) => $get('key') === 'navigation_format'),

                                    \Filament\Forms\Components\Builder::make('footer_value')
                                        ->label('Footer Configuration')
                                        ->blocks([
                                            \Filament\Forms\Components\Builder\Block::make('simple_footer')
                                                ->label('Simple Footer')
                                                ->icon('heroicon-m-document-minus')
                                                ->schema([
                                                    TextInput::make('copyright_text')->required(),
                                                ]),
                                            \Filament\Forms\Components\Builder\Block::make('multi_column_footer')
                                                ->label('Multi-Column Footer')
                                                ->icon('heroicon-m-view-columns')
                                                ->schema([
                                                    TextInput::make('copyright_text')->required(),
                                                    \Filament\Forms\Components\Repeater::make('columns')->schema([
                                                        TextInput::make('title')->required(),
                                                        \Filament\Forms\Components\Repeater::make('links')->schema([
                                                            TextInput::make('label')->required(),
                                                            TextInput::make('url')->required(),
                                                        ])->columns(2)
                                                    ])
                                                ]),
                                        ])
                                        ->maxItems(1)
                                        ->visible(fn ($get) => in_array($get('key'), ['footer_format', 'footer_links'])),

                                    \Filament\Forms\Components\Textarea::make('schema_value')
                                        ->label('Schema.org JSON-LD')
                                        ->rows(10)
                                        ->visible(fn ($get) => $get('key') === 'schema_org_jsonld'),

                                    KeyValue::make('properties_value')
                                        ->label('Properties Data')
                                        ->visible(fn ($get) => !in_array($get('key'), ['app_timezone', 'navigation_format', 'footer_format', 'footer_links', 'schema_org_jsonld'])),
                                ]),
                        ])
                ])->columnSpan(['default' => 12, 'md' => 8]),

                Group::make()->schema([
                    Section::make('Information')
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('help')
                                ->content('This setting affects the site-wide frontend appearance. The Properties Data table allows you to define flexible key-value pairs.')
                                ->hiddenLabel(),
                        ])->columns(1),
                ])->columnSpan(['default' => 12, 'md' => 4]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => \Illuminate\Support\Str::headline($state))
                    ->icon('heroicon-m-cog-6-tooth')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('primary'),
                    
                TextColumn::make('value')
                    ->label('Configured Format')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && !empty($state)) {
                            $first = reset($state);
                            if (is_array($first) && isset($first['type'])) {
                                return \Illuminate\Support\Str::headline($first['type']);
                            }
                        }
                        return 'Legacy / Empty';
                    })
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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

