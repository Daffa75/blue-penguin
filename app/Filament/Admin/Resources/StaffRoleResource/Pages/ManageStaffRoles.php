<?php

namespace App\Filament\Admin\Resources\StaffRoleResource\Pages;

use App\Filament\Admin\Resources\StaffRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStaffRoles extends ManageRecords
{
    protected static string $resource = StaffRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
