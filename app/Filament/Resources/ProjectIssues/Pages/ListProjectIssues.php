<?php

namespace App\Filament\Resources\ProjectIssues\Pages;

use App\Filament\Resources\ProjectIssues\ProjectIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectIssues extends ListRecords
{
    protected static string $resource = ProjectIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
