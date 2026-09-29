<?php

namespace App\Filament\Resources\DomainHostingAssets\Pages;

use App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDomainHostingAsset extends ViewRecord
{
    protected static string $resource = DomainHostingAssetResource::class;

    protected static ?string $title = 'Detail Aset Domain & Hosting';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
