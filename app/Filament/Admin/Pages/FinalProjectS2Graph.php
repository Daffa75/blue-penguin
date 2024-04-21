<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Widgets;
use Filament\Pages\Page;

class FinalProjectS2Graph extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.final-project-graph';
    protected static ?string $title = '';
    public static function getNavigationLabel(): string
    {
        return __('Final Project Master Graph');
    }

    protected static ?string $navigationGroup = 'Statistics';
    public static function getNavigationGroup(): ?string
    {
        return __('Statistics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\FinalProjectStudentS2ApexChart::class,
        ];
    }
}
