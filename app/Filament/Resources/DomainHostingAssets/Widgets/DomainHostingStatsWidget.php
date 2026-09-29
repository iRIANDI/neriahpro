<?php

namespace App\Filament\Resources\DomainHostingAssets\Widgets;

use App\Models\DomainHostingAsset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DomainHostingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $totalActive = DomainHostingAsset::active()->count();
            $expiringSoon = DomainHostingAsset::expiringSoon(30)->count();
            $expired = DomainHostingAsset::expired()->count();
            $totalAnnualCost = DomainHostingAsset::active()->sum('cost_price');

            return [
                Stat::make('Aset Aktif Berjalan', $totalActive)
                    ->description('Domain, VPS, & Cloud Hosting')
                    ->descriptionIcon('heroicon-m-server-stack')
                    ->color('success'),

                Stat::make('Jatuh Tempo (<= 30 Hari)', $expiringSoon)
                    ->description($expiringSoon > 0 ? 'Perlu perpanjangan segera' : 'Semua aset aman terkendali')
                    ->descriptionIcon($expiringSoon > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                    ->color($expiringSoon > 0 ? 'warning' : 'gray'),

                Stat::make('Sudah Kadaluarsa', $expired)
                    ->description($expired > 0 ? 'Layanan berisiko ditangguhkan' : 'Nol aset kadaluarsa')
                    ->descriptionIcon($expired > 0 ? 'heroicon-m-x-circle' : 'heroicon-m-shield-check')
                    ->color($expired > 0 ? 'danger' : 'gray'),

                Stat::make('Total Pengeluaran Sewa', 'Rp ' . number_format($totalAnnualCost, 0, ',', '.'))
                    ->description('Estimasi beban modal operasional')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('primary'),
            ];
        } catch (\Throwable $e) {
            return [
                Stat::make('Aset Layanan', '0'),
            ];
        }
    }
}
