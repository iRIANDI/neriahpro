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

        $page = \Illuminate\Support\Facades\Cache::rememberForever("cms_page_{$slug}", function () use ($slug) {
            $record = CmsPage::where('slug', $slug)->where('is_published', true)->first();

            // Auto-seed default landing page if missing on fresh deployment
            if (! $record && $slug === 'home') {
                try {
                    Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\LandingPageSeeder',
                        '--force' => true,
                    ]);
                    $record = CmsPage::where('slug', 'home')->first();
                } catch (\Throwable $e) {
                    // Ignore seed error and fallback gracefully
                }
            }

            return $record;
        });

        if (! $page) {
            abort(404);
        }

        $globalSettings = \Illuminate\Support\Facades\Cache::rememberForever('cms_global_settings', function () {
            try {
                return CmsGlobalSetting::all()->keyBy('key');
            } catch (\Throwable) {
                return collect();
            }
        });

        return view('page', compact('page', 'globalSettings'));
    }
}
