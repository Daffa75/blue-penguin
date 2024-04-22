<?php

namespace App\Filament\Lecturer\Resources\FinalProjectResource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinalProject extends EditRecord
{
    protected static string $resource = FinalProjectResource::class;

    public static function getNavigationLabel(): string
    {
        return __("Edit Final Project");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
