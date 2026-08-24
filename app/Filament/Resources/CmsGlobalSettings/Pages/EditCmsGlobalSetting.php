<?php

namespace App\Filament\Resources\CmsGlobalSettings\Pages;

use App\Filament\Resources\CmsGlobalSettings\CmsGlobalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCmsGlobalSetting extends EditRecord
{
    protected static string $resource = CmsGlobalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $value = $data['value'] ?? null;

        if (empty($value)) {
            $data['value'] = [];
            return $data;
        }

        if (is_array($value)) {
            $first = reset($value);
            if (is_array($first) && isset($first['type'])) {
                return $data; // Already a valid Builder array
            }
            
            // Legacy key-value array
            $data['value'] = [
                [
                    'type' => 'properties',
                    'data' => ['data' => $value],
                ]
            ];
            return $data;
        }

        // Fallback for non-array legacy data (e.g. string or true)
        $data['value'] = [
            [
                'type' => 'properties',
                'data' => ['data' => ['legacy_value' => (string) $value]],
            ]
        ];

        return $data;
    }
}
