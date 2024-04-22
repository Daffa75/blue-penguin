<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Widgets\FinalProjectLecturerGraph;
use Filament\Pages\Page;

class LecturerGraph extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.admin.pages.final-project-graph';
    protected static ?string $title = '';
    public static function getNavigationLabel(): string
    {
        return __('Mentoring Chart');
    }

    protected static ?string $navigationGroup = 'Statistics';
    public static function getNavigationGroup(): ?string
    {
        return __('Statistics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FinalProjectLecturerGraph::class,
        ];
    }
}
