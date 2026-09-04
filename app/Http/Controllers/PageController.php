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

        // Auto-seed superadmin and global settings if missing
        try {
            if (\App\Models\User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->doesntExist()) {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\SuperAdminSeeder',
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

        $page = CmsPage::where('slug', $slug)->where('is_published', true)->first();

        // Auto-seed default landing page if missing on fresh deployment
        if (! $page && $slug === 'home') {
            try {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\LandingPageSeeder',
                    '--force' => true,
                ]);
                $page = CmsPage::where('slug', 'home')->first();
            } catch (\Throwable $e) {
                // Ignore seed error and fallback gracefully
            }
        }

        if (! $page) {
            abort(404);
        }

        $globalSettings = CmsGlobalSetting::all()->keyBy('key');

        return view('page', compact('page', 'globalSettings'));
    }
}
