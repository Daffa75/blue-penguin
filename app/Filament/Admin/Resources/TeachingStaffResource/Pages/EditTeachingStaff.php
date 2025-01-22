<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Pages;

use App\Filament\Admin\Resources\TeachingStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeachingStaff extends EditRecord
{
    protected static string $resource = TeachingStaffResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
