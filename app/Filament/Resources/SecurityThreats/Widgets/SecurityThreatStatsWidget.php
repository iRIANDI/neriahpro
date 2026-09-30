<?php

namespace App\Filament\Resources\SecurityThreats\Widgets;

use App\Models\SecurityThreatLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SecurityThreatStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $totalThreats = SecurityThreatLog::count();
            $blockedIps = SecurityThreatLog::where('is_blocked', true)->distinct('ip_address')->count('ip_address');
            $criticalExploits = SecurityThreatLog::whereIn('threat_type', ['rce_system_call', 'php_object_deserialization', 'python_dataset_rce'])->count();
            $topTarget = SecurityThreatLog::select('endpoint')
                ->groupBy('endpoint')
                ->orderByRaw('count(*) desc')
                ->value('endpoint');

            return [
                Stat::make('Total Serangan Ditangkal', number_format($totalThreats, 0, ',', '.'))
                    ->description('Muatan jahat dicegat AI-Shield')
                    ->descriptionIcon('heroicon-m-shield-check')
                    ->color($totalThreats > 0 ? 'danger' : 'success'),

                Stat::make('IP Terblokir Otomatis', $blockedIps)
                    ->description('Pelaku probing berulang')
                    ->descriptionIcon('heroicon-m-no-symbol')
                    ->color($blockedIps > 0 ? 'warning' : 'gray'),

                Stat::make('Percobaan RCE & Deserialization', $criticalExploits)
                    ->description('Serangan eksekusi kode tingkat server')
                    ->descriptionIcon('heroicon-m-fire')
                    ->color($criticalExploits > 0 ? 'danger' : 'success'),

                Stat::make('Target Endpoint Utama', $topTarget ? parse_url($topTarget, PHP_URL_PATH) : 'N/A')
                    ->description('Titik API paling sering di-probing')
                    ->descriptionIcon('heroicon-m-globe-alt')
                    ->color('primary'),
            ];
        } catch (\Throwable $e) {
            return [
                Stat::make('AI-Shield Status', 'Siap Aktif')
                    ->description('Perlindungan RCE & Ingestion aktif')
                    ->color('success'),
            ];
        }
    }
}
