<?php

namespace App\Filament\Resources\VisionBlueprints\Pages;

use App\Filament\Resources\VisionBlueprints\VisionBlueprintResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVisionBlueprint extends ViewRecord
{
    protected static string $resource = VisionBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
