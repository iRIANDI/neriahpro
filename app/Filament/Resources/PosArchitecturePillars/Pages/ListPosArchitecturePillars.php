<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosArchitecturePillars\Pages;

use App\Filament\Resources\PosArchitecturePillars\PosArchitecturePillarResource;
use App\Support\PosRealWorldRuntimeCatalog;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosArchitecturePillars extends ListRecords
{
    protected static string $resource = PosArchitecturePillarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_canonical_15_pillars')
                ->label('Sinkronkan 15 Pilar Kanonik (100% Freeze)')
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->action(function (): void {
                    PosRealWorldRuntimeCatalog::seedFifteenArchitecturePillars();
                    Notification::make()
                        ->title('15 Pilar Project OS berhasil disinkronkan & dikunci (100% Freeze)')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

