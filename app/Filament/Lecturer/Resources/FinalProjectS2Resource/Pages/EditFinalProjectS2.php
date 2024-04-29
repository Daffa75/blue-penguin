<?php

namespace App\Filament\Lecturer\Resources\FinalProjectS2Resource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinalProjectS2 extends EditRecord
{
    protected static string $resource = FinalProjectS2Resource::class;
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
