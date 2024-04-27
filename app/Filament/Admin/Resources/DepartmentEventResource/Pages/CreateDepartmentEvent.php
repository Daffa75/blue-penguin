<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Pages;

use App\Filament\Admin\Resources\DepartmentEventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartmentEvent extends CreateRecord
{
    protected static string $resource = DepartmentEventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
