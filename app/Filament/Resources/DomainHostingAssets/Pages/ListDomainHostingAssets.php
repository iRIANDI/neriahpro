<?php

namespace App\Filament\Resources\DomainHostingAssets\Pages;

use App\Filament\Resources\DomainHostingAssets\DomainHostingAssetResource;
use App\Filament\Resources\DomainHostingAssets\Widgets\DomainHostingStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDomainHostingAssets extends ListRecords
{
    protected static string $resource = DomainHostingAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Aset Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DomainHostingStatsWidget::class,
        ];
    }
}
