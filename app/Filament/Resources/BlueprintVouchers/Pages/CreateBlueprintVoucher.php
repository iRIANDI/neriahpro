<?php

namespace App\Filament\Resources\BlueprintVouchers\Pages;

use App\Filament\Resources\BlueprintVouchers\BlueprintVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlueprintVoucher extends CreateRecord
{
    protected static string $resource = BlueprintVoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));
        $data['created_by'] = auth()->user()?->email;
        return $data;
    }
}
