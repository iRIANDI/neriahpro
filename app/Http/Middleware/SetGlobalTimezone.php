<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\Cache;

class SetGlobalTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = Cache::rememberForever('app_timezone', function () {
            $setting = CmsGlobalSetting::where('key', 'app_timezone')->first();
            return $setting ? $setting->value : 'UTC';
        });

        if ($timezone && in_array($timezone, timezone_identifiers_list())) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
