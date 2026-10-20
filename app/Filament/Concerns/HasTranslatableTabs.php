<?php

namespace App\Filament\Concerns;

use App\Services\Translation\GoogleTranslateService;

trait HasTranslatableTabs
{
    /**
     * List of field keys that should be auto-translated if empty.
     * Can be overridden in the implementing page class.
     *
     * @return array<string>
     */
    protected function getTranslatableFields(): array
    {
        return ['name', 'title', 'description', 'content', 'label', 'headline', 'subtitle'];
    }

    /**
     * Mutate form data before saving to automatically translate missing locale values.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->autoTranslateMissingLocales($data);
    }

    /**
     * Mutate form data before creating to automatically translate missing locale values.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->autoTranslateMissingLocales($data);
    }

    /**
     * Auto translate missing locales between ID and EN.
     */
    protected function autoTranslateMissingLocales(array $data): array
    {
        $fields = $this->getTranslatableFields();

        foreach ($fields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $idVal = trim((string) ($data[$field]['id'] ?? ''));
                $enVal = trim((string) ($data[$field]['en'] ?? ''));

                // If ID is filled but EN is empty, auto-translate to EN
                if (!empty($idVal) && empty($enVal)) {
                    $data[$field]['en'] = GoogleTranslateService::idToEn($idVal);
                }
                // If EN is filled but ID is empty, auto-translate to ID
                elseif (!empty($enVal) && empty($idVal)) {
                    $data[$field]['id'] = GoogleTranslateService::enToId($enVal);
                }
            }
        }

        return $data;
    }
}
