<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosSectors\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosSectors\PosSectorResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosSector extends CreateRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosSectorResource::class;
}

