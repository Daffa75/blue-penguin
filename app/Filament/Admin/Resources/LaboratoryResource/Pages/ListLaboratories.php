<?php

namespace App\Filament\Admin\Resources\LaboratoryResource\Pages;

use App\Filament\Admin\Resources\LaboratoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaboratories extends ListRecords
{
    protected static string $resource = LaboratoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
