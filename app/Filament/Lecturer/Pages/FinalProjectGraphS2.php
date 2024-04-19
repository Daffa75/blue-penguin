<?php

namespace App\Filament\Lecturer\Pages;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource\Widgets;
use Filament\Pages\Page;

class FinalProjectGraphS2 extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.admin.pages.final-project-graph';
    protected static ?string $title = '';
    public static function getNavigationLabel(): string
    {
        return __('Final Project Master Chart');
    }

    protected static ?string $navigationGroup = 'Statistics';
    public static function getNavigationGroup(): ?string
    {
        return __('Statistics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\FinalProjectStudentS2Chart::class,
        ];
    }
}
