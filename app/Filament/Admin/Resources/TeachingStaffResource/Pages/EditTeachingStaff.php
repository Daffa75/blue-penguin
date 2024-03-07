<?php

namespace App\Filament\Admin\Resources\TeachingStaffResource\Pages;

use App\Filament\Admin\Resources\TeachingStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeachingStaff extends EditRecord
{
    protected static string $resource = TeachingStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
