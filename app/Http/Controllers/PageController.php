<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\Artisan;

class PageController extends Controller
{
    public function show($slug = null)
    {
        $slug = (empty($slug) || $slug === '/') ? 'home' : ltrim($slug, '/');

        $globalSettings = CmsGlobalSetting::getAllCached();
        $isCvProEnabled = (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true);
        $isCvPricingEnabled = (bool) ($globalSettings['feature_enable_cv_pricing']->value ?? true);
        $isMidtransStrict = (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false);


        // Auto-seed superadmin, midtrans reviewer, and workflow data if missing
        try {
            if (\App\Models\User::where('email', 'reviewer.midtrans@neriahpro.com')->doesntExist()) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\SuperAdminSeeder',
                    '--force' => true,
                ]);
            }
            if (\App\Models\LegalPolicy::count() === 0) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\WorkflowEndToEndSeeder',
                    '--force' => true,
                ]);
            }
            if (CmsGlobalSetting::count() === 0) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\CmsSeeder',
                    '--force' => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Table might not exist or connection issue, ignore safely
        }

        // Clean up any stale legacy incomplete class cache
        try {
            $legacy = \Illuminate\Support\Facades\Cache::get("cms_page_{$slug}");
            if ($legacy instanceof \__PHP_Incomplete_Class) {
                \Illuminate\Support\Facades\Cache::forget("cms_page_{$slug}");
            }
        } catch (\Throwable) {
            \Illuminate\Support\Facades\Cache::forget("cms_page_{$slug}");
        }

        // Cache raw attribute arrays to prevent __PHP_Incomplete_Class deserialization
        $pageAttributes = \Illuminate\Support\Facades\Cache::rememberForever("cms_page_data_{$slug}", function () use ($slug) {
            $record = CmsPage::where('slug', $slug)->where('is_published', true)->first();

            // Auto-seed default landing page or pricing page if missing on fresh deployment
            if (! $record && in_array($slug, ['home', 'pricing'])) {
                try {
                    Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\LandingPageSeeder',
                        '--force' => true,
                    ]);
                    $record = CmsPage::where('slug', $slug)->first();
                } catch (\Throwable $e) {
                    // Ignore seed error and fallback gracefully
                }
            }

            return $record ? $record->getAttributes() : null;
        });

        $page = null;
        if (is_array($pageAttributes)) {
            $page = (new CmsPage)->newFromBuilder($pageAttributes);
        } else {
            // Defensive recovery if cache returned incomplete class or was empty
            \Illuminate\Support\Facades\Cache::forget("cms_page_data_{$slug}");
            $record = CmsPage::where('slug', $slug)->where('is_published', true)->first();
            if ($record) {
                $page = $record;
                \Illuminate\Support\Facades\Cache::forever("cms_page_data_{$slug}", $record->getAttributes());
            }
        }

        if (! $page) {
            abort(404);
        }

        // Hard filter: ensure feature_grid, product_grid, and redundant pricing on homepage are purged
        if (! empty($page->plugins) && is_array($page->plugins)) {
            $page->plugins = array_values(array_filter($page->plugins, function ($item) use ($slug) {
                $type = is_array($item) ? ($item['type'] ?? $item['plugin_type'] ?? '') : ($item->type ?? $item->plugin_type ?? '');
                if (in_array($type, ['feature_grid', 'product_grid'])) {
                    return false;
                }
                // Strictly isolate full pricing to /pricing page; eliminate duplicate pricing island on homepage
                if ($slug === 'home' && in_array($type, ['architecture_pricing', 'pricing_section'])) {
                    return false;
                }
                return true;
            }));
        }

        $globalSettings = CmsGlobalSetting::getAllCached();

        return view('page', compact('page', 'globalSettings'));
    }
}
