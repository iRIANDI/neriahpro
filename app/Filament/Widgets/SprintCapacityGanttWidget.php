<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SprintTimelinePage;
use App\Models\VisionBlueprint;
use Filament\Widgets\Widget;

class SprintCapacityGanttWidget extends Widget
{
    protected string $view = 'filament.widgets.sprint-capacity-gantt-widget';

    protected static ?int $sort = -5;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $page = new SprintTimelinePage();
        $data = $page->getViewData();

        return [
            'batches' => $data['batches'],
            'totalMaxSlots' => $data['totalMaxSlots'],
            'totalAssignedSlots' => $data['totalAssignedSlots'],
            'remainingOverallSlots' => $data['remainingOverallSlots'],
            'activeSprintCount' => $data['activeSprintCount'],
            'timelineUrl' => url('/admin/sprint-timeline'),
        ];
    }
}
