<?php

namespace App\Filament\Resources\VisionBlueprints\Pages;

use App\Filament\Resources\VisionBlueprints\VisionBlueprintResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisionBlueprints extends ListRecords
{
    protected static string $resource = VisionBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
