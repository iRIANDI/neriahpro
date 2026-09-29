<?php

namespace App\Filament\Resources\Resumes\Pages;

use App\Filament\Resources\Resumes\ResumeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditResume extends EditRecord
{
    protected static string $resource = ResumeResource::class;

    protected static ?string $title = 'Edit Resume';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
