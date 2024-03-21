<?php

namespace App\Filament\Admin\Resources\InventarisResource\Pages;

use App\Filament\Admin\Resources\InventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventaris extends ListRecords
{
    protected static string $resource = InventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
