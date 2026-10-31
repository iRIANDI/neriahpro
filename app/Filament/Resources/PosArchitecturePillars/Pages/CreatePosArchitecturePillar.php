<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosArchitecturePillars\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosArchitecturePillars\PosArchitecturePillarResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosArchitecturePillar extends CreateRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosArchitecturePillarResource::class;
}

