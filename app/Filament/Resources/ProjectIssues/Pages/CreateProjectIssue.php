<?php

namespace App\Filament\Resources\ProjectIssues\Pages;

use App\Filament\Resources\ProjectIssues\ProjectIssueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectIssue extends CreateRecord
{
    use \App\Filament\Concerns\HasTranslatableTabs;

    protected static string $resource = ProjectIssueResource::class;
}
