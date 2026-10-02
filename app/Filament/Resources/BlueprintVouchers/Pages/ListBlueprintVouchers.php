<?php

namespace App\Filament\Resources\BlueprintVouchers\Pages;

use App\Filament\Resources\BlueprintVouchers\BlueprintVoucherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlueprintVouchers extends ListRecords
{
    protected static string $resource = BlueprintVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
