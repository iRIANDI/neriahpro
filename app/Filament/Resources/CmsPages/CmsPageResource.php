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
use App\Filament\Resources\CmsPages\RelationManagers\PluginsRelationManager;

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
            PluginsRelationManager::class,
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
