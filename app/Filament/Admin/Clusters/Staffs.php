<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Staffs extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }

    public static function getPluralLabel(): ?string
    {
        return __('Staffs');
    }

    protected static ?int $navigationSort = 5;

}
