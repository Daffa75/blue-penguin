<?php

namespace App\Filament\Admin\Resources\CurriculumStructure\StructureResource\Pages;

use App\Filament\Admin\Resources\CurriculumStructure\StructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewStructure extends ViewRecord
{
    protected static string $resource = StructureResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var Post */
        $record = $this->getRecord();

        return $record->curriculum_name;
    }
}
