<?php

namespace App\Filament\Resources\SecurityThreats\Pages;

use App\Filament\Resources\SecurityThreats\SecurityThreatResource;
use App\Filament\Resources\SecurityThreats\Widgets\SecurityThreatStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListSecurityThreats extends ListRecords
{
    protected static string $resource = SecurityThreatResource::class;

    protected ?string $heading = 'AI Threat Shield & Security Firewall';

    protected ?string $subheading = 'Pemantauan real-time terhadap percobaan Remote Code Execution (RCE), Unsafe Deserialization, dan AI Probing Anomaly.';

    protected function getHeaderWidgets(): array
    {
        return [
            SecurityThreatStatsWidget::class,
        ];
    }
}
