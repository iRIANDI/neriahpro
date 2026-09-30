<?php

namespace App\Filament\Resources\EmailCampaigns\Pages;

use App\Filament\Resources\EmailCampaigns\EmailCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmailCampaigns extends ListRecords
{
    protected static string $resource = EmailCampaignResource::class;

    protected ?string $heading = 'Kampanye Email & Promosi Klien';

    protected ?string $subheading = 'Kelola siaran promosi, buletin penawaran khusus, dan pengingat layanan secara terpusat.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Kampanye Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
