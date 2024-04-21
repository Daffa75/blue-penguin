<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInternship extends ViewRecord
{
    protected static string $resource = InternshipResource::class;

    public static function getNavigationLabel(): string
    {
        return __("View Internship");
    }
}
