<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\CmsGlobalSetting;
use App\Models\CmsPage;
use App\Models\VisionBlueprint;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML Sitemap with real-time feature flag awareness.
     */
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url', 'https://neriahpro.com'), '/');
        $globalSettings = CmsGlobalSetting::getAllCached();

        $isCvProEnabled = (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true);
        $isCvPricingEnabled = (bool) ($globalSettings['feature_enable_cv_pricing']->value ?? true);
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);
        $isMidtransStrict = (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false);

        $urls = [];

        // 1. Root Homepage
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => Carbon::now()->toDateString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // 2. Project OS Blueprint (Only if enabled)
        if ($isBlueprintEnabled) {
            $urls[] = [
                'loc' => $baseUrl . '/blueprint',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            // Publicly Published Blueprints
            try {
                $blueprints = VisionBlueprint::where('is_published', true)
                    ->orderBy('id', 'asc')
                    ->limit(100)
                    ->get(['slug', 'updated_at']);

                foreach ($blueprints as $bp) {
                    $urls[] = [
                        'loc' => $baseUrl . '/blueprint/' . $bp->slug,
                        'lastmod' => ($bp->updated_at ?? Carbon::now())->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ];
                }
            } catch (\Throwable) {
                // Ignore DB error during build or setup
            }
        }

        // 3. CV Pro Studio & Pricing (STRICTLY OMITTED IF DISABLED IN ADMIN)
        if ($isCvProEnabled && ! $isMidtransStrict) {
            $urls[] = [
                'loc' => $baseUrl . '/cv-pro',
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];

            if ($isCvPricingEnabled) {
                $urls[] = [
                    'loc' => $baseUrl . '/pricing',
                    'lastmod' => Carbon::now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        }

        // 4. Dynamic Published CMS Pages (Excluding home, and excluding pricing if disabled)
        try {
            $pages = CmsPage::where('is_published', true)
                ->where('slug', '!=', 'home')
                ->orderBy('id', 'asc')
                ->get(['slug', 'updated_at']);

            foreach ($pages as $p) {
                // Never include pricing in sitemap if CV Pro is disabled
                if ($p->slug === 'pricing' && (! $isCvProEnabled || ! $isCvPricingEnabled || $isMidtransStrict)) {
                    continue;
                }

                $urls[] = [
                    'loc' => $baseUrl . '/' . $p->slug,
                    'lastmod' => ($p->updated_at ?? Carbon::now())->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }
        } catch (\Throwable) {
            // Ignore DB error
        }

        // Build XML string
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $item) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $item['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $item['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $item['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // Sitemaps themselves don't need to be indexed as web pages
        ]);
    }

    /**
     * Generate dynamic robots.txt with disallow directives when modules are disabled.
     */
    public function robots(): Response
    {
        $content = self::getRobotsTxtContent();

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Generate robots.txt content string.
     */
    public static function getRobotsTxtContent(): string
    {
        $baseUrl = rtrim(config('app.url', 'https://neriahpro.com'), '/');
        $globalSettings = CmsGlobalSetting::getAllCached();

        $isCvProEnabled = (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true);
        $isMidtransStrict = (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false);

        $lines = [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /cart',
            'Disallow: /invite/',
            'Disallow: /api/',
        ];

        // If CV Pro is deactivated in Admin, explicitly disallow Googlebot from crawling it
        if (! $isCvProEnabled || $isMidtransStrict) {
            $lines[] = 'Disallow: /cv-pro';
            $lines[] = 'Disallow: /cv/';
            $lines[] = 'Disallow: /pricing';
        }

        $lines[] = '';
        $lines[] = 'Sitemap: ' . $baseUrl . '/sitemap.xml';
        $lines[] = '';

        return implode("\n", $lines);
    }

    /**
     * Synchronize physical public/robots.txt file on disk.
     */
    public static function syncRobotsTxtFile(): void
    {
        try {
            $path = public_path('robots.txt');
            $content = self::getRobotsTxtContent();
            file_put_contents($path, $content);
        } catch (\Throwable) {
            // Silently ignore permission issue
        }
    }
}
