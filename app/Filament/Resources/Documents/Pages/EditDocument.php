<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status']) && $data['status'] === 'signed') {
            if (isset($data['digital_signature_image']) && !empty($data['digital_signature_image'])) {
                // If it's just signed, set the timestamp and hash
                if (empty($data['signed_at'])) {
                    $data['signed_at'] = now();
                }
                if (empty($data['document_hash'])) {
                    // Create a simple hash based on time, title, and signature string
                    $data['document_hash'] = hash('sha256', $data['title'] . $data['digital_signature_image'] . now()->timestamp . Str::random(10));
                }
            }
        }

        return $data;
    }
}
