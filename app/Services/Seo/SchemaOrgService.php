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
