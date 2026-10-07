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
            try {
                $setting = CmsGlobalSetting::where('key', 'app_timezone')->first();
                $val = $setting ? $setting->value : null;
                if (is_array($val)) {
                    $val = reset($val);
                }
                return is_string($val) && trim($val) !== '' ? trim($val) : config('app.timezone', 'Asia/Jakarta');
            } catch (\Throwable) {
                return config('app.timezone', 'Asia/Jakarta');
            }
        });

        if (is_string($timezone) && in_array($timezone, timezone_identifiers_list())) {
            config(['app.timezone' => $timezone]);
            try {
                date_default_timezone_set($timezone);
            } catch (\Throwable) {
                date_default_timezone_set('Asia/Jakarta');
            }
        }

        return $next($request);
    }
}
