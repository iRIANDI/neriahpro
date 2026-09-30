<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ProjectOsComplianceWidget extends Widget
{
    protected string $view = 'filament.widgets.project-os-compliance-widget';

    protected static ?int $sort = -10;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
}
