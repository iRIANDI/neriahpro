<?php

namespace App\Filament\Resources\CmsPages\Pages;

use App\Filament\Resources\CmsPages\CmsPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCmsPage extends CreateRecord
{
    use \App\Filament\Concerns\HasTranslatableTabs;

    protected static string $resource = CmsPageResource::class;
}
