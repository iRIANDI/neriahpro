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

    /**
     * Fallback standard market rates (IDR per 1 Foreign Currency Unit).
     */
    public const DEFAULT_RATES = [
        'IDR' => 1.0,
        'USD' => 16000.0,
        'EUR' => 17200.0,
        'SGD' => 12000.0,
        'GBP' => 20500.0,
        'AUD' => 10500.0,
        'JPY' => 105.0,
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
     */
    public static function getAllRates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $rates = self::DEFAULT_RATES;
            $source = 'Standard Market Baseline';

            // Check database overrides first
            try {
                $overrideUsd = CmsGlobalSetting::getVal('usd_to_idr_rate', null);
                if ($overrideUsd && (float) $overrideUsd > 1000) {
                    $rates['USD'] = (float) $overrideUsd;
                }
            } catch (\Throwable) {
                // Ignore DB error during early boot/migrations
            }

            // Attempt fetching live real-time rate feed (Graceful 2.5s timeout)
            try {
                $response = Http::timeout(2.5)->get('https://open.er-api.com/v6/latest/USD');
                if ($response->successful()) {
                    $apiData = $response->json();
                    $apiRates = $apiData['rates'] ?? [];
                    $idrPerUsd = (float) ($apiRates['IDR'] ?? 0);

                    if ($idrPerUsd > 10000) {
                        $rates['USD'] = round($idrPerUsd, 2);
                        if (!empty($apiRates['EUR']) && $apiRates['EUR'] > 0) {
                            $rates['EUR'] = round($idrPerUsd / (float) $apiRates['EUR'], 2);
                        }
                        if (!empty($apiRates['SGD']) && $apiRates['SGD'] > 0) {
                            $rates['SGD'] = round($idrPerUsd / (float) $apiRates['SGD'], 2);
                        }
                        if (!empty($apiRates['GBP']) && $apiRates['GBP'] > 0) {
                            $rates['GBP'] = round($idrPerUsd / (float) $apiRates['GBP'], 2);
                        }
                        if (!empty($apiRates['AUD']) && $apiRates['AUD'] > 0) {
                            $rates['AUD'] = round($idrPerUsd / (float) $apiRates['AUD'], 2);
                        }
                        if (!empty($apiRates['JPY']) && $apiRates['JPY'] > 0) {
                            $rates['JPY'] = round($idrPerUsd / (float) $apiRates['JPY'], 2);
                        }
                        $source = 'Real-Time Open FX Market Feed';
                    }
                }
            } catch (\Throwable $e) {
                Log::info('Currency exchange live fetch fallback used: ' . $e->getMessage());
            }

            return [
                'base' => 'IDR',
                'source' => $source,
                'timestamp' => now()->toIso8601String(),
                'formatted_date' => now()->format('d M Y, H:i') . ' WIB',
                'rates' => $rates,
                'rates_to_idr' => $rates,
                'symbols' => self::CURRENCY_SYMBOLS,
                'names' => self::CURRENCY_NAMES,
            ];
        });
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
        return (float) ($all['rates'][$currency] ?? (self::DEFAULT_RATES[$currency] ?? 16000.0));
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
