<?php

namespace Tests\Feature;

use App\Models\CmsGlobalSetting;
use App\Services\Currency\CurrencyExchangeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CurrencyExchangeAndMultiCurrencyInvoiceTest extends TestCase
{
    public function test_currency_exchange_service_returns_supported_currencies(): void
    {
        $rates = CurrencyExchangeService::getAllRates();

        $this->assertEquals('IDR', $rates['base']);
        $this->assertArrayHasKey('rates_to_idr', $rates);
        $this->assertArrayHasKey('symbols', $rates);

        $expectedCurrencies = ['IDR', 'USD', 'EUR', 'SGD', 'GBP', 'AUD', 'JPY'];
        foreach ($expectedCurrencies as $currency) {
            $this->assertArrayHasKey($currency, $rates['rates_to_idr']);
            $this->assertGreaterThan(0, $rates['rates_to_idr'][$currency]);
            $this->assertArrayHasKey($currency, $rates['symbols']);
        }
    }

    public function test_currency_conversion_calculates_correct_foreign_amount(): void
    {
        $idrAmount = 1_490_000.0;
        $convertedUsd = CurrencyExchangeService::convert($idrAmount, 'USD');

        $this->assertEquals('USD', $convertedUsd['currency']);
        $this->assertEquals('$', $convertedUsd['symbol']);
        $this->assertEquals(1_490_000.0, $convertedUsd['idr_amount']);
        $this->assertGreaterThan(0, $convertedUsd['foreign_amount']);
        $this->assertIsInt($convertedUsd['rounded_amount']);
        $this->assertEquals((int) round($convertedUsd['foreign_amount']), $convertedUsd['rounded_amount']);
        $this->assertStringNotContainsString('.', $convertedUsd['formatted']);
        $this->assertArrayHasKey('formatted_exact', $convertedUsd);
        $this->assertGreaterThan(0, $convertedUsd['rate_to_idr']);

        // Test EUR conversion specifically for German/European localization
        $convertedEur = CurrencyExchangeService::convert($idrAmount, 'EUR');
        $this->assertEquals('EUR', $convertedEur['currency']);
        $this->assertEquals('€', $convertedEur['symbol']);
        $this->assertGreaterThan(0, $convertedEur['foreign_amount']);
        $this->assertIsInt($convertedEur['rounded_amount']);
        $this->assertGreaterThan(0, $convertedEur['rate_to_idr']);
    }

    public function test_currency_rates_api_endpoint_returns_json(): void
    {
        $response = $this->getJson(route('api.currency.rates'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'base',
            'rates_to_idr' => [
                'IDR', 'USD', 'EUR', 'SGD', 'GBP', 'AUD', 'JPY'
            ],
            'symbols'
        ]);
        $this->assertTrue($response->json('success'));
    }

    public function test_live_fx_rates_persist_to_database_and_act_as_offline_fallback(): void
    {
        if (! Schema::hasTable('cms_global_settings')) {
            Schema::create('cms_global_settings', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->string('key')->unique();
                $table->json('value')->nullable();
                $table->timestamps();
            });
        }

        CmsGlobalSetting::purgeRuntimeCache();
        Cache::forget(CurrencyExchangeService::CACHE_KEY);

        // 1. Simulate live FX API returning 1 USD = Rp 16,412.75 on first call, then going offline (500) on subsequent calls
        Http::fake([
            'open.er-api.com/*' => Http::sequence()
                ->push([
                    'result' => 'success',
                    'rates' => [
                        'IDR' => 16412.75,
                        'EUR' => 0.92,
                        'SGD' => 1.34,
                        'GBP' => 0.78,
                        'AUD' => 1.52,
                        'JPY' => 154.5,
                    ],
                ], 200)
                ->push([], 500)
                ->push([], 500),
            'cdn.jsdelivr.net/*' => Http::response([], 500),
        ]);

        $synced = CurrencyExchangeService::syncLiveRatesToDatabase();
        $this->assertEquals(16412.75, $synced['rates']['USD']);
        $this->assertEquals(16412.75, (float) CmsGlobalSetting::getVal(CurrencyExchangeService::DB_LAST_USD_KEY));

        $dbSnapshot = CmsGlobalSetting::getVal(CurrencyExchangeService::DB_SNAPSHOT_KEY);
        $this->assertIsArray($dbSnapshot);
        $this->assertEquals(16412.75, $dbSnapshot['rates']['USD']);

        // 2. Simulate cache expiring while external FX APIs are offline (500 error)
        Cache::forget(CurrencyExchangeService::CACHE_KEY);

        $fallbackResult = CurrencyExchangeService::getAllRates();

        // Must use the last real-time USD rate stored in database (16412.75), NOT jump back to static baseline!
        $this->assertEquals(16412.75, $fallbackResult['rates']['USD']);
        $this->assertEquals('Database Last-Known Real-Time FX', $fallbackResult['source']);
    }
}

