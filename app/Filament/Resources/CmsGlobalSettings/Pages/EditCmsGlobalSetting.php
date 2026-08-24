<?php

namespace App\Filament\Resources\CmsGlobalSettings\Pages;

use App\Filament\Resources\CmsGlobalSettings\CmsGlobalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCmsGlobalSetting extends EditRecord
{
    protected static string $resource = CmsGlobalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
