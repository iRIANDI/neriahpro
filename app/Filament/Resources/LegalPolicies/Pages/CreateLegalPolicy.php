<?php

namespace App\Filament\Resources\LegalPolicies\Pages;

use App\Filament\Resources\LegalPolicies\LegalPolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalPolicy extends CreateRecord
{
    use \App\Filament\Concerns\HasTranslatableTabs;

    protected static string $resource = LegalPolicyResource::class;
}
