<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Widgets;

use App\Filament\Admin\Resources\DepartmentEventResource;
use App\Models\DepartmentEvent;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{

    public function fetchEvents(array $fetchInfo): array
    {
        return DepartmentEvent::query()
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (DepartmentEvent $event) => [
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'url' => DepartmentEventResource::getUrl(name: 'view', parameters: ['record' => $event]),
                    'shouldOpenUrlInNewTab' => true
                ]
            )
            ->all();
    }
}


