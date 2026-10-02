<?php

namespace App\Filament\Resources\LeadContacts\Pages;

use App\Filament\Resources\LeadContacts\LeadContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadContacts extends ListRecords
{
    protected static string $resource = LeadContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kontak Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
