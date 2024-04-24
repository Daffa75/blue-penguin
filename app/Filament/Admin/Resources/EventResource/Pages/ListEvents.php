<?php

namespace App\Filament\Admin\Resources\EventResource\Pages;

use App\Filament\Admin\Resources\EventResource;
use Filament\Resources\Components\Tab;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Sarjana' => Tab::make()->query(fn ($query) => $query->where('website', 'bachelor')),
            'Magister' => Tab::make()->query(fn ($query) => $query->where('website', 'master')),
            'All' => Tab::make('All'),
        ];
    }
}
