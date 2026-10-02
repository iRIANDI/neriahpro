<?php

namespace App\Filament\Resources\LeadContacts\Pages;

use App\Filament\Resources\LeadContacts\LeadContactResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeadContact extends EditRecord
{
    protected static string $resource = LeadContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
