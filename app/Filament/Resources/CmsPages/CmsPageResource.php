<?php

namespace App\Filament\Resources\CmsPages;

use App\Filament\Resources\CmsPages\Pages\CreateCmsPage;
use App\Filament\Resources\CmsPages\Pages\EditCmsPage;
use App\Filament\Resources\CmsPages\Pages\ListCmsPages;
use App\Models\CmsPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;

class CmsPageResource extends Resource
{
    protected static ?string $model = CmsPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Group::make()->schema([
                    Section::make('Page Information')
                        ->description('Basic information and content for this page.')
                        ->schema([
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->prefix('/')
                                ->helperText('The URL slug for this page (e.g. about-us)'),
                            KeyValue::make('title')
                                ->label('Page Titles (Multilingual)')
                                ->keyLabel('Language Code (e.g. en, id)')
                                ->valueLabel('Title')
                                ->helperText('Define the page title in multiple languages.'),
                        ])->columns(1),
                        
                    Section::make('SEO & Metadata')
                        ->description('Search engine optimization settings.')
                        ->schema([
                            KeyValue::make('meta_description')
                                ->label('Meta Descriptions')
                                ->keyLabel('Language Code')
                                ->valueLabel('Description')
                                ->helperText('A brief description for search engines.'),
                        ])->columns(1),
                ])->columnSpan(['default' => 12, 'md' => 8]),

                Group::make()->schema([
                    Section::make('Visibility')
                        ->description('Control the page visibility.')
                        ->schema([
                            Toggle::make('is_published')
                                ->label('Published')
                                ->helperText('Toggle to make this page visible to the public.')
                                ->default(false),
                        ])->columns(1),
                ])->columnSpan(['default' => 12, 'md' => 4]),
                
                Group::make()->schema([
                    Section::make('Page Content (Plugins)')
                        ->description('Add and arrange the content blocks (React Islands) for this page.')
                        ->schema([
                            Repeater::make('plugins')
                                ->relationship('plugins')
                                ->schema([
                                    Select::make('plugin_type')
                                        ->options([
                                            'hero_section' => 'Hero Island (React)',
                                            'onboarding_form' => 'Client Onboarding (React)',
                                            'product_grid' => 'Product Grid (React)',
                                            'html_block' => 'Raw HTML Block',
                                        ])
                                        ->required()
                                        ->searchable()
                                        ->live()
                                        ->columnSpan(['default' => 12, 'md' => 4]),
                                    TextInput::make('order')
                                        ->numeric()
                                        ->default(0)
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 2]),
                                    Toggle::make('is_active')
                                        ->default(true)
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 2]),
                                    KeyValue::make('content_data')
                                        ->label('Plugin Configuration Data')
                                        ->keyLabel('Property Name')
                                        ->valueLabel('Property Value')
                                        ->required()
                                        ->columnSpan(['default' => 12, 'md' => 12]),
                                ])
                                ->columns(12)
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->reorderableWithDragAndDrop()
                                ->orderColumn('order')
                                ->itemLabel(function (array $state): ?string {
                                    $type = $state['plugin_type'] ?? null;
                                    if (! $type) return null;
                                    return ucwords(str_replace('_', ' ', $type));
                                })
                                ->extraItemAttributes(function (array $state): array {
                                    $type = $state['plugin_type'] ?? 'default';
                                    $colors = [
                                        'hero_section' => 'border-l-4 border-l-blue-500 bg-blue-500/5',
                                        'onboarding_form' => 'border-l-4 border-l-purple-500 bg-purple-500/5',
                                        'product_grid' => 'border-l-4 border-l-emerald-500 bg-emerald-500/5',
                                        'html_block' => 'border-l-4 border-l-amber-500 bg-amber-500/5',
                                        'default' => 'border-l-4 border-l-gray-500 bg-gray-500/5',
                                    ];
                                    return [
                                        'class' => $colors[$type] ?? $colors['default'],
                                    ];
                                }),
                        ])->columns(1),
                ])->columnSpan(['default' => 12, 'md' => 12]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')->searchable()->sortable(),
                IconColumn::make('is_published')->boolean(),
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
            'index' => ListCmsPages::route('/'),
            'create' => CreateCmsPage::route('/create'),
            'edit' => EditCmsPage::route('/{record}/edit'),
        ];
    }
}
