<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Actions\FilamentImport\Action\ImportAction;
use App\Filament\Admin\Resources\InternshipResource;
use App\Models\Internship;
use App\Models\Lecturer;
use App\Models\Student;
use Konnco\FilamentImport\Actions\ImportField;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListInternships extends ListRecords
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->hidden(auth()->user()->role !== '0'),
            ImportAction::make()
                ->fields([
                    ImportField::make('nim1'),
                    ImportField::make('nim2'),
                    ImportField::make('lecturer_id'),
                    ImportField::make('company_name'),
                    ImportField::make('location'),
                    ImportField::make('job_description'),
                    ImportField::make('supervisor_name'),
                    ImportField::make('start_date'),
                    ImportField::make('end_date'),
                ])
                ->hidden(auth()->user()->role !== '0')
                ->handleRecordCreation(function ($data) {
                    $lecturer = Lecturer::where('nip', $data['lecturer_id'])->first();
                    if ($lecturer) {
                        $data['lecturer_id'] = $lecturer->id;
                    }

                    $startDate = $data['start_date'] ? Carbon::createFromFormat('d/m/Y', $data['start_date']) : null;
                    $endDate = $data['end_date'] ? Carbon::createFromFormat('d/m/Y', $data['end_date']) : null;
                    $newData = [
                        'lecturer_id' => $data['lecturer_id'],
                        'company_name' => $data['company_name'],
                        'location' => $data['location'],
                        'job_description' => $data['job_description'],
                        'supervisor_name' => $data['supervisor_name'],
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ];

                    if (isset($data['nim2'])) {
                        $student2 = Student::where('nim', $data['nim2'])->first();
                        if ($student2) {
                            $newData['student2_id'] = $student2->id;
                        }
                    }

                    $internship = Internship::create($newData);

                    $student1 = Student::where('nim', $data['nim1'])->first();
                    if ($student1) {
                        $internship->student()->attach($student1->id);
                    }

                    if (isset($data['nim2']) && $student2) {
                        $internship->student()->attach($student2->id);
                    }

                    return $internship;
                })
        ];
    }
}
