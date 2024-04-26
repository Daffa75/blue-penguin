<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Pages;

use App\Filament\Admin\Resources\DepartmentEventResource;
use App\Filament\Admin\Resources\DepartmentEventResource\Widgets\CalendarWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartmentEvents extends ListRecords
{
    protected static string $resource = DepartmentEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }
}
