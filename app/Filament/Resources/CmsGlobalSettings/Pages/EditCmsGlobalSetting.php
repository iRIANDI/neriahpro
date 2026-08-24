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
        $key = $data['key'] ?? '';
        $value = $data['value'] ?? [];
        
        // Initialize all virtual fields to prevent hydration issues
        $data['navigation_value'] = [];
        $data['footer_value'] = [];
        $data['schema_value'] = '';
        $data['properties_value'] = [];
        $data['timezone_value'] = 'UTC';
        
        // Helper to check if an array is a valid Builder block array
        $isBuilderArray = function($val) {
            if (!is_array($val) || empty($val)) return false;
            
            // Filament Builder expects a sequential array (list) of blocks, or string keys (UUIDs)
            // But fundamentally, EVERY item must be an array with a 'type' key.
            foreach ($val as $item) {
                if (!is_array($item) || !isset($item['type'])) {
                    return false;
                }
            }
            return true;
        };

        if ($key === 'navigation_format') {
            $data['navigation_value'] = $isBuilderArray($value) ? $value : [];
        } elseif (in_array($key, ['footer_format', 'footer_links'])) {
            $data['footer_value'] = $isBuilderArray($value) ? $value : [];
        } elseif ($key === 'schema_org_jsonld') {
            $data['schema_value'] = is_string($value) ? $value : (is_array($value) ? json_encode($value) : '');
        } elseif ($key === 'app_timezone') {
            $data['timezone_value'] = is_string($value) ? $value : 'UTC';
        } else {
            // For other legacy keys, wrap in a properties block if it's not already a builder
            if (empty($value)) {
                $data['properties_value'] = [];
            } elseif ($isBuilderArray($value)) {
                $data['properties_value'] = $value;
            } elseif (is_array($value)) {
                $data['properties_value'] = [['type' => 'properties', 'data' => ['data' => $value]]];
            } else {
                $data['properties_value'] = [['type' => 'properties', 'data' => ['data' => ['legacy_value' => (string) $value]]]];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $key = $data['key'] ?? '';
        
        if ($key === 'navigation_format') {
            $data['value'] = $data['navigation_value'] ?? [];
        } elseif (in_array($key, ['footer_format', 'footer_links'])) {
            $data['value'] = $data['footer_value'] ?? [];
        } elseif ($key === 'schema_org_jsonld') {
            $data['value'] = $data['schema_value'] ?? '';
        } elseif ($key === 'app_timezone') {
            $data['value'] = $data['timezone_value'] ?? 'UTC';
        } else {
            $data['value'] = $data['properties_value'] ?? [];
        }
        
        unset($data['navigation_value'], $data['footer_value'], $data['schema_value'], $data['properties_value'], $data['timezone_value']);
        
        return $data;
    }
}
