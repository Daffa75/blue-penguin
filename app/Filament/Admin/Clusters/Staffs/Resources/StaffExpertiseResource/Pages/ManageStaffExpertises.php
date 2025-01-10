<?php

namespace App\Filament\Admin\Clusters\Staffs\Resources\StaffExpertiseResource\Pages;

use App\Filament\Admin\Clusters\Staffs\Resources\StaffExpertiseResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStaffExpertises extends ManageRecords
{
    protected static string $resource = StaffExpertiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
