<?php

namespace App\Filament\Admin\Resources\InventarisResource\Pages;

use App\Exports\InventarisExport;
use App\Actions\FilamentImport\Action\ImportAction;
use App\Filament\Admin\Resources\InventarisResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Konnco\FilamentImport\Actions\ImportField;
use App\Models\Lecturer;
use App\Models\Inventaris;
use Illuminate\Support\Carbon;

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
            ImportAction::make()
                ->fields([
                    ImportField::make('lecturer_id'),
                    ImportField::make('name'),
                    ImportField::make('date'),
                    ImportField::make('price'),
                    ImportField::make('condition'),
                    ImportField::make('quantity'),
                    ImportField::make('registration_number'),
                    ImportField::make('is_found'),
                    ImportField::make('is_used'),
                ])
                ->hidden(auth()->user()->role !== '0')
                ->handleRecordCreation(function ($data) {
                    $lecturer = Lecturer::where('nip', $data['lecturer_id'])->first();
                    if ($lecturer) {
                        $data['lecturer_id'] = $lecturer->id;
                    }

                    $date = $data['date'] ? Carbon::createFromFormat('d/m/Y', $data['date']) : null;
                    
                    $newData = [
                        'lecturer_id' => $data['lecturer_id'],
                        'name' => $data['name'],
                        'price' => $data['price'],
                        'date' => $date,
                        'condition' => $data['condition'],
                        'quantity' => $data['quantity'],
                        'registration_number' => $data['registration_number'],
                        'is_found' => $data['is_found'],
                        'is_used' => $data['is_used'],
                    ];

                    $inventaris = Inventaris::create($newData);

                    return $inventaris;
                }),
            Action::make('export')
                ->action(fn () => $this->export()),
        ];
    }
}
