<?php

namespace App\Filament\Resources\CmsGlobalSettings\Pages;

use App\Filament\Resources\CmsGlobalSettings\CmsGlobalSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCmsGlobalSettings extends ListRecords
{
    protected static string $resource = CmsGlobalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
