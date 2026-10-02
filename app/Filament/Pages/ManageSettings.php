<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use App\Models\CmsGlobalSetting;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Global Settings';
    protected static ?string $title = 'Global Settings';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.manage-settings';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com') {
            return false;
        }

        return $user->hasRole('super_admin') || $user->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = CmsGlobalSetting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General Setup')
                            ->icon('heroicon-m-adjustments-horizontal')
                            ->schema([
                                TextInput::make('company_whatsapp')
                                    ->label('Nomor WhatsApp Resmi (CS & Konsultasi Proyek)')
                                    ->helperText('Nomor WhatsApp resmi tanpa tanda + (contoh: 628123456789). Otomatis memperbarui tombol Chat WhatsApp di seluruh website.')
                                    ->default('628123456789')
                                    ->required(),

                                Select::make('app_timezone')
                                    ->label('Master Timezone (UTC Offset)')
                                    ->options([
                                        'UTC' => 'UTC (Standard)',
                                        'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat (GMT+7)',
                                        'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah (GMT+8)',
                                        'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur (GMT+9)',
                                    ])
                                    ->searchable()
                                    ->required()
                                    ->default('Asia/Jakarta'),
                                    
                                KeyValue::make('site_identity')
                                    ->label('Site Identity')
                                    ->keyLabel('Key (e.g. site_name, tagline)')
                                    ->valueLabel('Value'),
                                    
                                KeyValue::make('contact_info')
                                    ->label('Contact Information')
                                    ->keyLabel('Type (e.g. email, phone, address)')
                                    ->valueLabel('Value'),
                                    
                                KeyValue::make('social_links')
                                    ->label('Social Media Links')
                                    ->keyLabel('Platform (e.g. facebook, instagram)')
                                    ->valueLabel('URL'),
                            ]),

                        Tabs\Tab::make('Frontend Feature Flags')
                            ->icon('heroicon-m-bolt')
                            ->badge('Live Controls')
                            ->schema([
                                Section::make('Modul CV Pro Enterprise Studio')
                                    ->description('Aktifkan atau sembunyikan fitur dan tab di antarmuka publik CV Pro secara instan.')
                                    ->schema([
                                        Toggle::make('feature_enable_cv_pro')
                                            ->label('Aktifkan Modul CV Pro Studio')
                                            ->helperText('Jika dinonaktifkan, akses publik ke /cv-pro disembunyikan.')
                                            ->default(true),

                                        Toggle::make('feature_enable_cv_pricing')
                                            ->label('Tampilkan Paket Harga & Modal Top-Up Kuota')
                                            ->helperText('Menampilkan tombol "Upgrade / Top-Up" dan modal paket di CV Pro.')
                                            ->default(true),

                                        Toggle::make('feature_enable_cv_job_hub')
                                            ->label('Tampilkan Tab Job Hub (Kanban Board)')
                                            ->helperText('Pelacakan tahapan lamaran kerja (Wishlist, Applied, Interview, Offer).')
                                            ->default(true),

                                        Toggle::make('feature_enable_cv_keuangan')
                                            ->label('Tampilkan Tab Keuangan Pro')
                                            ->helperText('Kalkulator gaji bersih, budget persiapan karir, dan target tabungan.')
                                            ->default(true),

                                        Toggle::make('feature_enable_cv_mock_interview')
                                            ->label('Tampilkan Fitur Mock Interview AI')
                                            ->helperText('Simulasi wawancara kerja interaktif dengan rekaman suara dan evaluasi STAR.')
                                            ->default(true),

                                        Toggle::make('feature_enable_cv_linkedin_suite')
                                            ->label('Tampilkan Generator LinkedIn Personal Branding')
                                            ->helperText('Headline, About summary, dan konten postingan LinkedIn teroptimasi.')
                                            ->default(true),
                                    ])->columns(2),

                                Section::make('Modul Project OS & Digital Contracts')
                                    ->description('Kendali visibilitas modul arsitektur proyek dan onboarding klien.')
                                    ->schema([
                                        Toggle::make('feature_enable_vision_blueprint')
                                            ->label('Aktifkan Modul Vision Blueprint PRD')
                                            ->helperText('Akses publik kuesioner Project OS di /blueprint.')
                                            ->default(true),

                                        Toggle::make('feature_enable_client_onboarding')
                                            ->label('Aktifkan Form Lead Onboarding Klien')
                                            ->helperText('Form intake cepat onboarding klien di landing page.')
                                            ->default(true),

                                        Toggle::make('feature_enable_digital_contract')
                                            ->label('Aktifkan Modul Kontrak Digital & E-Sign')
                                            ->helperText('Menampilkan fitur surat kontrak kerja digital dan penandatanganan elektronik.')
                                            ->default(true),

                                        Toggle::make('feature_enable_ai_threat_shield')
                                            ->label('Aktifkan AI Threat Shield Protection')
                                            ->helperText('Proteksi serangan otonom AI, deteksi payload RCE, dan isolasi bot honeypot.')
                                            ->default(true),
                                    ])->columns(2),

                                Section::make('Mode Verifikasi Midtrans (Project OS Scope Freeze)')
                                    ->description('Mode isolasi khusus untuk membatasi sistem hanya pada modul Project OS saat inspeksi/audit Midtrans berlangsung.')
                                    ->schema([
                                        Toggle::make('midtrans_compliance_strict_mode')
                                            ->label('Aktifkan Strict Mode Audit Midtrans')
                                            ->helperText('Jika diaktifkan, modul non-Project OS (CV Pro, dsb) akan dibatasi sehingga tim inspeksi Midtrans hanya memverifikasi modul Project OS & PRD Generator.')
                                            ->default(false),
                                    ]),
                            ]),

                        Tabs\Tab::make('Navigation & Footer')
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->schema([
                                Builder::make('navigation_format')
                                    ->label('Main Navigation')
                                    ->blocks([
                                        Builder\Block::make('simple_nav')
                                            ->label('Simple Navigation')
                                            ->icon('heroicon-m-bars-3')
                                            ->schema([
                                                Repeater::make('links')->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('url')->required(),
                                                ])->columns(2)
                                            ]),
                                        Builder\Block::make('mega_menu_nav')
                                            ->label('Mega Menu Navigation')
                                            ->icon('heroicon-m-queue-list')
                                            ->schema([
                                                Repeater::make('menus')->schema([
                                                    TextInput::make('title')->required(),
                                                    Repeater::make('links')->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('url')->required(),
                                                    ])->columns(2)
                                                ])
                                            ]),
                                    ])
                                    ->maxItems(1),
                                    
                                Builder::make('footer_format')
                                    ->label('Footer Configuration')
                                    ->blocks([
                                        Builder\Block::make('simple_footer')
                                            ->label('Simple Footer')
                                            ->icon('heroicon-m-document-minus')
                                            ->schema([
                                                TextInput::make('copyright_text')->required(),
                                            ]),
                                        Builder\Block::make('multi_column_footer')
                                            ->label('Multi-Column Footer')
                                            ->icon('heroicon-m-view-columns')
                                            ->schema([
                                                TextInput::make('copyright_text')->required(),
                                                Repeater::make('columns')->schema([
                                                    TextInput::make('title')->required(),
                                                    Repeater::make('links')->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('url')->required(),
                                                    ])->columns(2)
                                                ])
                                            ]),
                                    ])
                                    ->maxItems(1),
                            ]),
                            
                        Tabs\Tab::make('SEO Schema Markup')
                            ->icon('heroicon-m-magnifying-glass')
                            ->schema([
                                Section::make('Organization / Local Business Data')
                                    ->description('These details will be injected as JSON-LD Schema to help Google understand your business entity.')
                                    ->schema([
                                        Select::make('seo_schema.organization.type')
                                            ->label('Business Entity Type')
                                            ->options([
                                                'Organization' => 'General Organization',
                                                'LocalBusiness' => 'Local Business',
                                                'Corporation' => 'Corporation',
                                            ])
                                            ->default('Organization')
                                            ->required(),
                                        TextInput::make('seo_schema.organization.name')
                                            ->label('Company / Organization Name')
                                            ->required(),
                                        TextInput::make('seo_schema.organization.logo')
                                            ->label('Logo URL')
                                            ->url()
                                            ->helperText('Absolute URL to your logo image.'),
                                        TextInput::make('seo_schema.organization.telephone')
                                            ->label('Official Telephone')
                                            ->tel(),
                                        TextInput::make('seo_schema.organization.email')
                                            ->label('Official Email')
                                            ->email(),
                                    ])->columns(2),
                                    
                                Section::make('Physical Address')
                                    ->schema([
                                        TextInput::make('seo_schema.address.streetAddress')
                                            ->label('Street Address')
                                            ->columnSpanFull(),
                                        TextInput::make('seo_schema.address.addressLocality')
                                            ->label('City / Locality'),
                                        TextInput::make('seo_schema.address.addressRegion')
                                            ->label('State / Province / Region'),
                                        TextInput::make('seo_schema.address.postalCode')
                                            ->label('Postal Code'),
                                        TextInput::make('seo_schema.address.addressCountry')
                                            ->label('Country (e.g. ID, US)')
                                            ->default('ID'),
                                    ])->columns(2),
                                    
                                Section::make('Social Profiles (sameAs)')
                                    ->description('Link your official social media profiles to build knowledge graph presence.')
                                    ->schema([
                                        Repeater::make('seo_schema.sameAs')
                                            ->label('Social Media URLs')
                                            ->schema([
                                                TextInput::make('url')->label('Profile URL')->url()->required(),
                                            ])
                                            ->defaultItems(1)
                                    ]),

                                Section::make('Kustom Schema.org JSON-LD (Ekstensi Fleksibel)')
                                    ->description('Injeksi skema terstruktur tambahan secara bebas untuk kebutuhan SEO lanjutan (FAQPage, SoftwareApplication, Event, dll).')
                                    ->schema([
                                        Textarea::make('seo_schema.custom_json_ld')
                                            ->label('Raw JSON-LD Object')
                                            ->rows(5)
                                            ->placeholder('{"@context": "https://schema.org", "@type": "SoftwareApplication", ...}')
                                            ->helperText('Format harus berupa JSON valid tanpa tag <script>. Seluruh entri di-cache permanen (Cache::rememberForever) dan otomatis direset saat disimpan.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full')
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        
        foreach ($data as $key => $value) {
            CmsGlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Flush schema & global cache forever immediately
        \Illuminate\Support\Facades\Cache::forget('app_timezone');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_organization');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_website');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_project_os');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_raw');
        \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
        \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');

        Notification::make()
            ->title('Pengaturan & Schema.org Berhasil Disimpan')
            ->body('Cache forever telah otomatis di-reset. Seluruh mesin pencari akan menerima struktur data JSON-LD teranyar.')
            ->success()
            ->send();
    }
}
