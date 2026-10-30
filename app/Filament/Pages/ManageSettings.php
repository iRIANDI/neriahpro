<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\Placeholder;
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
use App\Support\CountryZoneHelper;
use Filament\Schemas\Components\FusedGroup;
use Filament\Support\RawJs;

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

        if ($user->hasRole('midtrans_reviewer')) {
            return false;
        }

        return $user->hasRole('super_admin') || $user->can('manage_settings');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $dbSettings = CmsGlobalSetting::all()->pluck('value', 'key')->toArray();

        $defaults = [
            'company_whatsapp' => '6281380448253',
            'company_whatsapp_template_id' => 'Halo Yoseph Iriandi, saya ingin konsultasi terkait layanan pengembangan software dan arsitektur digital di Neriah Pro.',
            'company_whatsapp_template_en' => 'Hello Yoseph Iriandi, I would like to inquire about software development and digital architecture services at Neriah Pro.',
            'company_owner_name' => 'Yoseph Iriandi',
            'company_owner_title' => 'Independent Full-Stack Developer & Software Architect',
            'company_address' => 'Gianyar, Bali, Indonesia',
            'business_profile_mode' => 'individual',
            'service_agreement_title' => 'Developer Service Agreement (Kesepakatan Layanan Pengembang)',
            'app_timezone' => 'Asia/Jakarta',
            'developer_entity_name' => 'Neriah Pro (Yoseph Iriandi)',
            'developer_support_email' => 'support@neriahpro.com',
            'developer_phone' => '+6281380448253',
            'developer_location' => 'Gianyar, Bali, Indonesia',
            'developer_pic_name' => 'Yoseph Iriandi',
            'developer_pic_title' => 'Independent Full-Stack Developer & Software Architect',
            'developer_seal_text' => 'NERIAH PRO INDEPENDENT DEVELOPER',
            'midtrans_terms_mandatory' => true,
            'midtrans_terms_checkbox_label_id' => 'Saya telah membaca dan menyetujui Kesepakatan Layanan Developer, Kebijakan Pengembalian Dana, Penanganan Pembayaran Ganda, dan Ketentuan Pembatalan Neriah Pro.',
            'midtrans_terms_checkbox_label_en' => 'I have read and agree to the Developer Service Agreement, Refund Policy, Double-Payment Protection, and Project Cancellation Terms of Neriah Pro.',
            'midtrans_terms_content_id' => "1. Lisensi & Hak Pakai: Setiap blueprint dan kode sumber yang telah dilunasi diserahkan hak gunanya secara penuh kepada klien.\n2. Batasan Bantuan Revisi: Bantuan revisi dibatasi sesuai tier yang dipilih (Spark 2x/bln, Lite 30 hari, Pro 6 bulan, Ultimate 1 tahun).\n3. Penguncian Lingkup Kerja: Setelah uang muka (DP) 50% atau pelunasan terkonfirmasi, ruang lingkup pengerjaan disepakati bersama untuk menjamin ketepatan waktu deliverable.",
            'midtrans_terms_content_en' => "1. License & Deliverables: Blueprints and source code settled in full are 100% transferred to the client.\n2. Revision Window: Revision assistance is scoped by tier (Spark 2x/mo, Lite 30 days, Pro 6 months, Ultimate 1 year).\n3. Scope Agreement: Once 50% DP or full settlement is confirmed, project scope is agreed upon to ensure milestone completion.",
            'midtrans_refund_policy_id' => "1. Garansi Bug 30 Hari: Paket Pengerjaan Turnkey Studio dilindungi 30 Hari Garansi Bug pasca serah terima.\n2. Jaminan Refund 100%: Pengembalian dana penuh 100% berlaku jika terjadi kendala teknis fatal dari pihak Developer sebelum pengerjaan dimulai.\n3. Kebijakan Produk Digital: Karena produk digital dan blueprint arsitektur bersifat langsung diakses seketika, pembayaran yang telah diselesaikan setelah berkas diunduh tidak dapat dibatalkan sepihak. Pengembalian dana yang disetujui diproses dalam 7–14 hari kerja.",
            'midtrans_refund_policy_en' => "1. 30-Day Bug Warranty: Turnkey Studio packages include 30-day bug fix warranty after handover.\n2. 100% Refund Guarantee: Full 100% refund applies if critical technical impediment occurs on Developer side prior to work start.\n3. Digital Deliverable Policy: Because digital blueprints are accessed immediately upon payment, fees paid after download are non-refundable for unilateral cancellation. Approved refunds are credited back in 7-14 business days.",
            'midtrans_double_payment_policy_id' => "1. Deteksi Idempotency: Sistem Neriah Pro mendeteksi setiap transaksi menggunakan ID pesanan unik untuk mencegah duplikasi.\n2. Reversal Otomatis: Jika pelanggan tidak sengaja melakukan transfer ganda melalui gateway bank, sistem otomatis mencatat transaksi berlebih.\n3. Pengembalian Dana: Kelebihan pembayaran akan diverifikasi dan dikembalikan ke rekening asal dalam 3 - 5 hari kerja tanpa potongan biaya sistem.",
            'midtrans_double_payment_policy_en' => "1. Idempotency Detection: Neriah Pro utilizes unique order IDs to prevent duplicate transaction charges.\n2. Automated Reversal: If duplicate transfers occur due to bank network retries, the event is trapped and audited.\n3. Refund Timeline: Excess payments are verified and reimbursed to the source account within 3 - 5 business days with zero deduction.",
            'midtrans_cancellation_policy_id' => "1. Sebelum Kickoff / DP: Pembatalan pesanan dapat dilakukan kapan saja tanpa penalti biaya.\n2. Pasca Pembayaran DP 50%: Jika klien membatalkan proyek secara sepihak saat pengerjaan telah berlangsung, DP yang telah dibayarkan dialokasikan untuk kompensasi jam kerja developer, dan seluruh berkas yang telah dikerjakan tetap diserahkan kepada klien.",
            'midtrans_cancellation_policy_en' => "1. Prior to Kickoff / DP: Orders can be cancelled anytime with zero penalty.\n2. Post-DP 50% Kickoff: If the client cancels unilaterally while development is active, the DP is allocated toward incurred developer hours, while all produced blueprints and source code remain delivered to the client.",
            'ai_default_provider' => 'relayrouter',
            'ai_relayrouter_api_key' => config('ai.providers.relayrouter.api_key', 'sk-Uz4TzEyHYy5PSFvXvgi0guhlrauD6iWofllyoYwFDg1JxlzE'),
            'ai_relayrouter_base_url' => config('ai.providers.relayrouter.base_url', 'https://api.relayrouter.ai/v1'),
            'ai_relayrouter_discovery_model' => config('ai.providers.relayrouter.models.discovery', 'gpt-4o-mini'),
            'ai_relayrouter_prd_model' => config('ai.providers.relayrouter.models.prd', 'claude-3-7-sonnet-20250219'),
            'default_frontend_locale' => 'id',
            'google_translate_enabled' => true,
            'google_translate_allowed_languages' => ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'],
            'feature_enable_cv_pro' => true,
            'feature_enable_cv_pricing' => true,
            'feature_enable_cv_job_hub' => true,
            'feature_enable_cv_keuangan' => true,
            'feature_enable_cv_mock_interview' => true,
            'feature_enable_cv_linkedin_suite' => true,
            'site_tagline_id' => 'Layanan Pengembangan Software & Arsitektur Sistem Digital Profesional',
            'site_tagline_en' => 'Professional Software Development & Digital Architecture Services',
            'seo_meta_title_id' => 'neriahpro.com - Independent Developer & Digital Services',
            'seo_meta_title_en' => 'neriahpro.com - Independent Developer & Digital Services',
            'seo_meta_description_id' => 'Layanan rekayasa perangkat lunak mandiri oleh Yoseph Iriandi, cetak biru PRD sistem digital, dan kesepakatan layanan pengembang resmi.',
            'seo_meta_description_en' => 'Independent software engineering services by Yoseph Iriandi, digital system PRD blueprints, and professional developer service agreements.',
            'seo_schema' => [
                'organization' => [
                    'type' => 'Organization',
                    'name' => 'Neriah Pro',
                    'legal_name' => 'Neriah Pro (Yoseph Iriandi)',
                    'url' => 'https://neriahpro.com',
                    'logo' => 'https://neriahpro.com/favicon.ico',
                    'email' => 'support@neriahpro.com',
                    'telephone' => '+6281380448253',
                    'address' => [
                        'street' => 'Gianyar, Bali',
                        'city' => 'Gianyar',
                        'region' => 'Bali',
                        'postal_code' => '80511',
                        'country' => 'ID',
                    ],
                ],
                'website' => [
                    'name' => 'Neriah Pro',
                    'url' => 'https://neriahpro.com',
                ],
            ],
            'pricing_advisory_price' => '2.500.000',
            'pricing_mvp_price' => 'Custom Quotation',
            'pricing_umkm_price' => '7.500.000',
            'pricing_active_promo_banner' => 'Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!',
            'pricing_retail_spark_price' => '0',
            'pricing_retail_lite_price' => '99.000',
            'pricing_retail_pro_price' => '399.000',
            'pricing_retail_ultimate_price' => '1.490.000',
            'pricing_retail_spark_limit' => '2x Audit Ide / Bulan (Reset tiap tanggal 1)',
            'pricing_retail_lite_limit' => '1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)',
            'pricing_retail_pro_limit' => '1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)',
            'pricing_retail_ultimate_limit' => '1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call 60 Menit)',
            'pricing_retail_login_policy' => 'Guest Mode untuk Spark (Free). Wajib Login / Daftar Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.',
            'pricing_retail_disclaimer' => 'Paket Instant Architectural Blueprint 100% Self-Service: Dihasilkan instan oleh AI Project OS untuk Anda atau tim developer Anda kerjakan sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro.',
            'pricing_sales_pic_email' => 'sales@neriahpro.com',
            'pricing_consultation_sla' => 'Maksimal 2 Jam Kerja',
            'pricing_enable_instant_whatsapp' => true,
            'pricing_whatsapp_template' => 'Halo Lead Architect Neriah Pro, saya tertarik memesan paket layanan arsitektur dan ingin mendiskusikan kebutuhan sistem kami...',
        ];

        // Filter out null or empty strings from database so defaults prevail
        $filteredDb = [];
        foreach ($dbSettings as $k => $v) {
            if ($v !== null && $v !== '') {
                $filteredDb[$k] = $v;
            }
        }

        $settings = array_replace_recursive($defaults, $filteredDb);

        // Defensive normalization for scalar & array settings
        if (isset($settings['default_frontend_locale']) && is_array($settings['default_frontend_locale'])) {
            $settings['default_frontend_locale'] = reset($settings['default_frontend_locale']) ?: 'id';
        }
        if (isset($settings['app_timezone']) && is_array($settings['app_timezone'])) {
            $settings['app_timezone'] = reset($settings['app_timezone']) ?: 'Asia/Jakarta';
        }
        if (isset($settings['google_translate_allowed_languages']) && is_string($settings['google_translate_allowed_languages'])) {
            $decoded = json_decode($settings['google_translate_allowed_languages'], true);
            $settings['google_translate_allowed_languages'] = is_array($decoded) ? $decoded : [$settings['google_translate_allowed_languages']];
        }

        if (!empty($settings['developer_signature_image']) && is_string($settings['developer_signature_image']) && str_starts_with($settings['developer_signature_image'], 'data:image')) {
            $rawBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $settings['developer_signature_image']);
            $decoded = base64_decode($rawBase64);
            if ($decoded !== false) {
                $filename = 'signatures/developer_sig_migrated.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);
                $settings['developer_signature_image'] = $filename;
                CmsGlobalSetting::updateOrCreate(['key' => 'developer_signature_image'], ['value' => $filename]);
            }
        }

        $waParsed = CountryZoneHelper::splitPhone($settings['company_whatsapp'] ?? '6281380448253');
        $settings['company_whatsapp_country'] = $settings['company_whatsapp_country'] ?? $waParsed['iso'];
        $settings['company_whatsapp_number'] = $settings['company_whatsapp_number'] ?? $waParsed['local_number'];
        $settings['company_whatsapp'] = $waParsed['full_e164'];

        $devParsed = CountryZoneHelper::splitPhone($settings['developer_phone'] ?? '+6281380448253');
        $settings['developer_phone_country'] = $settings['developer_phone_country'] ?? $devParsed['iso'];
        $settings['developer_phone_number'] = $settings['developer_phone_number'] ?? $devParsed['local_number'];
        $settings['developer_phone'] = $devParsed['formatted'];

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
                                FusedGroup::make([
                                    Select::make('company_whatsapp_country')
                                        ->options(CountryZoneHelper::getSelectOptions())
                                        ->searchable()
                                        ->placeholder('Pilih Negara...')
                                        ->hiddenLabel()
                                        ->live()
                                        ->default('ID')
                                        ->columnSpan([
                                            'default' => 12,
                                            'md' => 5,
                                        ]),
                                    TextInput::make('company_whatsapp_number')
                                        ->placeholder('81380448253')
                                        ->prefix(fn ($get) => CountryZoneHelper::getDialCode($get('company_whatsapp_country') ?? 'ID'))
                                        ->hiddenLabel()
                                        ->required()
                                        ->live(onBlur: true)
                                        ->columnSpan([
                                            'default' => 12,
                                            'md' => 7,
                                        ]),
                                ])
                                    ->columns(12)
                                    ->label('Nomor WhatsApp Resmi (CS & Konsultasi Proyek)')
                                    ->markAsRequired()
                                    ->helperText(function ($get) {
                                        $country = $get('company_whatsapp_country') ?? 'ID';
                                        $number = $get('company_whatsapp_number') ?? '';
                                        $cleanNum = CountryZoneHelper::cleanLocalNumber($number);
                                        $dial = CountryZoneHelper::getDialCode($country);
                                        $cleanDial = preg_replace('/\D/', '', $dial);
                                        $e164 = $cleanDial . $cleanNum;

                                        $badge = !empty($cleanNum)
                                            ? '<span class="inline-flex items-center px-1.5 py-0.5 text-xs font-mono font-bold bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 ml-1">E.164: +' . e($e164) . '</span>'
                                            : '<span class="inline-flex items-center px-1.5 py-0.5 text-xs font-mono text-zinc-500 dark:text-zinc-400 ml-1">(Nomor belum diisi)</span>';

                                        return new \Illuminate\Support\HtmlString(
                                            'Pilih zona negara (240+ negara) dan masukkan nomor lokal tanpa awalan 0. Format E.164 internasional otomatis tersinkron: ' . $badge
                                        );
                                    }),

                                TextInput::make('company_whatsapp_template_id')
                                    ->label('Template Chat WhatsApp Otomatis (Bahasa Indonesia)')
                                    ->helperText('Teks otomatis saat calon klien mengklik nomor kontak atau tombol WhatsApp di mode bahasa Indonesia.')
                                    ->default('Halo Yoseph Iriandi, saya ingin konsultasi terkait layanan pengembangan software dan arsitektur digital di Neriah Pro.')
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('company_whatsapp_template_en')
                                    ->label('Template Chat WhatsApp Otomatis (English)')
                                    ->helperText('Auto pre-filled message when visitors click the contact number or WhatsApp button on English mode.')
                                    ->default('Hello Yoseph Iriandi, I would like to inquire about software development and digital architecture services at Neriah Pro.')
                                    ->columnSpanFull()
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
                                Section::make('Profil Usaha & Kepatuhan Payment Gateway (Jalur Akun KTP / Perorangan)')
                                    ->description('Konfigurasi identitas pemilik, domisili fisik, dan peran profesional untuk menjamin kepatuhan pendaftaran akun perorangan (KTP) di Midtrans & Bank Acquirer BNI.')
                                    ->schema([
                                        Select::make('business_profile_mode')
                                            ->label('Mode Profil Usaha')
                                            ->options([
                                                'individual' => 'Usaha Perorangan (Independent Developer / Solo Studio - Jalur KTP)',
                                                'corporate' => 'Badan Usaha (PT / CV / Corporate Studio)',
                                            ])
                                            ->helperText('Pilih "Usaha Perorangan" agar copywriting website selaras dengan verifikasi KTP dan tidak ditolak regulasi perizinan PT/CV.')
                                            ->default('individual')
                                            ->required(),
                                        TextInput::make('company_owner_name')
                                            ->label('Nama Lengkap Pemilik (Sesuai KTP)')
                                            ->helperText('Nama individu yang didaftarkan ke Midtrans. Wajib sama persis dengan KTP yang diunggah.')
                                            ->default('Yoseph Iriandi')
                                            ->required(),
                                        TextInput::make('company_owner_title')
                                            ->label('Titel Profesional / Posisi')
                                            ->helperText('Contoh: Independent Full-Stack Developer & Software Architect')
                                            ->default('Independent Full-Stack Developer & Software Architect')
                                            ->required(),
                                        TextInput::make('company_address')
                                            ->label('Alamat Domisili Fisik Lengkap (Sesuai KTP / Usaha)')
                                            ->helperText('Alamat fisik yang ditampilkan di footer website dan faktur komersial. Wajib sama dengan alamat domisili di dashboard Midtrans.')
                                            ->default('Gianyar, Bali, Indonesia')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('developer_entity_name')
                                            ->label('Nama Brand Layanan')
                                            ->helperText('Nama brand layanan (misal "Neriah Pro (Yoseph Iriandi)").')
                                            ->default('Neriah Pro (Yoseph Iriandi)')
                                            ->required(),
                                        TextInput::make('developer_support_email')
                                            ->label('Email Dukungan Resmi')
                                            ->email()
                                            ->helperText('Email resmi untuk korespondensi kontrak dan dokumen pembayaran.')
                                            ->default('support@neriahpro.com')
                                            ->required(),
                                        FusedGroup::make([
                                            Select::make('developer_phone_country')
                                                ->options(CountryZoneHelper::getSelectOptions())
                                                ->searchable()
                                                ->placeholder('Pilih Negara...')
                                                ->hiddenLabel()
                                                ->live()
                                                ->default('ID')
                                                ->columnSpan([
                                                    'default' => 12,
                                                    'md' => 5,
                                                ]),
                                            TextInput::make('developer_phone_number')
                                                ->placeholder('81380448253')
                                                ->prefix(fn ($get) => CountryZoneHelper::getDialCode($get('developer_phone_country') ?? 'ID'))
                                                ->hiddenLabel()
                                                ->live(onBlur: true)
                                                ->columnSpan([
                                                    'default' => 12,
                                                    'md' => 7,
                                                ]),
                                        ])
                                            ->columns(12)
                                            ->columnSpan(2)
                                            ->label('Nomor Telepon / WhatsApp Resmi Developer')
                                            ->helperText(function ($get) {
                                                $country = $get('developer_phone_country') ?? 'ID';
                                                $number = $get('developer_phone_number') ?? '';
                                                $cleanNum = CountryZoneHelper::cleanLocalNumber($number);
                                                $dial = CountryZoneHelper::getDialCode($country);
                                                $formatted = $dial . $cleanNum;

                                                $badge = !empty($cleanNum)
                                                    ? '<span class="inline-flex items-center px-1.5 py-0.5 text-xs font-mono font-bold bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 ml-1">E.164: ' . e($formatted) . '</span>'
                                                    : '<span class="inline-flex items-center px-1.5 py-0.5 text-xs font-mono text-zinc-500 dark:text-zinc-400 ml-1">(Nomor belum diisi)</span>';

                                                return new \Illuminate\Support\HtmlString(
                                                    'Nomor telepon terformat E.164 resmi untuk dicantumkan pada SPK Kontrak Digital: ' . $badge
                                                );
                                            }),
                                        TextInput::make('developer_location')
                                            ->label('Domisili / Wilayah Hukum')
                                            ->default('Gianyar, Bali, Indonesia'),
                                        TextInput::make('service_agreement_title')
                                            ->label('Judul Dokumen Perjanjian Layanan')
                                            ->helperText('Gunakan "Developer Service Agreement" atau "Kesepakatan Layanan Pengembang" untuk akun perorangan.')
                                            ->default('Developer Service Agreement (Kesepakatan Layanan Pengembang)')
                                            ->required(),
                                    ])->columns(2),

                                Section::make('Penanggung Jawab Teknis & Tanda Tangan Developer')
                                    ->description('Unggah gambar tanda tangan Anda atau goreskan langsung pada canvas pad modal untuk dibubuhkan otomatis pada dokumen kontrak digital Pihak Kedua.')
                                    ->schema([
                                        TextInput::make('developer_pic_name')
                                            ->label('Nama Lengkap Penanggung Jawab (PIC)')
                                            ->helperText('Nama yang dicantumkan di bawah tanda tangan Pihak Kedua.')
                                            ->default('Yoseph Iriandi')
                                            ->required(),
                                        TextInput::make('developer_pic_title')
                                            ->label('Jabatan / Peran PIC')
                                            ->default('Independent Full-Stack Developer & Software Architect')
                                            ->required(),
                                        TextInput::make('developer_seal_text')
                                            ->label('Teks Segel Digital Korporat')
                                            ->default('NERIAH PRO INDEPENDENT DEVELOPER')
                                            ->required()
                                            ->columnSpanFull(),
                                        \App\Support\FilamentCuratorHelper::picker('developer_signature_image', 'signatures', 'Tanda Tangan Digital Developer (Opsi A: Upload Berkas / Curator)')
                                            ->helperText('Opsi A: Unggah berkas gambar tanda tangan Anda (format PNG transparan direkomendasikan). Otomatis tersimpan secara rapi di folder dangkal "storage/signatures".')
                                            ->columnSpan(1),
                                        Placeholder::make('developer_signature_preview')
                                            ->label('Pratinjau Tanda Tangan Aktif')
                                            ->content(function ($get) {
                                                $sig = $get('developer_signature_image');
                                                if (blank($sig)) {
                                                    return new \Illuminate\Support\HtmlString('<div class="p-3 border-2 border-dashed border-zinc-200 dark:border-zinc-700 text-center rounded-sm text-xs text-zinc-400 italic">Belum ada tanda tangan. Unggah berkas PNG di sebelah kiri atau gunakan tombol Goreskan Tanda Tangan di bawah.</div>');
                                                }
                                                $src = str_starts_with($sig, 'data:image') ? e($sig) : (str_starts_with($sig, 'http') ? e($sig) : ('/storage/' . ltrim(e($sig), '/')));
                                                return new \Illuminate\Support\HtmlString('<div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-sm inline-block"><img src="' . $src . '" alt="Tanda Tangan Developer" class="max-h-24 max-w-full object-contain" /></div>');
                                            })
                                            ->columnSpan(1),
                                        Actions::make([
                                            $this->getDeveloperSignaturePadAction(),
                                        ])->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Kepatuhan Midtrans, Syarat & Ketentuan, serta Kebijakan Refund & Double-Payment')
                                    ->description('Konfigurasi teks persetujuan checkout pembayaran Midtrans Snap, jaminan garansi bug, penanganan double payment, dan klausul pembatalan dalam 2 bahasa (Tier 1: ID & EN).')
                                    ->schema([
                                        Toggle::make('midtrans_terms_mandatory')
                                            ->label('Wajibkan Centang Syarat & Ketentuan di Modal Pembayaran')
                                            ->helperText('Jika aktif, tombol bayar Midtrans Snap terkunci hingga pelanggan mencentang persetujuan.')
                                            ->default(true),
                                        TextInput::make('midtrans_terms_checkbox_label_id')
                                            ->label('Label Checkbox Persetujuan (Indonesia)')
                                            ->default('Saya telah membaca dan menyetujui Kesepakatan Layanan Developer, Kebijakan Pengembalian Dana, Penanganan Pembayaran Ganda, dan Ketentuan Pembatalan Neriah Pro.')
                                            ->required(),
                                        TextInput::make('midtrans_terms_checkbox_label_en')
                                            ->label('Label Checkbox Persetujuan (English)')
                                            ->default('I have read and agree to the Developer Service Agreement, Refund Policy, Double-Payment Protection, and Project Cancellation Terms of Neriah Pro.')
                                            ->required(),
                                        Textarea::make('midtrans_terms_content_id')
                                            ->label('Syarat & Ketentuan Layanan (Indonesia)')
                                            ->rows(4)
                                            ->default("1. Lisensi & Hak Pakai: Setiap blueprint dan kode sumber yang telah dilunasi diserahkan hak gunanya secara penuh kepada klien.\n2. Batasan Bantuan Revisi: Bantuan revisi dibatasi sesuai tier yang dipilih (Spark 2x/bln, Lite 30 hari, Pro 6 bulan, Ultimate 1 tahun).\n3. Penguncian Lingkup Kerja: Setelah uang muka (DP) 50% atau pelunasan terkonfirmasi, ruang lingkup pengerjaan disepakati bersama untuk menjamin ketepatan waktu deliverable."),
                                        Textarea::make('midtrans_terms_content_en')
                                            ->label('Terms of Service (English)')
                                            ->rows(4)
                                            ->default("1. License & Deliverables: Blueprints and source code settled in full are 100% transferred to the client.\n2. Revision Window: Revision assistance is scoped by tier (Spark 2x/mo, Lite 30 days, Pro 6 months, Ultimate 1 year).\n3. Scope Agreement: Once 50% DP or full settlement is confirmed, project scope is agreed upon to ensure milestone completion."),
                                        Textarea::make('midtrans_refund_policy_id')
                                            ->label('Kebijakan Jaminan Garansi & Pengembalian Dana / Refund (Indonesia)')
                                            ->rows(4)
                                            ->default("1. Garansi Bug 30 Hari: Paket Pengerjaan Turnkey Studio dilindungi 30 Hari Garansi Bug pasca serah terima.\n2. Jaminan Refund 100%: Pengembalian dana penuh 100% berlaku jika terjadi kendala teknis fatal dari pihak Developer sebelum pengerjaan dimulai.\n3. Kebijakan Produk Digital: Karena produk digital dan blueprint arsitektur bersifat langsung diakses seketika, pembayaran yang telah diselesaikan setelah berkas diunduh tidak dapat dibatalkan sepihak. Pengembalian dana yang disetujui diproses dalam 7–14 hari kerja."),
                                        Textarea::make('midtrans_refund_policy_en')
                                            ->label('Warranty & Refund Policy (English)')
                                            ->rows(4)
                                            ->default("1. 30-Day Bug Warranty: Turnkey Studio packages include 30-day bug fix warranty after handover.\n2. 100% Refund Guarantee: Full 100% refund applies if critical technical impediment occurs on Developer side prior to work start.\n3. Digital Deliverable Policy: Because digital blueprints are accessed immediately upon payment, fees paid after download are non-refundable for unilateral cancellation. Approved refunds are credited back in 7-14 business days."),
                                        Textarea::make('midtrans_double_payment_policy_id')
                                            ->label('Kebijakan Penanganan Pembayaran Ganda / Double Payment (Indonesia)')
                                            ->rows(4)
                                            ->default("1. Deteksi Idempotency: Sistem Neriah Pro mendeteksi setiap transaksi menggunakan ID pesanan unik untuk mencegah duplikasi.\n2. Reversal Otomatis: Jika pelanggan tidak sengaja melakukan transfer ganda melalui gateway bank, sistem otomatis mencatat transaksi berlebih.\n3. Pengembalian Dana: Kelebihan pembayaran akan diverifikasi dan dikembalikan ke rekening asal dalam 3 - 5 hari kerja tanpa potongan biaya sistem."),
                                        Textarea::make('midtrans_double_payment_policy_en')
                                            ->label('Double-Payment Anti-Collision Policy (English)')
                                            ->rows(4)
                                            ->default("1. Idempotency Detection: Neriah Pro utilizes unique order IDs to prevent duplicate transaction charges.\n2. Automated Reversal: If duplicate transfers occur due to bank network retries, the event is trapped and audited.\n3. Refund Timeline: Excess payments are verified and reimbursed to the source account within 3 - 5 business days with zero deduction."),
                                        Textarea::make('midtrans_cancellation_policy_id')
                                            ->label('Kebijakan Pembatalan Proyek (Indonesia)')
                                            ->rows(4)
                                            ->default("1. Sebelum Kickoff / DP: Pembatalan pesanan dapat dilakukan kapan saja tanpa penalti biaya.\n2. Pasca Pembayaran DP 50%: Jika klien membatalkan proyek secara sepihak saat pengerjaan telah berlangsung, DP yang telah dibayarkan dialokasikan untuk kompensasi jam kerja developer, dan seluruh berkas yang telah dikerjakan tetap diserahkan kepada klien."),
                                        Textarea::make('midtrans_cancellation_policy_en')
                                            ->label('Project Cancellation Policy (English)')
                                            ->rows(4)
                                            ->default("1. Prior to Kickoff / DP: Orders can be cancelled anytime with zero penalty.\n2. Post-DP 50% Kickoff: If the client cancels unilaterally while development is active, the DP is allocated toward incurred developer hours, while all produced blueprints and source code remain delivered to the client."),
                                    ])->columns(2),

                                Section::make('Panduan Hak Akses & Alur Uji Coba Reviewer Midtrans')
                                    ->description('Informasi alur publik yang diuji oleh tim verifikator Midtrans dan Bank Acquirer BNI.')
                                    ->collapsible()
                                    ->schema([
                                        Placeholder::make('midtrans_reviewer_guidance')
                                            ->hiddenLabel()
                                            ->content(new \Illuminate\Support\HtmlString('
                                                <div class="space-y-3 text-xs font-mono leading-relaxed text-zinc-700 dark:text-zinc-300">
                                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100">
                                                        <strong class="font-bold text-sm block mb-1">✓ APA SAJA YANG DAPAT DIAKSES TIM REVIEWER MIDTRANS:</strong>
                                                        <ol class="list-decimal ml-4 space-y-1 mt-1 text-[11px]">
                                                            <li><strong>Katalog Publik & Halaman Utama:</strong> URL <code>https://neriahpro.com</code> dan <code>https://neriahpro.com/pricing</code>.</li>
                                                            <li><strong>Alur Guest Checkout (Tanpa Wajib Login):</strong> Reviewer dapat memilih paket retail (Rp 99.000 atau Rp 0), mengisi form pemesanan dengan email uji coba, dan langsung memicu pop-up Snap Midtrans.</li>
                                                            <li><strong>Pilihan Pembayaran (QRIS, VA BNI, Kartu Kredit):</strong> Reviewer menguji pop-up Snap untuk memastikan QRIS dan Virtual Account muncul dengan nominal di bawah batas limit BI Rp 10 Juta.</li>
                                                            <li><strong>Halaman Kebijakan Hukum di Footer:</strong> Reviewer membuka tautan Terms of Service, Privacy Policy, dan Refund Policy yang memuat nama pemilik (Yoseph Iriandi), SLA refund 7-14 hari kerja, dan alamat fisik (Gianyar, Bali).</li>
                                                            <li><strong>Invoice Komersial & Bukti Bayar:</strong> Di portal klien <code>/customer/dashboard</code> tanpa jargon Faktur Pajak.</li>
                                                        </ol>
                                                    </div>
                                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-100">
                                                        <strong class="font-bold text-sm block mb-1">⚠️ AREA YANG TIDAK BOLEH DIAKSES REVIEWER MIDTRANS:</strong>
                                                        <p class="text-[11px]">Panel Admin Backend (<code>/admin</code>) adalah area privat internal yang memuat API Keys (Midtrans Server Key, AI Keys, DB Settings) dan data sensitif pengguna. Reviewer Midtrans <strong>tidak memerlukan dan tidak boleh diberikan</strong> kredensial login ke panel admin.</p>
                                                    </div>
                                                </div>
                                            ')),
                                    ]),
                            ]),

                        Tabs\Tab::make('Multi-AI Model Hub')
                            ->key('multi-ai-hub')
                            ->id('multi-ai-hub')
                            ->icon('heroicon-m-cpu-chip')
                            ->badge('Failover Engine')
                            ->schema([
                                Section::make('Orkestrasi AI & Kebijakan Failover Otomatis')
                                    ->description('Konfigurasikan model AI default untuk Project OS & PRD Generator. Jika model utama kehabisan token atau mengalami 429 rate-limit, sistem otomatis berpindah ke model alternatif yang masih sehat tanpa downtime.')
                                    ->headerActions([
                                        Action::make('syncActiveModels')
                                            ->label('🔄 Muat Model API Semua Provider Aktif')
                                            ->color('primary')
                                            ->icon('heroicon-m-arrow-path')
                                            ->action(function ($livewire) {
                                                $providers = ['relayrouter', 'gemini', 'openai', 'groq', 'openrouter', 'deepseek', 'anthropic'];
                                                $synced = [];
                                                foreach ($providers as $p) {
                                                    $apiKey = \App\Services\Ai\MultiAiModelManager::getApiKey($p);
                                                    if (!empty($apiKey)) {
                                                        $res = \App\Services\Ai\MultiAiModelManager::fetchAvailableModels($p, forceRefresh: true);
                                                        if ($res['success']) {
                                                            $synced[] = strtoupper($p) . " ({$res['count']} model)";
                                                        }
                                                    }
                                                }

                                                if (!empty($synced)) {
                                                    Notification::make()
                                                        ->title('✅ Berhasil Memuat Model API!')
                                                        ->body('Katalog model terbaru berhasil disinkronkan: ' . implode(', ', $synced))
                                                        ->success()
                                                        ->send();
                                                } else {
                                                    Notification::make()
                                                        ->title('Belum Ada API Key Provider Aktif')
                                                        ->body('Silakan isi dan simpan API Key provider di bawah untuk menarik katalog model langsung dari API.')
                                                        ->info()
                                                        ->send();
                                                }
                                            }),
                                    ])
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
                                                    'relayrouter' => 'RelayRouter AI (Universal Aggregator Shopee API Key)',
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
                                            ->helperText('Pilih "RelayRouter AI" jika Anda ingin 1 kunci API dari Shopee/RelayRouter otomatis menangani seluruh sintesis PRD dan discovery.')
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

                                Section::make('Universal Router Configuration (RelayRouter / Shopee 1-Key Multi-Model)')
                                    ->description('Sesuaikan Base URL dan alokasi model AI untuk kunci API universal/sharing (1 Key untuk semua model) seperti RelayRouter.ai, OpenRouter, atau OneAPI.')
                                    ->headerActions([
                                        Action::make('testRelayRouter')
                                            ->label('⚡ Tes Koneksi RelayRouter ($1 Kuota)')
                                            ->color('success')
                                            ->icon('heroicon-m-bolt')
                                            ->action(function ($livewire) {
                                                $formData = $livewire->data ?? [];
                                                $apiKey = !empty($formData['ai_relayrouter_api_key'])
                                                    ? trim($formData['ai_relayrouter_api_key'])
                                                    : \App\Services\Ai\MultiAiModelManager::getApiKey('relayrouter');

                                                if (empty($apiKey)) {
                                                    Notification::make()
                                                        ->title('RelayRouter API Key Masih Kosong')
                                                        ->body('Silakan ketik atau tempel (paste) API Key RelayRouter Anda pada field di atas, klik "Save Settings", lalu klik tombol ini lagi.')
                                                        ->warning()
                                                        ->persistent()
                                                        ->send();
                                                    return;
                                                }

                                                $baseUrl = !empty($formData['ai_relayrouter_base_url'])
                                                    ? rtrim(trim($formData['ai_relayrouter_base_url']), '/')
                                                    : \App\Services\Ai\MultiAiModelManager::getBaseUrl('relayrouter');

                                                $model = !empty($formData['ai_relayrouter_discovery_model'])
                                                    ? trim($formData['ai_relayrouter_discovery_model'])
                                                    : 'gpt-4o-mini';

                                                try {
                                                    $response = \Illuminate\Support\Facades\Http::timeout(15)
                                                        ->withHeaders([
                                                            'Authorization' => "Bearer {$apiKey}",
                                                            'Content-Type' => 'application/json',
                                                        ])
                                                        ->post("{$baseUrl}/chat/completions", [
                                                            'model' => $model,
                                                            'messages' => [
                                                                ['role' => 'user', 'content' => 'Ping test. Reply in 5 words with "PONG - RelayRouter OK" and model name.'],
                                                            ],
                                                            'max_tokens' => 30,
                                                            'temperature' => 0.1,
                                                        ]);

                                                    if ($response->successful()) {
                                                        $reply = $response->json('choices.0.message.content', 'OK');
                                                        \App\Services\Ai\MultiAiModelManager::recordSuccess('relayrouter');

                                                        Notification::make()
                                                            ->title('✅ RelayRouter AI Berhasil Terhubung!')
                                                            ->body("Respon [{$model}]: \"{$reply}\". Kunci API dan pulsa $1 aktif siap digunakan!")
                                                            ->success()
                                                            ->persistent()
                                                            ->send();
                                                    } else {
                                                        $status = $response->status();
                                                        $errJson = $response->json();
                                                        $errMsg = $errJson['error']['message'] ?? $errJson['message'] ?? $response->body();

                                                        Notification::make()
                                                            ->title("❌ Gagal Terhubung ke RelayRouter (HTTP {$status})")
                                                            ->body("Pesan dari RelayRouter: {$errMsg}")
                                                            ->danger()
                                                            ->persistent()
                                                            ->send();
                                                    }
                                                } catch (\Throwable $e) {
                                                    Notification::make()
                                                        ->title('❌ Koneksi RelayRouter Error / Timeout')
                                                        ->body($e->getMessage())
                                                        ->danger()
                                                        ->persistent()
                                                        ->send();
                                                }
                                            }),
                                        Action::make('fetchRelayRouterModels')
                                            ->label('🔄 Tarik 380+ Model dari API')
                                            ->color('info')
                                            ->icon('heroicon-m-arrow-down-tray')
                                            ->action(function ($livewire) {
                                                $formData = $livewire->data ?? [];
                                                $apiKey = !empty($formData['ai_relayrouter_api_key'])
                                                    ? trim($formData['ai_relayrouter_api_key'])
                                                    : \App\Services\Ai\MultiAiModelManager::getApiKey('relayrouter');
                                                $baseUrl = !empty($formData['ai_relayrouter_base_url'])
                                                    ? rtrim(trim($formData['ai_relayrouter_base_url']), '/')
                                                    : \App\Services\Ai\MultiAiModelManager::getBaseUrl('relayrouter');

                                                if (empty($apiKey)) {
                                                    Notification::make()
                                                        ->title('RelayRouter API Key Masih Kosong')
                                                        ->body('Silakan tempel (paste) API Key RelayRouter terlebih dahulu sebelum menarik daftar model.')
                                                        ->warning()
                                                        ->send();
                                                    return;
                                                }

                                                $res = \App\Services\Ai\MultiAiModelManager::fetchAvailableModels('relayrouter', $apiKey, $baseUrl, forceRefresh: true);

                                                 if ($res['success']) {
                                                    $count = $res['count'];
                                                    $discCount = count($res['categorized']['discovery'] ?? []);
                                                    $prdCount = count($res['categorized']['prd'] ?? []);
                                                    Notification::make()
                                                        ->title("✅ Berhasil Memuat {$count} Model dari API!")
                                                        ->body("Katalog {$count} model dari RelayRouter/OpenAI telah disinkronkan & disortir ke dua dropdown: {$discCount} model Discovery / Audit Cepat dan {$prdCount} model PRD & Arsitektur Kompleks.")
                                                        ->success()
                                                        ->send();
                                                } else {
                                                    Notification::make()
                                                        ->title('❌ Gagal Menarik Model dari API')
                                                        ->body($res['error'] ?? 'Gagal menghubungi endpoint /models.')
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ])
                                    ->schema([
                                        TextInput::make('ai_relayrouter_api_key')
                                            ->label('RelayRouter API Token / Key')
                                            ->placeholder('sk-Uz4TzEyHYy5PSFvXvgi0guhlrauD6iWofllyoYwFDg1JxlzE')
                                            ->password()
                                            ->revealable()
                                            ->helperText('Token API RelayRouter Anda (sk-...). 1 Token ini dapat mengakses Claude 3.7, GPT-4o, DeepSeek R1, dan 400+ model AI sekaligus.')
                                            ->columnSpan(1),
                                        TextInput::make('ai_relayrouter_base_url')
                                            ->label('RelayRouter Base URL')
                                            ->default('https://api.relayrouter.ai/v1')
                                            ->helperText('Endpoint proxy OpenAI-compatible (standar: https://api.relayrouter.ai/v1).')
                                            ->columnSpan(1),
                                        Select::make('ai_relayrouter_discovery_model')
                                            ->label('Model Discovery / Audit Cepat')
                                            ->default('gpt-4o-mini')
                                            ->searchable()
                                            ->preload()
                                            ->options(function ($get) {
                                                return \App\Services\Ai\MultiAiModelManager::getGroupedModelOptions(
                                                    'relayrouter',
                                                    'discovery',
                                                    $get('ai_relayrouter_discovery_model')
                                                );
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->createOptionForm([
                                                TextInput::make('custom_model_id')
                                                    ->label('Nama / ID Model AI Kustom')
                                                    ->placeholder('contoh: gpt-4o-mini atau nama model baru')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data): string {
                                                return trim($data['custom_model_id']);
                                            })
                                            ->helperText('Model respons cepat & hemat untuk audit ide awal. Tersortir khusus dari 380+ API (Rekomendasi: gpt-4o-mini, deepseek-chat, gemini-2.0-flash-lite).')
                                            ->columnSpan(1),
                                        Select::make('ai_relayrouter_prd_model')
                                            ->label('Model PRD & Arsitektur Kompleks')
                                            ->default('claude-3-7-sonnet-20250219')
                                            ->searchable()
                                            ->preload()
                                            ->options(function ($get) {
                                                return \App\Services\Ai\MultiAiModelManager::getGroupedModelOptions(
                                                    'relayrouter',
                                                    'prd',
                                                    $get('ai_relayrouter_prd_model')
                                                );
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->createOptionForm([
                                                TextInput::make('custom_model_id')
                                                    ->label('Nama / ID Model AI Kustom')
                                                    ->placeholder('contoh: claude-3-7-sonnet-20250219 atau nama model baru')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data): string {
                                                return trim($data['custom_model_id']);
                                            })
                                            ->helperText('Model penalaran arsitektur komprehensif. Tersortir khusus dari 380+ API (Rekomendasi: claude-3-7-sonnet-20250219, deepseek-reasoner, gpt-4o).')
                                            ->columnSpan(1),
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
                                            ->helperText('Jika dinonaktifkan (default), akses publik ke /cv-pro dan tab CV Pro di pricing disembunyikan.')
                                            ->default(false),

                                        Toggle::make('pricing_show_cv_tab')
                                            ->label('Tampilkan Tab Switcher CV Pro di Halaman Pricing')
                                            ->helperText('Jika dinonaktifkan (default), tab CV Pro di halaman pricing disembunyikan. Jika diaktifkan, tab switcher CV Pro akan muncul.')
                                            ->default(false),

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
                                                TextInput::make('url')->label('Profile URL')->url(),
                                            ])
                                            ->defaultItems(0)
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
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('2.500.000')
                                            ->required(),
                                        TextInput::make('pricing_mvp_price')
                                            ->label('Tarif Kontrak Full MVP Monolith')
                                            ->helperText('Label harga paket Full MVP (Gunakan "Custom Quotation" agar mematuhi batas limit transaksi QRIS perorangan Midtrans).')
                                            ->default('Custom Quotation')
                                            ->required(),
                                        TextInput::make('pricing_umkm_price')
                                            ->label('Tarif Paket UMKM Digital Starter (Rp)')
                                            ->helperText('Biaya dasar paket digitalisasi UMKM sebelum dipotong kuota voucher subsidi.')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('7.500.000')
                                            ->required(),
                                        TextInput::make('pricing_active_promo_banner')
                                            ->label('Teks Pengumuman Banner Promosi & Voucher')
                                            ->helperText('Pesan promosi yang tampil di bagian atas halaman paket & harga.')
                                            ->default('Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!')
                                            ->columnSpanFull(),
                                    ])->columns(3),

                                Section::make('Paket Eceran Project OS Self-Service (Micro-SaaS & Retail)')
                                    ->description('Konfigurasi tarif, batasan kuota, siklus reset, dan syarat login untuk paket generator PRD digital mandiri (self-service).')
                                    ->schema([
                                        TextInput::make('pricing_retail_spark_price')
                                            ->label('Tarif Spark / Free (Rp)')
                                            ->helperText('Akses audit ide cepat & lean PRD ringkas.')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('pricing_retail_lite_price')
                                            ->label('Tarif Lite PRD (Rp)')
                                            ->helperText('PRD 26 parameter esensial + skema DDL.')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('99.000')
                                            ->required(),
                                        TextInput::make('pricing_retail_pro_price')
                                            ->label('Tarif Pro Full PRD & WBS (Rp)')
                                            ->helperText('PRD komprehensif, Mermaid diagrams, WBS 5 sprint.')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('399.000')
                                            ->required(),
                                        TextInput::make('pricing_retail_ultimate_price')
                                            ->label('Tarif Ultimate Advisory (Rp)')
                                            ->helperText('PRD Ultimate + AI Failover + 1-on-1 Call 60 menit.')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->stripCharacters([',', '.'])
                                            ->default('1.490.000')
                                            ->required(),

                                        TextInput::make('pricing_retail_spark_limit')
                                            ->label('Batasan & Siklus Reset Spark')
                                            ->helperText('Contoh: 2x Audit Ide / Bulan (Reset tiap tanggal 1)')
                                            ->default('2x Audit Ide / Bulan (Reset tiap tanggal 1)')
                                            ->required(),
                                        TextInput::make('pricing_retail_lite_limit')
                                            ->label('Batasan & Masa Berlaku Lite')
                                            ->helperText('Contoh: 1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)')
                                            ->default('1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)')
                                            ->required(),
                                        TextInput::make('pricing_retail_pro_limit')
                                            ->label('Batasan & Masa Berlaku Pro')
                                            ->helperText('Contoh: 1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)')
                                            ->default('1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)')
                                            ->required(),
                                        TextInput::make('pricing_retail_ultimate_limit')
                                            ->label('Batasan & Masa Berlaku Ultimate')
                                            ->helperText('Contoh: 1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call)')
                                            ->default('1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call 60 Menit)')
                                            ->required(),

                                        TextInput::make('pricing_retail_login_policy')
                                            ->label('Kebijakan Login & Kepemilikan Akun')
                                            ->helperText('Penjelasan syarat login bagi pengguna untuk berlangganan / menyimpan dokumen.')
                                            ->default('Guest Mode untuk Spark (Free). Wajib Login / Daftar Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.')
                                            ->columnSpanFull()
                                            ->required(),

                                        Textarea::make('pricing_retail_disclaimer')
                                            ->label('Disclaimer Penegasan 100% Self-Service (Bukan Koding Neriah Pro)')
                                            ->helperText('Teks penegasan bahwa paket digital di atas dikerjakan sendiri oleh pelanggan tanpa keterlibatan coding oleh Neriah Pro.')
                                            ->default('Paket Instant Architectural Blueprint 100% Self-Service: Dihasilkan instan oleh AI Project OS untuk Anda atau tim developer Anda kerjakan sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro.')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->required(),
                                    ])->columns(4),

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

    public function getDeveloperSignaturePadAction(): Action
    {
        return Action::make('open_developer_signature_pad')
            ->label('✍️ Goreskan Tanda Tangan Langsung (Opsi B: Canvas Pad Modal)')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->button()
            ->modalHeading('Goreskan Tanda Tangan Digital Developer')
            ->modalDescription('Gunakan mouse, stylus pen, atau touchscreen untuk membubuhkan tanda tangan Anda langsung di canvas pad.')
            ->modalSubmitActionLabel('Terapkan Tanda Tangan ke Pengaturan')
            ->form([
                SignaturePad::make('modal_canvas_signature')
                    ->label('Canvas Pad Tanda Tangan')
                    ->helperText('Goreskan tanda tangan Anda pada area putih di bawah ini. Klik Clear jika ingin mengulang.')
                    ->dotSize(2.0)
                    ->lineMinWidth(1.0)
                    ->lineMaxWidth(2.5)
                    ->penColor('blue')
                    ->backgroundColor('rgba(255, 255, 255, 1)')
                    ->clearable()
                    ->required(),
            ])
            ->action(function (array $data): void {
                if (!empty($data['modal_canvas_signature'])) {
                    $raw = $data['modal_canvas_signature'];
                    if (is_string($raw) && str_starts_with($raw, 'data:image')) {
                        $rawBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $raw);
                        $decoded = base64_decode($rawBase64);
                        if ($decoded !== false) {
                            $filename = 'signatures/developer_sig_' . time() . '.png';
                            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);
                            $this->data['developer_signature_image'] = [$filename];
                            CmsGlobalSetting::updateOrCreate(['key' => 'developer_signature_image'], ['value' => $filename]);
                        }
                    } else {
                        $this->data['developer_signature_image'] = is_array($raw) ? $raw : [$raw];
                        CmsGlobalSetting::updateOrCreate(['key' => 'developer_signature_image'], ['value' => is_array($raw) ? reset($raw) : $raw]);
                    }
                    Notification::make()
                        ->title('Tanda Tangan Diterapkan')
                        ->body('Goresan tanda tangan berhasil disimpan sebagai berkas PNG dan dipasang.')
                        ->success()
                        ->send();
                }
            });
    }

    protected function getActions(): array
    {
        return [];
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

    public function save(): void
    {
        $this->submit();
    }

    public function submit(): void
    {
        $formState = $this->form->getState();
        $data = array_merge($this->data ?? [], $formState);

        if (!empty($data['company_whatsapp_number']) || !empty($data['company_whatsapp_country'])) {
            $country = $data['company_whatsapp_country'] ?? 'ID';
            $number = $data['company_whatsapp_number'] ?? '';
            $cleanNumber = CountryZoneHelper::cleanLocalNumber($number);
            $dial = CountryZoneHelper::getDialCode($country);
            $cleanDial = preg_replace('/\D/', '', $dial);
            $data['company_whatsapp_country'] = $country;
            $data['company_whatsapp_number'] = $cleanNumber;
            $data['company_whatsapp'] = $cleanDial . $cleanNumber;
        } elseif (!empty($data['company_whatsapp'])) {
            $parsed = CountryZoneHelper::splitPhone($data['company_whatsapp']);
            $data['company_whatsapp'] = $parsed['full_e164'];
            $data['company_whatsapp_country'] = $parsed['iso'];
            $data['company_whatsapp_number'] = $parsed['local_number'];
        }

        if (!empty($data['developer_phone_number']) || !empty($data['developer_phone_country'])) {
            $country = $data['developer_phone_country'] ?? 'ID';
            $number = $data['developer_phone_number'] ?? '';
            $cleanNumber = CountryZoneHelper::cleanLocalNumber($number);
            $dial = CountryZoneHelper::getDialCode($country);
            $data['developer_phone_country'] = $country;
            $data['developer_phone_number'] = $cleanNumber;
            $data['developer_phone'] = $dial . $cleanNumber;
        }

        if (isset($data['seo_schema']) && is_array($data['seo_schema']) && !empty($data['company_whatsapp'])) {
            if (isset($data['seo_schema']['organization']) && is_array($data['seo_schema']['organization'])) {
                $data['seo_schema']['organization']['telephone'] = '+' . $data['company_whatsapp'];
            }
        }

        if (!empty($data['developer_signature_pad'])) {
            $raw = $data['developer_signature_pad'];
            if (is_string($raw) && str_starts_with($raw, 'data:image')) {
                $rawBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $raw);
                $decoded = base64_decode($rawBase64);
                if ($decoded !== false) {
                    $filename = 'signatures/developer_sig_' . time() . '.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);
                    $data['developer_signature_image'] = $filename;
                }
            } else {
                $data['developer_signature_image'] = is_array($raw) ? reset($raw) : $raw;
            }
        }
        unset($data['developer_signature_pad']);

        if (isset($data['developer_signature_image']) && is_array($data['developer_signature_image'])) {
            $data['developer_signature_image'] = reset($data['developer_signature_image']) ?: '';
        }
        
        foreach ($data as $key => $value) {
            $safeValue = $value !== null ? $value : '';
            if ($key === 'app_timezone' && is_array($safeValue)) {
                $safeValue = reset($safeValue) ?: 'Asia/Jakarta';
            }
            if ($key === 'default_frontend_locale') {
                if (is_array($safeValue)) {
                    $safeValue = reset($safeValue) ?: 'id';
                }
                $safeValue = in_array($safeValue, ['id', 'en']) ? $safeValue : 'id';
            }
            if ($key === 'google_translate_allowed_languages') {
                if (is_string($safeValue)) {
                    $decoded = json_decode($safeValue, true);
                    $safeValue = is_array($decoded) ? $decoded : [$safeValue];
                }
                if (!is_array($safeValue) || empty($safeValue)) {
                    $safeValue = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
                }
                $safeValue = array_values(array_filter($safeValue));
            }
            if ($key === 'google_translate_enabled') {
                $safeValue = (bool) $safeValue;
            }

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
        \Illuminate\Support\Facades\Cache::forget('midtrans_compliance_settings');
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
