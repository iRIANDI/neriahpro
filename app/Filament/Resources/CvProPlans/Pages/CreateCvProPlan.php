<?php

namespace App\Filament\Resources\CvProPlans\Pages;

use App\Filament\Resources\CvProPlans\CvProPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCvProPlan extends CreateRecord
{
    use \App\Filament\Concerns\HasTranslatableTabs;

    protected static string $resource = CvProPlanResource::class;
}
