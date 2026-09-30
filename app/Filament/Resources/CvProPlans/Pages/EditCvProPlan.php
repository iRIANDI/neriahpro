<?php

namespace App\Filament\Resources\CvProPlans\Pages;

use App\Filament\Resources\CvProPlans\CvProPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCvProPlan extends EditRecord
{
    protected static string $resource = CvProPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
