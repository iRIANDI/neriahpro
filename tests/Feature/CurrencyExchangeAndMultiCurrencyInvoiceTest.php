<?php

namespace Tests\Feature;

use App\Services\Currency\CurrencyExchangeService;
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
}

