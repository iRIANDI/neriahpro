<?php

namespace App\Filament\Resources\InterviewSessions\Pages;

use App\Filament\Resources\InterviewSessions\InterviewSessionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInterviewSession extends ViewRecord
{
    protected static string $resource = InterviewSessionResource::class;

    protected static ?string $title = 'Detail Sesi Wawancara AI';
}
