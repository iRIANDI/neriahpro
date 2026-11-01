<?php

declare(strict_types=1);

namespace App\Filament\Resources\PosVerifiedTestimonials\Pages;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Resources\PosVerifiedTestimonials\PosVerifiedTestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosVerifiedTestimonial extends CreateRecord
{
    use HasTranslatableTabs;

    protected static string $resource = PosVerifiedTestimonialResource::class;
}
