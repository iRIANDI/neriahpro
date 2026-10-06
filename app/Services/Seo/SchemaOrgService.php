<?php

namespace App\Services\Seo;

use App\Models\CmsGlobalSetting;
use App\Models\Resume;

/**
 * Enterprise Schema.org JSON-LD Generator
 * 
 * Provides search-engine compliant structured data for:
 * - Organization & LocalBusiness
 * - WebSite & SearchAction
 * - WebApplication / SoftwareApplication (CV Pro & Project OS)
 * - ProfilePage & Person (ATS Candidate CVs)
 * - BreadcrumbList & WebPage
 */
class SchemaOrgService
{
    /**
     * Get root Organization Schema (Cached Forever, Dynamic via CmsGlobalSetting)
     */
    public static function organization(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('seo_schema_organization', function () {
            try {
                $setting = CmsGlobalSetting::where('key', 'seo_schema')->first();
                $schema = $setting?->value ?? [];
            } catch (\Throwable) {
                $schema = [];
            }

            $siteName = $schema['organization']['name'] ?? config('app.name', 'Neriah Pro');
            $siteUrl = url('/');
            $type = $schema['organization']['type'] ?? 'Organization';
            $logoUrl = !empty($schema['organization']['logo']) ? $schema['organization']['logo'] : asset('favicon.ico');
            $email = $schema['organization']['email'] ?? 'support@neriahpro.com';
            $telephone = $schema['organization']['telephone'] ?? null;

            $sameAsUrls = [];
            if (!empty($schema['sameAs']) && is_array($schema['sameAs'])) {
                foreach ($schema['sameAs'] as $item) {
                    if (!empty($item['url'])) {
                        $sameAsUrls[] = $item['url'];
                    }
                }
            }
            if (empty($sameAsUrls)) {
                $sameAsUrls = ['https://github.com/iRIANDI/neriahpro'];
            }

            $addressData = null;
            if (!empty($schema['address']['streetAddress']) || !empty($schema['address']['addressLocality'])) {
                $addressData = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $schema['address']['streetAddress'] ?? '',
                    'addressLocality' => $schema['address']['addressLocality'] ?? '',
                    'addressRegion' => $schema['address']['addressRegion'] ?? '',
                    'postalCode' => $schema['address']['postalCode'] ?? '',
                    'addressCountry' => $schema['address']['addressCountry'] ?? 'ID',
                ];
            }

            $payload = [
                '@context' => 'https://schema.org',
                '@type' => $type,
                '@id' => $siteUrl . '/#organization',
                'name' => $siteName,
                'url' => $siteUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                ],
                'description' => 'Architecting High-Performance Digital Platforms, Productized Agency Systems, and Enterprise Web Applications.',
                'founder' => [
                    '@type' => 'Person',
                    'name' => 'Yoseph Iriandi Tambunan',
                ],
                'contactPoint' => [
                    [
                        '@type' => 'ContactPoint',
                        'contactType' => 'customer support',
                        'email' => $email,
                        'availableLanguage' => ['id', 'en'],
                    ],
                ],
                'sameAs' => $sameAsUrls,
            ];

            if ($telephone) {
                $payload['telephone'] = $telephone;
            }
            if ($addressData) {
                $payload['address'] = $addressData;
            }

            return $payload;
        });
    }

    /**
     * Get WebSite Schema with SearchAction (Cached Forever)
     */
    public static function webSite(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('seo_schema_website', function () {
            try {
                $setting = CmsGlobalSetting::where('key', 'seo_schema')->first();
                $schema = $setting?->value ?? [];
            } catch (\Throwable) {
                $schema = [];
            }

            $siteName = $schema['organization']['name'] ?? config('app.name', 'Neriah Pro');
            $siteUrl = url('/');

            return [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                '@id' => $siteUrl . '/#website',
                'url' => $siteUrl,
                'name' => $siteName,
                'publisher' => [
                    '@id' => $siteUrl . '/#organization',
                ],
                'inLanguage' => app()->getLocale() === 'en' ? 'en-US' : 'id-ID',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => $siteUrl . '/?q={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        });
    }

    /**
     * Get WebApplication Schema for CV Pro Studio
     */
    public static function cvProApplication(): array
    {
        $appUrl = route('cv-pro.index');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            '@id' => $appUrl . '/#webapp',
            'name' => 'CV Pro - Enterprise ATS Resume Builder & AI Mock Interview Studio',
            'url' => $appUrl,
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'All modern web browsers (Chrome, Edge, Safari, Firefox)',
            'description' => 'Aplikasi pembuat CV standar ATS profesional dilengkapi Microsoft MarkItDown document & physical scan pipeline, audit skor ATS real-time, simulasi mock interview rekaman suara dengan evaluasi STAR, dan automated outreach letter.',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
            ],
            'featureList' => [
                'Microsoft MarkItDown Document & Physical Scan Engine (PDF, DOCX, Images, OCR)',
                'Real-Time ATS Resume Quality Score & Weak Action Verb Linter',
                'Interactive Speech Recognition Mock Interview Studio',
                'STAR Method Answer Evaluation (Situation, Task, Action, Result)',
                'Post-Interview Outreach Letter Generator (Thank You, Follow-up, Cold Pitch)',
                'High-DPI Standard A4 Printable Layout with Strict Margins',
            ],
            'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
            'softwareVersion' => '2.5.0',
            'inLanguage' => ['id', 'en'],
        ];
    }

    /**
     * Get WebApplication Schema for Project OS Blueprint (Cached Forever)
     */
    public static function projectOsApplication(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('seo_schema_project_os', function () {
            $appUrl = url('/blueprint');

            return [
                '@context' => 'https://schema.org',
                '@type' => 'WebApplication',
                '@id' => $appUrl . '/#blueprint',
                'name' => 'Project OS - AI Software Architecture & PRD Blueprint Engine',
                'url' => $appUrl,
                'applicationCategory' => 'DeveloperApplication',
                'operatingSystem' => 'All modern web browsers',
                'description' => 'Sistem perumusan arsitektur perangkat lunak komprehensif, PRD ultimate, diagram ERD PostgreSQL ULID, mitigasi bot AI Honeypot, dan konversi kontrak kerja digital dengan Scope Lock.',
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'IDR',
                ],
            ];
        });
    }

    /**
     * Get Schema.org Service, Product & Offer structured data for Pricing Page
     */
    public static function pricingServices(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('seo_schema_pricing_services', function () {
            $siteUrl = url('/');
            $pricingUrl = url('/pricing');

            return [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                '@id' => $pricingUrl . '/#service',
                'name' => 'Layanan Rekayasa Arsitektur Perangkat Lunak & Pengembangan Sistem Enterprise',
                'provider' => [
                    '@id' => $siteUrl . '/#organization',
                ],
                'serviceType' => 'Software Architecture & Development',
                'description' => 'Layanan profesional perancangan Product Requirements Document (PRD 26 parameter), skema basis data PostgreSQL Strict ULID, alur kerja sistem, dan pengembangan modern monolith Laravel 13 & Filament v5 dengan protokol Scope Locked.',
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Indonesia',
                ],
                'hasOfferCatalog' => [
                    '@type' => 'OfferCatalog',
                    'name' => 'Katalog Paket Layanan Neriah Pro',
                    'itemListElement' => [
                        [
                            '@type' => 'Offer',
                            'itemOffered' => [
                                '@type' => 'Service',
                                'name' => 'Project OS & Architecture PRD Blueprint (Advisory Only)',
                                'description' => 'Perancangan cetak biru spesifikasi sistem lengkap: Ultimate PRD 26 parameter, skema ERD PostgreSQL Strict ULID, alur kerja, dan alokasi 5 sprint kerja siap serah terima ke tim developer in-house.',
                            ],
                            'price' => '2500000',
                            'priceCurrency' => 'IDR',
                            'availability' => 'https://schema.org/InStock',
                            'url' => $pricingUrl,
                        ],
                        [
                            '@type' => 'Offer',
                            'itemOffered' => [
                                '@type' => 'Service',
                                'name' => 'Enterprise Rapid Monolith Development (Fase 1 MVP)',
                                'description' => 'Pembangunan aplikasi bisnis komplit berbasis Modern Monolith (Laravel 13, Filament v5, PostgreSQL ULID, Redis, dedicated VPS) dengan penguncian kontrak hukum dan termin DP 50% Midtrans.',
                            ],
                            'price' => '50000000',
                            'priceCurrency' => 'IDR',
                            'availability' => 'https://schema.org/InStock',
                            'url' => $pricingUrl,
                        ],
                        [
                            '@type' => 'Offer',
                            'itemOffered' => [
                                '@type' => 'Service',
                                'name' => 'UMKM Digital Starter (Program Subsidi & Hibah)',
                                'description' => 'Akselerasi aplikasi toko, kasir, dan operasional bisnis bagi UMKM dengan dukungan voucher subsidi DP 50% dan hibah 100% free bypass.',
                            ],
                            'price' => '5000000',
                            'priceCurrency' => 'IDR',
                            'availability' => 'https://schema.org/InStock',
                            'url' => $pricingUrl,
                        ],
                    ],
                ],
            ];
        });
    }

    /**
     * Get Schema.org FAQPage structured data for Google Rich Results on Pricing Page
     */
    public static function pricingFaq(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Kapan saya sebaiknya memilih paket Advisory Blueprint (Rp 2,5 jt) vs Full MVP Development (Rp 50 jt)?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Paket Advisory Blueprint (Rp 2.500.000) adalah opsi ideal bagi founder, CTO, atau pemilik bisnis yang sudah memiliki tim programmer in-house atau vendor sendiri, namun membutuhkan dokumen arsitektur komprehensif (PRD 26 parameter, skema ERD PostgreSQL Strict ULID, dan alokasi 5 sprint) agar pengerjaan tidak salah bangun dan terhindar dari scope creep. Sedangkan paket Full MVP Development (Rp 50.000.000) adalah layanan tuntas di mana Neriah Pro membangun aplikasi dari nol hingga rilis di VPS dengan jaminan Scope Locked dan garansi 30 hari.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Bagaimana mekanisme penguncian kontrak hukum dan pembayaran DP 50%?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Setelah dokumen PRD disetujui, sistem menerbitkan Surat Perjanjian Kerja Sama Digital dengan tanda tangan elektronik sah dan enkripsi kriptografi SHA-256. Pembayaran uang muka (DP 50%) dilakukan via Midtrans Snap (Kartu Kredit, QRIS, Virtual Account BCA/BRI/Mandiri/BNI) atau direct transfer. Pelunasan sisa 50% dilakukan setelah sistem serah terima di VPS.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Bagaimana pelaku UMKM dapat memanfaatkan voucher subsidi atau hibah aplikasi gratis?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Pelaku usaha mikro dan kecil dapat menggunakan kode voucher UMKM-SUBSIDI-50 (diskon 50% DP) atau mengajukan program hibah 100% free bypass melalui kode UMKM-DIGITAL-100 saat mengisi form konsultasi di Neriah Pro.',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get Custom Raw JSON-LD Schema from Backend Settings (Cached Forever)
     */
    public static function customJsonLd(): ?array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('seo_schema_raw', function () {
            try {
                $setting = CmsGlobalSetting::where('key', 'seo_schema')->first();
                $schema = $setting?->value ?? [];
                $raw = $schema['custom_json_ld'] ?? null;
                if (!empty($raw) && is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    return is_array($decoded) ? $decoded : null;
                }
            } catch (\Throwable) {
                return null;
            }
            return null;
        });
    }

    /**
     * Get ProfilePage and Person Schema for Public ATS Resume (/cv/{slug})
     */
    public static function resumeProfile(Resume $resume): array
    {
        $content = $resume->content ?? [];
        $personal = $content['personal_info'] ?? [];
        $name = $personal['full_name'] ?? $resume->title;
        $headline = $personal['headline'] ?? $resume->target_role ?? 'Professional';
        $summary = $personal['summary'] ?? 'Curriculum Vitae Profesional';
        $skills = $content['skills'] ?? [];
        $canonicalUrl = $resume->public_url;

        $worksFor = null;
        if (!empty($content['experiences'][0]['company'])) {
            $worksFor = [
                '@type' => 'Organization',
                'name' => $content['experiences'][0]['company'],
            ];
        }

        $alumniOf = [];
        if (!empty($content['education'])) {
            foreach ($content['education'] as $edu) {
                if (!empty($edu['institution'])) {
                    $alumniOf[] = [
                        '@type' => 'EducationalOrganization',
                        'name' => $edu['institution'],
                    ];
                }
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            '@id' => $canonicalUrl . '/#profilepage',
            'url' => $canonicalUrl,
            'name' => "{$name} - Resume & Portofolio Profesional",
            'description' => $summary,
            'dateCreated' => $resume->created_at?->toIso8601String(),
            'dateModified' => $resume->updated_at?->toIso8601String(),
            'mainEntity' => [
                '@type' => 'Person',
                '@id' => $canonicalUrl . '/#person',
                'name' => $name,
                'jobTitle' => $headline,
                'description' => $summary,
                'url' => $canonicalUrl,
                'email' => $personal['email'] ?? null,
                'telephone' => $personal['phone'] ?? null,
                'worksFor' => $worksFor,
                'alumniOf' => $alumniOf,
                'knowsAbout' => $skills,
            ],
        ];
    }

    /**
     * BreadcrumbList Schema
     */
    public static function breadcrumbs(array $items): array
    {
        $elements = [];
        $position = 1;

        foreach ($items as $name => $url) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name,
                'item' => $url ?: url('/'),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Render structured array as a clean <script type="application/ld+json"> tag
     */
    public static function render(array|object $schemas): string
    {
        $payload = is_array($schemas) && isset($schemas[0]) ? $schemas : [$schemas];
        $custom = self::customJsonLd();
        if ($custom) {
            $payload[] = $custom;
        }
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return "<script type=\"application/ld+json\">\n{$json}\n</script>";
    }
}
