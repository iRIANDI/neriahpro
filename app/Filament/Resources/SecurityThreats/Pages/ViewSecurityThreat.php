<?php

namespace App\Filament\Resources\SecurityThreats\Pages;

use App\Filament\Resources\SecurityThreats\SecurityThreatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSecurityThreat extends ViewRecord
{
    protected static string $resource = SecurityThreatResource::class;

    protected static ?string $title = 'Forensik Log Percobaan Serangan';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
