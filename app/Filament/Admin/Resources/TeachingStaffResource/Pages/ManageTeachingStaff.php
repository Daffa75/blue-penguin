<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Pages;

use App\Filament\Admin\Resources\TeachingStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTeachingStaff extends ManageRecords
{
    protected static string $resource = TeachingStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
