<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Widgets;

use App\Filament\Admin\Resources\DepartmentEventResource;
use App\Models\DepartmentEvent;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $panelId = Filament::getCurrentPanel()->getId();

        if ($panelId == 'admin') {
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
        } else {
            // Modify the query to filter events based on lecturer's nip
            return DepartmentEvent::query()
                ->where('start', '>=', $fetchInfo['start'])
                ->where('end', '<=', $fetchInfo['end'])
                ->whereHas('lecturers', function ($query) {
                    $query->where('nip', auth()->user()->lecturer?->nip);
                })
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
}
