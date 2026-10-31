<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosArchitecturePillars\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosArchitecturePillars\PosArchitecturePillarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPosArchitecturePillar extends EditRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosArchitecturePillarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

