<?php

namespace App\Filament\Resources\VisionBlueprints\Pages;

use App\Filament\Resources\VisionBlueprints\VisionBlueprintResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVisionBlueprint extends EditRecord
{
    protected static string $resource = VisionBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
