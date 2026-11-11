<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('currency:sync-rates', function () {
    $result = \App\Services\Currency\CurrencyExchangeService::syncLiveRatesToDatabase();
    $usdRate = number_format((float) ($result['rates']['USD'] ?? 0), 2, ',', '.');
    $this->info("[CurrencyExchangeService] Synced FX rates ({$result['source']}) -> 1 USD = Rp {$usdRate}");
})->purpose('Fetch live FX exchange rates and persist last-known real-time USD/multi-currency snapshot into CmsGlobalSetting database');

Schedule::command('assets:check-expirations --days=30')->dailyAt('08:00');
Schedule::command('currency:sync-rates')->hourly()->withoutOverlapping();

