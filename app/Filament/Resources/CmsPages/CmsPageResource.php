<?php

namespace App\Filament\Resources\CmsPages;

use App\Filament\Forms\Components\TranslatableTabs;
use App\Filament\Resources\CmsPages\Pages\CreateCmsPage;
use App\Filament\Resources\CmsPages\Pages\EditCmsPage;
use App\Filament\Resources\CmsPages\Pages\ListCmsPages;
use App\Models\CmsPage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsPageResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = CmsPage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-window';

    protected static string | \UnitEnum | null $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Manajemen Halaman (CMS)';

    protected static ?string $modelLabel = 'Halaman CMS';

    protected static ?string $pluralModelLabel = 'Halaman CMS';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        $getCopyDropdown = function (string $blockType) {
            return Select::make('copy_from_uuid')
                ->label('💡 Copy Settings Dari Halaman Lain')
                ->placeholder('Pilih plugin sejenis dari halaman lain untuk disalin...')
                ->options(function () use ($blockType) {
                    $options = [];
                    foreach (CmsPage::all() as $page) {
                        if (! is_array($page->plugins)) {
                            continue;
                        }

                        $titles = is_array($page->title) ? $page->title : [];
                        $titleStr = collect($titles)->map(fn ($t, $k) => strtoupper($k) . ': ' . $t)->implode(' | ');
                        $pageName = $page->slug . ' - ' . ($titleStr ?: 'No Title');

                        foreach ($page->plugins as $uuid => $plugin) {
                            if (! is_array($plugin) || ! isset($plugin['type'])) {
                                continue;
                            }
                            if ($plugin['type'] !== $blockType) {
                                continue;
                            }

                            $anchor = $plugin['data']['anchor_id'] ?? 'no-anchor';
                            $options[$uuid] = "{$pageName} (Anchor: #{$anchor})";
                        }
                    }

                    return $options;
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if (! $state) {
                        return;
                    }
                    foreach (CmsPage::all() as $page) {
                        if (! is_array($page->plugins)) {
                            continue;
                        }
                        if (isset($page->plugins[$state])) {
                            $pluginData = $page->plugins[$state]['data'] ?? [];
                            foreach ($pluginData as $key => $value) {
                                if ($key === 'copy_from_uuid') {
                                    continue;
                                }
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
                    Section::make('Page Information (Tier-1 Dwibahasa ID & EN)')
                        ->description('Identitas URL halaman dan judul dwibahasa resmi (ID & EN) menggunakan TranslatableTabs.')
                        ->schema([
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->prefix('/')
                                ->disabled(fn (?CmsPage $record) => $record && in_array($record->slug, ['home', 'beranda', 'index', '/']))
                                ->dehydrated()
                                ->helperText(fn (?CmsPage $record) => $record && in_array($record->slug, ['home', 'beranda', 'index', '/']) ? 'Slug landing page permanen dan tidak dapat diubah.' : 'The URL slug for this page (e.g. about-us)'),

                            TranslatableTabs::make(
                                label: 'Judul Halaman CMS',
                                idSchema: [
                                    TextInput::make('title.id')
                                        ->label('Judul Halaman (Indonesia)')
                                        ->placeholder('e.g. Digital Services & Project OS Hub')
                                        ->required()
                                        ->maxLength(255),
                                ],
                                enSchema: [
                                    TextInput::make('title.en')
                                        ->label('Page Title (English)')
                                        ->placeholder('e.g. Digital Services & Project OS Hub')
                                        ->maxLength(255),
                                ],
                                fieldKeys: ['title']
                            ),
                        ])->columns(1),

                    Section::make('SEO & Metadata (Tier-1 Dwibahasa ID & EN)')
                        ->description('Deskripsi meta mesin pencari untuk Bahasa Indonesia dan Bahasa Inggris.')
                        ->schema([
                            TranslatableTabs::make(
                                label: 'Meta Description SEO',
                                idSchema: [
                                    Textarea::make('meta_description.id')
                                        ->label('Meta Description (Indonesia)')
                                        ->rows(3)
                                        ->maxLength(320),
                                ],
                                enSchema: [
                                    Textarea::make('meta_description.en')
                                        ->label('Meta Description (English)')
                                        ->rows(3)
                                        ->maxLength(320),
                                ],
                                fieldKeys: ['meta_description']
                            ),
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

                                    Builder\Block::make('cv_pricing_table')
                                        ->label('💎 CV Pro Pricing Table (React Island)')
                                        ->icon('heroicon-m-banknotes')
                                        ->schema([
                                            $getCopyDropdown('cv_pricing_table'),
                                            TextInput::make('anchor_id')
                                                ->label('Anchor ID')
                                                ->prefix('#')
                                                ->default('pricing')
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Toggle::make('is_active')
                                                ->default(true)
                                                ->required()
                                                ->columnSpan(['default' => 12, 'md' => 6]),
                                            Group::make()->schema([
                                                TextInput::make('headline')
                                                    ->default('INVESTASI KARIR IMPIAN // PILIHAN KELAS & KUOTA CV PRO')
                                                    ->required(),
                                                TextInput::make('subheadline')
                                                    ->default('Pilih paket yang sesuai dengan akselerasi karir Anda. Pengunjung gratis tetap dapat mengisi form manual dan unduh PDF sepuasnya.'),
                                            ])->columns(1)->columnSpan(['default' => 12, 'md' => 12]),
                                        ])->columns(12),
                                ])
                                ->collapsible()
                                ->collapsed()
                                ->cloneable()
                                ->reorderableWithDragAndDrop()
                                ->columnSpan(['default' => 12, 'md' => 12]),
                        ])->columns(1),
                ])->columnSpan(['default' => 12, 'md' => 12]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Page Title')
                    ->getStateUsing(function (CmsPage $record) {
                        $titles = is_array($record->title) ? $record->title : [];
                        $locale = app()->getLocale();

                        return $titles[$locale] ?? $titles['id'] ?? $titles['en'] ?? collect($titles)->first() ?? 'No Title';
                    })
                    ->description(fn (CmsPage $record) => 'URL: /' . $record->slug)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plugins_count')
                    ->label('Total Blocks')
                    ->getStateUsing(fn (CmsPage $record) => is_array($record->plugins) ? count($record->plugins) : 0)
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-puzzle-piece'),

                \Filament\Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Public / Published')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('view_live')
                    ->label('Lihat Web')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (CmsPage $record) => url('/' . ($record->slug === 'home' || $record->slug === 'index' ? '' : $record->slug)))
                    ->openUrlInNewTab(),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->disabled(fn (CmsPage $record) => in_array($record->slug, ['home', 'beranda', 'index', '/']))
                    ->tooltip(fn (CmsPage $record) => in_array($record->slug, ['home', 'beranda', 'index', '/']) ? 'Landing page inti tidak boleh dihapus.' : ''),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                if (! in_array($record->slug, ['home', 'beranda', 'index', '/'])) {
                                    $record->delete();
                                }
                            });
                        }),
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
