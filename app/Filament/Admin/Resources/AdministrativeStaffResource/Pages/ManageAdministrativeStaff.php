<?php

namespace App\Filament\Admin\Resources\AdministrativeStaffResource\Pages;

use App\Filament\Admin\Resources\AdministrativeStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAdministrativeStaff extends ManageRecords
{
    protected static string $resource = AdministrativeStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
