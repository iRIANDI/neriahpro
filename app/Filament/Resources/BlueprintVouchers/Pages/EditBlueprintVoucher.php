<?php

namespace App\Filament\Resources\BlueprintVouchers\Pages;

use App\Filament\Resources\BlueprintVouchers\BlueprintVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlueprintVoucher extends EditRecord
{
    protected static string $resource = BlueprintVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['code'] = strtoupper(trim($data['code']));
        return $data;
    }
}
