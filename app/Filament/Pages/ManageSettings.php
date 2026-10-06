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
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

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
        if (!empty($settings['developer_signature_image']) && str_starts_with($settings['developer_signature_image'], 'data:image')) {
            $settings['developer_signature_pad'] = $settings['developer_signature_image'];
        }
        $this->form->fill($settings);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Tabs::make('Settings')
                    ->id('global-settings-tabs')
                    ->persistTabInQueryString('tab')
                    ->scrollable()
                    ->tabs([
                        Tabs\Tab::make('General Setup')
                            ->key('general-setup')
                            ->id('general-setup')
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

                        Tabs\Tab::make('Legal & Developer Signature')
                            ->key('legal-developer-signature')
                            ->id('legal-developer-signature')
                            ->icon('heroicon-m-pencil-square')
                            ->badge('Pihak Kedua')
                            ->schema([
                                Section::make('Identitas Pihak Kedua (Pengembang Sistem / Studio)')
                                    ->description('Konfigurasi nama entitas, kontak, dan alamat resmi Neriah Pro yang dicantumkan pada kontrak hukum digital.')
                                    ->schema([
                                        TextInput::make('developer_entity_name')
                                            ->label('Nama Entitas Pihak Kedua')
                                            ->helperText('Nama resmi studio/agensi (cukup "Neriah Pro", belum berbadan hukum PT).')
                                            ->default('Neriah Pro')
                                            ->required(),
                                        TextInput::make('developer_support_email')
                                            ->label('Email Resmi Support & Kontrak')
                                            ->email()
                                            ->helperText('Email resmi untuk korespondensi kontrak dan dokumen hukum.')
                                            ->default('support@neriahpro.com')
                                            ->required(),
                                        TextInput::make('developer_phone')
                                            ->label('Nomor Telepon / WhatsApp Resmi')
                                            ->default('+628123456789'),
                                        TextInput::make('developer_location')
                                            ->label('Domisili / Wilayah Hukum')
                                            ->default('Jakarta, Indonesia'),
                                    ])->columns(2),

                                Section::make('Penanggung Jawab Teknis & Tanda Tangan Developer')
                                    ->description('Unggah gambar tanda tangan Anda selaku developer / arsitek sistem untuk dibubuhkan otomatis pada dokumen kontrak digital Pihak Kedua.')
                                    ->schema([
                                        TextInput::make('developer_pic_name')
                                            ->label('Nama Lengkap Penanggung Jawab (PIC)')
                                            ->helperText('Nama yang dicantumkan di bawah tanda tangan Pihak Kedua.')
                                            ->default('Yoseph Iriandi Tambunan')
                                            ->required(),
                                        TextInput::make('developer_pic_title')
                                            ->label('Jabatan / Peran PIC')
                                            ->default('Lead Software Architect & Tech Lead')
                                            ->required(),
                                        TextInput::make('developer_seal_text')
                                            ->label('Teks Segel Digital Korporat')
                                            ->default('NERIAH PRO VERIFIED ARCHITECT')
                                            ->required(),
                                        \App\Support\FilamentCuratorHelper::picker('developer_signature_image', 'signatures', 'Tanda Tangan Digital Developer (Opsi A: Upload Berkas / Curator)')
                                            ->helperText('Opsi A: Unggah berkas gambar tanda tangan Anda (format PNG transparan direkomendasikan). Otomatis tersimpan secara rapi di folder dangkal "storage/signatures".'),
                                        SignaturePad::make('developer_signature_pad')
                                            ->label('Tanda Tangan Digital Developer (Opsi B: Goreskan Langsung / Canvas Pad)')
                                            ->helperText('Opsi B: Goreskan tanda tangan Anda langsung di canvas menggunakan stylus pen, mouse, atau touchscreen jika tidak memiliki berkas gambar PNG.')
                                            ->dotSize(2.0)
                                            ->lineMinWidth(1.0)
                                            ->lineMaxWidth(2.5)
                                            ->penColor('blue')
                                            ->backgroundColor('rgba(255, 255, 255, 1)')
                                            ->clearable()
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Multi-AI Model Hub')
                            ->key('multi-ai-hub')
                            ->id('multi-ai-hub')
                            ->icon('heroicon-m-cpu-chip')
                            ->badge('Failover Engine')
                            ->schema([
                                Section::make('Orkestrasi AI & Kebijakan Failover Otomatis')
                                    ->description('Konfigurasikan model AI default untuk Project OS & PRD Generator. Jika model utama kehabisan token atau mengalami 429 rate-limit, sistem otomatis berpindah ke model alternatif yang masih sehat tanpa downtime.')
                                    ->schema([
                                        Select::make('ai_default_provider')
                                            ->label('AI Provider Utama (Default)')
                                            ->options(function () {
                                                $providers = [
                                                    'deepseek' => 'DeepSeek AI (DeepSeek-R1 SOTA Reasoning)',
                                                    'gemini' => 'Google Gemini (Gemini 2.5 Pro / Flash)',
                                                    'anthropic' => 'Anthropic Claude (Claude 3.7 Sonnet)',
                                                    'openai' => 'OpenAI ChatGPT (GPT-4o / o3-mini)',
                                                    'xai' => 'xAI Grok (Grok-2)',
                                                    'groq' => 'Groq LPU (Llama 3.3 70B)',
                                                    'openrouter' => 'OpenRouter Universal Hub',
                                                ];

                                                $options = [
                                                    'auto' => '⚡ Auto Failover (Otomatis beralih ke provider aktif yang sehat)',
                                                ];

                                                foreach ($providers as $key => $label) {
                                                    $hasKey = !empty(\App\Services\Ai\MultiAiModelManager::getApiKey($key));
                                                    $options[$key] = $hasKey ? "{$label} [✅ AKTIF]" : "{$label} [⚪ BELUM ADA KEY]";
                                                }

                                                return $options;
                                            })
                                            ->default('auto')
                                            ->required(),
                                        Toggle::make('ai_enable_auto_failover')
                                            ->label('Aktifkan Graceful Auto-Failover')
                                            ->helperText('Otomatis mengalihkan ke model AI cadangan jika model aktif kehabisan token atau limit harian tercapai.')
                                            ->default(true),
                                    ])->columns(2),

                                Section::make('Manajemen Kredensial API Keys Penyedia AI')
                                    ->description('Kunci API yang Anda simpan di sini akan memprioritaskan konfigurasi di atas file .env dan langsung aktif tanpa restart server.')
                                    ->schema([
                                        TextInput::make('ai_deepseek_api_key')
                                            ->label('DeepSeek API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di platform.deepseek.com. Sangat hemat dan powerful untuk penalaran PRD.'),
                                        TextInput::make('ai_gemini_api_key')
                                            ->label('Google Gemini API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di aistudio.google.com. Tersedia Free Tier kuota gratis.'),
                                        TextInput::make('ai_anthropic_api_key')
                                            ->label('Anthropic Claude API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di console.anthropic.com untuk Claude 3.7 Sonnet.'),
                                        TextInput::make('ai_openai_api_key')
                                            ->label('OpenAI API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di platform.openai.com untuk ChatGPT GPT-4o / o3-mini.'),
                                        TextInput::make('ai_grok_api_key')
                                            ->label('xAI Grok API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di console.x.ai untuk Grok-2.'),
                                        TextInput::make('ai_groq_api_key')
                                            ->label('Groq API Key (Model Gratis & Cepat)')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di console.groq.com. Inferensi Llama 3.3 70B gratis & super cepat.'),
                                        TextInput::make('ai_openrouter_api_key')
                                            ->label('OpenRouter API Key')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Dapatkan di openrouter.ai untuk akses ratusan model open-source gratis.'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Multi-Language (2-Tier Locale)')
                            ->key('multi-language')
                            ->id('multi-language')
                            ->icon('heroicon-m-language')
                            ->badge('Tier 1 & Tier 2')
                            ->schema([
                                Section::make('Tier 1: Native Dual-Locale (Backend & Frontend)')
                                    ->description('Standar bahasa presisi tinggi yang didukung secara native pada database JSON ({"id": "...", "en": "..."}) dan antarmuka.')
                                    ->schema([
                                        Select::make('default_frontend_locale')
                                            ->label('Bahasa Default Frontend (Tier 1)')
                                            ->options([
                                                'id' => '🇮🇩 Bahasa Indonesia (ID) - Standar Default',
                                                'en' => '🇬🇧 English (EN) - Global Default',
                                            ])
                                            ->default('id')
                                            ->required(),
                                    ]),

                                Section::make('Tier 2: Global Google Translate Plugin (Whitelist Bahasa)')
                                    ->description('Sistem penerjemah global multi-bahasa otomatis di frontend. Hanya bahasa yang Anda pilih dalam whitelist di bawah yang akan tampil di pilihan pengguna.')
                                    ->schema([
                                        Toggle::make('google_translate_enabled')
                                            ->label('Aktifkan Google Translate (Tier 2 Global Locale)')
                                            ->helperText('Jika diaktifkan, tombol pemilih bahasa global Tier 2 akan muncul di header navigasi frontend.')
                                            ->default(true),

                                        Select::make('google_translate_allowed_languages')
                                            ->label('Daftar Bahasa Tier 2 yang Diizinkan (Whitelist)')
                                            ->helperText('Pilih negara/bahasa resmi yang diizinkan tampil di frontend untuk menjaga UI/UX tetap rapi dan performa cepat.')
                                            ->multiple()
                                            ->options([
                                                'en' => '🇬🇧 English (EN)',
                                                'id' => '🇮🇩 Bahasa Indonesia (ID)',
                                                'ja' => '🇯🇵 Japanese (日本語)',
                                                'zh-CN' => '🇨🇳 Chinese Simplified (简体中文)',
                                                'ar' => '🇸🇦 Arabic (العربية)',
                                                'de' => '🇩🇪 German (Deutsch)',
                                                'fr' => '🇫🇷 French (Français)',
                                                'es' => '🇪🇸 Spanish (Español)',
                                                'ko' => '🇰🇷 Korean (한국어)',
                                                'ru' => '🇷🇺 Russian (Русский)',
                                                'pt' => '🇵🇹 Portuguese (Português)',
                                                'nl' => '🇳🇱 Dutch (Nederlands)',
                                                'vi' => '🇻🇳 Vietnamese (Tiếng Việt)',
                                                'th' => '🇹🇭 Thai (ไทย)',
                                            ])
                                            ->default(['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'])
                                            ->required(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Frontend Feature Flags')
                            ->key('feature-flags')
                            ->id('feature-flags')
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
                            ->key('navigation-footer')
                            ->id('navigation-footer')
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
                            ->key('seo-schema')
                            ->id('seo-schema')
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

                        Tabs\Tab::make('Project OS & Pricing Strategy')
                            ->key('pricing-strategy')
                            ->id('pricing-strategy')
                            ->icon('heroicon-m-currency-dollar')
                            ->badge('Layanan & CRM')
                            ->schema([
                                Section::make('Konfigurasi Tarif & Pilihan Paket Proyek')
                                    ->description('Atur harga resmi layanan arsitektur perangkat lunak dan promosi aktif yang tampil di halaman publik /pricing.')
                                    ->schema([
                                        TextInput::make('pricing_advisory_price')
                                            ->label('Tarif Jasa Advisory & Blueprint (Rp)')
                                            ->helperText('Biaya one-time perancangan PRD 26 parameter, skema DDL PostgreSQL Strict ULID, dan WBS 5 sprint.')
                                            ->default('2.500.000')
                                            ->required(),
                                        TextInput::make('pricing_mvp_price')
                                            ->label('Tarif Kontrak Full MVP Monolith (Rp)')
                                            ->helperText('Biaya pengembangan penuh enterprise modern monolith (DP 50% = Rp 25.000.000).')
                                            ->default('50.000.000')
                                            ->required(),
                                        TextInput::make('pricing_umkm_price')
                                            ->label('Tarif Paket UMKM Digital Starter (Rp)')
                                            ->helperText('Biaya dasar paket digitalisasi UMKM sebelum dipotong kuota voucher subsidi.')
                                            ->default('7.500.000')
                                            ->required(),
                                        TextInput::make('pricing_active_promo_banner')
                                            ->label('Teks Pengumuman Banner Promosi & Voucher')
                                            ->helperText('Pesan promosi yang tampil di bagian atas halaman paket & harga.')
                                            ->default('Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!')
                                            ->columnSpanFull(),
                                    ])->columns(3),

                                Section::make('Strategi Penjualan & World-Class CRM Lead Intake')
                                    ->description('Pengaturan otomatisasi follow-up lead dan routing konsultasi teknis.')
                                    ->schema([
                                        TextInput::make('pricing_sales_pic_email')
                                            ->label('Email Notifikasi Penjualan & Lead Intake')
                                            ->email()
                                            ->default('sales@neriahpro.com'),
                                        TextInput::make('pricing_consultation_sla')
                                            ->label('Komitmen SLA Respon Tim Teknis')
                                            ->default('Maksimal 2 Jam Kerja'),
                                        Toggle::make('pricing_enable_instant_whatsapp')
                                            ->label('Buka WhatsApp Otomatis Setelah Submit')
                                            ->helperText('Mengarahkan calon klien langsung ke chat WhatsApp resmi dengan teks template terisi otomatis.')
                                            ->default(true),
                                        Textarea::make('pricing_whatsapp_template')
                                            ->label('Draft Pesan Awal WhatsApp Klien')
                                            ->rows(3)
                                            ->default('Halo Lead Architect Neriah Pro, saya tertarik memesan paket layanan arsitektur dan ingin mendiskusikan kebutuhan sistem kami...')
                                            ->columnSpanFull(),
                                    ])->columns(3),
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

        if (!empty($data['developer_signature_pad'])) {
            $data['developer_signature_image'] = $data['developer_signature_pad'];
        }
        unset($data['developer_signature_pad']);
        
        foreach ($data as $key => $value) {
            $safeValue = $value !== null ? $value : '';
            CmsGlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $safeValue]
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
        \Illuminate\Support\Facades\Cache::forget('google_translate_settings');
        \Illuminate\Support\Facades\Cache::forget('frontend_locale_settings');
        \Illuminate\Support\Facades\Cache::forget('developer_signature_settings');
        \Illuminate\Support\Facades\Cache::forget('cms_contract_developer_info');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_pricing_services');
        \Illuminate\Support\Facades\Cache::forget('seo_schema_pricing_faq');
        \Illuminate\Support\Facades\Cache::forget('cms_page_data_pricing');
        \Illuminate\Support\Facades\Cache::forget('cms_page_pricing');

        Notification::make()
            ->title('Pengaturan & Schema.org Berhasil Disimpan')
            ->body('Cache forever telah otomatis di-reset. Seluruh mesin pencari akan menerima struktur data JSON-LD teranyar.')
            ->success()
            ->send();
    }
}
