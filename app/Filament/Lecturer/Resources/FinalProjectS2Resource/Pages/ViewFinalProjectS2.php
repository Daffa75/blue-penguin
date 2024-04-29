<?php

namespace App\Filament\Lecturer\Resources\FinalProjectS2Resource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinalProjectS2 extends ViewRecord
{
    protected static string $resource = FinalProjectS2Resource::class;

    public static function getNavigationLabel(): string
    {
        return __("View Final Project");
    }
}
