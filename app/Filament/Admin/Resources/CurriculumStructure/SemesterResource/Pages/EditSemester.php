<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\SemesterResource\Pages;

use App\Filament\Admin\Resources\CurriculumStructure\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSemester extends EditRecord
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
