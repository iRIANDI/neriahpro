<?php

namespace App\Filament\Resources\DomainHostingAssets\Pages;

use App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainHostingAsset extends CreateRecord
{
    protected static string $resource = DomainHostingAssetResource::class;

    protected static ?string $title = 'Catat Aset Domain & Hosting Baru';
}
