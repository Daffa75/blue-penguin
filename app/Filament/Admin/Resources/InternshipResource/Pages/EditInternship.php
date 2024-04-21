<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternship extends EditRecord
{
    protected static string $resource = InternshipResource::class;

    public static function getNavigationLabel(): string
    {
        return __("Edit Internship");
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
