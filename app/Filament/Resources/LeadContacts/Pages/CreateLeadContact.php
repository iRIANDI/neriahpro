<?php

namespace App\Filament\Resources\LeadContacts\Pages;

use App\Filament\Resources\LeadContacts\LeadContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadContact extends CreateRecord
{
    protected static string $resource = LeadContactResource::class;
}
