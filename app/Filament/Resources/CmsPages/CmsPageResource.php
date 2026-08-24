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
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use App\Models\CmsPlugin;
use Illuminate\Support\Str;

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
                                    
                                    TextInput::make('content_data.anchor_id')
                                        ->label('Anchor ID')
                                        ->prefix('#')
                                        ->helperText('Used for page scrolling (e.g. hero)')
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

                                    // Dynamic Form: Hero Section
                                    Group::make()->schema([
                                        TextInput::make('content_data.headline')->required(),
                                        TextInput::make('content_data.subheadline'),
                                        TextInput::make('content_data.button_text'),
                                        TextInput::make('content_data.button_url'),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(['default' => 12, 'md' => 12])
                                    ->visible(fn ($get) => $get('plugin_type') === 'hero_section'),

                                    // Dynamic Form: Onboarding Form
                                    Group::make()->schema([
                                        TextInput::make('content_data.form_title')->required(),
                                        TextInput::make('content_data.webhook_url')->label('Webhook/Submission URL'),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(['default' => 12, 'md' => 12])
                                    ->visible(fn ($get) => $get('plugin_type') === 'onboarding_form'),
                                    
                                    // Dynamic Form: Product Grid
                                    Group::make()->schema([
                                        TextInput::make('content_data.section_title'),
                                        TextInput::make('content_data.limit')->numeric()->default(6),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(['default' => 12, 'md' => 12])
                                    ->visible(fn ($get) => $get('plugin_type') === 'product_grid'),

                                    // Dynamic Form: HTML Block
                                    Group::make()->schema([
                                        Textarea::make('content_data.html_content')->rows(5),
                                    ])
                                    ->columns(1)
                                    ->columnSpan(['default' => 12, 'md' => 12])
                                    ->visible(fn ($get) => $get('plugin_type') === 'html_block'),
                                ])
                                ->columns(12)
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->reorderableWithDragAndDrop()
                                ->orderColumn('order')
                                ->extraItemActions([
                                    Action::make('copy_plugin')
                                        ->label('Salin Settings')
                                        ->icon('heroicon-m-document-duplicate')
                                        ->form([
                                            Select::make('source_page_id')
                                                ->label('Source Page')
                                                ->options(function () {
                                                    return CmsPage::all()->pluck('slug', 'id');
                                                })
                                                ->required()
                                                ->live(),
                                            Select::make('source_plugin_id')
                                                ->label('Plugin to Copy')
                                                ->options(function ($get, array $arguments, Repeater $component) {
                                                    $pageId = $get('source_page_id');
                                                    if (! $pageId) return [];
                                                    
                                                    $itemUuid = $arguments['item'] ?? null;
                                                    $itemState = $itemUuid ? $component->getItemState($itemUuid) : [];
                                                    $currentType = $itemState['plugin_type'] ?? null;
                                                    
                                                    $query = CmsPlugin::where('cms_page_id', $pageId);
                                                    if ($currentType) {
                                                        $query->where('plugin_type', $currentType);
                                                    }
                                                    
                                                    return $query->get()
                                                        ->mapWithKeys(function ($plugin) {
                                                            $anchor = $plugin->content_data['anchor_id'] ?? 'no-anchor';
                                                            return [$plugin->id => "{$plugin->plugin_type} (#{$anchor})"];
                                                        });
                                                })
                                                ->required(),
                                        ])
                                        ->action(function (array $data, array $arguments, Repeater $component) {
                                            $pluginToCopy = CmsPlugin::find($data['source_plugin_id']);
                                            $itemUuid = $arguments['item'] ?? null;
                                            if ($pluginToCopy && $itemUuid) {
                                                $state = $component->getState();
                                                if (isset($state[$itemUuid])) {
                                                    $state[$itemUuid]['content_data'] = $pluginToCopy->content_data;
                                                    $component->state($state);
                                                }
                                            }
                                        }),
                                ])
                                ->itemLabel(function (array $state): ?string {
                                    $type = $state['plugin_type'] ?? null;
                                    if (! $type) return null;
                                    
                                    $emojis = [
                                        'hero_section' => '✨ ',
                                        'onboarding_form' => '📝 ',
                                        'product_grid' => '🛍️ ',
                                        'html_block' => '💻 ',
                                    ];
                                    
                                    $icon = $emojis[$type] ?? '📦 ';
                                    return $icon . ucwords(str_replace('_', ' ', $type));
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
