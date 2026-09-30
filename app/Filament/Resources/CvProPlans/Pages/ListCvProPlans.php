<?php

namespace App\Filament\Resources\CvProPlans\Pages;

use App\Filament\Resources\CvProPlans\CvProPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCvProPlans extends ListRecords
{
    protected static string $resource = CvProPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
