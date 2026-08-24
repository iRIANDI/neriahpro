<?php

namespace App\Filament\Resources\CmsGlobalSettings\Pages;

use App\Filament\Resources\CmsGlobalSettings\CmsGlobalSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCmsGlobalSetting extends CreateRecord
{
    protected static string $resource = CmsGlobalSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $key = $data['key'] ?? '';
        
        if ($key === 'navigation_format') {
            $data['value'] = $data['navigation_value'] ?? [];
        } elseif (in_array($key, ['footer_format', 'footer_links'])) {
            $data['value'] = $data['footer_value'] ?? [];
        } elseif ($key === 'schema_org_jsonld') {
            $data['value'] = $data['schema_value'] ?? '';
        } else {
            $data['value'] = $data['properties_value'] ?? [];
        }
        
        unset($data['navigation_value'], $data['footer_value'], $data['schema_value'], $data['properties_value']);
        
        return $data;
    }
}
