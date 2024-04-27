<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Pages;

use App\Filament\Admin\Resources\DepartmentEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Spatie\CalendarLinks\Link;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

class ViewDepartmentEvent extends ViewRecord
{
    protected static string $resource = DepartmentEventResource::class;

    protected function getActions(): array
    {
        return [
            CopyAction::make()->copyable(function () {
                $description = "Link: " . $this->record->url . "\n" . $this->record->description;

                    return Link::create(
                        $this->record->title,
                        $this->record->start->timezone('Asia/Makassar'),
                        $this->record->end
                    )
                        ->description($description)
                        ->google();
            })
            ->label(__('Share Event'))
            ->icon('heroicon-m-share'),

        ];
    }
}
