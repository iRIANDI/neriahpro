<?php

namespace App\Services;

use App\Models\CmsGlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocaleDetector
{
    /**
     * Map of Country Codes (ISO 3166-1 alpha-2) to Tier 2 Language Codes.
     */
    public const COUNTRY_TO_LANG = [
        // Indonesia (Tier 1 Native)
        'ID' => 'id',

        // Japanese -> Tier 2 'ja'
        'JP' => 'ja',

        // Chinese / Mandarin -> Tier 2 'zh-CN'
        'CN' => 'zh-CN',
        'TW' => 'zh-CN',
        'HK' => 'zh-CN',
        'MO' => 'zh-CN',

        // Arabic -> Tier 2 'ar'
        'SA' => 'ar', 'AE' => 'ar', 'EG' => 'ar', 'QA' => 'ar', 'KW' => 'ar',
        'BH' => 'ar', 'OM' => 'ar', 'JO' => 'ar', 'LB' => 'ar', 'IQ' => 'ar',
        'YE' => 'ar', 'SY' => 'ar', 'MA' => 'ar', 'DZ' => 'ar', 'TN' => 'ar',
        'LY' => 'ar', 'SD' => 'ar',

        // German -> Tier 2 'de'
        'DE' => 'de', 'AT' => 'de', 'CH' => 'de', 'LI' => 'de',

        // French -> Tier 2 'fr'
        'FR' => 'fr', 'BE' => 'fr', 'MC' => 'fr', 'LU' => 'fr', 'SN' => 'fr',
        'CI' => 'fr', 'ML' => 'fr', 'NE' => 'fr', 'TD' => 'fr', 'MG' => 'fr',
        'CM' => 'fr',

        // Spanish -> Tier 2 'es'
        'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'CL' => 'es',
        'PE' => 'es', 'VE' => 'es', 'EC' => 'es', 'GT' => 'es', 'CU' => 'es',
        'DO' => 'es', 'BO' => 'es', 'HN' => 'es', 'PY' => 'es', 'SV' => 'es',
        'NI' => 'es', 'CR' => 'es', 'PA' => 'es', 'UY' => 'es', 'PR' => 'es',

        // Extended Tier 2 Languages (if enabled in Admin Whitelist)
        'KR' => 'ko',
        'RU' => 'ru', 'BY' => 'ru', 'KZ' => 'ru',
        'PT' => 'pt', 'BR' => 'pt',
        'NL' => 'nl',
        'VN' => 'vi',
        'TH' => 'th',
    ];

    /**
     * Detect country and resolve optimal Tier 1 / Tier 2 language for incoming request.
     *
     * @param Request $request
     * @return array{country: string, locale: string, tier: int, tier2_lang: ?string, is_indonesia: bool, source: string}
     */
    public static function detect(Request $request): array
    {
        $country = self::detectCountryCode($request);
        $country = strtoupper(trim($country ?: ''));

        // Retrieve active Tier 2 Whitelist from Centralized CMS Settings
        $rawAllowed = CmsGlobalSetting::getVal('google_translate_allowed_languages', ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        $allowedTier2 = is_array($rawAllowed) ? $rawAllowed : (is_string($rawAllowed) ? json_decode($rawAllowed, true) : ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        if (!is_array($allowedTier2) || empty($allowedTier2)) {
            $allowedTier2 = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
        }

        $isGoogleTranslateEnabled = (bool) CmsGlobalSetting::getVal('google_translate_enabled', true);

        // 1. Visitors from Indonesia -> Stay in Bahasa Indonesia (Tier 1 Native)
        if ($country === 'ID') {
            return [
                'country' => 'ID',
                'locale' => 'id',
                'tier' => 1,
                'tier2_lang' => null,
                'is_indonesia' => true,
                'source' => 'detected_indonesia',
            ];
        }

        // 2. Visitors outside Indonesia -> Check if country matches whitelisted Tier 2 language
        $mappedLang = self::COUNTRY_TO_LANG[$country] ?? null;

        if ($isGoogleTranslateEnabled && $mappedLang && in_array($mappedLang, $allowedTier2, true) && $mappedLang !== 'id') {
            return [
                'country' => $country,
                'locale' => 'id', // Base language for Google Translate engine
                'tier' => 2,
                'tier2_lang' => $mappedLang,
                'is_indonesia' => false,
                'source' => 'tier2_matched_country',
            ];
        }

        // 3. Visitors outside Indonesia without Tier 2 match -> Auto-switch to English (Tier 1 Native)
        return [
            'country' => $country ?: 'GLOBAL',
            'locale' => 'en',
            'tier' => 1,
            'tier2_lang' => null,
            'is_indonesia' => false,
            'source' => 'foreign_default_english',
        ];
    }

    /**
     * High-precision Country Code detection via Headers, GeoIP, and Browser Accept-Language.
     */
    public static function detectCountryCode(Request $request): string
    {
        // 1. Trusted Reverse Proxy & CDN Edge Headers (Cloudflare, AWS CloudFront, Fastly, Nginx)
        $proxyHeaders = [
            'CF-IPCountry',
            'X-Country-Code',
            'CloudFront-Viewer-Country',
            'X-Geo-Country',
            'GEOIP_COUNTRY_CODE',
        ];

        foreach ($proxyHeaders as $header) {
            $val = $request->header($header);
            if (!empty($val) && strlen($val) === 2 && ctype_alpha($val)) {
                return strtoupper($val);
            }
        }

        // 2. Client IP Address Lookup (Cached in Redis/Cache for sub-millisecond O(1) performance)
        $ip = $request->ip();
        if ($ip && !self::isPrivateIp($ip)) {
            $cachedCountry = Cache::remember("geoip_country_{$ip}", 86400 * 7, function () use ($ip) {
                try {
                    $response = Http::timeout(1.2)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode");
                    if ($response->successful() && $response->json('status') === 'success') {
                        $code = $response->json('countryCode');
                        if (!empty($code) && strlen($code) === 2 && ctype_alpha($code)) {
                            return strtoupper($code);
                        }
                    }
                } catch (\Throwable $e) {
                    // Silently fail to fallback
                }
                return null;
            });

            if (!empty($cachedCountry)) {
                return $cachedCountry;
            }
        }

        // 3. Browser Accept-Language Header Fallback (Present in 100% of standard HTTP browser requests)
        $acceptLanguage = strtolower($request->header('Accept-Language', ''));
        if (!empty($acceptLanguage)) {
            if (str_starts_with($acceptLanguage, 'id') || str_contains($acceptLanguage, 'id-id')) {
                return 'ID';
            }
            if (str_starts_with($acceptLanguage, 'ja')) {
                return 'JP';
            }
            if (str_starts_with($acceptLanguage, 'zh')) {
                return 'CN';
            }
            if (str_starts_with($acceptLanguage, 'de')) {
                return 'DE';
            }
            if (str_starts_with($acceptLanguage, 'fr')) {
                return 'FR';
            }
            if (str_starts_with($acceptLanguage, 'ar')) {
                return 'SA';
            }
            if (str_starts_with($acceptLanguage, 'es')) {
                return 'ES';
            }
            if (str_starts_with($acceptLanguage, 'ko')) {
                return 'KR';
            }
            if (str_starts_with($acceptLanguage, 'ru')) {
                return 'RU';
            }
            if (str_starts_with($acceptLanguage, 'pt')) {
                return 'BR';
            }
            if (str_starts_with($acceptLanguage, 'nl')) {
                return 'NL';
            }
            if (str_starts_with($acceptLanguage, 'vi')) {
                return 'VN';
            }
            if (str_starts_with($acceptLanguage, 'th')) {
                return 'TH';
            }
            if (str_starts_with($acceptLanguage, 'en')) {
                return 'US';
            }
        }

        // Default: If completely ambiguous, treat as Indonesia or default CMS locale
        return 'ID';
    }

    /**
     * Check if an IP address is a private, loopback, or local network address.
     */
    protected static function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
