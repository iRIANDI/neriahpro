<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosSectors\Pages;

use App\Filament\Pages\ProjectOsMatrixPage;
use App\Filament\Resources\PosSectors\PosSectorResource;
use App\Support\ProjectOsMatrixCatalog;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosSectors extends ListRecords
{
    protected static string $resource = PosSectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_matrix_cockpit')
                ->label(fn (): string => app()->getLocale() === 'en' ? 'Open 12-Sector Matrix Cockpit' : 'Buka Kokpit Matriks 12 Sektor')
                ->icon('heroicon-o-squares-plus')
                ->color('gray')
                ->url(fn (): string => ProjectOsMatrixPage::getUrl()),

            Action::make('sync_12_sectors')
                ->label(fn (): string => app()->getLocale() === 'en' ? 'Sync Canonical 12 Sectors' : 'Sinkronkan 12 Sektor Kanonik')
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') || (bool) auth()->user()?->can('manage_project_os_matrix'))
                ->action(function (): void {
                    ProjectOsMatrixCatalog::ensureSeeded();
                    Notification::make()
                        ->title('12 Sektor Fungsional berhasil disinkronkan (Dual-Locale ID & EN)')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}

