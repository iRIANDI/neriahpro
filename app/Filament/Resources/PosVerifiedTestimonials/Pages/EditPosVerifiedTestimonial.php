<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosVerifiedTestimonials\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosVerifiedTestimonials\PosVerifiedTestimonialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPosVerifiedTestimonial extends EditRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosVerifiedTestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
