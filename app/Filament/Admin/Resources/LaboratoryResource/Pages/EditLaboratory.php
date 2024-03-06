<?php

namespace App\Filament\Admin\Resources\LaboratoryResource\Pages;

use App\Filament\Admin\Resources\LaboratoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaboratory extends EditRecord
{
    protected static string $resource = LaboratoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Edit Laboratory');
    }
}
