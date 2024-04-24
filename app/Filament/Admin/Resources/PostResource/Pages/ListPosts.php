<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

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
