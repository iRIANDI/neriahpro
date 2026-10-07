<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Handle an incoming request and set the active application locale.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check URL query parameter (?lang=en or ?lang=id)
        if ($request->has('lang') && in_array($request->query('lang'), ['id', 'en'])) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
            cookie()->queue(cookie()->forever('neriah_locale', $locale));
        }
        // 2. Check Session
        elseif (Session::has('locale') && in_array(Session::get('locale'), ['id', 'en'])) {
            $locale = Session::get('locale');
        }
        // 3. Check Cookie
        elseif ($request->hasCookie('neriah_locale') && in_array($request->cookie('neriah_locale'), ['id', 'en'])) {
            $locale = $request->cookie('neriah_locale');
        }
        // 4. Default fallback from CMS Global Settings Tier 1
        else {
            try {
                $cmsLocale = \App\Models\CmsGlobalSetting::getVal('default_frontend_locale');
                if (is_array($cmsLocale)) {
                    $cmsLocale = reset($cmsLocale);
                }
                $locale = (is_string($cmsLocale) && in_array($cmsLocale, ['id', 'en']))
                    ? $cmsLocale
                    : config('app.locale', 'id');
            } catch (\Throwable) {
                $locale = config('app.locale', 'id');
            }
        }

        if (is_array($locale)) {
            $locale = reset($locale) ?: 'id';
        }
        if (!is_string($locale) || !in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
