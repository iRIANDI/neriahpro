<?php

namespace App\Filament\Resources\DomainHostingAssets\Pages;

use App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDomainHostingAsset extends EditRecord
{
    protected static string $resource = DomainHostingAssetResource::class;

    protected static ?string $title = 'Edit Aset Domain & Hosting';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
