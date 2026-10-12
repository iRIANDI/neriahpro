<?php

namespace App\Filament\Resources\ProjectIssues\Pages;

use App\Filament\Resources\ProjectIssues\ProjectIssueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectIssue extends EditRecord
{
    protected static string $resource = ProjectIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
