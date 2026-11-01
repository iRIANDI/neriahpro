<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosSectors\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosSectors\PosSectorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPosSector extends EditRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosSectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

