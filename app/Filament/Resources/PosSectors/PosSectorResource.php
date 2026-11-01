<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosSectors;

use App\Filament\Forms\Components\TranslatableTabs;
use App\Filament\Resources\PosSectors\Pages\CreatePosSector;
use App\Filament\Resources\PosSectors\Pages\EditPosSector;
use App\Filament\Resources\PosSectors\Pages\ListPosSectors;
use App\Models\PosSector;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;

class PosSectorResource extends Resource
{
    protected static ?string $model = PosSector::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Project OS // Core';

    protected static ?string $navigationLabel = '12 Sektor Domain (ID/EN)';

    protected static ?string $modelLabel = 'Sektor Fungsional Project OS';

    protected static ?string $pluralModelLabel = '12 Sektor Fungsional (Domain Taxonomies)';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'code';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user || $user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'developer', 'investor'])
            || $user->can('manage_project_os_matrix')
            || $user->can('view_project_os_matrix');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->hasRole('super_admin') || $user->can('manage_project_os_matrix'));
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->hasRole('super_admin') || $user->can('manage_project_os_matrix'));
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Kanonik 12 Sektor Fungsional (Domain-Driven Taxonomies)')
                    ->description('Master data 12 Sektor Fungsional yang mengendalikan 36 Varian Visual Frontend UI/UX, 25 Menu L1–L4, dan 12 Preset Audit Kriptografi.')
                    ->schema([
                        TextInput::make('sector_number')
                            ->label('Nomor Sektor (1–12)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('code')
                            ->label('Kode Kanonik Sektor')
                            ->placeholder('e.g. SECTOR_01_COMMERCE_OMNICHANNEL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80),

                        Toggle::make('is_active')
                            ->label('Aktif di Project OS & Studio')
                            ->default(true),
                    ])->columns(3),

                Section::make('Narasi Dwibahasa Tier-1 (TranslatableTabs ID & EN — Zero LocaleSwitcher)')
                    ->description('Nama dan deskripsi arsitektur sektor menggunakan TranslatableTabs in-place dengan tombol Google Translate 1-klik.')
                    ->schema([
                        TranslatableTabs::make(
                            label: 'Spesifikasi Dwibahasa Sektor Fungsional',
                            idSchema: [
                                TextInput::make('name.id')
                                    ->label('Nama Sektor Bisnis (Indonesia)')
                                    ->required()
                                    ->maxLength(180),
                                Textarea::make('description.id')
                                    ->label('Deskripsi Domain & Cakupan Sistem (Indonesia)')
                                    ->required()
                                    ->rows(3),
                            ],
                            enSchema: [
                                TextInput::make('name.en')
                                    ->label('Functional Sector Name (English)')
                                    ->maxLength(180),
                                Textarea::make('description.en')
                                    ->label('Domain Scope & Architecture Description (English)')
                                    ->rows(3),
                            ],
                            fieldKeys: ['name', 'description']
                        ),
                    ]),

                Section::make('Standar Regulasi, Invarian Domain & Kata Kunci Deteksi AI')
                    ->schema([
                        TagsInput::make('default_compliance_standards')
                            ->label('Standar Regulasi & Kepatuhan Default')
                            ->placeholder('e.g. PSAK 109, ISAK 35, UU PDP, SOC2'),

                        TagsInput::make('blueprint_invariants')
                            ->label('Invarian Mutlak Domain Bisnis')
                            ->placeholder('Tambah aturan invarian lalu tekan Enter'),

                        TagsInput::make('detection_keywords')
                            ->label('Kata Kunci Deteksi Otomatis AI Discovery')
                            ->placeholder('e.g. kasir, pos, klinik, donasi, yayasan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationMode(PaginationMode::Cursor)
            ->defaultSort('sector_number', 'asc')
            ->columns([
                TextColumn::make('sector_number')
                    ->label('#')
                    ->sortable()
                    ->badge()
                    ->color('emerald'),

                TextColumn::make('name')
                    ->label(fn (): string => app()->getLocale() === 'en' ? 'Sector Name (ID / EN)' : 'Nama Sektor (ID / EN)')
                    ->getStateUsing(fn (PosSector $record): string => $record->localizedName())
                    ->description(fn (PosSector $record): string => $record->code)
                    ->wrap(),

                TextColumn::make('description')
                    ->label(fn (): string => app()->getLocale() === 'en' ? 'Domain Architecture Scope' : 'Cakupan Arsitektur Domain')
                    ->getStateUsing(fn (PosSector $record): string => $record->localizedDescription())
                    ->limit(85)
                    ->wrap(),

                TextColumn::make('default_compliance_standards')
                    ->label(fn (): string => app()->getLocale() === 'en' ? 'Compliance' : 'Standar Regulasi')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosSectors::route('/'),
            'create' => CreatePosSector::route('/create'),
            'edit' => EditPosSector::route('/{record}/edit'),
        ];
    }
}

