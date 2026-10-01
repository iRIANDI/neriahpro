<?php

namespace App\Filament\Resources\VisionBlueprints\Pages;

use App\Filament\Resources\VisionBlueprints\VisionBlueprintResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVisionBlueprint extends EditRecord
{
    protected static string $resource = VisionBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('regenerate')
                ->label('Regenerate PRD')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->generateAndSavePrd();
                    \Filament\Notifications\Notification::make()
                        ->title('PRD Berhasil Digenerate Ulang')
                        ->success()
                        ->send();
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
