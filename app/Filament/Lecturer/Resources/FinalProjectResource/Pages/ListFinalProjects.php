<?php

namespace App\Filament\Lecturer\Resources\FinalProjectResource\Pages;

use App\Actions\FilamentImport\Action\ImportAction;
use App\Exports\FinalProjectsExport;
use App\Filament\Lecturer\Resources\FinalProjectResource;
use App\Models\FinalProject;
use App\Models\Lecturer;
use App\Models\Student;
use Closure;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Konnco\FilamentImport\Actions\ImportField;
use Filament\Actions\Action;

class ListFinalProjects extends ListRecords
{
    protected static string $resource = FinalProjectResource::class;
    protected function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }

    private function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);

        $finalProjects = $query->get();

        $currentUrl = FinalProjectResource::getUrl();

        $currentUrl = explode('/', $currentUrl);

        $currentUrl = $currentUrl[count($currentUrl) - 1];

        return new FinalProjectsExport($finalProjects, $currentUrl);
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->fields([
                    ImportField::make('nim'),
                    ImportField::make('title'),
                    ImportField::make('supervisorOne'),
                    ImportField::make('supervisorTwo'),
                    ImportField::make('evaluatorOne'),
                    ImportField::make('evaluatorTwo'),
                    ImportField::make('submitted_at'),
                    ImportField::make('status')
                ])
                ->hidden(auth()->user()->role !== '0')
                ->handleRecordCreation(function ($data) {
                    $keysToAdd = [
                        'title',
                        'submitted_at',
                        'status',
                    ];

                    $evaluatorList = [
                        $data['evaluatorOne'],
                        $data['evaluatorTwo'],
                    ];

                    $newData = [];
                    // Loop through the keys to add
                    foreach ($keysToAdd as $key) {
                        // Check if the key exists in the original array
                        if (array_key_exists($key, $data)) {
                            // If it exists, copy it to the new array
                            $newData[$key] = $data[$key];
                        } else {
                            // If it doesn't exist, add it with a default value of null
                            $newData[$key] = null;
                        }
                    }

                    $studentId = Student::where('nim', '=', $data['nim'])->get()->first()->id;
                    $newData['student_id'] = $studentId;


                    $year = $data['submitted_at'];
                    $newData['submitted_at'] = Carbon::parse($year);

                    $evaluatorIds = function () use ($evaluatorList) {
                        $lecturers = Lecturer::whereIn('nip', $evaluatorList)->get();
                        return $lecturers->pluck('id')->toArray();
                    };

                    $supervisorOneId = Lecturer::where('nip', '=', $data['supervisorOne'])->get()->first()->id;
                    
                    $supervisorTwoId = null;
                    if (!empty($data['supervisorTwo'])) {
                        $supervisorTwoId = Lecturer::where('nip', '=', $data['supervisorTwo'])->get()->first()->id;
                    }


                    $newFinalProject = function () use ($newData, $supervisorOneId, $supervisorTwoId, $evaluatorIds) {
                        $finalProject = FinalProject::create($newData);
                        $finalProject->lecturers()->attach($supervisorOneId, ['role' => 'supervisor 1']);
                        $finalProject->lecturers()->attach($supervisorTwoId, ['role' => 'supervisor 2']);
                        $finalProject->lecturers()->attach($evaluatorIds(), ['role' => 'evaluator']);

                        return $finalProject;
                    };
                    return $newFinalProject();
                }),
            Action::make('export')
                ->action(fn () => $this->export())
                ->hidden(auth()->user()->role !== '0')
        ];
    }
}
