<?php

namespace App\Support;

use App\Models\CmsGlobalSetting;

class BrandLogoHelper
{
    /**
     * Retrieve the official active brand logo URL from backend admin settings,
     * with priority-ordered fallbacks.
     */
    public static function getLogoUrl(): string
    {
        // 1. Direct site_logo uploaded or set in Admin
        $siteLogo = CmsGlobalSetting::getVal('site_logo');
        if (!empty($siteLogo)) {
            $formatted = static::formatUrl(is_array($siteLogo) ? reset($siteLogo) : $siteLogo);
            if (!empty($formatted)) {
                return $formatted;
            }
        }

        // 2. Direct site_logo_url
        $siteLogoUrl = CmsGlobalSetting::getVal('site_logo_url');
        if (!empty($siteLogoUrl) && is_string($siteLogoUrl)) {
            $formatted = static::formatUrl($siteLogoUrl);
            if (!empty($formatted)) {
                return $formatted;
            }
        }

        // 3. Fallback brand_logo or app_logo
        $brandLogo = CmsGlobalSetting::getVal('brand_logo') ?? CmsGlobalSetting::getVal('app_logo');
        if (!empty($brandLogo)) {
            $formatted = static::formatUrl(is_array($brandLogo) ? reset($brandLogo) : $brandLogo);
            if (!empty($formatted)) {
                return $formatted;
            }
        }

        // 4. Organization logo from SEO schema
        $seoSchema = CmsGlobalSetting::getVal('seo_schema');
        if (is_array($seoSchema) && !empty($seoSchema['organization']['logo'])) {
            $formatted = static::formatUrl($seoSchema['organization']['logo']);
            if (!empty($formatted)) {
                return $formatted;
            }
        }

        // 5. Static file fallbacks in public/
        if (file_exists(public_path('images/logo.png'))) {
            return asset('images/logo.png');
        }
        if (file_exists(public_path('images/logo.svg'))) {
            return asset('images/logo.svg');
        }
        if (file_exists(public_path('favicon.ico'))) {
            return asset('favicon.ico');
        }

        return asset('favicon.ico');
    }

    /**
     * Helper to preview logo from arbitrary raw values (used in Admin form get() closures).
     */
    public static function getLogoUrlFromValues(mixed $siteLogo = null, mixed $siteLogoUrl = null, mixed $seoLogo = null): string
    {
        if (!empty($siteLogo)) {
            $val = is_array($siteLogo) ? reset($siteLogo) : $siteLogo;
            if (!empty($val)) {
                return static::formatUrl((string) $val);
            }
        }

        if (!empty($siteLogoUrl) && is_string($siteLogoUrl)) {
            return static::formatUrl($siteLogoUrl);
        }

        if (!empty($seoLogo) && is_string($seoLogo)) {
            return static::formatUrl($seoLogo);
        }

        return static::getLogoUrl();
    }

    /**
     * Format a path or URL cleanly into an absolute web-accessible URL.
     */
    public static function formatUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:image')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/' . $path);
    }
}

