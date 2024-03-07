<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Pages;

use App\Filament\Admin\Resources\TeachingStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeachingStaff extends ViewRecord
{
    protected static string $resource = TeachingStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
