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
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action as FormAction; // prevent collision with extraItemActions Action
use Illuminate\Support\Str;

class CmsPageResource extends Resource
{
    protected static ?string $model = CmsPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        $getCopyDropdown = function (string $blockType) {
            return \Filament\Forms\Components\Select::make('copy_from_uuid')
                ->label('💡 Copy Settings Dari Halaman Lain')
                ->placeholder('Pilih plugin sejenis dari halaman lain untuk disalin...')
                ->options(function () use ($blockType) {
                    $options = [];
                    foreach (\App\Models\CmsPage::all() as $page) {
                        if (! is_array($page->plugins)) continue;
                        
                        $titles = is_array($page->title) ? $page->title : [];
                        $titleStr = collect($titles)->map(fn($t, $k) => strtoupper($k) . ': ' . $t)->implode(' | ');
                        $pageName = $page->slug . ' - ' . ($titleStr ?: 'No Title');
                        
                        foreach ($page->plugins as $uuid => $plugin) {
                            if (! is_array($plugin) || ! isset($plugin['type'])) continue;
                            if ($plugin['type'] !== $blockType) continue;
                            
                            $anchor = $plugin['data']['anchor_id'] ?? 'no-anchor';
                            $options[$uuid] = "{$pageName} (Anchor: #{$anchor})";
                        }
                    }
                    return $options;
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                    if (! $state) return;
                    foreach (\App\Models\CmsPage::all() as $page) {
                        if (! is_array($page->plugins)) continue;
                        if (isset($page->plugins[$state])) {
                            $pluginData = $page->plugins[$state]['data'] ?? [];
                            foreach ($pluginData as $key => $value) {
                                if ($key === 'copy_from_uuid') continue;
                                $set($key, $value);
                            }
                            break;
                        }
                    }
                    $set('copy_from_uuid', null);
                })
                ->dehydrated(false)
                ->columnSpan(['default' => 12, 'md' => 12]);
        };

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
                            Builder::make('plugins')
                                ->blocks([
                                    Builder\Block::make('hero_section')
                                        ->label('✨ Hero Island (React)')
                                        ->icon('heroicon-m-sparkles')
                                        ->schema([
                                            $getCopyDropdown('hero_section'),
                                            TextInput::make('anchor_id')
                                                ->label('Anchor ID')
                                                ->prefix('#')
                                                ->helperText('Used for page scrolling (e.g. hero)')
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Toggle::make('is_active')
                                                ->default(true)
                                                ->required()
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Group::make()->schema([
                                                TextInput::make('headline')->required(),
                                                TextInput::make('subheadline'),
                                                TextInput::make('button_text'),
                                                TextInput::make('button_url'),
                                            ])->columns(2)->columnSpan(['default' => 12, 'md' => 12]),
                                        ])->columns(12),
                                        
                                    Builder\Block::make('onboarding_form')
                                        ->label('📝 Client Onboarding (React)')
                                        ->icon('heroicon-m-clipboard-document-list')
                                        ->schema([
                                            $getCopyDropdown('onboarding_form'),
                                            TextInput::make('anchor_id')
                                                ->label('Anchor ID')
                                                ->prefix('#')
                                                ->helperText('Used for page scrolling (e.g. hero)')
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Toggle::make('is_active')
                                                ->default(true)
                                                ->required()
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Group::make()->schema([
                                                TextInput::make('form_title')->required(),
                                                TextInput::make('webhook_url')->label('Webhook/Submission URL'),
                                            ])->columns(2)->columnSpan(['default' => 12, 'md' => 12]),
                                        ])->columns(12),

                                    Builder\Block::make('product_grid')
                                        ->label('🛍️ Product Grid (React)')
                                        ->icon('heroicon-m-shopping-bag')
                                        ->schema([
                                            $getCopyDropdown('product_grid'),
                                            TextInput::make('anchor_id')
                                                ->label('Anchor ID')
                                                ->prefix('#')
                                                ->helperText('Used for page scrolling (e.g. hero)')
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Toggle::make('is_active')
                                                ->default(true)
                                                ->required()
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Group::make()->schema([
                                                TextInput::make('section_title'),
                                                TextInput::make('limit')->numeric()->default(6),
                                            ])->columns(2)->columnSpan(['default' => 12, 'md' => 12]),
                                        ])->columns(12),

                                    Builder\Block::make('html_block')
                                        ->label('💻 Raw HTML Block')
                                        ->icon('heroicon-m-code-bracket')
                                        ->schema([
                                            $getCopyDropdown('html_block'),
                                            TextInput::make('anchor_id')
                                                ->label('Anchor ID')
                                                ->prefix('#')
                                                ->helperText('Used for page scrolling (e.g. hero)')
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Toggle::make('is_active')
                                                ->default(true)
                                                ->required()
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Group::make()->schema([
                                                Textarea::make('html_content')->rows(5),
                                            ])->columns(1)->columnSpan(['default' => 12, 'md' => 12]),
                                        ])->columns(12),
                                ])
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->reorderableWithDragAndDrop()
                                ->columnSpan(['default' => 12, 'md' => 12]),
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
