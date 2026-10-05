<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FrontpageQuickLaunchWidget extends Widget
{
    protected string $view = 'filament.widgets.frontpage-quick-launch-widget';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
}
