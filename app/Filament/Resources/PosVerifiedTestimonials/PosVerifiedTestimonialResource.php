<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosVerifiedTestimonials;

use App\Filament\Forms\Components\TranslatableTabs;
use App\Filament\Resources\PosVerifiedTestimonials\Pages\CreatePosVerifiedTestimonial;
use App\Filament\Resources\PosVerifiedTestimonials\Pages\EditPosVerifiedTestimonial;
use App\Filament\Resources\PosVerifiedTestimonials\Pages\ListPosVerifiedTestimonials;
use App\Models\PosVerifiedTestimonial;
use App\Support\InputHygieneStandard;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\ProjectOsMatrixCatalog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PosVerifiedTestimonialResource extends Resource
{
    protected static ?string $model = PosVerifiedTestimonial::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'Project OS & Engineering';

    protected static ?string $navigationLabel = 'Verified Aniwall & Case Studies';

    protected static ?string $modelLabel = 'Testimoni Arsitektur Terverifikasi';

    protected static ?string $pluralModelLabel = 'Verified Aniwall & Case Studies (v10.0)';

    protected static ?int $navigationSort = 14;

    protected static ?string $recordTitleAttribute = 'client_company';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->can('manage_verified_testimonials')
            || $user->hasAnyRole(['super_admin', 'developer']);
    }

    public static function form(Schema $schema): Schema
    {
        $sectorOptions = [];
        foreach (ProjectOsMatrixCatalog::sectors() as $sec) {
            $sectorOptions[$sec['code']] = ($sec['name']['id'] ?? $sec['code']).' ('.$sec['code'].')';
        }

        $publicUxOptions = [];
        foreach (PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets('id') as $code => $item) {
            $publicUxOptions[$code] = $code.' — '.($item['name'] ?? $code);
        }

        return $schema
            ->components([
                Section::make('Identitas Klien & Kontrak Verifikasi Kriptografi')
                    ->description('Testimoni terikat secara kriptografi ke BAST Digital Mitra atau kelulusan Fase 6 Retail Studio.')
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Nama PIC Klien')
                            ->required()
                            ->maxLength(160),

                        TextInput::make('client_company')
                            ->label('Nama Perusahaan / Entitas')
                            ->required()
                            ->maxLength(180),

                        TextInput::make('client_email')
                            ->label('Email Resmi PIC (Strict Email Hygiene)')
                            ->email()
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? InputHygieneStandard::sanitizeStrictEmail($state) : null)
                            ->maxLength(180),

                        Select::make('client_phone_country_code')
                            ->label('Kode Zona Negara (E.164)')
                            ->options(InputHygieneStandard::dialCodeSelectOptions())
                            ->searchable()
                            ->default('+62'),

                        TextInput::make('client_phone')
                            ->label('Nomor Telepon / WhatsApp (Tanpa Angka 0 di Awal)')
                            ->placeholder('81234567890')
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? InputHygieneStandard::sanitizePhoneLocalNumber($state) : null)
                            ->maxLength(24),

                        Select::make('verification_trigger')
                            ->label('Sumber Verifikasi Kriptografi')
                            ->options([
                                'MITRA_BAST_SIGNED' => 'MITRA_BAST_SIGNED (Digital BAST Signed)',
                                'RETAIL_PHASE6_VERIFIED' => 'RETAIL_PHASE6_VERIFIED (Phase 6 Verified)',
                                'ADMIN_VERIFIED_AUDIT' => 'ADMIN_VERIFIED_AUDIT (Audit Terverifikasi)',
                            ])
                            ->default('MITRA_BAST_SIGNED')
                            ->required(),

                        Select::make('sector_code')
                            ->label('Sektor Fungsional (12 Sektor)')
                            ->options($sectorOptions)
                            ->searchable()
                            ->required(),

                        Select::make('public_ux_preset')
                            ->label('Zone B Public Front-Stage UX Preset')
                            ->options($publicUxOptions)
                            ->required()
                            ->default('UX_F1_SCROLLYTELLING'),

                        TextInput::make('operational_ui_variant')
                            ->label('Zone A Operational UI Variant')
                            ->default('UI_01A_POS_SPLIT_TERMINAL')
                            ->required()
                            ->maxLength(80),

                        TextInput::make('stack_code')
                            ->label('Stack Topology Code')
                            ->default('STACK_M1_LARAVEL_MONOLITH')
                            ->maxLength(80),

                        TextInput::make('rating')
                            ->label('Rating (1–5)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(5),

                        Toggle::make('is_published')
                            ->label('Publikasikan di Aniwall & /explore')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Sematkan Utama (Featured)')
                            ->default(true),
                    ])->columns(3),

                Section::make('Narasi Dwibahasa Tier-1 (TranslatableTabs ID & EN — Zero LocaleSwitcher)')
                    ->description('Jabatan PIC, kutipan testimoni, dan ringkasan studi kasus dalam Bahasa Indonesia & Inggris.')
                    ->schema([
                        TranslatableTabs::make(
                            label: 'Narasi Testimoni & Studi Kasus Dwibahasa',
                            idSchema: [
                                TextInput::make('client_role.id')
                                    ->label('Jabatan PIC (Indonesia)')
                                    ->required()
                                    ->maxLength(160),
                                Textarea::make('quote.id')
                                    ->label('Kutipan Testimoni Terverifikasi (Indonesia)')
                                    ->required()
                                    ->rows(3),
                                Textarea::make('case_study_summary.id')
                                    ->label('Ringkasan Studi Kasus & Metrik (Indonesia)')
                                    ->rows(3),
                            ],
                            enSchema: [
                                TextInput::make('client_role.en')
                                    ->label('PIC Role / Title (English)')
                                    ->maxLength(160),
                                Textarea::make('quote.en')
                                    ->label('Verified Testimonial Quote (English)')
                                    ->rows(3),
                                Textarea::make('case_study_summary.en')
                                    ->label('Case Study & Telemetry Summary (English)')
                                    ->rows(3),
                            ],
                            fieldKeys: ['client_role', 'quote', 'case_study_summary']
                        ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('client_company')
                    ->label('Perusahaan & PIC')
                    ->description(fn (PosVerifiedTestimonial $record): string => $record->client_name.' · '.$record->localizedClientRole())
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sector_code')
                    ->label('Sektor & Dual-Zone UX')
                    ->description(fn (PosVerifiedTestimonial $record): string => ($record->operational_ui_variant ?: '-').' + '.($record->public_ux_preset ?: '-'))
                    ->badge()
                    ->color('emerald')
                    ->searchable(),

                TextColumn::make('verification_trigger')
                    ->label('Bukti Verifikasi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('quote')
                    ->label('Kutipan (ID/EN)')
                    ->getStateUsing(fn (PosVerifiedTestimonial $record): string => $record->localizedQuote())
                    ->limit(80)
                    ->wrap(),

                IconColumn::make('is_published')
                    ->label('Live')
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
            'index' => ListPosVerifiedTestimonials::route('/'),
            'create' => CreatePosVerifiedTestimonial::route('/create'),
            'edit' => EditPosVerifiedTestimonial::route('/{record}/edit'),
        ];
    }
}
