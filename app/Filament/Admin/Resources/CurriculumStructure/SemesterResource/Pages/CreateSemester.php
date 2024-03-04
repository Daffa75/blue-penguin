<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\SemesterResource\Pages;

use App\Filament\Admin\Resources\CurriculumStructure\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSemester extends CreateRecord
{
    protected static string $resource = SemesterResource::class;
}
