<?php

namespace App\Services\Currency;

use App\Models\CmsGlobalSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyExchangeService
{
    public const CACHE_KEY = 'neriah_currency_exchange_rates_v1';
    public const CACHE_TTL_SECONDS = 3600; // 1 Hour
    public const DB_SNAPSHOT_KEY = 'last_known_fx_rates_snapshot';
    public const DB_LAST_USD_KEY = 'last_realtime_usd_to_idr';

    /**
     * Emergency cold-boot market rates (used ONLY before the first real-time DB snapshot is stored).
     */
    public const DEFAULT_RATES = [
        'IDR' => 1.0,
        'USD' => 16250.0,
        'EUR' => 17550.0,
        'SGD' => 12150.0,
        'GBP' => 20650.0,
        'AUD' => 10600.0,
        'JPY' => 107.0,
    ];

    public const CURRENCY_SYMBOLS = [
        'IDR' => 'Rp',
        'USD' => '$',
        'EUR' => '€',
        'SGD' => 'S$',
        'GBP' => '£',
        'AUD' => 'A$',
        'JPY' => '¥',
    ];

    public const CURRENCY_NAMES = [
        'IDR' => ['id' => 'Rupiah Indonesia', 'en' => 'Indonesian Rupiah'],
        'USD' => ['id' => 'Dolar Amerika Serikat', 'en' => 'US Dollar'],
        'EUR' => ['id' => 'Euro Uni Eropa', 'en' => 'Euro'],
        'SGD' => ['id' => 'Dolar Singapura', 'en' => 'Singapore Dollar'],
        'GBP' => ['id' => 'Poundsterling Inggris', 'en' => 'British Pound'],
        'AUD' => ['id' => 'Dolar Australia', 'en' => 'Australian Dollar'],
        'JPY' => ['id' => 'Yen Jepang', 'en' => 'Japanese Yen'],
    ];

    /**
     * Get all supported currencies with real-time exchange rates against IDR.
     * Priority hierarchy:
     *   1. Live Real-Time FX Feed (auto-persisted to Database CmsGlobalSetting on every success)
     *   2. Database Last-Known Real-Time FX Snapshot (`last_known_fx_rates_snapshot` / `last_realtime_usd_to_idr`)
     *   3. Emergency cold-boot baseline (only if DB has never been seeded yet)
     */
    public static function getAllRates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return self::resolveAndPersistRates();
        });
    }

    /**
     * Force-refresh live FX rates from external market feed, persist to DB, and update cache.
     */
    public static function syncLiveRatesToDatabase(): array
    {
        Cache::forget(self::CACHE_KEY);
        $payload = self::resolveAndPersistRates();
        Cache::put(self::CACHE_KEY, $payload, self::CACHE_TTL_SECONDS);

        return $payload;
    }

    /**
     * Resolve rates from Live Feed -> Database Last-Known Real-Time Snapshot -> Cold Baseline.
     */
    protected static function resolveAndPersistRates(): array
    {
        $rates = self::DEFAULT_RATES;
        $source = 'Emergency Cold-Boot Baseline';
        $lastSyncAt = null;

        // 1. Load Last-Known Real-Time FX Snapshot from Database first (prevents jumping to static baseline)
        $dbSnapshot = self::loadLastKnownDatabaseRates();
        if ($dbSnapshot !== null) {
            $rates = $dbSnapshot['rates'];
            $source = $dbSnapshot['source'];
            $lastSyncAt = $dbSnapshot['updated_at'] ?? null;
        }

        // 2. Fetch fresh Real-Time Market FX Feed & immediately persist to Database
        $liveResult = self::fetchLiveMarketRates();
        if ($liveResult !== null) {
            $rates = $liveResult['rates'];
            $source = $liveResult['source'];
            $lastSyncAt = now()->toIso8601String();
            self::persistSnapshotToDatabase($rates, $source, $lastSyncAt);
        }

        // 3. Apply explicit Admin Override (`usd_to_idr_rate`) if configured in CmsGlobalSetting
        try {
            $overrideUsd = CmsGlobalSetting::getVal('usd_to_idr_rate', null);
            if ($overrideUsd && is_numeric($overrideUsd) && (float) $overrideUsd > 10000) {
                $rates = self::scaleRatesByUsd($rates, (float) $overrideUsd);
                $source = 'Admin Configured FX Rate Override';
            }
        } catch (\Throwable) {
            // Ignore DB error during early boot/migrations
        }

        return [
            'base' => 'IDR',
            'source' => $source,
            'last_realtime_sync_at' => $lastSyncAt ?? now()->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
            'formatted_date' => now()->format('d M Y, H:i') . ' WIB',
            'rates' => $rates,
            'rates_to_idr' => $rates,
            'symbols' => self::CURRENCY_SYMBOLS,
            'names' => self::CURRENCY_NAMES,
        ];
    }

    /**
     * Load the last known real-time FX snapshot from CmsGlobalSetting database table.
     */
    public static function loadLastKnownDatabaseRates(): ?array
    {
        try {
            $snapshot = CmsGlobalSetting::getVal(self::DB_SNAPSHOT_KEY, null);
            if (is_array($snapshot) && !empty($snapshot['rates']) && is_array($snapshot['rates'])) {
                $savedUsd = (float) ($snapshot['rates']['USD'] ?? 0);
                if ($savedUsd > 10000) {
                    $mergedRates = self::DEFAULT_RATES;
                    foreach (array_keys(self::DEFAULT_RATES) as $code) {
                        if ($code === 'IDR') {
                            $mergedRates['IDR'] = 1.0;
                            continue;
                        }
                        if (!empty($snapshot['rates'][$code]) && (float) $snapshot['rates'][$code] > 0) {
                            $mergedRates[$code] = round((float) $snapshot['rates'][$code], 2);
                        }
                    }

                    return [
                        'rates' => $mergedRates,
                        'source' => 'Database Last-Known Real-Time FX',
                        'updated_at' => $snapshot['updated_at'] ?? null,
                    ];
                }
            }

            $lastUsd = CmsGlobalSetting::getVal(self::DB_LAST_USD_KEY, null);
            if (is_numeric($lastUsd) && (float) $lastUsd > 10000) {
                return [
                    'rates' => self::scaleRatesByUsd(self::DEFAULT_RATES, (float) $lastUsd),
                    'source' => 'Database Last-Known Real-Time USD Baseline',
                    'updated_at' => null,
                ];
            }
        } catch (\Throwable) {
            // Database not ready or migrating
        }

        return null;
    }

    /**
     * Persist the latest real-time FX snapshot and USD rate into CmsGlobalSetting database.
     */
    public static function persistSnapshotToDatabase(array $rates, string $source = 'Real-Time Open FX Market Feed', ?string $timestamp = null): void
    {
        try {
            $usdRate = round((float) ($rates['USD'] ?? 0), 2);
            if ($usdRate <= 10000) {
                return;
            }

            $syncTime = $timestamp ?? now()->toIso8601String();
            CmsGlobalSetting::setVal(self::DB_LAST_USD_KEY, $usdRate);
            CmsGlobalSetting::setVal(self::DB_SNAPSHOT_KEY, [
                'rates' => $rates,
                'usd_to_idr' => $usdRate,
                'source' => $source,
                'updated_at' => $syncTime,
            ]);
        } catch (\Throwable $e) {
            Log::debug('Could not persist FX snapshot to database: ' . $e->getMessage());
        }
    }

    /**
     * Proportionally scale multi-currency rates when only a single USD->IDR rate is provided.
     */
    protected static function scaleRatesByUsd(array $baseRates, float $targetUsdRate): array
    {
        $currentUsd = (float) ($baseRates['USD'] ?? self::DEFAULT_RATES['USD']);
        $ratio = $currentUsd > 0 ? ($targetUsdRate / $currentUsd) : 1.0;

        $scaled = $baseRates;
        $scaled['IDR'] = 1.0;
        $scaled['USD'] = round($targetUsdRate, 2);

        foreach (['EUR', 'SGD', 'GBP', 'AUD', 'JPY'] as $code) {
            $baseVal = (float) ($baseRates[$code] ?? self::DEFAULT_RATES[$code]);
            $scaled[$code] = round($baseVal * $ratio, 2);
        }

        return $scaled;
    }

    /**
     * Fetch live exchange rates from primary and backup public FX feeds.
     */
    protected static function fetchLiveMarketRates(): ?array
    {
        // Primary Feed: Open ER API
        try {
            $response = Http::timeout(2.5)->get('https://open.er-api.com/v6/latest/USD');
            if (!$response->successful() && app()->environment(['local', 'testing'])) {
                $response = Http::withoutVerifying()->timeout(2.5)->get('https://open.er-api.com/v6/latest/USD');
            }
            if ($response->successful()) {
                $parsed = self::parseUsdBaseApiRates($response->json('rates') ?? []);
                if ($parsed !== null) {
                    return [
                        'rates' => $parsed,
                        'source' => 'Real-Time Open FX Market Feed',
                    ];
                }
            }
        } catch (\Throwable $e) {
            if (app()->environment(['local', 'testing'])) {
                try {
                    $retry = Http::withoutVerifying()->timeout(2.5)->get('https://open.er-api.com/v6/latest/USD');
                    if ($retry->successful()) {
                        $parsed = self::parseUsdBaseApiRates($retry->json('rates') ?? []);
                        if ($parsed !== null) {
                            return [
                                'rates' => $parsed,
                                'source' => 'Real-Time Open FX Market Feed',
                            ];
                        }
                    }
                } catch (\Throwable) {
                }
            }
            Log::info('Primary FX feed fallback triggered: ' . $e->getMessage());
        }

        // Secondary Backup Feed: Fawazahmed0 Currency API via JSDelivr Global CDN
        try {
            $backup = Http::timeout(2.5)->get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');
            if ($backup->successful()) {
                $usdMap = array_change_key_case((array) ($backup->json('usd') ?? []), CASE_UPPER);
                $parsed = self::parseUsdBaseApiRates($usdMap);
                if ($parsed !== null) {
                    return [
                        'rates' => $parsed,
                        'source' => 'Real-Time Global CDN FX Feed',
                    ];
                }
            }
        } catch (\Throwable) {
            // Both live feeds unreachable; caller will use Database Last-Known Real-Time FX Snapshot
        }

        return null;
    }

    /**
     * Convert a USD-base rates table (`['IDR' => 16250, 'EUR' => 0.92, ...]`) into IDR-per-unit table.
     */
    protected static function parseUsdBaseApiRates(array $apiRates): ?array
    {
        $idrPerUsd = (float) ($apiRates['IDR'] ?? 0);
        if ($idrPerUsd <= 10000) {
            return null;
        }

        $rates = self::DEFAULT_RATES;
        $rates['IDR'] = 1.0;
        $rates['USD'] = round($idrPerUsd, 2);

        foreach (['EUR', 'SGD', 'GBP', 'AUD', 'JPY'] as $code) {
            if (!empty($apiRates[$code]) && (float) $apiRates[$code] > 0) {
                $rates[$code] = round($idrPerUsd / (float) $apiRates[$code], 2);
            }
        }

        return $rates;
    }

    /**
     * Get exchange rate for a single currency against IDR (How many IDR per 1 Unit).
     */
    public static function getRate(string $currency): float
    {
        $currency = strtoupper(trim($currency));
        if ($currency === 'IDR' || empty($currency)) {
            return 1.0;
        }

        $all = self::getAllRates();
        return (float) ($all['rates'][$currency] ?? (self::DEFAULT_RATES[$currency] ?? 16250.0));
    }

    /**
     * Convert an IDR amount into a target currency.
     */
    public static function convert(float $idrAmount, string $targetCurrency = 'IDR'): array
    {
        $targetCurrency = strtoupper(trim($targetCurrency));
        $rate = self::getRate($targetCurrency);
        $symbol = self::CURRENCY_SYMBOLS[$targetCurrency] ?? $targetCurrency;

        if ($targetCurrency === 'IDR' || $rate <= 0) {
            $roundedIdr = (int) round($idrAmount);

            return [
                'currency' => 'IDR',
                'symbol' => 'Rp',
                'rate' => 1.0,
                'rate_to_idr' => 1.0,
                'idr_amount' => $idrAmount,
                'converted_amount' => $idrAmount,
                'foreign_amount' => $idrAmount,
                'rounded_amount' => $roundedIdr,
                'formatted' => 'Rp ' . number_format($idrAmount, 0, ',', '.'),
                'formatted_exact' => 'Rp ' . number_format($idrAmount, 0, ',', '.'),
                'formatted_short' => 'Rp ' . number_format($idrAmount, 0, ',', '.'),
            ];
        }

        $converted = $idrAmount / $rate;
        $exactDecimals = in_array($targetCurrency, ['JPY'], true) ? 0 : 2;
        $roundedForeign = $idrAmount > 0 ? max(1, (int) round($converted)) : 0;

        return [
            'currency' => $targetCurrency,
            'symbol' => $symbol,
            'rate' => $rate,
            'rate_to_idr' => $rate,
            'idr_amount' => $idrAmount,
            'converted_amount' => round($converted, $exactDecimals),
            'foreign_amount' => round($converted, $exactDecimals),
            'rounded_amount' => $roundedForeign,
            'formatted' => $symbol . ' ' . number_format($roundedForeign, 0, '.', ','),
            'formatted_exact' => $symbol . ' ' . number_format($converted, $exactDecimals, '.', ','),
            'formatted_with_idr' => $symbol . ' ' . number_format($roundedForeign, 0, '.', ',') . ' (Rp ' . number_format($idrAmount, 0, ',', '.') . ')',
        ];
    }

    /**
     * Strict thousand separator formatter for IDR amounts.
     */
    public static function formatIdr(float|int|string $val, string $locale = 'id'): string
    {
        $numeric = is_numeric($val) ? (float) $val : (float) preg_replace('/[^\d]/', '', (string) $val);
        $thousandSep = $locale === 'id' ? '.' : ',';
        $decSep = $locale === 'id' ? ',' : '.';

        return 'Rp ' . number_format($numeric, 0, $decSep, $thousandSep);
    }
}
