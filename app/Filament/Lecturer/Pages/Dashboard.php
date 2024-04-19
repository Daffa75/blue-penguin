<?php

namespace App\Filament\Lecturer\Pages;

use App\Filament\Widgets;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    public function getColumns(): int|string|array
    {
        return 2;
    }

    protected static string $view = 'filament.widgets.final-project-stats-overview-custom';

    public function getHeaderWidgets(): array
    {
        return [
            Widgets\AccountOverviewCustom::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            Widgets\QuotaOverview::class,
            Widgets\PublicationChart::class,
            Widgets\PublicationLineChart::class
        ];
    }
}
