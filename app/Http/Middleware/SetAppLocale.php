<?php

namespace App\Http\Middleware;

use App\Services\GeoLocaleDetector;
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
        // 1. Explicit URL query parameter (?lang=en or ?lang=id)
        if ($request->has('lang') && in_array($request->query('lang'), ['id', 'en'], true)) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
            cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 365, '/', null, false, false));
            cookie()->queue(cookie('neriah_locale_manual', '1', 60 * 24 * 365, '/', null, false, false));
            cookie()->queue(cookie()->forget('googtrans'));
            App::setLocale($locale);
            return $next($request);
        }

        // 2. User has explicit session preference saved -> Respect it without overriding
        if (Session::has('locale') && in_array(Session::get('locale'), ['id', 'en'], true)) {
            $locale = Session::get('locale');
            App::setLocale($locale);
            return $next($request);
        }

        // 3. User has explicit manual preference saved previously in cookie -> Respect it without overriding
        $isManual = $request->hasCookie('neriah_locale_manual');
        if ($isManual) {
            $locale = $request->cookie('neriah_locale') ?: Session::get('locale', 'id');
            if (!is_string($locale) || !in_array($locale, ['id', 'en'], true)) {
                $locale = 'id';
            }
            App::setLocale($locale);
            return $next($request);
        }

        // 3. User was already auto-detected previously -> Retain current session & cookie
        if ($request->hasCookie('neriah_auto_detected')) {
            $locale = $request->cookie('neriah_locale') ?: Session::get('locale', 'id');
            if (!is_string($locale) || !in_array($locale, ['id', 'en'], true)) {
                $locale = 'id';
            }
            App::setLocale($locale);
            return $next($request);
        }

        // 4. First Visit: Perform Intelligent Geo & Tier 2 Auto-Detection
        $detection = GeoLocaleDetector::detect($request);

        if ($detection['tier'] === 2 && !empty($detection['tier2_lang'])) {
            // Visitor is from a Tier 2 country (e.g. Japan -> ja, Germany -> de, France -> fr)
            $tier2Lang = $detection['tier2_lang'];
            $locale = 'id'; // Base language for Google Translate engine
            Session::put('locale', $locale);
            cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie('neriah_auto_detected', '1', 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie('googtrans', "/id/{$tier2Lang}", 60 * 24 * 30, '/', null, false, false));
        } elseif (!$detection['is_indonesia']) {
            // Visitor is outside Indonesia and not in Tier 2 -> Auto-switch to English (Tier 1 Native)
            $locale = 'en';
            Session::put('locale', $locale);
            cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie('neriah_auto_detected', '1', 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie()->forget('googtrans'));
        } else {
            // Visitor is from Indonesia -> Stay in Bahasa Indonesia (Tier 1 Native)
            $locale = 'id';
            Session::put('locale', $locale);
            cookie()->queue(cookie('neriah_locale', $locale, 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie('neriah_auto_detected', '1', 60 * 24 * 30, '/', null, false, false));
            cookie()->queue(cookie()->forget('googtrans'));
        }

        App::setLocale($locale);

        return $next($request);
    }
}
