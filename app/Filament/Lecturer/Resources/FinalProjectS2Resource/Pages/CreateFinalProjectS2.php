<?php

namespace App\Filament\Lecturer\Resources\FinalProjectS2Resource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateFinalProjectS2 extends CreateRecord
{
    protected static string $resource = FinalProjectS2Resource::class;
    public function getTitle(): string|Htmlable
    {
        return (__('Make Final Project'));
    }
}
