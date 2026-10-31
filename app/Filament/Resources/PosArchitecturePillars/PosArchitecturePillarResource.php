<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosArchitecturePillars;

use App\Filament\Forms\Components\TranslatableTabs;
use App\Filament\Resources\PosArchitecturePillars\Pages\CreatePosArchitecturePillar;
use App\Filament\Resources\PosArchitecturePillars\Pages\EditPosArchitecturePillar;
use App\Filament\Resources\PosArchitecturePillars\Pages\ListPosArchitecturePillars;
use App\Models\PosArchitecturePillar;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PosArchitecturePillarResource extends Resource
{
    use \App\Filament\Traits\RestrictedToSuperAdmin;

    protected static ?string $model = PosArchitecturePillar::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Project OS & Engineering';

    protected static ?string $navigationLabel = '15 Pilar Project OS (Freeze)';

    protected static ?string $modelLabel = 'Pilar Arsitektur Project OS';

    protected static ?string $pluralModelLabel = '15 Pilar Project OS (100% Freeze)';

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Kanonik Pilar Project OS (1–15 Freeze)')
                    ->description('Master data resmi 15 Pilar Project OS Ultimate 100% (neriahpro.com) dengan penguncian arsitektur dan sinkronisasi lintas tahap.')
                    ->schema([
                        TextInput::make('pillar_number')
                            ->label('Nomor Pilar (1–15)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('code')
                            ->label('Kode Kanonik Pilar')
                            ->placeholder('e.g. PILLAR_09_HARDWARE_IOT_AND_WEBSOCKET')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80),

                        Select::make('category')
                            ->label('Kategori Lapisan Arsitektur')
                            ->options([
                                'DOMAIN_CORE' => 'Domain Core & Menu Matrix',
                                'STACK_UI' => 'Stack Topology & Zero-Slop UI',
                                'DATA_RUNTIME' => 'PG16, Concurrency, Hardware & WebSocket',
                                'DAY2_AI_HARNESS' => 'Day-2 Ops, AI Copilot, Ponytail & DAG',
                                'COMMERCIAL_LIFECYCLE' => 'Delta Engine, MCP & Dual Business Engine',
                            ])
                            ->required(),

                        Toggle::make('is_locked_freeze')
                            ->label('100% Architectural Freeze Lock')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('Aktif di Project OS Studio')
                            ->default(true),
                    ])->columns(3),

                Section::make('Narasi Dwibahasa Tier-1 (TranslatableTabs ID & EN)')
                    ->description('Nama pilar, fungsi jaminan utama, dan kontrak teknis menggunakan TranslatableTabs in-place dengan tombol Google Translate 1-klik.')
                    ->schema([
                        TranslatableTabs::make(
                            label: 'Spesifikasi Dwibahasa 15 Pilar',
                            idSchema: [
                                TextInput::make('name.id')
                                    ->label('Nama Pilar Sistem (Indonesia)')
                                    ->required()
                                    ->maxLength(180),
                                Textarea::make('function_guarantee.id')
                                    ->label('Fungsi & Jaminan Utama (Indonesia)')
                                    ->required()
                                    ->rows(3),
                                Textarea::make('technical_contract.id')
                                    ->label('Kontrak Teknis & Invarian Lintas Tahap (Indonesia)')
                                    ->required()
                                    ->rows(3),
                            ],
                            enSchema: [
                                TextInput::make('name.en')
                                    ->label('System Pillar Name (English)')
                                    ->maxLength(180),
                                Textarea::make('function_guarantee.en')
                                    ->label('Core Function & Guarantee (English)')
                                    ->rows(3),
                                Textarea::make('technical_contract.en')
                                    ->label('Technical Contract & Cross-Stage Invariant (English)')
                                    ->rows(3),
                            ],
                            fieldKeys: ['name', 'function_guarantee', 'technical_contract']
                        ),
                    ]),

                Section::make('Artefak Kode & Ketergantungan Lintas Tahap (Cross-Stage Graph)')
                    ->schema([
                        TagsInput::make('artifacts_list')
                            ->label('Artefak Sistem / Tabel / File yang Dihasilkan')
                            ->placeholder('Tambah artefak lalu tekan Enter'),
                        TagsInput::make('related_stage_dependencies')
                            ->label('Pilar Hilir yang Terpengaruh (Cross-Stage Dependencies)')
                            ->placeholder('e.g. PILLAR_09, PILLAR_13, PILLAR_15'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('pillar_number', 'asc')
            ->columns([
                TextColumn::make('pillar_number')
                    ->label('#')
                    ->sortable()
                    ->badge()
                    ->color('emerald'),

                TextColumn::make('name')
                    ->label('Pilar Sistem (ID / EN)')
                    ->getStateUsing(fn (PosArchitecturePillar $record): string => $record->localizedName())
                    ->description(fn (PosArchitecturePillar $record): string => $record->code)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('function_guarantee')
                    ->label('Fungsi & Jaminan Utama')
                    ->getStateUsing(fn (PosArchitecturePillar $record): string => $record->localizedFunctionGuarantee())
                    ->limit(95)
                    ->wrap(),

                IconColumn::make('is_locked_freeze')
                    ->label('Freeze 100%')
                    ->boolean(),

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
            'index' => ListPosArchitecturePillars::route('/'),
            'create' => CreatePosArchitecturePillar::route('/create'),
            'edit' => EditPosArchitecturePillar::route('/{record}/edit'),
        ];
    }
}

