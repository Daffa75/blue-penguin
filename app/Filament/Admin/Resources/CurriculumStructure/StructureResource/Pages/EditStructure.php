<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\StructureResource\Pages;

use App\Filament\Admin\Resources\CurriculumStructure\StructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStructure extends EditRecord
{
    protected static string $resource = StructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}