<?php

namespace App\Filament\Resources\Resumes\Pages;

use App\Filament\Resources\Resumes\ResumeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResume extends ViewRecord
{
    protected static string $resource = ResumeResource::class;

    protected static ?string $title = 'Detail Dokumen Resume';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_public')
                ->label('Buka Tautan Publik')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('primary')
                ->url(fn ($record) => $record->public_url)
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
