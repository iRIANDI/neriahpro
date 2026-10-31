<?php

namespace App\Filament\Resources\EmailTemplates\Pages;

use App\Filament\Resources\EmailTemplates\EmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailTemplate extends CreateRecord
{
    use \App\Filament\Concerns\HasTranslatableTabs;

    protected static string $resource = EmailTemplateResource::class;
}
