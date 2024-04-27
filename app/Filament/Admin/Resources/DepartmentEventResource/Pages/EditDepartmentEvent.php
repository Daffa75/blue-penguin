<?php

namespace App\Filament\Admin\Resources\DepartmentEventResource\Pages;

use App\Filament\Admin\Resources\DepartmentEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartmentEvent extends EditRecord
{
    protected static string $resource = DepartmentEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
