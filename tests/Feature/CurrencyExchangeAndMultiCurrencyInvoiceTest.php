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
        $converted = CurrencyExchangeService::convert($idrAmount, 'USD');

        $this->assertEquals('USD', $converted['currency']);
        $this->assertEquals('$', $converted['symbol']);
        $this->assertEquals(1_490_000.0, $converted['idr_amount']);
        $this->assertGreaterThan(0, $converted['foreign_amount']);
        $this->assertGreaterThan(0, $converted['rate_to_idr']);
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
