<?php

namespace App\Filament\Resources\EmailCampaigns\Pages;

use App\Filament\Resources\EmailCampaigns\EmailCampaignResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailCampaign extends ViewRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
