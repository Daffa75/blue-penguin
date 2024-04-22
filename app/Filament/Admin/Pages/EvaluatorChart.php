<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Widgets\FinalProjectEvaluatorChart;
use Filament\Pages\Page;

class EvaluatorChart extends Page
{
    protected static ?string $navigationIcon = 'phosphor-presentation-chart';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.admin.pages.final-project-graph';
    protected static ?string $title = '';
    public static function getNavigationLabel(): string
    {
        return __('Evaluator Chart');
    }

    protected static ?string $navigationGroup = 'Statistics';
    public static function getNavigationGroup(): ?string
    {
        return __('Statistics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FinalProjectEvaluatorChart::class,
        ];
    }
}
