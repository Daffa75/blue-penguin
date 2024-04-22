<?php

namespace App\Filament\Admin\Resources\InventarisResource\Pages;

use App\Exports\InventarisExport;
use App\Filament\Admin\Resources\InventarisResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListInventaris extends ListRecords
{
    protected static string $resource = InventarisResource::class;

    private function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);

        $inventaris = $query->get();

        return new InventarisExport($inventaris);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('export')
                ->action(fn () => $this->export())
        ];
    }
}
